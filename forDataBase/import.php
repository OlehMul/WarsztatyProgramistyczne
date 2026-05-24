<?php
use forDatabase\DataBase;
$db = DataBase::get();
$repo = new \forme\TaskRepository($db);
$task = null;



if(isset($_POST['sub'])){
try {
    $db->beginTransaction();
    $ar = explode("\n", $_POST['area']);
    foreach($ar as $line){
        $elements = explode(';', $line);
        if (count($elements) > 4 || count($elements) < 4) {
            echo "Error, invalid count of elements";
            $db->rollBack();
            break;
        } else if ($elements[2] != "todo" && $elements[1] != "done" && $elements[1] != "in progress") {
            $db->rollBack();
            break;
        } else if ($elements[1] != "low" && $elements[1] != "high" && $elements[1] != "medium") {
            $db->rollBack();
            break;
        } else {
            $task = new \forme\Task(null, $elements[0], $_SESSION['user']->getLogin(), null, '', $elements[1], $elements[2], $elements[3]);
            $repo->add($task);
        }


    }
    $db->commit();


}catch(PDOException $e){
    $db->rollBack();
    echo "Something terrible happened in import.php";
}


}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="/men.css" rel="stylesheet" type="text/css">
</head>

<body>

<form action="import.php" method="post">
    <label>Add task</label>
    <textarea placeholder="title;priority;status;tags" name="area"></textarea>
    <button type="submit" name="sub">BIG BUTTON NAME</button>

</form>



</body>
</html>