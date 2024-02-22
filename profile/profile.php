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
                        <a class="nav-link" href="index.html">Вернуться на главную</a>
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
                            <input type="text" class="form-control" id="route_name" name="route_name" required>
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