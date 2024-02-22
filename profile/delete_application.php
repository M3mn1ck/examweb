<?php

include_once '../configs/config.php';


if (isset($_POST['id'])) {
    $id = $_POST['id'];


    $sql = "DELETE FROM applications WHERE id = '$id'";

    // Выполняем запрос
    if ($conn->query($sql) === TRUE) {

        $conn->close();
        

        session_start();
        $_SESSION['notification'] = "Заявка успешно удалена";


        header("Location: profile.php");
        exit();
    } else {

        echo "Ошибка при удалении заявки: " . $conn->error;
    }
} else {

    echo "Ошибка: Идентификатор записи не был отправлен";
}
?>
