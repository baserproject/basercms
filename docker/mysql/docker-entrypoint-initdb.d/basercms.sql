-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- ホスト: bc5-db
-- 生成日時: 2021 年 3 月 14 日 09:56
-- サーバのバージョン： 5.7.33
-- PHP のバージョン: 7.4.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `basercms`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `contents`
--

DROP TABLE IF EXISTS `contents`;
CREATE TABLE `contents` (
  `id` int(8) NOT NULL,
  `name` text,
  `plugin` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `entity_id` int(8) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `site_id` int(8) DEFAULT '0',
  `alias_id` int(8) DEFAULT NULL,
  `main_site_content_id` int(8) DEFAULT NULL,
  `parent_id` int(8) DEFAULT NULL,
  `lft` int(8) DEFAULT NULL,
  `rght` int(8) DEFAULT NULL,
  `level` int(8) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `eyecatch` varchar(255) DEFAULT NULL,
  `author_id` int(8) DEFAULT NULL,
  `layout_template` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `publish_begin` datetime DEFAULT NULL,
  `publish_end` datetime DEFAULT NULL,
  `self_status` tinyint(1) DEFAULT NULL,
  `self_publish_begin` datetime DEFAULT NULL,
  `self_publish_end` datetime DEFAULT NULL,
  `exclude_search` tinyint(1) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `site_root` tinyint(1) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `exclude_menu` tinyint(1) DEFAULT '0',
  `blank_link` tinyint(1) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `contents`
--

INSERT INTO `contents` (`id`, `name`, `plugin`, `type`, `entity_id`, `url`, `site_id`, `alias_id`, `main_site_content_id`, `parent_id`, `lft`, `rght`, `level`, `title`, `description`, `eyecatch`, `author_id`, `layout_template`, `status`, `publish_begin`, `publish_end`, `self_status`, `self_publish_begin`, `self_publish_end`, `exclude_search`, `created_date`, `modified_date`, `site_root`, `deleted_date`, `deleted`, `exclude_menu`, `blank_link`, `created`, `modified`) VALUES
(1, '', 'Core', 'ContentFolder', 1, '/', 0, NULL, NULL, NULL, 1, 18, 0, 'ryuring.com', '', '', 1, 'default', 1, NULL, NULL, 1, NULL, NULL, 0, '2017-05-03 14:22:08', '2017-05-03 14:22:08', 1, NULL, 0, 0, 0, '2016-07-29 18:02:53', '2017-05-03 14:51:52'),
(2, 'index', 'Core', 'Page', 1, '/index', 0, NULL, NULL, 1, 14, 15, 1, 'トップページ', '', '', 1, '', 1, NULL, NULL, 1, NULL, NULL, 0, '2017-05-03 14:22:08', '2017-05-03 14:22:08', 0, NULL, 0, 0, 0, '2016-07-29 18:13:03', '2017-05-03 15:12:27');

-- --------------------------------------------------------

--
-- テーブルの構造 `content_folders`
--

DROP TABLE IF EXISTS `content_folders`;
CREATE TABLE `content_folders` (
  `id` int(8) NOT NULL,
  `folder_template` varchar(255) DEFAULT NULL,
  `page_template` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `content_folders`
--

INSERT INTO `content_folders` (`id`, `folder_template`, `page_template`, `created`, `modified`) VALUES
(1, '', '', '2017-05-03 14:21:45', '2017-05-03 14:51:52');

-- --------------------------------------------------------

--
-- テーブルの構造 `pages`
--

DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int(8) NOT NULL,
  `contents` mediumtext,
  `draft` text,
  `page_template` varchar(255) DEFAULT NULL,
  `code` text,
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `pages`
--

INSERT INTO `pages` (`id`, `contents`, `draft`, `page_template`, `code`, `modified`, `created`) VALUES
(1, '<p>座右の銘「やるときゃやる」</p>\r\n', '', '', '', '2017-05-03 15:12:27', '2015-06-26 20:34:06');

-- --------------------------------------------------------

--
-- テーブルの構造 `password_requests`
--

DROP TABLE IF EXISTS `password_requests`;
CREATE TABLE `password_requests` (
  `id` int(8) NOT NULL,
  `user_id` int(8) DEFAULT NULL,
  `request_key` varchar(255) DEFAULT NULL,
  `used` int(2) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `phinxlog`
--

DROP TABLE IF EXISTS `phinxlog`;
CREATE TABLE `phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `phinxlog`
--

INSERT INTO `phinxlog` (`version`, `migration_name`, `start_time`, `end_time`, `breakpoint`) VALUES
(20210314094339, 'Initial', '2021-03-14 18:43:40', '2021-03-14 18:43:40', 0);

-- --------------------------------------------------------

--
-- テーブルの構造 `plugins`
--

DROP TABLE IF EXISTS `plugins`;
CREATE TABLE `plugins` (
  `id` int(8) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `version` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `db_init` tinyint(1) DEFAULT NULL,
  `priority` int(8) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `real_name_1` varchar(50) DEFAULT NULL,
  `real_name_2` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `name`, `password`, `real_name_1`, `real_name_2`, `email`, `nickname`, `created`, `modified`) VALUES
(1, 'basercake3', '$2y$10$x6WQstawmuyS7XrqutyDjOSOLxJp3dv72O73B7lhqzP8XvVlmcx4G', 'basercake4', '', 'admin@example.com', '', '2017-05-03 14:22:08', '2020-04-22 10:24:08');

-- --------------------------------------------------------

--
-- テーブルの構造 `users_user_groups`
--

DROP TABLE IF EXISTS `users_user_groups`;
CREATE TABLE `users_user_groups` (
  `id` int(8) NOT NULL COMMENT 'ID',
  `user_id` int(8) DEFAULT NULL COMMENT 'ユーザーID',
  `user_group_id` int(8) DEFAULT NULL COMMENT 'ユーザーグループID',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `users_user_groups`
--

INSERT INTO `users_user_groups` (`id`, `user_id`, `user_group_id`, `created`, `modified`) VALUES
(1, 1, 1, '2020-04-01 19:28:31', '2020-04-01 19:28:31');

-- --------------------------------------------------------

--
-- テーブルの構造 `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
CREATE TABLE `user_groups` (
  `id` int(8) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `auth_prefix` varchar(20) DEFAULT NULL,
  `default_favorites` text,
  `use_move_contents` tinyint(1) DEFAULT '0',
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `user_groups`
--

INSERT INTO `user_groups` (`id`, `name`, `title`, `auth_prefix`, `default_favorites`, `use_move_contents`, `modified`, `created`) VALUES
(1, 'admins', 'システム管理', 'admin', 'YTo3OntpOjA7YToyOntzOjQ6Im5hbWUiO3M6MjE6IuOCs+ODs+ODhuODs+ODhOeuoeeQhiI7czozOiJ1cmwiO3M6MjE6Ii9hZG1pbi9jb250ZW50cy9pbmRleCI7fWk6MTthOjI6e3M6NDoibmFtZSI7czoxODoi5paw552A5oOF5aCx566h55CGIjtzOjM6InVybCI7czozMDoiL2FkbWluL2Jsb2cvYmxvZ19wb3N0cy9pbmRleC8xIjt9aToyO2E6Mjp7czo0OiJuYW1lIjtzOjMwOiLmlrDnnYDmg4XloLHjgrPjg6Hjg7Pjg4jkuIDopqciO3M6MzoidXJsIjtzOjMzOiIvYWRtaW4vYmxvZy9ibG9nX2NvbW1lbnRzL2luZGV4LzEiO31pOjM7YToyOntzOjQ6Im5hbWUiO3M6MjQ6IuOBiuWVj+OBhOWQiOOCj+OBm+ioreWumiI7czozOiJ1cmwiO3M6MzE6Ii9hZG1pbi9tYWlsL21haWxfZmllbGRzL2luZGV4LzEiO31pOjQ7YToyOntzOjQ6Im5hbWUiO3M6MjQ6IuOBiuWVj+OBhOWQiOOCj+OBm+S4gOimpyI7czozOiJ1cmwiO3M6MzM6Ii9hZG1pbi9tYWlsL21haWxfbWVzc2FnZXMvaW5kZXgvMSI7fWk6NTthOjI6e3M6NDoibmFtZSI7czoyNDoi44Ki44OD44OX44Ot44O844OJ566h55CGIjtzOjM6InVybCI7czozMToiL2FkbWluL3VwbG9hZGVyL3VwbG9hZGVyX2ZpbGVzLyI7fWk6NjthOjI6e3M6NDoibmFtZSI7czoxNToi44Kv44Os44K444OD44OIIjtzOjM6InVybCI7czoyMDoiamF2YXNjcmlwdDpjcmVkaXQoKTsiO319', 1, '2016-08-16 19:47:07', '2015-06-26 20:34:07'),
(2, 'operators', 'サイト運営', 'admin', 'YTo3OntpOjA7YToyOntzOjQ6Im5hbWUiO3M6MjE6IuOCs+ODs+ODhuODs+ODhOeuoeeQhiI7czozOiJ1cmwiO3M6MjE6Ii9hZG1pbi9jb250ZW50cy9pbmRleCI7fWk6MTthOjI6e3M6NDoibmFtZSI7czoxODoi5paw552A5oOF5aCx566h55CGIjtzOjM6InVybCI7czozMDoiL2FkbWluL2Jsb2cvYmxvZ19wb3N0cy9pbmRleC8xIjt9aToyO2E6Mjp7czo0OiJuYW1lIjtzOjMwOiLmlrDnnYDmg4XloLHjgrPjg6Hjg7Pjg4jkuIDopqciO3M6MzoidXJsIjtzOjMzOiIvYWRtaW4vYmxvZy9ibG9nX2NvbW1lbnRzL2luZGV4LzEiO31pOjM7YToyOntzOjQ6Im5hbWUiO3M6MjQ6IuOBiuWVj+OBhOWQiOOCj+OBm+ioreWumiI7czozOiJ1cmwiO3M6MzE6Ii9hZG1pbi9tYWlsL21haWxfZmllbGRzL2luZGV4LzEiO31pOjQ7YToyOntzOjQ6Im5hbWUiO3M6MjQ6IuOBiuWVj+OBhOWQiOOCj+OBm+S4gOimpyI7czozOiJ1cmwiO3M6MzM6Ii9hZG1pbi9tYWlsL21haWxfbWVzc2FnZXMvaW5kZXgvMSI7fWk6NTthOjI6e3M6NDoibmFtZSI7czoyNDoi44Ki44OD44OX44Ot44O844OJ566h55CGIjtzOjM6InVybCI7czozMToiL2FkbWluL3VwbG9hZGVyL3VwbG9hZGVyX2ZpbGVzLyI7fWk6NjthOjI6e3M6NDoibmFtZSI7czoxNToi44Kv44Os44K444OD44OIIjtzOjM6InVybCI7czoyMDoiamF2YXNjcmlwdDpjcmVkaXQoKTsiO319', 0, NULL, '2015-06-26 20:34:07');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `contents`
--
ALTER TABLE `contents`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `content_folders`
--
ALTER TABLE `content_folders`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `password_requests`
--
ALTER TABLE `password_requests`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `phinxlog`
--
ALTER TABLE `phinxlog`
  ADD PRIMARY KEY (`version`);

--
-- テーブルのインデックス `plugins`
--
ALTER TABLE `plugins`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `users_user_groups`
--
ALTER TABLE `users_user_groups`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `user_groups`
--
ALTER TABLE `user_groups`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `contents`
--
ALTER TABLE `contents`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- テーブルの AUTO_INCREMENT `content_folders`
--
ALTER TABLE `content_folders`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- テーブルの AUTO_INCREMENT `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- テーブルの AUTO_INCREMENT `password_requests`
--
ALTER TABLE `password_requests`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `plugins`
--
ALTER TABLE `plugins`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- テーブルの AUTO_INCREMENT `users_user_groups`
--
ALTER TABLE `users_user_groups`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=2;

--
-- テーブルの AUTO_INCREMENT `user_groups`
--
ALTER TABLE `user_groups`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
