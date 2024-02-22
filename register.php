<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="css/st.css">
</head>
<body>
    <form action="../auth/register_process.php" method="post">
        <h2>Регистрация</h2>
        <input type="text" name="username" placeholder="Имя пользователя" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <input type="submit" value="Зарегистрироваться">
    </form>
    <p>Уже зарегистрированны? <a href="login.php">Войти</a></p>
</body>
</html>
