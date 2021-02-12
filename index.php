<?php
session_start();
include_once "db_connect.php";
include_once "userAuthorization.php";

//пагинация
if (!empty($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}
$countPage = 5;
$from = ($page - 1) * $countPage;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Гостевая книга</title>
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<div id="wrapper">
<h1>Гостевая книга</h1>
<?php
if(!empty($_SESSION['error'])){
    echo $_SESSION['error'];
    unset($_SESSION['error']);
}
$query = "SELECT * FROM gb.gb_comment ORDER BY data DESC LIMIT $from, $countPage";
$result = mysqli_query($link, $query);
foreach ($result as $res): ?>
    <div class="note">
        <p>
            <span class="date"><?= date('d.m.Y h:i:s', strtotime($res['data'])); ?></span>
            <span class="name"><a href="profile.php?id=<?= $res['name'];?>"><?= $res['name']; ?></a></span>
            <?php if ($_SESSION['authGB']): ?>
                <a id='delete' href="?page=<?= $_GET['page']; ?>&delete=<?= $res['id']; ?>">УДАЛИТЬ</a>
                <?php switch ($res['moderator']): case 0: ?>
                    <a id='open' href="?page=<?= $_GET['page']; ?>&open=<?= $res['id']; ?>">ОТКРЫТЬ</a>
                    <?php break;
                    case 1: ?>
                        <a id='hide' href="?page=<?= $_GET['page']; ?>&hide=<?= $res['id']; ?>">СКРЫТЬ</a>
                        <?php break; endswitch; ?>
                <style>
                    #delete {
                        color: red;
                    }

                    #hide {
                        color: blue;
                    }

                    #open {
                        color: green;
                    }
                </style>
            <?php endif; ?>
        </p>
        <p><?php if ($res['moderator'] == 0) {
                echo "комментарий скрыт модератором";
            } else {
                echo $res['comment'];
            } ?></p>
    </div>
<?php
endforeach;

//пагинация
$query = "SELECT COUNT(*) as count FROM gb.gb_comment";
$result = mysqli_query($link, $query);
$count = mysqli_fetch_assoc($result)['count'];
$count = ceil($count / $countPage);
if ($page != 1) {
    $prev = $page - 1;
    echo "<a href='?page=$prev'><<</a>";
}
for ($i = 1; $i <= $count; $i++):
    ?>
    <a href="?page=<?= $i; ?>"><?= $i; ?></a>
<?php
endfor;
if ($page < $count) {
    $pos = $page + 1;
    echo "<a href='?page=$pos'>>></a>";
}

if (!empty($_POST['sub'])) {
    $commentName = $_SESSION['userLogin'];
    $commentText = htmlspecialchars($_POST['comment']);
    $commentDate = date('Y-m-d h:i:s');
    mysqli_query($link,
        "INSERT INTO gb.gb_comment SET name='$commentName', comment='$commentText', data='$commentDate'"
    );
    ?>
    <div class="info alert alert-info">
        Запись успешно сохранена!
    </div>
    <?php
}
?>
    <?php if(($_SESSION['authUser']) OR ($_SESSION['authGB'])): ?>
<div id="form">
    <form action="" method="POST">
        <p><textarea class="form-control" name="comment" placeholder="Ваш отзыв" required></textarea></p>
        <p><input type="submit" name="sub" class="btn btn-info btn-block" value="Сохранить"></p>
    </form>
</div>
    <?php else: ?>
    <p class="err-message">Чтобы отправлять сообщения вам нужно авторизоваться.</p>
    <a href="register.php">Если у вас нет аккаунта, зарегистрируйтесь</a>
        <style>
            .err-message{
                font-size: 30px;
                color: blueviolet;
                font-style: italic;
            }
        </style>
    <?php endif; ?>
</div>
</body>
</html>

