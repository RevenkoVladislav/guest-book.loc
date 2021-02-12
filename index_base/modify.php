<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<?php
$host = 'localhost';
$namedb = 'root';
$password = '';
$dbname = 'gb';
$id = $_GET['mod'];
$link = mysqli_connect($host, $namedb, $password, $dbname);
$query = "SELECT * FROM gb.workers WHERE id = '$id'";
$result = mysqli_query($link, $query);

for($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);
foreach($data as $elem){}

function input($type, $name, $value){
    if(!empty($_POST['sub'])){
        $value = $_POST[$name];
    }
    return "<input type='$type', name='$name', value ='$value', placeholder='$name', required> ";
}
?>
<form action="" method="post">
    <?php
    echo input('text', 'age', $elem['age']);
    echo input('text', 'name', $elem['name']);
    echo input('text', 'salary', $elem['salary']);
    ?>
    <input type="submit" name="sub">
</form>

<?php
if(!empty($_POST['sub'])){
    $age = $_POST['age'];
    $name = $_POST['name'];
    $salary = $_POST['salary'];
    $query = "UPDATE gb.workers SET `age` = '$age', `name` = '$name', `salary` = '$salary' WHERE id = '$id'";
    mysqli_query($link, $query);
}
?>
<a href="index.php">на главную</a>
</body>
</html>