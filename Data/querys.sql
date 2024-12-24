-- Base de données : Bibliothèque
CREATE DATABASE Bibliotheque;
USE Bibliotheque;

-- Table des utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'authenticated', 'visitor') DEFAULT 'visitor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des catégories de livres
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- Table des livres
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    category_id INT NOT NULL,
    cover_image VARCHAR(255), 
    summary TEXT,
    status ENUM('available', 'borrowed', 'reserved') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Table des emprunts
CREATE TABLE borrowings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE DEFAULT NULL,
    notification_sent TINYINT(1) DEFAULT 0, -- 1 si un e-mail a été envoyé
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

/********************INSERT**********************/


USE bibliotheque;

INSERT INTO users (name, email, password, role) VALUES
('admin', 'admin@example.com', 'admin', 'admin'),
('ilyas', 'ilyasdoe@example.com', 'pass', 'authenticated'),
('abdel', 'abdel@example.com', 'pass', 'authenticated');

INSERT INTO categories (name) VALUES
('Fiction'),
('Science'),
('History'),
('Biography');

INSERT INTO books (title, author, category_id, cover_image, summary, status) VALUES
('1984', 'George Orwell', 1, 'https://cdn.example.com/images/1984.jpg', 'A dystopian novel about totalitarianism.', 'available'),
('The Origin of Species', 'Charles Darwin', 2, 'https://cdn.example.com/images/origin_of_species.jpg', 'A groundbreaking work on evolution.', 'available'),
('A History of Ancient Rome', 'Mary Beard', 3, 'https://cdn.example.com/images/rome_history.jpg', 'An in-depth look at ancient Rome.', 'borrowed'),
('The Diary of a Young Girl', 'Anne Frank', 4, 'https://cdn.example.com/images/anne_frank.jpg', 'The diary of Anne Frank during WWII.', 'reserved');


INSERT INTO borrowings (user_id, book_id, borrow_date, due_date) VALUES
(2, 3, '2024-12-01', '2024-12-15'),
(3, 4, '2024-12-05', '2024-12-20');
