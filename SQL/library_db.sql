-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2025 at 01:03 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `stock` int(11) DEFAULT 1,
  `imglink` varchar(10000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `category`, `stock`, `imglink`) VALUES
(2, 'Animal Farm', 'George Orewell', 'History', 12, 'https://m.media-amazon.com/images/I/71JUJ6pGoIL._AC_UF1000,1000_QL80_.jpg'),
(3, 'Art Of War', 'Sun Tzu', 'History', 21, 'https://m.media-amazon.com/images/I/71MizulW5AL.jpg'),
(4, 'The Autumn Of The Patriarch ', 'Gabriel García Márquez', 'Fiction', 7, 'https://cdn.penguin.co.uk/dam-assets/books/9780241968635/9780241968635-jacket-large.jpg'),
(5, 'ტიბეტი არ არის შორს', 'დათო ტურაშვილი', 'Non-Fiction', 10, 'https://sulakauri.ge/uploads/2023/11/tibeti-ar-aris-shors.webp'),
(6, '451 Fahrenheit', 'Rey Bradbury', 'Fiction', 34, 'https://freebooksummary.com/wp-content/uploads/2022/12/8c09d9fcb63e9bee8b462ffe27d7de18.jpg'),
(7, '99 Francs', ' Frédéric Beigbeder', 'Non-Fiction', 99, 'https://upload.wikimedia.org/wikipedia/en/8/8e/99_Francs.jpg'),
(8, 'Pale Blue Dot', 'Carl Sagan', 'Science', 13, 'https://m.media-amazon.com/images/I/51wd9h7r4CL._AC_UF1000,1000_QL80_DpWeblab_.jpg'),
(9, 'Odyssey', 'Homer', 'History', 1, 'https://imgv2-2-f.scribdassets.com/img/document/704990196/original/1cb4341518/1?v=1');

-- --------------------------------------------------------

--
-- Table structure for table `borrows`
--

CREATE TABLE `borrows` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `borrow_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `return_date` timestamp NULL DEFAULT NULL,
  `status` enum('borrowed','returned','overdue') DEFAULT 'borrowed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `borrow_records`
--

CREATE TABLE `borrow_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `borrow_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('borrowed','returned') DEFAULT 'borrowed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_records`
--

INSERT INTO `borrow_records` (`id`, `user_id`, `book_id`, `borrow_date`, `return_date`, `status`) VALUES
(1, 1, 2, '2025-06-08', '2025-06-22', 'returned'),
(2, 1, 4, '2025-06-08', '2025-06-22', 'returned'),
(3, 1, 3, '2025-06-08', '2025-06-22', 'returned'),
(4, 4, 5, '2025-06-08', '2025-06-22', 'borrowed'),
(5, 1, 5, '2025-06-08', '2025-06-08', 'returned'),
(6, 1, 4, '2025-06-08', '2025-06-08', 'returned'),
(7, 1, 3, '2025-06-08', '2025-06-08', 'returned'),
(8, 1, 2, '2025-06-08', '2025-06-08', 'returned'),
(9, 1, 7, '2025-06-10', '2025-06-24', 'returned');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Saba', 'saba@email.com', '$2y$10$CM8A2CJoN21PUDkncMpJ8OYmOiQ4y4xn4xyx87C3MJ/VA.8LF5Lpm', 'user'),
(2, 'Saba', 'admin@email.com', '$2y$10$Mv684A098D/NADxi6NqYLOkikCMYzMnAf1B8/IKIi.8ReR0v4VIje', 'admin'),
(3, 'Admin', 'admin@library.com', '$2y$10$rJJKGIYKH9fQGtDioPU48.4bRYjNaIoqwfGaOHSJlAQjIyQ7cAbPa', 'admin'),
(4, 'Saba 2', 'saba2@gmail.com', '$2y$10$yDZioeXlyBAsoYpK8boHYujWjRYn3lw3x6DPJlZ2LtC4mxpxJLw.S', 'user'),
(5, 'admin 2', 'admin2@email.com', '$2y$10$k0NMoj7b3DPRGadUGwOmRuoS46FLhfE6z4YJ.ElvJNX63WpGQk8CC', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `borrows`
--
ALTER TABLE `borrows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `borrow_records`
--
ALTER TABLE `borrow_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `borrows`
--
ALTER TABLE `borrows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `borrow_records`
--
ALTER TABLE `borrow_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrows`
--
ALTER TABLE `borrows`
  ADD CONSTRAINT `borrows_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `borrows_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);

--
-- Constraints for table `borrow_records`
--
ALTER TABLE `borrow_records`
  ADD CONSTRAINT `borrow_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `borrow_records_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
