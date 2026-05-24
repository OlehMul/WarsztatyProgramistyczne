<?php
session_start();
require_once("forme/AuthService.php");


$auth = new \forme\AuthService();


$user ="";
$pass = "";

if(isset($_POST['sub2'])) {
 //   session_regenerate_id(true);
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $_SESSION['user'] = $user;
    if ($auth->login($user, $pass)) {
        $US = new \forme\User($user, $pass);
        header('Location: index.php');
        exit();
    }




}


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="men.css" rel="stylesheet" type="text/css">
</head>
<header id="h">
    <h1>Welcome to the login page</h1>
</header>


<body>
<div id="g">

<div id="login_main">
<form method="post" action="login.php">
    <div id="firstPart">
    <label>Username</label>
    <div>
        <input name="user" type="text">
    </div>


    <label>Password</label>
    <div>
        <input name="pass" type="password">
    </div>
    <button type="submit" name="sub2">Submit button</button>
</div>


</form>
</div>
</div>
</body>


