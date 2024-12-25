<?php

include_once '../config/db.php';
include_once 'book.php';

$database = new DataBase();
$conn = $database->getConnection();

$book = new Book($conn);    

$stmt =  $book->addBook('To Kill a Mockingbird','Harper Lee',1,'mockingbird_cover.jpg','A powerful story of racial injustice and the loss of innocence in a small Southern town, told through the eyes of young Scout Finch.','available');

//check $stmt->execute

if($stmt){
    echo "Book added successfully";
}else{
    echo "Error adding book";
}



?>