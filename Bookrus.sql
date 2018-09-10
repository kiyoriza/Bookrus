-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Sep 10, 2018 at 11:03 AM
-- Server version: 5.7.23
-- PHP Version: 7.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Bookrus`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookmark`
--

CREATE TABLE `bookmark` (
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bookmark`
--

INSERT INTO `bookmark` (`book_id`, `user_id`, `created`) VALUES
(5, 3, '2018-09-10 18:37:43'),
(2, 3, '2018-09-10 18:40:52');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `phrase` text NOT NULL,
  `title` text NOT NULL,
  `picture_url_API` text,
  `author_API` text,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `API_id` text CHARACTER SET utf8mb4
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `phrase`, `title`, `picture_url_API`, `author_API`, `category_id`, `user_id`, `created`, `modified`, `API_id`) VALUES
(2, 'dasd', 'ハリーポッター', 'http://books.google.com/books/content?id=v_h8oAEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'J.K. ローリング', 1, 1, '2018-09-10 14:44:20', '2018-09-10 05:44:20', 'v_h8oAEACAAJ'),
(3, 'test', 'ハリーポッター', 'http://books.google.com/books/content?id=c2cl-DRqhtwC&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', 'J.K. Rowling', 1, 1, '2018-09-10 15:30:31', '2018-09-10 06:30:31', 'c2cl-DRqhtwC'),
(4, 'test', '経済', 'http://books.google.com/books/content?id=tpJQTskXvZ0C&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', '木暮太一', 2, 1, '2018-09-10 15:31:17', '2018-09-10 06:31:17', 'tpJQTskXvZ0C'),
(5, 'test', '経済', 'http://books.google.com/books/content?id=Nje1AAAAIAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', '小西唯雄', 1, 1, '2018-09-10 16:54:30', '2018-09-10 07:54:30', 'Nje1AAAAIAAJ'),
(6, 'test', 'ハリーポッター', 'http://books.google.com/books/content?id=v_h8oAEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 'J.K. ローリング', 1, 3, '2018-09-10 18:58:21', '2018-09-10 09:58:21', 'v_h8oAEACAAJ');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `created`, `modified`) VALUES
(1, '経済', '2018-09-08 00:00:00', '2018-09-09 06:20:38'),
(2, '社会', '2018-09-09 00:00:00', '2018-09-09 06:20:38');

-- --------------------------------------------------------

--
-- Table structure for table `finish_reading`
--

CREATE TABLE `finish_reading` (
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `finish_reading`
--

INSERT INTO `finish_reading` (`book_id`, `user_id`, `created`) VALUES
(5, 3, '2018-09-10 18:37:45'),
(3, 3, '2018-09-10 19:31:20');

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `picture_path` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `email`, `password`, `picture_path`, `created`, `modified`) VALUES
(1, 'test', 'test@test', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', '20180909062322201703010003hiroyuki.jpg', '2018-09-09 15:23:28', '2018-09-09 06:23:28'),
(3, 'test2', 'test2@test', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', '20180910092810スクリーンショット 2018-09-10 18.27.37.png', '2018-09-10 18:28:15', '2018-09-10 09:28:15');

-- --------------------------------------------------------

--
-- Table structure for table `user_selected_category`
--

CREATE TABLE `user_selected_category` (
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_selected_category`
--

INSERT INTO `user_selected_category` (`category_id`, `user_id`, `created`) VALUES
(1, 1, '2018-09-09 15:27:14'),
(1, 1, '2018-09-09 00:00:00'),
(2, 3, '2018-09-10 18:28:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
