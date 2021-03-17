-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- ホスト: bc5-db
-- 生成日時: 2021 年 3 月 16 日 06:56
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
-- テーブルの構造 `baser_core_phinxlog`
--

DROP TABLE IF EXISTS `baser_core_phinxlog`;
CREATE TABLE `baser_core_phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `baser_core_phinxlog`
--

INSERT INTO `baser_core_phinxlog` (`version`, `migration_name`, `start_time`, `end_time`, `breakpoint`) VALUES
(20210316065511, 'Initial', '2021-03-16 15:55:11', '2021-03-16 15:55:11', 0);

-- --------------------------------------------------------

--
-- テーブルの構造 `bc_blog_phinxlog`
--

DROP TABLE IF EXISTS `bc_blog_phinxlog`;
CREATE TABLE `bc_blog_phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `bc_blog_phinxlog`
--

INSERT INTO `bc_blog_phinxlog` (`version`, `migration_name`, `start_time`, `end_time`, `breakpoint`) VALUES
(20210316064713, 'Initial', '2021-03-16 15:47:13', '2021-03-16 15:47:13', 0);

-- --------------------------------------------------------

--
-- テーブルの構造 `bc_sample_phinxlog`
--

DROP TABLE IF EXISTS `bc_sample_phinxlog`;
CREATE TABLE `bc_sample_phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `bc_sample_phinxlog`
--

INSERT INTO `bc_sample_phinxlog` (`version`, `migration_name`, `start_time`, `end_time`, `breakpoint`) VALUES
(20210316065250, 'Initial', '2021-03-16 15:52:50', '2021-03-16 15:52:50', 0);

-- --------------------------------------------------------

--
-- テーブルの構造 `blog_posts`
--

DROP TABLE IF EXISTS `blog_posts`;
CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `blog_content_id` int(8) DEFAULT NULL,
  `no` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` longtext,
  `detail` longtext,
  `blog_category_id` int(8) DEFAULT NULL,
  `user_id` int(8) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `posted` datetime DEFAULT NULL,
  `content_draft` longtext,
  `detail_draft` longtext,
  `publish_begin` datetime DEFAULT NULL,
  `publish_end` datetime DEFAULT NULL,
  `exclude_search` tinyint(1) DEFAULT NULL,
  `eye_catch` mediumtext,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `blog_content_id`, `no`, `name`, `title`, `content`, `detail`, `blog_category_id`, `user_id`, `status`, `posted`, `content_draft`, `detail_draft`, `publish_begin`, `publish_end`, `exclude_search`, `eye_catch`, `created`, `modified`) VALUES
(1, 1, 1, NULL, 'メールフォーム機能について説明します', '', '<p>baserCMSのメールフォームでは、管理画面上にて入力項目を自由に変更することができ、受信したメールを管理画面で閲覧することができます。</p>\n\n<h3>入力項目の変更</h3>\n\n<p>メールフォームの各入力項目をフィールドと呼びます。フィールドを削除したり新しく追加するには、まず、管理画面より、[お問い合わせ] &rarr; [フィールド] と移動し、登録されているフィールドを確認しましょう。その画面よりフィールドの新規登録や変更、削除が行えます。</p>\n\n<h3>受信メールの確認</h3>\n\n<p>管理画面より、[お問い合わせ] &rarr; [受信メール] と移動すると、受信したメールを一覧で確認できます。データベースに受信したメールを保存しない場合は、[お問い合わせ] &rarr; [設定] &rarr; [詳細設定] より、[送信情報をデータベースに保存しない] にチェックを入れて保存します。</p>', 1, 1, 1, '2021-03-08 11:36:50', '', '', NULL, NULL, 0, '2016/08/00000001_eye_catch.jpg', '2016-08-12 00:48:33', '2021-03-09 19:52:42'),
(2, 1, 2, NULL, 'ブログ機能について説明します', '<p>この文章はブログ記事の [概要] 欄に入力されています。ブログ記事の一覧にて概要だけを表示する場合に利用しますが、テーマの構成上で利用しない場合は、各ブログの [設定] より、 [概要] 欄を利用しないようにする事もできます。ちなみにこのサンプルテーマではブログ記事一覧において概要を利用していません。</p>', '<p>ここからは、ブログ記事の [本文] 欄に入力されている文章となります。</p>\n\n<h3>カテゴリ・タグ機能</h3>\n\n<p>baserCMSでのカテゴリとタグは少し仕様が違います。一つの記事は複数のタグを付けることができますが、複数のカテゴリに属すことはできません。また、タグは全ブログ共通ですが、カテゴリは各ブログごとに分けて作ることができます。</p>\n\n<p>なお、タグやカテゴリを利用するにはテーマ側が対応している必要があります。このサンプルテーマでは、タグの利用を想定していません。</p>\n\n<h3>ブログコメント機能</h3>\n\n<p>ブログの各記事には一般ユーザーがコメントを付ける機能がありますが、利用しない場合は、各ブログの [設定] 画面より簡単に非表示にすることができます。</p>', 1, 1, 1, '2021-03-08 11:36:50', '', '', NULL, NULL, 0, '2016/08/00000002_eye_catch.jpg', '2016-08-12 00:48:33', '2021-03-09 19:52:44');

-- --------------------------------------------------------

--
-- テーブルの構造 `contents`
--

DROP TABLE IF EXISTS `contents`;
CREATE TABLE `contents` (
  `id` int(8) NOT NULL,
  `name` mediumtext,
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
  `description` mediumtext,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `contents` longtext,
  `draft` mediumtext,
  `page_template` varchar(255) DEFAULT NULL,
  `code` mediumtext,
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `plugins`
--

INSERT INTO `plugins` (`id`, `name`, `title`, `version`, `status`, `db_init`, `priority`, `created`, `modified`) VALUES
(1, 'BcBlog', 'ブログ', '5.0.0', 1, 1, 1, '2021-03-16 06:36:24', '2021-03-16 06:36:24'),
(2, 'BcSample', 'サンプル', '1.0.0', 1, 1, 2, '2021-03-16 06:46:11', '2021-03-16 06:46:11');

-- --------------------------------------------------------

--
-- テーブルの構造 `samples`
--

DROP TABLE IF EXISTS `samples`;
CREATE TABLE `samples` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `default_favorites` mediumtext,
  `use_move_contents` tinyint(1) DEFAULT '0',
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- テーブルのインデックス `baser_core_phinxlog`
--
ALTER TABLE `baser_core_phinxlog`
  ADD PRIMARY KEY (`version`);

--
-- テーブルのインデックス `bc_blog_phinxlog`
--
ALTER TABLE `bc_blog_phinxlog`
  ADD PRIMARY KEY (`version`);

--
-- テーブルのインデックス `bc_sample_phinxlog`
--
ALTER TABLE `bc_sample_phinxlog`
  ADD PRIMARY KEY (`version`);

--
-- テーブルのインデックス `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blog_content_id_no_index` (`blog_content_id`,`no`);

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
-- テーブルのインデックス `plugins`
--
ALTER TABLE `plugins`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- テーブルのインデックス `samples`
--
ALTER TABLE `samples`
  ADD PRIMARY KEY (`id`);

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
-- テーブルの AUTO_INCREMENT `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- テーブルの AUTO_INCREMENT `samples`
--
ALTER TABLE `samples`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
