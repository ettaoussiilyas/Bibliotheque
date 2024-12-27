<?php

include_once '../config/db.php';
include_once 'book.php';

$database = new DataBase();
$conn = $database->getConnection();

$book = new Book($conn);   

/******************add book********************* */

// $stmt =  $book->addBook('To Kill a Mockingbird','Harper Lee',1,'mockingbird_cover.jpg','A powerful story of racial injustice and the loss of innocence in a small Southern town, told through the eyes of young Scout Finch.','available');

// //check $stmt->execute

// if($stmt){
//     echo "Book added successfully";
// }else{
//     echo "Error adding book";
// }

/******************delete book********************* */

// $stmt = $book->deleteBook(5);
// if($stmt){
//     echo "Book deleted successfully";
// }else{  
//     echo "Error deleting book";
// }

/******************insert book********************* */
// INSERT INTO books (title, author, category_id, cover_image, summary, status) VALUES
// (
//     'The Great Gatsby',
//     'F. Scott Fitzgerald',
//     1,  -- Assuming category_id 1 exists (e.g., 'Fiction')
//     'gatsby_cover.jpg',
//     'A story of decadence and excess, exploring the American Dream through the eyes of Nick Carraway and the mysterious millionaire Jay Gatsby.',
//     'available'
// )

?>