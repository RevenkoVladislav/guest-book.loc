<?php
session_start();

if (empty($_SESSION['authUser'])) {
    header('Location: /index.php');
}

if (!empty($_SESSION['accountCheck'])) {
    unset($_SESSION['accountCheck']);
}

if(!empty($_SESSION['messagePass'])){
    unset($_SESSION['messagePass']);
}

require_once 'db_connect.php';

$query = "SELECT * FROM gb.gb_user WHERE id = '{$_SESSION['id']}'";
$result = mysqli_query($link, $query) or die;
$data = mysqli_fetch_assoc($result);

if (!empty($_POST['accountChange'])) {
    $login = htmlspecialchars($_POST['userLogin']);
    $userName = htmlspecialchars($_POST['userName']);
    $userSecondName = htmlspecialchars($_POST['userSecondName']);
    $email = htmlspecialchars($_POST['userEmail']);
    $BD = date('Y-m-d', strtotime($_POST['userBD']));
    $self = htmlspecialchars($_POST['userSelf']);

    $errors = validateChange($login, $email, $link);

    if (empty($errors)) {
        $query = "UPDATE gb.gb_user SET userName='$userName', userSecondName='$userSecondName', email='$email', login='$login', userBD='$BD', userSelf='$self' WHERE id='{$_SESSION['id']}'";
        if (mysqli_query($link, $query)) {
            $_SESSION['accountCheck'] = true;
            $_SESSION['userLogin'] = $login;
            $_SESSION['success'] = 'Добро пожаловать - ' . $_SESSION['userLogin'];
            $_SESSION['message'] = "<label id='label' class='success'>" . $_SESSION['success'] . "</label>";
            $_SESSION['messageSuccess'] = "<label id='label' class='success'>Данные успешно изменены</label>";
            header("refresh: 1");
        } else {
            $_SESSION['errorChange'] = "Произошла ошибка";
        }
    }
}

if(!empty($_POST['passwordChange'])){
    $pass = trim(htmlspecialchars($_POST['userPassword']));
    $passRep = trim(htmlspecialchars($_POST['userPasswordRepeat']));
    $errorsPass = validatePassword($pass, $passRep);
    if(empty($errorsPass)){
        $pass = md5(md5($pass));
        $query = "UPDATE gb.gb_user SET password = '$pass' WHERE id='{$_SESSION['id']}'";
        if(mysqli_query($link, $query)){
            $_SESSION['messagePass'] = "<label id='label' class='success'>Пароль изменен</label>";
            header("refresh: 1");
        } else {
            $_SESSION['messagePass'] = "<label id='label' class='errorValidate'>Произошла ошибка</label>";
        }
    }
}


if (!empty($data)) { ?>
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
        <h1>Личный кабинет - <?= $data['login']; ?></h1>
        <?php if ($_SESSION['accountCheck']) { ?>
            <div class="info alert alert-info">
                <?= $_SESSION['messageSuccess']; ?>
            </div>
        <?php } ?>
        <div id="form">
            <form action="" class="accountUser" method="POST">
                <p><input class="form-control" type="text" name="userName" value="<?= $data['userName']; ?>" placeholder="Ваше Имя" required></p>
                <p><input class="form-control" type="text" name="userSecondName" value="<?= $data['userSecondName']; ?>" placeholder="Ваша Фамилия" required></p>
                <p><input class="form-control" type="text" id="email" name="userEmail" value="<?= $data['email']; ?>" placeholder="Ваш Email" required><?php if (!empty($errors['email'])) echo $errors['email']; ?></p>
                <p><input class="form-control" type="text" id="login" name="userLogin" value="<?= $data['login']; ?>" placeholder="Ваш Логин" required><?php if (!empty($errors['login'])) echo $errors['login']; if (!empty($errors['loginLang'])) echo " {$errors['loginLang']}"; if (!empty($errors['loginLen'])) echo $errors['loginLen']; ?></p>
                <p><input class="form-control" type="date" name="userBD" value="<?= $data['userBD']; ?>" required></p>
                <p><textarea class="form-control" name="userSelf" placeholder="О себе" required><?= $data['userSelf']; ?></textarea></p>
                <p><input type="submit" name="accountChange" class="btn btn-info btn-block" value="Изменить данные"></p>
            </form>
            <?php if (!empty($_SESSION['errorChange'])) { ; ?>
                <p class="has-error"><?= $_SESSION['errorChange']; ?></p>
                <?php unset($_SESSION['errorChange']); } ?>
        </div>
        <form action="" method="POST">
            <?php if ($_SESSION['messagePass']) { ?>
                <div class="info alert alert-info">
                    <?= $_SESSION['messagePass']; ?>
                </div>
            <?php } ?>
            <p><input class="form-control" type="password" id="pass" name="userPassword" placeholder="Пароль" required><?php if(!empty($errorsPass['passLen'])) echo $errorsPass['passLen'];?></p>
            <p><input class="form-control" type="password" id="password" name="userPasswordRepeat" placeholder="Повторите пароль" required><?php if(!empty($errorsPass['pass'])) echo $errorsPass['pass'];?></p>
            <p><input type="submit" name="passwordChange" class="btn btn-info btn-block" value="Изменить пароль"></p>
        </form>
        <a href="/index.php">Вернуться на главную.</a>
    </div>

    <style>
        .has-error {
            color: red;
        }
        .errorValidate {
            color: red;
            font-size: 20px;
            font-weight: bold;
        }
    </style>

    </body>
    </html>
<?php } ?>

<?php
function validateChange($login, $email, $link)
{
    $query = "SELECT * FROM gb.gb_user WHERE login = '$login'";
    $result = mysqli_fetch_assoc(mysqli_query($link, $query));
    if ($result) {
        $errorLogin = "<label for='login' class='errorValidate'>Логин занят</label>";
        $errors['login'] = $errorLogin;
    }
    if (!preg_match('#^[a-zA-Z][a-zA-Z0-9-_\.]{1,20}$#', $login)) {
        $errorLang = "<label for='login' class='errorValidate'>Допустимы только латинские буквы и цифры</label>";
        $errors['loginLang'] = $errorLang;
    }
    if (strlen($login) < 4) {
        $errorLoginLen = "<label for='login' class='errorValidate'>Короткий логин (менее 4х символов)</label>";
        $errors['loginLen'] = $errorLoginLen;
    }
    if (filter_var($email, FILTER_VALIDATE_EMAIL) != true) {
        $errorEmail = "<label for='email' class='errorValidate'>Некорректный email</label>";
        $errors['email'] = $errorEmail;
    }
    return $errors;
}

function validatePassword($pass, $passRepeat){
    if ($pass != $passRepeat){
        $errorPass = "<label for='password' class='errorValidate'>Пароли не совпадают</label>";
        $errors['pass'] = $errorPass;
    }
    if(strlen($pass) <= 5){
        $errorLen = "<label for='pass' class='errorValidate'>Слишком короткий пароль(менее 6 символов)</label>";
        $errors['passLen'] = $errorLen;
    }
    return $errors;
}
