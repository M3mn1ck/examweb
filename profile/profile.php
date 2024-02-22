<?php
session_start();

// Проверяем, установлена ли сессия с именем пользователя
if (!isset($_SESSION['username'])) {
    // Если сессия не установлена, перенаправляем на страницу входа
    header("Location: ../login.php");
    exit();
}

// Подключаемся к базе данных
include_once '../configs/config.php';

$username = $_SESSION['username'];

// Обработка формы создания заявки
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $route_name = $_POST['route_name'];
    $excursion_date = $_POST['excursion_date'];
    $people_count = $_POST['people_count'];
    $duration = $_POST['duration'];

    // Рассчитываем итоговую стоимость
    $base_cost = 0;
    switch ($duration) {
        case '30m':
            $base_cost = 300;
            break;
        case '1h':
            $base_cost = 1000;
            break;
        case '2h':
            $base_cost = 1500;
            break;
        case '3h':
            $base_cost = 2000;
            break;
    }
    // Увеличиваем стоимость на выходные
    $excursion_date_time = strtotime($excursion_date);
    $weekend = (date('N', $excursion_date_time) >= 6); // Проверяем, является ли день выходным
    if ($weekend) {
        $base_cost += 1000;
    }

    // Рассчитываем итоговую стоимость
    $total_cost = $base_cost * $people_count;

    // Вставляем данные в базу данных
    $sql = "INSERT INTO applications (username, route_name, excursion_date, people_count, duration, total_cost)
            VALUES ('$username', '$route_name', '$excursion_date', '$people_count', '$duration', '$total_cost')";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['notification'] = "Заявка успешно создана.";
    } else {
        echo "Ошибка: " . $sql . "<br>" . $conn->error;
    }
}


$sql = "SELECT * FROM applications WHERE username='$username'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="assets/styles.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous">
        <script src="js/route.js"></script> 
    <style>

        .create-application-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>

<body>


    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">

            <a class="navbar-brand" href="#">
                <img src="favicon.png" alt="Логотип" height="50" title="Логотип">
            </a>


            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.html">Вернуться на главную</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <button type="button" class="btn btn-success create-application-button" data-bs-toggle="modal" data-bs-target="#createApplicationModal">
        Создать заявку
    </button>


    <div class="container mt-3">
        <?php
        if (isset($_SESSION['notification'])) {
            echo '<div class="alert alert-success" role="alert">' . $_SESSION['notification'] . '</div>';
            unset($_SESSION['notification']);
        }
        ?>
            <?php


if (isset($_SESSION['notification'])) {
    echo '<div class="container mt-3">';
    echo '<div class="alert alert-success" role="alert">';
    echo $_SESSION['notification'];
    echo '</div>';
    echo '</div>';


    unset($_SESSION['notification']);
}
?>
    </div>

    <!-- Таблица с данными заявок -->
    <div class="container mt-3">
        <h2>Мои заявки</h2>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Название маршрута</th>
                    <th scope="col">Дата экскурсии</th>
                    <th scope="col">Итоговая стоимость</th>
                    <th scope="col">Действия</th>
                </tr>
            </thead>
            <tbody class="ways">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<th scope='row'>" . $row['id'] . "</th>";
                        echo "<td>" . $row['route_name'] . "</td>";
                        echo "<td>" . $row['excursion_date'] . "</td>";
                        echo "<td>" . $row['total_cost'] . " рублей</td>";
                        echo "<td>";
                        echo '<form method="post" action="delete_application.php">';
                        echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
                        echo '<button type="submit" class="btn btn-danger">Удалить</button>';
                        echo '</form>';
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Нет заявок</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">Предыдущая</a></li>
                <li class="page-item active" aria-current="page">
                    <span class="page-link">1</span>
                </li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">Следующая</a></li>
            </ul>
        </nav>
    </div>


    <div class="modal fade" id="createApplicationModal" tabindex="-1" role="dialog" aria-labelledby="createApplicationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createApplicationModalLabel">Создать заявку</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="mb-3">
    <label for="route_name" class="form-label">Название маршрута</label>
    <select class="form-select" id="route_name" name="route_name" required>
        <option value="">Выберите маршрут</option>
        <option value="Нескучный сад">Нескучный сад</option>
<option value="Воробьевы горы">Воробьевы горы</option>
<option value="Арбат - душа Москвы">Арбат - душа Москвы</option>
<option value="Для всё семьи: планетрий, зоопарк и музеи">Для всё семьи: планетрий, зоопарк и музеи</option>
<option value="Исторический маршрут для всей семьи">Исторический маршрут для всей семьи</option>
<option value="Знаковые мосты Москвы">Знаковые мосты Москвы</option>
<option value="Ахматовские места в Москве">Ахматовские места в Москве</option>
<option value="Авторские экскурсионные маршруты «Японцы в Москве»: маршрут первый и второй">Авторские экскурсионные маршруты «Японцы в Москве»: маршрут первый и второй</option>
<option value="Авторский маршрут Леднёва А.В. «Остров Сыромятники»">Авторский маршрут Леднёва А.В. «Остров Сыромятники»</option>
<option value="Авторский маршрут Королькова А.Ю. «Правда о Соколе»">Авторский маршрут Королькова А.Ю. «Правда о Соколе»</option>
<option value="Авторский маршрут Советова А.В. «Зелёное Кольцо Москвы»">Авторский маршрут Советова А.В. «Зелёное Кольцо Москвы»</option>
<option value="Авторский маршрут Екатерининской Е.Д. «Прошлое и настоящее Филёвского парка»">Авторский маршрут Екатерининской Е.Д. «Прошлое и настоящее Филёвского парка»</option>
<option value="Авторский маршрут Безносик Ю.В. «Дореволюционная Шаболовка»">Авторский маршрут Безносик Ю.В. «Дореволюционная Шаболовка»</option>
<option value="Авторский маршрут Кувшиновой С.К. «От пустоши Леоново до Сада будущего»">Авторский маршрут Кувшиновой С.К. «От пустоши Леоново до Сада будущего»</option>
<option value="Авторский маршрут Коробкиной Н.А. «Здесь был аэропорт»">Авторский маршрут Коробкиной Н.А. «Здесь был аэропорт»</option>
<option value="Авторский маршрут Гнилорыбова П.А. «Троице-Лыково: село внутри большого города»">Авторский маршрут Гнилорыбова П.А. «Троице-Лыково: село внутри большого города»</option>
<option value="Авторский маршрут Ермолаевой Г.А. «Удивительные Сокольники»">Авторский маршрут Ермолаевой Г.А. «Удивительные Сокольники»</option>
<option value="Авторский маршрут Кузнецовой Л.В. «Ребятам о зверятах»">Авторский маршрут Кузнецовой Л.В. «Ребятам о зверятах»</option>
<option value="Авторский маршрут Боровских М.В. «Герцогство Беляевское»">Авторский маршрут Боровских М.В. «Герцогство Беляевское»</option>
<option value="Авторский маршрут Беляевой В.Н. «Зеленоградские аллеи»">Авторский маршрут Беляевой В.Н. «Зеленоградские аллеи»</option>
<option value="Авторский маршрут Шитикова А.Ю. «Как стать почетным гражданином города»">Авторский маршрут Шитикова А.Ю. «Как стать почетным гражданином города»</option>
<option value="Авторский маршрут Русина А.О. «Новый район с древней историей»">Авторский маршрут Русина А.О. «Новый район с древней историей»</option>
<option value="Парк Горького и окрестности">Парк Горького и окрестности</option>
<option value="Космическая одиссея">Космическая одиссея</option>
<option value="Прогулки по Сокольникам">Прогулки по Сокольникам</option>
<option value="Успеть до полуночи: Москва для торопливых">Успеть до полуночи: Москва для торопливых</option>
<option value="ВДНХ: побег от реальности">ВДНХ: побег от реальности</option>
<option value="Прогулки по ВДНХ">Прогулки по ВДНХ</option>
<option value="Лесной эскапизм в Мещерском парке">Лесной эскапизм в Мещерском парке</option>
<option value="Воробьевы горы и окрестности">Воробьевы горы и окрестности</option>
<option value="Прогулка по «Зарядью»">Прогулка по «Зарядью»</option>
<option value="Через скверы к звездам">Через скверы к звездам</option>
<option value="Выходной в «русском Версале»">Выходной в «русском Версале»</option>
<option value="Лесные лабиринты Покровского-Стрешнева">Лесные лабиринты Покровского-Стрешнева</option>
<option value="Тропы «Лосиного острова»">Тропы «Лосиного острова»</option>
<option value="Пешком по Царицыно">Пешком по Царицыно</option>
<option value="Я познаю мир">Я познаю мир</option>
<option value="В центр проездом">В центр проездом</option>
<option value="Камерный выходной">Камерный выходной</option>
<option value="Эстетика Якиманки">Эстетика Якиманки</option>
<option value="Культпросвет">Культпросвет</option>
<option value="Тропа знаний">Тропа знаний</option>
<option value="Фотоохота на ВДНХ">Фотоохота на ВДНХ</option>
<option value="Москва из кино">Москва из кино</option>
<option value="Друзья, дети, суббота">Друзья, дети, суббота</option>
<option value="Царицыно для юных исследователей">Царицыно для юных исследователей</option>
<option value="Знакомство с Царицыно">Знакомство с Царицыно</option>
<option value="По следам великих писателей">По следам великих писателей</option>
<option value="Четыре стихии искусства">Четыре стихии искусства</option>
<option value="Новый взгляд на историю: современные музеи Москвы">Новый взгляд на историю: современные музеи Москвы</option>
<option value="Путешествие в центр Москвы">Путешествие в центр Москвы</option>
<option value="Невероятные приключения на Пресне">Невероятные приключения на Пресне</option>
<option value="Загадки парка Коломенское">Загадки парка Коломенское</option>
<option value="В погоне за призраками Москвы">В погоне за призраками Москвы</option>
<option value="Путешествие по Москве на трамвае №39">Путешествие по Москве на трамвае №39</option>
<option value="Прогулка по паркам с виртуальным гидом">Прогулка по паркам с виртуальным гидом</option>
<option value="История Великих Побед">История Великих Побед</option>
<option value="Щербинка и округа">Щербинка и округа</option>
<option value="Путевые дороги в историю">Путевые дороги в историю</option>
<option value="Даниловская слобода">Даниловская слобода</option>
<option value="Можно ли изучать Москву по конфетным фантикам">Можно ли изучать Москву по конфетным фантикам</option>
<option value="Велопрогулка по самому зеленому округу Москвы">Велопрогулка по самому зеленому округу Москвы</option>
<option value="Заправлены в планшеты космические карты">Заправлены в планшеты космические карты</option>
<option value="Измайлово известное и неизвестное">Измайлово известное и неизвестное</option>
<option value="Война и Мир спального района.">Война и Мир спального района.</option>
<option value="Дачная местность «Новое Кунцево» - центр. «Из Кунцево голодным не уедешь».">Дачная местность «Новое Кунцево» - центр. «Из Кунцево голодным не уедешь».</option>
<option value="Экскурсия в стиле ЭКО">Экскурсия в стиле ЭКО</option>
<option value="Моё Ясенево">Моё Ясенево</option>
<option value="Палитра Подолья: поселение Щаповское">Палитра Подолья: поселение Щаповское</option>
<option value="Покровское-Стрешнево на рубеже войны">Покровское-Стрешнево на рубеже войны</option>
<option value="Плющиха: больше чем три тополя">Плющиха: больше чем три тополя</option>
<option value="Москва экспериментальная. Эксперименты конца 19 начала 20 веков в районе Девичьего поля и Усачевки">Москва экспериментальная. Эксперименты конца 19 начала 20 веков в районе Девичьего поля и Усачевки</option>
<option value="ПОД МАСКОЙ ВРЕМЕНИ">ПОД МАСКОЙ ВРЕМЕНИ</option>
<option value="Теперь театр здесь. А прежде">Теперь театр здесь. А прежде</option>
<option value="Архитектурная прогулка по ВДНХ. Центральный вход">Архитектурная прогулка по ВДНХ. Центральный вход</option>
<option value="Физика и жизнь: Москва Андрея Сахарова">Физика и жизнь: Москва Андрея Сахарова</option>
<option value="Где течет Раменка: семейная прогулка в парке 50-летия Октября">Где течет Раменка: семейная прогулка в парке 50-летия Октября</option>
<option value="Городские девчонки">Городские девчонки</option>
<option value="История Битвы за Москву">История Битвы за Москву</option>
<option value="Культура и искусство вокруг Ваганьковского холма">Культура и искусство вокруг Ваганьковского холма</option>
<option value="Мастерская сказок: прогулка по ВДНХ">Мастерская сказок: прогулка по ВДНХ</option>
<option value="Москва купеческая">Москва купеческая</option>
<option value="Видеогид «Москва в деталях с Владимиром Раевским»">Видеогид «Москва в деталях с Владимиром Раевским»</option>
<option value="На берегах Химкинского водохранилища">На берегах Химкинского водохранилища</option>
<option value="Набережными Москвы-реки">Набережными Москвы-реки</option>
<option value="Неповторимый воздух весны">Неповторимый воздух весны</option>
<option value="Островная жизнь">Островная жизнь</option>
<option value="От центра до окраин: архитектурные истории Москвы">От центра до окраин: архитектурные истории Москвы</option>
<option value="По аллеям Царицына">По аллеям Царицына</option>
<option value="Полумарафон «Моя столица»">Полумарафон «Моя столица»</option>
<option value="Приключения в большом городе">Приключения в большом городе</option>
<option value="Пять веков Царицына">Пять веков Царицына</option>
<option value="Сказочные оранжереи Царицына">Сказочные оранжереи Царицына</option>
<option value="Столичная прогулка: Москва романтическая">Столичная прогулка: Москва романтическая</option>
<option value="Столичная прогулка: знаковые здания Москвы">Столичная прогулка: знаковые здания Москвы</option>
<option value="В эпицентре городской жизни">В эпицентре городской жизни</option>
<option value="ВДНХ: прогулка к «Золотому колосу»">ВДНХ: прогулка к «Золотому колосу»</option>
<option value="Вези меня, «Букашка»">Вези меня, «Букашка»</option>
<option value="Вокруг и около Кремля">Вокруг и около Кремля</option>
<option value="Выходной на улице Вильгельма Пика">Выходной на улице Вильгельма Пика</option>
<option value="Я покажу тебе Москву">Я покажу тебе Москву</option>
<option value="Петр Великий, немцы России и маленькая Европа в Москве">Петр Великий, немцы России и маленькая Европа в Москве</option>
<option value="Сделано в Зеленограде">Сделано в Зеленограде</option>
<option value="Утраченная Ходынка">Утраченная Ходынка</option>
<option value="1812-2022 усадьба Воронцово. Тайны сгоревших архивов">1812-2022 усадьба Воронцово. Тайны сгоревших архивов</option>
<option value="От Авангарда до Барокко и обратно">От Авангарда до Барокко и обратно</option>
<option value="Москва научная">Москва научная</option>
<option value="Сокольники - жемчужина Москвы">Сокольники - жемчужина Москвы</option>
<option value="Литературный трамвайчик">Литературный трамвайчик</option>
<option value="Рукотворная природа Москвы">Рукотворная природа Москвы</option>
<option value="Тушино - город авиации">Тушино - город авиации</option>
<option value="Неизведанный север Москвы">Неизведанный север Москвы</option>
<option value="В нежных объятьях Авдотьи - Плющихи">В нежных объятьях Авдотьи - Плющихи</option>
<option value="Прогулка по Инновационному центру Сколково">Прогулка по Инновационному центру Сколково</option>
<option value="Сто тринадцатая любовь. Семейное счастье или роковая ошибка.">Сто тринадцатая любовь. Семейное счастье или роковая ошибка.</option>
<option value="Коммунарка беговая">Коммунарка беговая</option>
<option value="Бегом вокруг Садового кольца">Бегом вокруг Садового кольца</option>

    </select>
</div>

                    <div class="mb-3">
                        <label for="excursion_date" class="form-label">Дата экскурсии</label>
                        <input type="date" class="form-control" id="excursion_date" name="excursion_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="people_count" class="form-label">Количество человек</label>
                        <input type="number" class="form-control" id="people_count" name="people_count" required>
                    </div>
                    <div class="mb-3">
                        <label for="duration" class="form-label">Время</label>
                        <select class="form-select" id="duration" name="duration" required>
                            <option value="30m">30 минут</option>
                            <option value="1h">1 час</option>
                            <option value="2h">2 часа</option>
                            <option value="3h">3 часа</option>
                        </select>
                    </div>
                    <div class="mb-3">
    <label for="guide" class="form-label">Выбрать гида</label>
    <select class="form-select" id="guide" name="guide" required>
        <option value="">Выберите гида</option>
        <option value="Пожилой Гибон">Пожилой Гибон</option>
        <option value="Саша Грей">Саша Грей</option>
        <option value="Владимир Путин">Владимир Путин</option>
    </select>
</div>
                    <button type="submit" class="btn btn-primary">Создать заявку</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>


    <footer class="bg-dark text-center text-white mt-5">
        <div class="text-center p-3">
            Контактные данные: ваш адрес, телефон, email и т.д.
        </div>
    </footer>
</body>

</html>