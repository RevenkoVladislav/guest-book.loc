<?php
session_start();

if (!empty($_POST['sub'])) {
    header('refresh: 1');
}

if (!empty($_POST['logoutUser'])) {
    session_destroy();
    header('Location: /index.php');
}

if (!empty($_POST['logoutModerator'])) {
    session_destroy();
    header('Location: /index.php');
}

if (!empty($_POST['profile'])){
    header("Location: /account.php?page={$_SESSION['id']}");
}

if (!empty($_POST['subUser'])) {
    if(!empty($_SESSION['loginGB'])){
        unset($_SESSION['loginGB']);
        unset($_SESSION['authGB']);
        unset($_SESSION['success']);
    }
    $login = htmlspecialchars($_POST['userLogin']);
    $password = md5(md5(htmlspecialchars($_POST['userPassword'])));
    $query = "SELECT * FROM gb.gb_user WHERE login = '$login' AND password = '$password'";
    $res = mysqli_query($link, $query);
    if (mysqli_fetch_assoc($res)) {
        foreach($res as $i){}
        $_SESSION['userLogin'] = $login;
        $_SESSION['authUser'] = true;
        $_SESSION['id'] = $i['id'];
        $_SESSION['success'] = 'Добро пожаловать - ' . $_SESSION['userLogin'];
        $_SESSION['message'] = "<label id='label' class='success'>" . $_SESSION['success'] . "</label>";
        header('Location: /index.php');
    } else {
        $_SESSION['message'] = "<label id='label' class='has-error'>Неверные данные</label>";
    }
}

//авторизация модератора
if (!empty($_POST['moderatorSub'])) {
    if(!empty($_SESSION['userLogin'])){
        unset($_SESSION['userLogin']);
        unset($_SESSION['authUser']);
        unset($_SESSION['success']);
    }
    $log = $_POST['loginModerator'];
    $pass = $_POST['passModerator'];
    $query = "SELECT * FROM gb.gb_moderator WHERE log='$log' AND pass='$pass'";
    $result = mysqli_query($link, $query);
    $result = mysqli_fetch_assoc($result);

    if ($result) {
        $_SESSION['loginGB'] = $log;
        $_SESSION['success'] = 'Добро пожаловать - ' . $_SESSION['loginGB'];
        $_SESSION['message'] = "<label id='label' class='success'>" . $_SESSION['success'] . "</label>";
        $_SESSION['authGB'] = true;
        header('Location: /index.php');
    } else {
        $_SESSION['message'] = "<label id='label' class='has-error'>Неверные данные</label>";
    }
}

//модерирование записи для модератора
if (!empty($_GET['delete']) AND $_SESSION['authGB'] === true) {
    $delComment = (int)$_GET['delete'];
    if (is_integer($delComment) AND $delComment != 0) {
        $query = "DELETE FROM gb.gb_comment WHERE id = '$delComment'";
        mysqli_query($link, $query) or die;
    } else {
        echo "Произошла ошибка удаления!!!";

    }
}

if (!empty($_GET['hide']) AND $_SESSION['authGB'] === true) {
    $hideComment = (int)$_GET['hide'];
    if (is_integer($hideComment) AND $hideComment != 0) {
        $query = "UPDATE gb.gb_comment SET moderator = FALSE WHERE id = '$hideComment'";
        mysqli_query($link, $query) or die;
    } else {
        echo "Скрыть комментарий не удалось !!!";
    }
}

if (!empty($_GET['open']) AND $_SESSION['authGB'] === true) {
    $openComment = (int)$_GET['open'];
    if (is_integer($openComment) AND $openComment != 0) {
        $query = "UPDATE gb.gb_comment SET moderator = TRUE WHERE id = '$openComment'";
        mysqli_query($link, $query) or die;
    } else {
        echo "Невозможно отобразить комментарий !!!";
    }
}

//авторизация модератора или юзера
if ((!$_SESSION['authUser']) AND (!$_SESSION['authGB'])): ?>
    <div id="formUser">
        <form action="" method="POST" class="formUser">
            <?= $_SESSION['message']; ?>
            <input type="text" class="form-control" name="userLogin" placeholder="Ваш логин" required>
            <input type="password" class="form-control" name="userPassword" placeholder="Ваш пароль" required>
            <input type="submit" name="subUser" class="btn btn-info btn-block" value="авторизация">
            <a href="register.php">Зарегистрироваться</a>
        </form>
    </div>
<?php elseif ($_SESSION['authUser']): ?>
    <div id="formUser">
        <form action="" method="POST" class="formUser">
            <input type="submit" name="profile" value="В личный кабинет" id="btn" class="btn btn-info btn-block">
            <input type="submit" name="logoutUser" value="Выйти" id="btn" class="btn btn-info btn-block">
            <?= $_SESSION['message']; ?>
        </form>
        <style>
            #formUser{
                position: absolute;
                left: 10px;
                width: 300px;
                padding: 5px;
            }
        </style>
    </div>
<?php elseif ($_SESSION['authGB']): ?>
    <div id="moder">
        <form action="" method="POST" class="formUser">
            <input type="submit" name="logoutModerator" value="Разлогиниться" id="btn" class="btn btn-info btn-block">
            <?= $_SESSION['message']; ?>
        </form>
        <style>
            #moder{
                position: absolute;
                right: 275px;
                width: 300px;
                margin: 5px;
            }
        </style>

    </div>
<?php endif; ?>
<style>
    .formUser {
        position: absolute;
        left: 65px;
        width: 300px;
        padding: 10px;
        margin: 20px;
    }

    .formUser input {
        margin: 5px;
    }
</style>

<?php if (!empty($_GET['moderator'])): { ?>
    <div>
        <form action="" method="post" class="formModerator">
            <input type="text" class="form-control" name="loginModerator" required placeholder="name">
            <input type="password" class="form-control" name="passModerator" required placeholder="pass">
            <input type="submit" class="btn btn-info btn-block" name="moderatorSub">
        </form>
        <style>
            .formModerator {
                position: absolute;
                right: 150px;
                width: 300px;
                padding: 10px;
                margin: 20px;
            }

            .formModerator input {
                margin: 5px;
            }
        </style>
    </div>
<?php } endif; ?>

<style>
    .success{
        color: green;
    }
    .has-error{
        color: red;
    }
</style>
