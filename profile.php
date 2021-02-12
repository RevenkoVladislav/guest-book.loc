<?php
session_start();
require_once ("db_connect.php");

if(!empty($_GET['id'])){
    $id = $_GET['id'];
}

if($id == $_SESSION['userLogin']){
    header("Location: /account.php?page={$_SESSION['id']}");
}

$query = "SELECT * FROM gb.gb_user WHERE login = '$id'";
$result = mysqli_query($link, $query);
foreach($result as $data){};
echo $data['userLogin'];

if(empty($data)){
    $_SESSION['error'] = "<label class='has-error'>Пользователь с таким логином не найден</label>";
header("Location: /index.php");
}
?>

<!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="utf-8">
        <title><?= $data['login']; ?></title>
<link rel="stylesheet" href="css/bootstrap/css/bootstrap.css">
<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div id="wrapper">
    <h1>Профиль - <?= $data['login']; ?></h1>
    <div id="form">
        <form action="" class="accountUser" method="POST">
            <p>Имя<input class="form-control" type="text" name="userName" value="<?= $data['userName']; ?>" disabled></p>
            <p>Фамилия<input class="form-control" type="text" name="userSecondName" value="<?= $data['userSecondName']; ?>" disabled></p>
            <p>Логин<input class="form-control" type="text" id="login" name="userLogin" value="<?= $data['login']; ?>" disabled></p>
            <p>День рождения<input class="form-control" type="date" name="userBD" value="<?= $data['userBD']; ?>" disabled></p>
            <p>О пользователе<textarea class="form-control" name="userSelf" disabled><?= $data['userSelf']; ?></textarea></p>
</body>
</html>
