<?php

require '../classes/SendEmail.php';

if(isset($_GET['email']) AND isset($_GET['name']) AND isset($_GET['date']) ){
    $email = new SendEmail($_GET['email'], $_GET['name'], $_GET['date']);
    $email->send();
}


?>
