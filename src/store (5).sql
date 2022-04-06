-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server
-- Generation Time: Apr 06, 2022 at 03:52 AM
-- Server version: 8.0.19
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `store`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_address` varchar(200) NOT NULL,
  `zipcode` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `product` varchar(50) NOT NULL,
  `quantity` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_name`, `customer_address`, `zipcode`, `product`, `quantity`) VALUES
(1, 'Abhishek Ojha', '138/84, Fatehganj, Lucknow.', '226018', 'Cricket Bat', '1'),
(3, 'Akash Nigam', 'Aliganj,Lucknow', '226018', 'Cricket Bat', '2'),
(4, 'Anshuman Rai', 'Vikas Nagar, Lucknow', '226022', 'Sony Bravia', '1');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int NOT NULL,
  `role` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `role`) VALUES
(2, 'admin'),
(3, 'manager'),
(4, 'accountant');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `tags` varchar(100) NOT NULL,
  `price` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `stock` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `tags`, `price`, `stock`) VALUES
(8, 'Cricket Bat', 'English Willow Bat ', 'bat', '55\r\n', '58'),
(74, 'Basket Ball', 'Basket ball weight 500gm', 'ball', '45', '20'),
(76, 'Sony Bravia', 'Tv', 'tv', '89', '20'),
(77, 'Iphone  8', 'Apple Iphone \r\nOs- IOS', 'Apple, Phone, Iphone, Ios', '99', '20'),
(83, 'Basket Ball', 'Ball', '#ball', '25', '5');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `role` varchar(100) NOT NULL,
  `controller` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `action` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role`, `controller`, `action`) VALUES
(12, 'manager ', 'Product', 'addProduct'),
(13, 'manager', 'Product', 'listProduct'),
(14, 'manager', 'Admin', 'index'),
(15, 'accountant', 'Order', 'AddOrder'),
(16, 'accountant', 'Order', 'listOrder'),
(17, 'accountant', 'Admin', 'index'),
(18, 'manager', 'Login', 'index'),
(19, 'accountant', 'Login', 'index');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `title_optimization` varchar(100) NOT NULL,
  `default_price` varchar(100) NOT NULL,
  `default_stock` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `default_zipcode` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `title_optimization`, `default_price`, `default_stock`, `default_zipcode`) VALUES
(1, 'without tags', '25', '5', '226018');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(10) NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `role` varchar(50) NOT NULL,
  `token` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `name`, `role`, `token`) VALUES
(24, 'arai@gmail.com', '1234', 'Anshuman', 'manager', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImN0eSI6ImFwcGxpY2F0aW9uXC9qc29uIn0.eyJhdWQiOlsiaHR0cHM6XC9cL3RhcmdldC5waGFsY29uLmlvIl0sImV4cCI6MTY0OTIyNDIwNiwianRpIjoiYWJjZDEyMzQiLCJpYXQiOjE2NDkxMzc4MDYsImlzcyI6Imh0dHBzOlwvXC9waGFsY29uLmlvIiwibmJmIjoxNjQ5MTM3NzQ2LCJzdWIiOiJtYW5hZ2VyIn0.fD2-c5arpFH1GgFXDKH9qH6WFrwogqOI34m7iGnoOfgBvAPE8OPH3s5IXFJyKBwmzj9AqDSYo7bUeGYnmQbYYg'),
(25, 'akash@gmail.com', '1234', 'Akash', 'accountant', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImN0eSI6ImFwcGxpY2F0aW9uXC9qc29uIn0.eyJhdWQiOlsiaHR0cHM6XC9cL3RhcmdldC5waGFsY29uLmlvIl0sImV4cCI6MTY0OTIyNDMwMywianRpIjoiYWJjZDEyMzQiLCJpYXQiOjE2NDkxMzc5MDMsImlzcyI6Imh0dHBzOlwvXC9waGFsY29uLmlvIiwibmJmIjoxNjQ5MTM3ODQzLCJzdWIiOiJhY2NvdW50YW50In0.uPaPepOPE7aWkzZFpvdiFwS_GiUtGc8sJCpiiuLbN7PeymHMUALNvgvEI1X8Gba2-ewG67RrE7QR2JACykl8PA'),
(26, 'aojha8120@gmail.com', '1234', 'Abhishek Ojha', 'admin', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImN0eSI6ImFwcGxpY2F0aW9uXC9qc29uIn0.eyJhdWQiOlsiaHR0cHM6XC9cL3RhcmdldC5waGFsY29uLmlvIl0sImV4cCI6MTY0OTIyNDMyNCwianRpIjoiYWJjZDEyMzQiLCJpYXQiOjE2NDkxMzc5MjQsImlzcyI6Imh0dHBzOlwvXC9waGFsY29uLmlvIiwibmJmIjoxNjQ5MTM3ODY0LCJzdWIiOiJhZG1pbiJ9.WJ00a9jfFE3lYekFeOo1IDLwvljbV-SvbNLb4SOb9QQ-2KlNvqxJ9yIEbWF8jBSm1lltnO6KEnguOtG2b1wmkA');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
