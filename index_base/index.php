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
<style>
    a{
        text-decoration: none;
    }
    a.active{
        color: green;
    }
</style>

<table border="1px">
    <tr>
        <th>id</th>
        <th>name</th>
        <th>age</th>
        <th>salary</th>
        <th>delete</th>
        <th>modify</th>
    </tr>
    <?php

    function input($name){
        if(isset($_POST[$name])) {
            $value = $_POST[$name];
        } else {
            $value = '';
        }
        return "<input name = '" . $name . "' value = '" . $value . "' placeholder = '" . $name . "' required>";
    }

    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'gb';

    $link = mysqli_connect($host, $user, $password, $dbname);

    if(isset($_GET['del'])){
        $del = $_GET['del'];
        $query = "DELETE FROM gb.workers WHERE id = '$del'";
        mysqli_query($link, $query);
    }

    if(!empty($_POST['sub'])){
        $age = $_POST['age'];
        $name = $_POST['name'];
        $salary = $_POST['salary'];

        $query = "INSERT INTO gb.workers (name, age, salary) VALUES ('$name', '$age', '$salary')";
        mysqli_query($link, $query);
    }

    //пагинация
    if(isset($_GET['page'])) {
        $page = $_GET['page'];
    } else {
        $page = 1;
    }
        $countPage = 3;
        $perPage = $page - 1;
        $from = $perPage * 3;

        $query = "SELECT * FROM gb.workers WHERE id > 0 LIMIT $from, $countPage";
    //

    $result = mysqli_query($link, $query);
    for($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);

    $result = '';
    foreach($data as $elem){
        $result .= "<tr>";
        $result .= "<td>" . $elem['id'] . "</td>";
        $result .= "<td>" . $elem['name'] . "</td>";
        $result .= "<td>" . $elem['age'] . "</td>";
        $result .= "<td>" . $elem['salary'] . "</td>";
        $result .= "<td><a href='?del=" . $elem['id'] . "'>Удалить</a></td>";
        $result .= "<td><a href='modify.php?mod=" . $elem['id'] . "'>Редактировать</a></td>";
        $result .= "</tr>";
    }
    echo $result;
    echo "</table>";

    //пагинация

    $query = "SELECT COUNT(*) as count FROM gb.workers";
    $result = mysqli_query($link, $query);
    $count = mysqli_fetch_assoc($result)['count'];
    $count = ceil($count / $countPage);

    if($page != 1){
        $prev = $page - 1;
        echo "<a href='?page=$prev'><<</a>";
    }

    for($i = 1; $i <= $count; $i++){
        if($page == $i){
            $class = "class='active' ";
        } else {
            $class = '';
        }
        echo "<a $class href='?page=$i'>$i</a> ";
    }
    if($page != $count){
        $pos = $page + 1;
        echo "<a href='?page=$pos'>>></a>";
    }
    ?>


<p>Добавить сотрудника</p>
<form action="" method="POST">
    <?php
    echo input('age');
    echo input('name');
    echo input('salary')
    ?>
    <input type="submit" name="sub" value="добавить">
</form>


</body>
</html>