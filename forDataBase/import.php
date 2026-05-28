<?php
session_start();
require_once 'DataBase.php';
require_once '../forme/Autoloader.php';
use forDataBase\DataBase;

$db = DataBase::get();
DataBase::migrate();
$repo = new \forme\TaskRepository($db);
$auth = new \forme\AuthService($db);
$task = null;


if(isset($_POST['sub'])){
    try {
        $db->beginTransaction();
        $ar = explode("\n", trim($_POST['area']));
        $success = true;

        foreach($ar as $line){
            if(empty(trim($line))){
              continue;
            }

            $elements = explode(';', $line);


            if(count($elements) != 4){
                echo "Error, invalid count of elements in line: " . htmlspecialchars($line);
                $success = false;
                break;
            }

            $title = trim($elements[0]);
            $priority = trim($elements[1]);
            $status = trim($elements[2]);
            $tags = trim($elements[3]);


            if($status != "todo" && $status != "done" && $status != "in progress"){
                echo "Error, invalid status: " . htmlspecialchars($status);
                $success = false;
                break;
            }


            if($priority != "low" && $priority != "high" && $priority != "medium"){
                echo "Error, invalid priority: " . htmlspecialchars($priority);
                $success = false;
                break;
            }


            $task = new \forme\Task("imp",$title, $auth->currentUser(), 0, '', $priority,$status,$tags, "Praca",'');

            if(!empty($task->getErrorArray())){
                echo "Error creating task: " . implode(', ', $task->getErrorArray());
                $success = false;
                break;
            }

            $repo->add($task);
        }

        if($success){
            $db->commit();
            echo "Import successful!";
            $check = $db->query("SELECT COUNT(*) as count FROM tasks");
            $result = $check->fetch();
            echo "<br>Total tasks in DB: " . $result['count'];
        } else {
            $db->rollBack();
            echo "Import failed, transaction rolled back.";
        }

    } catch(PDOException $e){
        $db->rollBack();
        echo "Database error: " . $e->getMessage();
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="../men.css" rel="stylesheet" type="text/css">
</head>

<body class="forImport">

    <div class="imp">
<form action="import.php" method="post">

    <label>Add task</label>
    <textarea placeholder="title;priority;status;tags" name="area"></textarea>
    <button type="submit" name="sub">BIG BUTTON NAME</button>
        <a href="../index.php">BACK TO MAIN PAGE</a>



</form>
    </div>



</body>

</html>