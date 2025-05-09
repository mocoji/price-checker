-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2025-05-09 08:27:32
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `price_checker`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `item_code` varchar(100) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `thumbnail_url` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `item_competitors`
--

CREATE TABLE `item_competitors` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `competitor_shop_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `operation_logs`
--

CREATE TABLE `operation_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) DEFAULT NULL,
  `target` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `operation_logs`
--

INSERT INTO `operation_logs` (`id`, `user_id`, `action`, `target`, `created_at`) VALUES
(1, 4, '商品登録', 'item_code=c-1dayms90-2p', '2025-05-08 14:26:16'),
(2, 4, '店舗登録', 'shop_code=lensfree', '2025-05-08 14:29:45'),
(3, 4, 'ユーザー登録', 'username=adsis', '2025-05-08 14:31:23'),
(4, 4, '商品登録', 'item_code=k_jj_oa90z0_02_h', '2025-05-08 15:35:03'),
(5, 4, '商品登録', 'item_code=jj1dao90-2', '2025-05-08 16:01:41'),
(6, 4, '商品削除', 'item_id=4', '2025-05-08 18:15:17'),
(7, 4, '商品登録', 'item_code=jj1dao90-2', '2025-05-08 18:15:36'),
(8, 4, '商品削除', 'item_id=3', '2025-05-08 18:16:50'),
(9, 4, '商品登録', 'item_code=c-1dayms90-2p', '2025-05-08 18:16:58'),
(10, 4, '商品削除', 'item_id=5', '2025-05-08 19:15:27'),
(11, 4, '商品登録', 'item_code=jj1dao90-2', '2025-05-08 19:15:48'),
(12, 4, '商品削除', 'item_id=7', '2025-05-08 19:28:59'),
(13, 4, '商品登録', 'item_code=jj1dam90-2', '2025-05-08 19:29:05'),
(14, 4, '商品削除', 'item_id=8', '2025-05-08 19:46:53'),
(15, 4, '商品削除', 'item_id=6', '2025-05-08 19:46:55'),
(16, 4, '商品登録', 'item_code=c-1dayms90-2p', '2025-05-08 19:47:02'),
(17, 4, '商品登録', 'item_code=jj1dam90-2', '2025-05-08 19:47:10'),
(18, 4, '商品削除', 'item_id=10', '2025-05-08 20:21:07'),
(19, 4, '商品登録', 'item_code=jj1dam90-2', '2025-05-09 12:00:15'),
(20, 4, '商品削除', 'item_id=11', '2025-05-09 14:12:07'),
(21, 4, '商品削除', 'item_id=9', '2025-05-09 14:12:09'),
(22, 4, '店舗登録', 'shop_code=earth-contact', '2025-05-09 15:21:45');

-- --------------------------------------------------------

--
-- テーブルの構造 `price_history`
--

CREATE TABLE `price_history` (
  `id` int(11) NOT NULL,
  `shop_item_id` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `recorded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `shops`
--

CREATE TABLE `shops` (
  `id` int(11) NOT NULL,
  `shop_code` varchar(100) NOT NULL,
  `shop_name` varchar(255) DEFAULT NULL,
  `is_own_shop` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `shops`
--

INSERT INTO `shops` (`id`, `shop_code`, `shop_name`, `is_own_shop`, `created_at`) VALUES
(1, 'atcontact', 'アットコンタクト', 1, '2025-05-08 14:21:24'),
(2, 'lensfree', 'レンズフリー', 0, '2025-05-08 14:29:45'),
(3, 'earth-contact', 'アースコンタクト', 0, '2025-05-09 15:21:45');

-- --------------------------------------------------------

--
-- テーブルの構造 `shop_items`
--

CREATE TABLE `shop_items` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `shop_id` int(11) NOT NULL,
  `item_code` varchar(100) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `url` text DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `is_latest` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_checked` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `shop_items`
--

INSERT INTO `shop_items` (`id`, `product_id`, `shop_id`, `item_code`, `price`, `url`, `stock`, `is_latest`, `created_at`, `last_checked`) VALUES
(15, 7, 1, 'c-1dayms90-2p', 14760, '', NULL, 1, '2025-05-09 15:16:02', '2025-05-09 15:16:02'),
(16, 7, 2, 'jj1dam90-2', 13750, '', NULL, 1, '2025-05-09 15:16:16', '2025-05-09 15:16:16'),
(17, 7, 3, 'oam90-02-0pt', 13552, '', NULL, 1, '2025-05-09 15:22:14', '2025-05-09 15:22:14');

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'viewer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role`) VALUES
(4, 'admin', '$2y$10$Ivs4/Q4HANuv9o7B6EHHNe8TBfbtRm9e/i4u7KnEPL5JiuB3rGNwi', 'admin'),
(5, 'adsis', '$2y$10$l8unbXl8F7gNJO1hWJ3hYeXuGpQWFWLPPy75fblDzRV0h0JNxcMp2', 'editor');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `item_competitors`
--
ALTER TABLE `item_competitors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_id` (`item_id`,`competitor_shop_id`);

--
-- テーブルのインデックス `operation_logs`
--
ALTER TABLE `operation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- テーブルのインデックス `price_history`
--
ALTER TABLE `price_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_item_id` (`shop_item_id`);

--
-- テーブルのインデックス `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `shop_items`
--
ALTER TABLE `shop_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- テーブルの AUTO_INCREMENT `item_competitors`
--
ALTER TABLE `item_competitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- テーブルの AUTO_INCREMENT `operation_logs`
--
ALTER TABLE `operation_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- テーブルの AUTO_INCREMENT `price_history`
--
ALTER TABLE `price_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- テーブルの AUTO_INCREMENT `shops`
--
ALTER TABLE `shops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- テーブルの AUTO_INCREMENT `shop_items`
--
ALTER TABLE `shop_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- テーブルの AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `operation_logs`
--
ALTER TABLE `operation_logs`
  ADD CONSTRAINT `operation_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- テーブルの制約 `price_history`
--
ALTER TABLE `price_history`
  ADD CONSTRAINT `price_history_ibfk_1` FOREIGN KEY (`shop_item_id`) REFERENCES `shop_items` (`id`);

--
-- テーブルの制約 `shop_items`
--
ALTER TABLE `shop_items`
  ADD CONSTRAINT `shop_items_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
