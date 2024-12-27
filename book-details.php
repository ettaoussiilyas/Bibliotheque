<?php
session_start();
include_once 'config/db.php';
include_once 'classes/book.php';

if (!isset($_GET['id'])) {
    header('Location: user.php');
    exit();
}

$database = new DataBase();
$conn = $database->getConnection();
$book = new Book($conn);

// Ajouter une méthode dans la classe Book pour récupérer un livre par son ID
$book_details = $book->getBookById($_GET['id']);

if (!$book_details) {
    header('Location: user.php');
    exit();
}

// Inclure le header
include 'includes/header.php';



// Inclure le footer
include 'includes/footer.php';
?> 