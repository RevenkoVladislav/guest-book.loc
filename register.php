<?php
session_start();
require_once ('db_connect.php');

function validate($email, $login, $pass, $passRepeat, $link){
    $query = "SELECT * FROM gb.gb_user WHERE login = '$login'";
    $result = mysqli_fetch_assoc(mysqli_query($link, $query));
    if($result){
        $errorLogin = "<label for='login' class='errorValidate'>Логин занят</label>";
        $errors['login'] = $errorLogin;
    }
    if ($pass != $passRepeat){
        $errorPass = "<label for='password' class='errorValidate'>Пароли не совпадают</label>";
        $errors['pass'] = $errorPass;
    }
    if(strlen($_POST['userPassword']) <= 5){
        $errorLen = "<label for='pass' class='errorValidate'>Слишком короткий пароль(менее 6 символов)</label>";
        $errors['passLen'] = $errorLen;
    }
    if(filter_var($email, FILTER_VALIDATE_EMAIL) != true){
        $errorEmail = "<label for='email' class='errorValidate'>Некорректный email</label>";
        $errors['email'] = $errorEmail;
    }
    if(!preg_match('/^[a-z0-9]+$/i', $login)){
        $errorLang = "<label for='login' class='errorValidate'>Допустимы только латинские буквы и цифры</label>";
        $errors['loginLang'] = $errorLang;
    }
    if(strlen($login) < 4){
        $errorLoginLen = "<label for='login' class='errorValidate'>Короткий логин (менее 4х символов)</label>";
        $errors['loginLen'] = $errorLoginLen;
    }
    return $errors;
}

if(!empty($_SESSION['errorRegistration'])) {
    unset($_SESSION['errorRegistration']);
}

if(!empty($_SESSION['authUser'])){
    header("Location: /index.php");
}

if(!empty($_POST['registration'])){
    $nameUser = htmlspecialchars($_POST['userName']);
    $secondName = htmlspecialchars($_POST['userSecondName']);
    $email = htmlspecialchars($_POST['userEmail']);
    $login = htmlspecialchars($_POST['userLogin']);
    $pass = md5(md5(trim(htmlspecialchars($_POST['userPassword']))));
    $passRepeat = md5(md5(trim(htmlspecialchars($_POST['userPasswordRepeat']))));
    $BD = date('Y-m-d', strtotime($_POST['userBD']));
    $self = htmlspecialchars($_POST['userSelf']);
    $dateRegistration = date('Y-m-d');
    $in = htmlspecialchars($_POST['checkIn']);

    $errors = validate($email, $login, $pass, $passRepeat, $link);

    if(empty($errors)){
        $query = "INSERT INTO gb.gb_user SET login='$login', password='$pass', email='$email', userName='$nameUser', userSecondName='$secondName', userBD='$BD', userSelf='$self', userRegistrationDate='$dateRegistration'";

    if(mysqli_query($link, $query)) {
        $registrationCheck = true;
        if($in){
            $_SESSION['userLogin'] = $login;
            $_SESSION['success'] = 'Добро пожаловать - ' . $_SESSION['userLogin'];
            $_SESSION['message'] = "<label id='label' class='success'>" . $_SESSION['success'] . "</label>";
            $id = mysqli_insert_id($link);
            $_SESSION['id'] = $id;
            $_SESSION['authUser'] = true;
            header("Location: /index.php");
        }
    } else {
        $registrationCheck = false;
        $_SESSION['errorRegistration'] = "Произошла ошибка";
    }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<div id="wrapper">
    <h1>Регистрация</h1>
    <?php if($registrationCheck === true): ?>
        <div class="info alert alert-info">
            Вы успешно зарегистрировались, <a href="/index.php">вернуться на главную.</a>
        </div>
    <?php endif;?>
    <div id="form">
        <form action="" class="regUser" method="POST">
        <p><input class="form-control" type="text" name="userName" placeholder="Ваше имя" <?php if(!empty($_POST['registration'])) echo " value = \"$nameUser\"";?> required></p>
        <p><input class="form-control" type="text" name="userSecondName" placeholder="Ваша Фамилия" <?php if(!empty($_POST['registration'])) echo " value = \"$secondName\"";?> required></p>
        <p><input class="form-control" type="text" id="email" name="userEmail" placeholder="Ваша почта" <?php if(!empty($_POST['registration'])) echo " value = \"$email\"";?> required><?php if(!empty($errors['email'])) echo $errors['email'];?></p>
        <p><input class="form-control" type="text" id="login" name="userLogin" placeholder="Ваш логин" <?php if(!empty($_POST['registration'])) echo " value = \"$login\"";?> required><?php if(!empty($errors['login'])) echo $errors['login']; if(!empty($errors['loginLang'])) echo " {$errors['loginLang']}"; if(!empty($errors['loginLen'])) echo $errors['loginLen'];?></p>
        <p><input class="form-control" type="password" id="pass" name="userPassword" placeholder="Пароль" required><?php if(!empty($errors['passLen'])) echo $errors['passLen'];?></p>
        <p><input class="form-control" type="password" id="password" name="userPasswordRepeat" placeholder="Повторите пароль" required><?php if(!empty($errors['pass'])) echo $errors['pass'];?></p>
        <p><input class="form-control" type="date" name="userBD" <?php if(!empty($_POST['registration'])) echo " value = \"$BD\"";?> placeholder="Введите дату рождения" required></p>
        <p><textarea class="form-control" name="userSelf" placeholder="О себе" required><?php if(!empty($_POST['registration'])) echo $self;?></textarea></p>
        <p><input type="checkbox" id="checkIn" name="checkIn" value="in" <?php if(!empty($in)) echo "checked";?>> <label for="checkIn">Войти в аккаунт при успешной регистрации ?</label></p>
        <p><input type="submit" name="registration" class="btn btn-info btn-block" value="Зарегистрироваться"></p>
    </form>
    <?php if(!empty($_SESSION['errorRegistration'])): ;?>
        <p class="has-error"><?=$_SESSION['errorRegistration'];?></p>
        <?php endif; ?>
    <style>
        .has-error{
            color: red;
        }
        .errorValidate{
            color: red;
            font-size: 20px;
            font-weight: bold;
        }
    </style>
    </div>
</div>

</body>
</html>

<?php
