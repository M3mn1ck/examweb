<?php
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    if ($error === 'user_not_found') {
        echo '<p>Пользователь не найден</p>';
    } elseif ($error === 'wrong_password') {
        echo '<p>Неверный пароль</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="css/st.css">
</head>
<body>
    <form action="../auth/login_process.php" method="post">
        <h2>Вход</h2>
        <input type="text" name="username" placeholder="Имя пользователя" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <input type="submit" value="Войти">
    </form>
</body>
</html>