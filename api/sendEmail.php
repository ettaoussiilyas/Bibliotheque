<?php

require '../classes/SendEmail.php';
session_start();
if(!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin')){
    header('Location: index.php');
    exit;
}
if(isset($_GET['email']) AND isset($_GET['name']) AND isset($_GET['date']) ){
    $email = new SendEmail($_GET['email'], $_GET['name'], $_GET['date']);
    $email->send();
}


?>
