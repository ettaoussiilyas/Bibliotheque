<?php 

require_once '../classes/User.php';
session_start();
if(!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin')){
    header('Location: index.php');
    exit;
}
if($_SERVER['REQUEST_METHOD'] === 'GET' AND isset($_GET['user'])){
    $user = new User();
    if($user->delete($_GET['user'])){
        echo "ok";
    }else{
        echo "Error Occured!";
    }
}


?>