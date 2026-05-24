<?php
session_start();
require_once("forme/UserPreferences.php");

$pref = new \forme\UserPreferences($_COOKIE);
$ar = [];

if(isset($_POST['submTHEME']) && isset($_POST['theme'])){
  $ar['theme'] = $_POST['theme'];
  $pref->save($ar);
}
if(isset($_POST['submNUM']) && isset($_POST['num'])){
    $ar['num'] = $_POST['num'];
    $pref->save($ar);
}
if(isset($_POST['submSORT']) && isset($_POST['sort'])){
  $ar['req'] = $_POST['sort'];
    $pref->save($ar);
}




?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="men.css" rel="stylesheet" type="text/css">
</head>



<body class="well">
<header >
    <a id="LINK" href="index.php">Go back</a>
</header>



<form action="header.php" method="post">
    <div class="formTHEME">
    <header>Choose your preffered theme of page</header>
    <div>
    <label>Dark theme</label>
    <input type="radio" name="theme" value="dark">
    </div>
    <div>
    <label>Light theme</label>
    <input type="radio" name="theme" value="light">
    </div>
    <button type="submit" name="submTHEME">SUBMIT</button>
    </div>
</form>




    <form action="header.php" method="post">
        <div class="formNUM">
<div>
    <label>Number of tasks on page</label>
</div>
    <div>
    <label>5</label>
    <input type="radio" name="num" value="5">
    </div>
    <div>
    <label>10</label>
    <input type="radio" name="num" value="10">
    </div>
    <div>
    <label>25</label>
    <input type="radio" name="num" value="25">
    </div>


    <button type="submit" name="submNUM">SUBMIT</button>
        </div>
</form>

<form action="header.php" method="post">
    <div class="formSORT">
      <div>
      <label>Choose how you want the tasks to be sorted</label>
      </div>
<div>
    <label>Data</label>
          <input type="radio" name="sort" value="data">
</div>
<div>
    <label>Prioritet</label>
    <input type="radio" name="sort" value="priority">
</div>
<div>
    <label>Nazwa</label>
    <input type="radio" name="sort" value="title">

</div>
<button type="submit" name="submSORT">SUBMIT</button>
    </div>

</form>
      



</body>