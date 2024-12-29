<?php 

require_once '../classes/User.php';
session_start();
if(!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin')){
    header('Location: index.php');
    exit;
}
if($_SERVER['REQUEST_METHOD'] === 'GET' AND isset($_GET['email']) AND isset($_GET['newemail']) AND isset($_GET['newname']) AND isset($_GET['newrole'])){
    $user = new User();
    if($user->update($_GET['email'], $_GET['newemail'], $_GET['newname'], $_GET['newrole'])){
        echo "ok";
    }else{
        echo "Error Occured!";
    }
}


?>