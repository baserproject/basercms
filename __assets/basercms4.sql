-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- ホスト: bc-db
-- 生成日時: 2020 年 12 月 19 日 05:14
-- サーバのバージョン： 5.7.30
-- PHP のバージョン: 7.4.5

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
-- テーブルの構造 `mysite_blog_categories`
--

CREATE TABLE `mysite_blog_categories` (
  `id` int(8) NOT NULL,
  `blog_content_id` int(8) DEFAULT NULL,
  `no` int(8) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `parent_id` int(8) DEFAULT NULL,
  `lft` int(8) DEFAULT NULL,
  `rght` int(8) DEFAULT NULL,
  `owner_id` int(8) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_blog_categories`
--

INSERT INTO `mysite_blog_categories` (`id`, `blog_content_id`, `no`, `name`, `title`, `status`, `parent_id`, `lft`, `rght`, `owner_id`, `created`, `modified`) VALUES
(1, 1, 1, 'release', 'プレスリリース', 1, NULL, 1, 2, NULL, '2016-08-12 00:48:33', NULL);

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_blog_comments`
--

CREATE TABLE `mysite_blog_comments` (
  `id` int(11) NOT NULL,
  `blog_content_id` int(8) DEFAULT NULL,
  `blog_post_id` int(8) DEFAULT NULL,
  `no` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `message` text,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_blog_configs`
--

CREATE TABLE `mysite_blog_configs` (
  `id` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_blog_contents`
--

CREATE TABLE `mysite_blog_contents` (
  `id` int(8) NOT NULL,
  `description` text,
  `template` varchar(20) DEFAULT NULL,
  `list_count` int(4) DEFAULT NULL,
  `list_direction` varchar(4) DEFAULT NULL,
  `feed_count` int(4) DEFAULT NULL,
  `tag_use` tinyint(1) DEFAULT NULL,
  `comment_use` tinyint(1) DEFAULT NULL,
  `comment_approve` tinyint(1) DEFAULT NULL,
  `auth_captcha` tinyint(1) DEFAULT NULL,
  `widget_area` int(4) DEFAULT NULL,
  `eye_catch_size` text,
  `use_content` tinyint(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_blog_contents`
--

INSERT INTO `mysite_blog_contents` (`id`, `description`, `template`, `list_count`, `list_direction`, `feed_count`, `tag_use`, `comment_use`, `comment_approve`, `auth_captcha`, `widget_area`, `eye_catch_size`, `use_content`, `created`, `modified`) VALUES
(1, '<p>このコンテンツはブログ機能により作られており、この文章については管理画面の [NEWS] &rarr; [設定] より更新ができます。また、ブログは [コンテンツ管理] よりいくつでも作成することができます。</p>', 'default', 10, 'DESC', 10, 1, 1, 0, 1, 2, 'YTo0OntzOjExOiJ0aHVtYl93aWR0aCI7czozOiIzMDAiO3M6MTI6InRodW1iX2hlaWdodCI7czozOiIzMDAiO3M6MTg6Im1vYmlsZV90aHVtYl93aWR0aCI7czozOiIxMDAiO3M6MTk6Im1vYmlsZV90aHVtYl9oZWlnaHQiO3M6MzoiMTAwIjt9', 1, '2016-08-07 23:10:38', '2020-09-14 19:27:57'),
(2, NULL, 'default', 10, 'DESC', 10, 0, 1, 0, 1, NULL, 'YTo0OntzOjExOiJ0aHVtYl93aWR0aCI7aTo2MDA7czoxMjoidGh1bWJfaGVpZ2h0IjtpOjYwMDtzOjE4OiJtb2JpbGVfdGh1bWJfd2lkdGgiO2k6MTUwO3M6MTk6Im1vYmlsZV90aHVtYl9oZWlnaHQiO2k6MTUwO30=', 1, '2020-12-14 14:26:56', '2020-12-14 14:26:56');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_blog_posts`
--

CREATE TABLE `mysite_blog_posts` (
  `id` int(11) NOT NULL,
  `blog_content_id` int(8) DEFAULT NULL,
  `no` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `content` longtext,
  `detail` longtext,
  `blog_category_id` int(8) DEFAULT NULL,
  `user_id` int(8) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `posts_date` datetime DEFAULT NULL,
  `content_draft` longtext,
  `detail_draft` longtext,
  `publish_begin` datetime DEFAULT NULL,
  `publish_end` datetime DEFAULT NULL,
  `exclude_search` tinyint(1) DEFAULT NULL,
  `eye_catch` text,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_blog_posts`
--

INSERT INTO `mysite_blog_posts` (`id`, `blog_content_id`, `no`, `name`, `content`, `detail`, `blog_category_id`, `user_id`, `status`, `posts_date`, `content_draft`, `detail_draft`, `publish_begin`, `publish_end`, `exclude_search`, `eye_catch`, `created`, `modified`) VALUES
(1, 1, 1, 'メールフォーム機能について説明します', '', '<p>baserCMSのメールフォームでは、管理画面上にて入力項目を自由に変更することができ、受信したメールを管理画面で閲覧することができます。</p>\r\n\r\n<h3>入力項目の変更</h3>\r\n\r\n<p>メールフォームの各入力項目をフィールドと呼びます。フィールドを削除したり新しく追加するには、まず、管理画面より、[お問い合わせ] &rarr; [フィールド] と移動し、登録されているフィールドを確認しましょう。その画面よりフィールドの新規登録や変更、削除が行えます。</p>\r\n\r\n<h3>受信メールの確認</h3>\r\n\r\n<p>管理画面より、[お問い合わせ] &rarr; [受信メール] と移動すると、受信したメールを一覧で確認できます。データベースに受信したメールを保存しない場合は、[お問い合わせ] &rarr; [設定] &rarr; [詳細設定] より、[送信情報をデータベースに保存しない] にチェックを入れて保存します。</p>\r\n', 1, 1, 1, '2020-12-03 14:41:46', '', '', NULL, NULL, 1, '', '2016-08-12 00:48:33', '2020-12-14 14:40:51'),
(2, 1, 2, 'ブログ機能について説明します', '<p>この文章はブログ記事の [概要] 欄に入力されています。ブログ記事の一覧にて概要だけを表示する場合に利用しますが、テーマの構成上で利用しない場合は、各ブログの [設定] より、 [概要] 欄を利用しないようにする事もできます。ちなみにこのサンプルテーマではブログ記事一覧において概要を利用していません。</p>\r\n', '<p>ここからは、ブログ記事の [本文] 欄に入力されている文章となります。</p>\r\n\r\n<h3>カテゴリ・タグ機能</h3>\r\n\r\n<p>baserCMSでのカテゴリとタグは少し仕様が違います。一つの記事は複数のタグを付けることができますが、複数のカテゴリに属すことはできません。また、タグは全ブログ共通ですが、カテゴリは各ブログごとに分けて作ることができます。</p>\r\n\r\n<p>なお、タグやカテゴリを利用するにはテーマ側が対応している必要があります。このサンプルテーマでは、タグの利用を想定していません。</p>\r\n\r\n<h3>ブログコメント機能</h3>\r\n\r\n<p>ブログの各記事には一般ユーザーがコメントを付ける機能がありますが、利用しない場合は、各ブログの [設定] 画面より簡単に非表示にすることができます。</p>\r\n', 1, 1, 1, '2020-12-03 14:41:46', '', '', NULL, NULL, 0, '2020/12/00000002_eye_catch.jpg', '2016-08-12 00:48:33', '2020-12-05 10:14:26');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_blog_posts_blog_tags`
--

CREATE TABLE `mysite_blog_posts_blog_tags` (
  `id` int(8) NOT NULL,
  `blog_post_id` int(8) DEFAULT NULL,
  `blog_tag_id` int(8) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_blog_posts_blog_tags`
--

INSERT INTO `mysite_blog_posts_blog_tags` (`id`, `blog_post_id`, `blog_tag_id`, `created`, `modified`) VALUES
(16, 2, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_blog_tags`
--

CREATE TABLE `mysite_blog_tags` (
  `id` int(8) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_blog_tags`
--

INSERT INTO `mysite_blog_tags` (`id`, `name`, `created`, `modified`) VALUES
(1, '新製品', '2016-08-12 00:48:33', NULL);

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_contents`
--

CREATE TABLE `mysite_contents` (
  `id` int(8) NOT NULL,
  `name` text,
  `plugin` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `entity_id` int(8) DEFAULT NULL,
  `url` text,
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
-- テーブルのデータのダンプ `mysite_contents`
--

INSERT INTO `mysite_contents` (`id`, `name`, `plugin`, `type`, `entity_id`, `url`, `site_id`, `alias_id`, `main_site_content_id`, `parent_id`, `lft`, `rght`, `level`, `title`, `description`, `eyecatch`, `author_id`, `layout_template`, `status`, `publish_begin`, `publish_end`, `self_status`, `self_publish_begin`, `self_publish_end`, `exclude_search`, `created_date`, `modified_date`, `site_root`, `deleted_date`, `deleted`, `exclude_menu`, `blank_link`, `created`, `modified`) VALUES
(1, '', 'Core', 'ContentFolder', 1, '/', 0, NULL, NULL, NULL, 1, 32, 0, 'baserCMSサンプル', '', '', 1, 'default', 1, NULL, NULL, 1, NULL, NULL, 0, NULL, '2019-06-11 12:27:01', 1, NULL, 0, 0, 0, '2016-07-29 18:02:53', '2020-12-14 14:51:08'),
(4, 'index', 'Core', 'Page', 1, '/index', 0, NULL, NULL, 1, 2, 3, 1, 'トップページ', '', '', 1, '', 1, NULL, NULL, 1, NULL, NULL, 0, '2016-07-29 18:13:03', '2020-09-14 20:13:10', 0, NULL, 0, 0, 0, '2016-07-29 18:13:03', '2020-09-14 20:13:25'),
(5, 'about', 'Core', 'Page', 2, '/about', 0, NULL, NULL, 1, 12, 13, 1, '会社案内', '', '', 1, '', 1, NULL, NULL, 1, NULL, NULL, 0, '2016-07-29 18:13:55', '2020-09-14 19:52:55', 0, NULL, 0, 0, 0, '2016-07-29 18:13:56', '2020-09-14 19:53:48'),
(6, 'service', 'Core', 'ContentFolder', 4, '/service/', 0, NULL, NULL, 1, 14, 23, 1, 'サービス', '', '', 1, '', 1, NULL, NULL, 1, NULL, NULL, 0, '2016-07-29 18:14:33', NULL, 0, NULL, 0, 0, 0, '2016-07-29 18:14:33', '2016-07-29 18:14:54'),
(7, 'sample', 'Core', 'Page', 5, '/sample', 0, NULL, NULL, 1, 26, 27, 1, 'サンプル', '', '', 1, '', 1, NULL, NULL, 1, NULL, NULL, 0, '2016-07-29 18:15:14', '2020-09-14 22:27:49', 0, NULL, 0, 0, 0, '2016-07-29 18:15:14', '2020-09-14 22:30:21'),
(9, 'contact', 'Mail', 'MailContent', 1, '/contact/', 0, NULL, NULL, 1, 24, 25, 1, 'お問い合わせ', '', '', 1, '', 1, NULL, NULL, 1, NULL, NULL, 0, '2016-07-30 21:51:49', '2020-09-14 19:36:02', 0, NULL, 0, 0, 0, '2016-07-30 21:51:49', '2020-09-14 19:37:11'),
(10, 'news', 'Blog', 'BlogContent', 1, '/news/', 0, NULL, NULL, 1, 4, 5, 1, 'NEWS', '', '', 1, '', 1, NULL, NULL, 1, NULL, NULL, 0, '2016-07-31 15:01:41', '2020-09-14 19:27:41', 0, NULL, 0, 0, 0, '2016-07-31 15:01:41', '2020-09-14 19:27:57'),
(11, 'service1', 'Core', 'Page', 3, '/service/service1', 0, NULL, NULL, 6, 15, 16, 2, 'サービス１', '', '', 1, '', 1, NULL, NULL, 1, NULL, NULL, 0, '2016-07-31 16:46:32', NULL, 0, NULL, 0, 0, 0, '2016-07-31 16:46:32', '2016-08-12 00:58:02'),
(12, 'service2', 'Core', 'Page', 6, '/service/service2', 0, NULL, NULL, 6, 17, 18, 2, 'サービス２', '', '', 1, '', 1, NULL, NULL, 1, NULL, NULL, 0, '2016-07-31 16:46:47', NULL, 0, NULL, 0, 0, 0, '2016-07-31 16:46:47', '2016-08-12 00:58:58'),
(13, 'service3', 'Core', 'Page', 7, '/service/service3', 0, NULL, NULL, 6, 19, 20, 2, 'サービス３', '', '', 1, '', 1, NULL, NULL, 1, NULL, NULL, 0, '2016-07-31 16:47:04', NULL, 0, NULL, 0, 0, 0, '2016-07-31 16:47:04', '2016-08-12 00:59:06'),
(14, 'aaa', 'Core', 'ContentFolder', 5, '/service/aaa/', 0, NULL, NULL, 6, 21, 22, 2, 'aaa', NULL, NULL, 1, NULL, 1, NULL, NULL, 1, NULL, NULL, 0, '2020-12-14 14:24:29', NULL, 0, NULL, 0, 0, 0, '2020-12-14 14:24:29', '2020-12-14 14:26:43'),
(15, 'aaa', 'Core', 'Page', 8, '/aaa', 0, NULL, NULL, 1, 6, 7, 1, 'aaa', '', NULL, 1, '', 1, NULL, NULL, 1, NULL, NULL, 0, '2020-12-14 14:24:35', '2020-12-14 14:29:24', 0, NULL, 0, 0, 0, '2020-12-14 14:24:35', '2020-12-14 14:29:33'),
(16, 'blog', 'Blog', 'BlogContent', 2, '/blog/', 0, NULL, NULL, 1, 8, 9, 1, 'blog', NULL, NULL, 1, NULL, 1, NULL, NULL, 1, NULL, NULL, 0, '2020-12-14 14:26:56', NULL, 0, NULL, 0, 0, 0, '2020-12-14 14:26:57', '2020-12-14 14:28:08'),
(17, 'mail', 'Mail', 'MailContent', 2, '/mail/', 0, NULL, NULL, 1, 10, 11, 1, 'mail', NULL, NULL, 1, NULL, 1, NULL, NULL, 1, NULL, NULL, 0, '2020-12-14 14:27:12', NULL, 0, NULL, 0, 0, 0, '2020-12-14 14:27:12', '2020-12-14 14:28:13'),
(18, 'en', 'Core', 'ContentFolder', 6, '/en/', 1, NULL, 1, 1, 28, 31, 1, '英語サイト', NULL, NULL, 1, 'default', 1, NULL, NULL, 1, NULL, NULL, 0, '2020-12-14 14:48:33', NULL, 1, NULL, 0, 0, 0, '2020-12-14 14:48:33', '2020-12-14 14:48:33'),
(19, 'index', 'Core', 'Page', 9, '/en/index', 1, NULL, 4, 18, 29, 30, 2, 'index', NULL, NULL, 1, NULL, 1, NULL, NULL, 1, NULL, NULL, 0, '2020-12-14 14:48:53', NULL, 0, NULL, 0, 0, 0, '2020-12-14 14:48:53', '2020-12-14 14:48:57');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_content_folders`
--

CREATE TABLE `mysite_content_folders` (
  `id` int(8) NOT NULL,
  `folder_template` varchar(255) DEFAULT NULL,
  `page_template` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_content_folders`
--

INSERT INTO `mysite_content_folders` (`id`, `folder_template`, `page_template`, `created`, `modified`) VALUES
(1, '', '', '2019-06-11 12:24:28', '2020-12-14 14:51:08'),
(2, '', '', '2019-06-11 12:24:28', '2020-09-14 21:10:40'),
(3, '', '', '2019-06-11 12:24:28', '2020-09-14 21:10:40'),
(4, '', '', '2019-06-11 12:24:28', NULL),
(5, NULL, NULL, '2020-12-14 14:24:29', '2020-12-14 14:24:29'),
(6, NULL, NULL, '2020-12-14 14:48:33', '2020-12-14 14:48:33');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_content_links`
--

CREATE TABLE `mysite_content_links` (
  `id` int(8) NOT NULL,
  `url` text,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_dblogs`
--

CREATE TABLE `mysite_dblogs` (
  `id` int(8) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `user_id` int(8) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_dblogs`
--

INSERT INTO `mysite_dblogs` (`id`, `name`, `user_id`, `created`, `modified`) VALUES
(1, '新規プラグイン「CuCustomField」を baserCMS に登録しました。', 1, '2020-12-04 12:38:25', '2020-12-04 12:38:25'),
(2, 'CuCustomFieldConfigsを追加しました。', 1, '2020-12-04 12:38:50', '2020-12-04 12:38:50'),
(3, 'フィールド定義「氏名」の追加が完了しました。', 1, '2020-12-04 12:41:31', '2020-12-04 12:41:31'),
(4, 'フィールド定義「肩書」の追加が完了しました。', 1, '2020-12-04 12:42:28', '2020-12-04 12:42:28'),
(5, 'フィールド定義「紹介文」の追加が完了しました。', 1, '2020-12-04 12:43:02', '2020-12-04 12:43:02'),
(6, 'フィールド定義「趣味」の追加が完了しました。', 1, '2020-12-04 12:43:21', '2020-12-04 12:43:21'),
(7, 'フィールド定義「メッセージ」の追加が完了しました。', 1, '2020-12-04 12:44:26', '2020-12-04 12:44:26'),
(8, 'フィールド定義「性別」の追加が完了しました。', 1, '2020-12-04 12:45:06', '2020-12-04 12:45:06'),
(9, 'プラグイン「CuCustomField」 を 無効化しました。', 1, '2020-12-04 12:54:55', '2020-12-04 12:54:55'),
(10, '新規プラグイン「CuStatic」を baserCMS に登録しました。', 1, '2020-12-04 12:55:05', '2020-12-04 12:55:05'),
(11, 'オプション設定を保存しました。', 1, '2020-12-04 12:55:22', '2020-12-04 12:55:22'),
(12, 'オプション設定を保存しました。', 1, '2020-12-04 13:41:08', '2020-12-04 13:41:08'),
(13, 'プラグイン「CuStatic」 を 無効化しました。', 1, '2020-12-05 08:08:29', '2020-12-05 08:08:29'),
(14, '新規プラグイン「CuCustomField」を baserCMS に登録しました。', 1, '2020-12-05 08:08:36', '2020-12-05 08:08:36'),
(15, 'フィールド定義「氏名」を更新しました。', 1, '2020-12-05 08:09:18', '2020-12-05 08:09:18'),
(16, '記事「ブログ機能について説明します」を更新しました。', 1, '2020-12-05 08:14:26', '2020-12-05 08:14:26'),
(17, '記事「ブログ機能について説明します」を更新しました。', 1, '2020-12-05 10:12:29', '2020-12-05 10:12:29'),
(18, '記事「ブログ機能について説明します」を更新しました。', 1, '2020-12-05 10:13:56', '2020-12-05 10:13:56'),
(19, '記事「ブログ機能について説明します」を更新しました。', 1, '2020-12-05 10:15:43', '2020-12-05 10:15:43'),
(20, 'フィールド定義「肩書」を更新しました。', 1, '2020-12-05 10:16:58', '2020-12-05 10:16:58'),
(21, 'フィールド定義「氏名」を更新しました。', 1, '2020-12-05 10:17:12', '2020-12-05 10:17:12'),
(22, 'フィールド定義「紹介文」を更新しました。', 1, '2020-12-05 10:17:30', '2020-12-05 10:17:30'),
(23, 'フィールド定義「氏名」を更新しました。', 1, '2020-12-05 10:23:17', '2020-12-05 10:23:17'),
(24, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 10:25:08', '2020-12-05 10:25:08'),
(25, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 10:28:29', '2020-12-05 10:28:29'),
(26, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 10:29:39', '2020-12-05 10:29:39'),
(27, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 10:31:08', '2020-12-05 10:31:08'),
(28, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 10:35:57', '2020-12-05 10:35:57'),
(29, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 10:43:47', '2020-12-05 10:43:47'),
(30, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 10:45:15', '2020-12-05 10:45:15'),
(31, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 10:45:30', '2020-12-05 10:45:30'),
(32, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 10:46:52', '2020-12-05 10:46:52'),
(33, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 10:47:03', '2020-12-05 10:47:03'),
(34, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 10:47:16', '2020-12-05 10:47:16'),
(35, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 10:50:45', '2020-12-05 10:50:45'),
(36, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 11:17:28', '2020-12-05 11:17:28'),
(37, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 11:21:00', '2020-12-05 11:21:00'),
(38, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 11:21:40', '2020-12-05 11:21:40'),
(39, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 11:26:15', '2020-12-05 11:26:15'),
(40, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 11:29:05', '2020-12-05 11:29:05'),
(41, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 11:29:19', '2020-12-05 11:29:19'),
(42, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 11:35:02', '2020-12-05 11:35:02'),
(43, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 11:35:16', '2020-12-05 11:35:16'),
(44, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 11:36:24', '2020-12-05 11:36:24'),
(45, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 11:37:39', '2020-12-05 11:37:39'),
(46, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 11:37:48', '2020-12-05 11:37:48'),
(47, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 11:38:02', '2020-12-05 11:38:02'),
(48, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 11:38:14', '2020-12-05 11:38:14'),
(49, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 11:38:42', '2020-12-05 11:38:42'),
(50, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 11:38:58', '2020-12-05 11:38:58'),
(51, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 11:39:13', '2020-12-05 11:39:13'),
(52, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 12:09:35', '2020-12-05 12:09:35'),
(53, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 12:12:02', '2020-12-05 12:12:02'),
(54, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 12:14:15', '2020-12-05 12:14:15'),
(55, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 12:25:51', '2020-12-05 12:25:51'),
(56, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 12:41:22', '2020-12-05 12:41:22'),
(57, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 12:41:36', '2020-12-05 12:41:36'),
(58, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 13:13:30', '2020-12-05 13:13:30'),
(59, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 13:13:51', '2020-12-05 13:13:51'),
(60, 'フィールド定義「趣味」を更新しました。', 1, '2020-12-05 13:15:31', '2020-12-05 13:15:31'),
(61, 'フィールド定義の並び順を繰り上げました。', 1, '2020-12-05 13:15:36', '2020-12-05 13:15:36'),
(62, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 13:20:37', '2020-12-05 13:20:37'),
(63, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 13:25:48', '2020-12-05 13:25:48'),
(64, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 13:46:30', '2020-12-05 13:46:30'),
(65, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 13:48:06', '2020-12-05 13:48:06'),
(66, 'フィールド定義「氏名」を更新しました。', 1, '2020-12-05 13:48:42', '2020-12-05 13:48:42'),
(67, 'フィールド定義「性別」を更新しました。', 1, '2020-12-05 13:50:50', '2020-12-05 13:50:50'),
(68, 'フィールド定義「肩書」を更新しました。', 1, '2020-12-05 13:51:25', '2020-12-05 13:51:25'),
(69, 'フィールド定義「紹介文」を更新しました。', 1, '2020-12-05 13:51:50', '2020-12-05 13:51:50'),
(70, 'フィールド定義「メッセージ」を更新しました。', 1, '2020-12-05 13:51:53', '2020-12-05 13:51:53'),
(71, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 14:02:28', '2020-12-05 14:02:28'),
(72, 'フィールド定義「趣味」を更新しました。', 1, '2020-12-05 14:03:09', '2020-12-05 14:03:09'),
(73, 'フィールド定義「紹介文」を更新しました。', 1, '2020-12-05 14:10:24', '2020-12-05 14:10:24'),
(74, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 14:12:17', '2020-12-05 14:12:17'),
(75, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 14:12:33', '2020-12-05 14:12:33'),
(76, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 14:20:00', '2020-12-05 14:20:00'),
(77, 'フィールド定義「紹介文」を更新しました。', 1, '2020-12-05 14:20:32', '2020-12-05 14:20:32'),
(78, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 14:21:04', '2020-12-05 14:21:04'),
(79, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 14:35:13', '2020-12-05 14:35:13'),
(80, 'フィールド定義「趣味」を更新しました。', 1, '2020-12-05 14:51:57', '2020-12-05 14:51:57'),
(81, 'フィールド定義「紹介文」を更新しました。', 1, '2020-12-05 14:58:34', '2020-12-05 14:58:34'),
(82, 'フィールド定義「氏名」を更新しました。', 1, '2020-12-05 14:59:01', '2020-12-05 14:59:01'),
(83, 'フィールド定義「氏名」を更新しました。', 1, '2020-12-05 15:04:15', '2020-12-05 15:04:15'),
(84, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 15:04:58', '2020-12-05 15:04:58'),
(85, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 15:13:35', '2020-12-05 15:13:35'),
(86, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 15:13:45', '2020-12-05 15:13:45'),
(87, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-05 15:21:31', '2020-12-05 15:21:31'),
(88, 'フォルダ「aaa」を追加しました。', 1, '2020-12-14 14:24:29', '2020-12-14 14:24:29'),
(89, '固定ページ「aaa」を追加しました。\nhttp://localhost/service/aaa/aaa', 1, '2020-12-14 14:24:36', '2020-12-14 14:24:36'),
(90, 'ブログ「blog」を追加しました。', 1, '2020-12-14 14:26:57', '2020-12-14 14:26:57'),
(91, 'メールフォーム「mail」を追加しました。', 1, '2020-12-14 14:27:13', '2020-12-14 14:27:13'),
(92, 'コンテンツ「aaa」の配置を移動しました。\n/service/aaa/aaa > /aaa', 1, '2020-12-14 14:28:04', '2020-12-14 14:28:04'),
(93, 'コンテンツ「blog」の配置を移動しました。\n/service/aaa/blog/ > /blog/', 1, '2020-12-14 14:28:09', '2020-12-14 14:28:09'),
(94, 'コンテンツ「mail」の配置を移動しました。\n/service/aaa/mail/ > /mail/', 1, '2020-12-14 14:28:14', '2020-12-14 14:28:14'),
(95, '固定ページ「aaa」を更新しました。\nhttp://localhost/aaa', 1, '2020-12-14 14:29:34', '2020-12-14 14:29:34'),
(96, 'ユーザー「bbb」を追加しました。', 1, '2020-12-14 14:37:25', '2020-12-14 14:37:25'),
(97, '記事「メールフォーム機能について説明します」を更新しました。', 1, '2020-12-14 14:40:52', '2020-12-14 14:40:52'),
(98, '新規メールフィールド「aaa」を追加しました。', 1, '2020-12-14 14:44:46', '2020-12-14 14:44:46'),
(99, 'サブサイト「en」を追加しました。', 1, '2020-12-14 14:48:33', '2020-12-14 14:48:33'),
(100, '固定ページ「index」を追加しました。\nhttp://localhost/en/', 1, '2020-12-14 14:48:54', '2020-12-14 14:48:54');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_editor_templates`
--

CREATE TABLE `mysite_editor_templates` (
  `id` int(8) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `html` text,
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_editor_templates`
--

INSERT INTO `mysite_editor_templates` (`id`, `name`, `image`, `description`, `html`, `modified`, `created`) VALUES
(1, '画像（左）とテキスト', 'template1.gif', '画像を左に配置し、その右にテキストを配置するブロックです。', '<div class=\"template-image-float-left clearfix\">\n	<div class=\"image\">ここに画像を挿入します</div>\n	<div class=\"text\">\n		<h2>見出しを挿入します。</h2>\n		<p>1段落目のテキストを挿入します。</p>\n		<p>2段落目のテキストを挿入します。</p>\n	</div>\n</div>\n<p>新しいブロックを挿入します。不要な場合はこの段落を削除します</p>', NULL, '2015-06-26 20:34:05'),
(2, '画像（右）とテキスト', 'template2.gif', '画像を右に配置し、その左にテキストを配置するブロックです。', '<div class=\"template-image-float-right clearfix\">\n	<div class=\"image\">ここに画像を挿入します</div>\n	<div class=\"text\">\n		<h2>見出しを挿入します。</h2>\n		<p>1段落目のテキストを挿入します。</p>\n		<p>2段落目のテキストを挿入します。</p>\n	</div>\n</div>\n<p>新しいブロックを挿入します。不要な場合はこの段落を削除します</p>', NULL, '2015-06-26 20:34:05'),
(3, 'テキスト２段組', 'template3.gif', 'テキストを左右に２段組するブロックです。', '<div class=\"template-two-block clearfix\">\n	<div class=\"block-left\">\n		<h2>\n			見出しを挿入します。</h2>\n		<p>\n			1段落目のテキストを挿入します。</p>\n		<p>\n			2段落目のテキストを挿入します。</p>\n	</div>\n	<div class=\"block-right\">\n		<h2>\n			見出しを挿入します。</h2>\n		<p>\n			1段落目のテキストを挿入します。</p>\n		<p>\n			2段落目のテキストを挿入します。</p>\n	</div>\n</div>\n<p>\n	新しいブロックを挿入します。不要な場合はこの段落を削除します</p>', NULL, '2015-06-26 20:34:05');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_favorites`
--

CREATE TABLE `mysite_favorites` (
  `id` int(8) NOT NULL,
  `user_id` int(8) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `sort` int(8) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_favorites`
--

INSERT INTO `mysite_favorites` (`id`, `user_id`, `name`, `url`, `sort`, `created`, `modified`) VALUES
(1, 1, 'クレジット', 'javascript:credit();', 1, '2020-12-03 14:41:39', '2020-12-03 14:41:39'),
(2, 1, 'カスタムフィールドプラグイン 管理', '/admin/cu_custom_field/cu_custom_field_configs/', 2, '2020-12-04 12:38:26', '2020-12-04 12:38:26'),
(3, 1, '静的HTML出力プラグイン 管理', '/admin/cu_static/cu_statics/', 3, '2020-12-04 12:55:05', '2020-12-04 12:55:05');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_feed_configs`
--

CREATE TABLE `mysite_feed_configs` (
  `id` int(8) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `feed_title_index` varchar(255) DEFAULT NULL,
  `category_index` varchar(255) DEFAULT NULL,
  `template` varchar(50) DEFAULT NULL,
  `display_number` int(3) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_feed_configs`
--

INSERT INTO `mysite_feed_configs` (`id`, `name`, `feed_title_index`, `category_index`, `template`, `display_number`, `created`, `modified`) VALUES
(1, 'baserCMS最新情報', '', '', 'default', 3, '2016-08-12 00:48:33', '2020-09-14 19:50:02');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_feed_details`
--

CREATE TABLE `mysite_feed_details` (
  `id` int(8) NOT NULL,
  `feed_config_id` int(8) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `category_filter` varchar(255) DEFAULT NULL,
  `cache_time` varchar(20) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_feed_details`
--

INSERT INTO `mysite_feed_details` (`id`, `feed_config_id`, `name`, `url`, `category_filter`, `cache_time`, `created`, `modified`) VALUES
(1, 1, 'baserCMS最新情報', 'https://basercms.net/news/index.rss?site=http://localhost/', '', '+30 minutes', '2016-08-12 00:48:33', '2020-12-03 14:41:46');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_mail_configs`
--

CREATE TABLE `mysite_mail_configs` (
  `id` int(11) NOT NULL,
  `site_name` varchar(50) DEFAULT NULL,
  `site_url` varchar(255) DEFAULT NULL,
  `site_email` varchar(50) DEFAULT NULL,
  `site_tel` varchar(20) DEFAULT NULL,
  `site_fax` varchar(20) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_mail_configs`
--

INSERT INTO `mysite_mail_configs` (`id`, `site_name`, `site_url`, `site_email`, `site_tel`, `site_fax`, `created`, `modified`) VALUES
(1, 'baserCMS - Based Website Development Project -', 'http://basercms.net/', 'info@basercms.net', '', '', '2016-08-12 00:48:33', NULL);

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_mail_contents`
--

CREATE TABLE `mysite_mail_contents` (
  `id` int(8) NOT NULL,
  `description` text,
  `sender_1` text,
  `sender_2` text,
  `sender_name` varchar(255) DEFAULT NULL,
  `subject_user` varchar(255) DEFAULT NULL,
  `subject_admin` varchar(255) DEFAULT NULL,
  `form_template` varchar(20) DEFAULT NULL,
  `mail_template` varchar(20) DEFAULT NULL,
  `redirect_url` varchar(255) DEFAULT NULL,
  `auth_captcha` tinyint(1) DEFAULT NULL,
  `widget_area` int(4) DEFAULT NULL,
  `ssl_on` tinyint(1) DEFAULT NULL,
  `save_info` tinyint(1) DEFAULT NULL,
  `publish_begin` datetime DEFAULT NULL,
  `publish_end` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_mail_contents`
--

INSERT INTO `mysite_mail_contents` (`id`, `description`, `sender_1`, `sender_2`, `sender_name`, `subject_user`, `subject_admin`, `form_template`, `mail_template`, `redirect_url`, `auth_captcha`, `widget_area`, `ssl_on`, `save_info`, `publish_begin`, `publish_end`, `created`, `modified`) VALUES
(1, '<p>このコンテンツはメールフォーム機能により作られており、この文章については管理画面の [お問い合わせ] &rarr; [設定] より更新ができます。また、メールフォームは [コンテンツ管理] よりいくつでも作成することができます。</p>', '', '', 'baserCMSサンプル', '【baserCMS】お問い合わせ頂きありがとうございます。', '【baserCMS】お問い合わせを受け付けました', 'default', 'mail_default', '/', 1, NULL, 0, 1, NULL, NULL, '2016-08-07 23:10:38', '2020-09-14 19:37:11'),
(2, NULL, NULL, NULL, '送信先名を入力してください', 'お問い合わせ頂きありがとうございます', 'お問い合わせを頂きました', 'default', 'mail_default', NULL, 0, NULL, 0, 1, NULL, NULL, '2020-12-14 14:27:12', '2020-12-14 14:27:12');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_mail_fields`
--

CREATE TABLE `mysite_mail_fields` (
  `id` int(11) NOT NULL,
  `mail_content_id` int(11) DEFAULT NULL,
  `no` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `field_name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `head` varchar(255) DEFAULT NULL,
  `attention` varchar(255) DEFAULT NULL,
  `before_attachment` varchar(255) DEFAULT NULL,
  `after_attachment` varchar(255) DEFAULT NULL,
  `source` text,
  `size` int(11) DEFAULT NULL,
  `rows` int(11) DEFAULT NULL,
  `maxlength` int(11) DEFAULT NULL,
  `options` varchar(255) DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL,
  `separator` varchar(20) DEFAULT NULL,
  `default_value` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `group_field` varchar(255) DEFAULT NULL,
  `group_valid` varchar(255) DEFAULT NULL,
  `valid` varchar(255) DEFAULT NULL,
  `valid_ex` varchar(255) DEFAULT NULL,
  `auto_convert` varchar(255) DEFAULT NULL,
  `not_empty` tinyint(1) DEFAULT NULL,
  `use_field` tinyint(1) DEFAULT NULL,
  `no_send` tinyint(1) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_mail_fields`
--

INSERT INTO `mysite_mail_fields` (`id`, `mail_content_id`, `no`, `name`, `field_name`, `type`, `head`, `attention`, `before_attachment`, `after_attachment`, `source`, `size`, `rows`, `maxlength`, `options`, `class`, `separator`, `default_value`, `description`, `group_field`, `group_valid`, `valid`, `valid_ex`, `auto_convert`, `not_empty`, `use_field`, `no_send`, `sort`, `created`, `modified`) VALUES
(1, 1, 1, '姓', 'name_1', 'text', 'お名前', '', '', '', '', NULL, NULL, 255, 'placeholder|姓', '', '', '', '', 'name', 'name', 'VALID_NOT_EMPTY', '', '', 1, 1, 0, 1, '2016-08-12 00:48:34', '2020-09-14 19:41:09'),
(2, 1, 2, '名', 'name_2', 'text', 'お名前', '', '', '', '', NULL, NULL, 255, 'placeholder|名', '', '', '', '', 'name', 'name', 'VALID_NOT_EMPTY', '', '', 1, 1, 0, 2, '2016-08-12 00:48:34', '2020-09-14 19:41:24'),
(5, 1, 5, '性別', 'sex', 'radio', '性別', '', '', '', '男性|女性', 0, 0, 0, '', '', '', '', '', '', '', '', '', '', 0, 1, 0, 5, '2016-08-12 00:48:34', NULL),
(6, 1, 7, 'メールアドレス', 'email_1', 'email', 'メールアドレス', '', '', '', '', NULL, NULL, 50, '', '', '', '', '', 'email', 'email', 'VALID_EMAIL', 'VALID_EMAIL_CONFIRM', '', 1, 1, 0, 6, '2016-08-12 00:48:34', '2020-09-14 19:42:22'),
(7, 1, 8, 'メールアドレス確認', 'email_2', 'text', 'メールアドレス（確認）', '', '', '', '', NULL, NULL, 50, 'placeholder|確認の為もう一度入力して下さい', '', '', '', '', 'email', 'email', 'VALID_EMAIL', 'VALID_EMAIL_CONFIRM', '', 1, 1, 1, 7, '2016-08-12 00:48:34', '2020-09-14 19:42:37'),
(8, 1, 9, '電話番号', 'tel', 'text', '電話番号', '', '', '', '', NULL, NULL, 11, 'placeholder|ハイフン抜きで入力して下さい', '', '', '', '', '', '', '', '', 'CONVERT_HANKAKU', 0, 1, 0, 8, '2016-08-12 00:48:34', '2020-09-14 19:39:56'),
(11, 1, 12, '郵便番号', 'zip', 'autozip', '住所', '', '', '', 'address_1\naddress_2', NULL, NULL, 8, 'placeholder|〒 郵便番号をハイフン抜きで入力してください', '', '', '', '', 'address', '', '', '', 'CONVERT_HANKAKU', 0, 1, 0, 11, '2016-08-12 00:48:34', '2020-09-14 19:43:45'),
(12, 1, 13, '都道府県', 'address_1', 'pref', '住所', '', '', '', '', NULL, NULL, NULL, '', '', '', '', '', 'address', '', '', '', '', 0, 1, 0, 12, '2016-08-12 00:48:34', '2020-09-14 19:43:59'),
(13, 1, 14, '市区町村・番地', 'address_2', 'text', '住所', '', '', '', '', NULL, NULL, 200, 'placeholder|市区町村・番地', '', '', '', '', 'address', '', '', '', '', 0, 1, 0, 13, '2016-08-12 00:48:34', '2020-09-14 19:44:29'),
(14, 1, 15, '建物名', 'address_3', 'text', '住所', '', '', '', '', NULL, NULL, 200, 'placeholder|建物名', '', '', '', '', 'address', '', '', '', '', 0, 1, 0, 14, '2016-08-12 00:48:34', '2020-09-14 19:44:41'),
(15, 1, 16, 'お問い合わせ項目', 'category', 'multi_check', 'お問い合わせ項目', '', '', '', '資料請求|問い合わせ|その他', 0, 0, 0, '', '', '', '', '', '', '', '', 'VALID_NOT_UNCHECKED', '', 1, 1, 0, 15, '2016-08-12 00:48:34', NULL),
(16, 1, 17, 'お問い合わせ内容', 'message', 'textarea', 'お問い合わせ内容', '', '', '', '', 48, 12, NULL, '', '', '', '', '', '', '', '', '', '', 0, 1, 0, 16, '2016-08-12 00:48:34', NULL),
(17, 1, 18, 'ルート', 'root', 'select', 'どうやってこのサイトをお知りになりましたか？', '', '', '<br>', '検索エンジン|web広告|紙面広告|求人案内|その他', 0, 0, 0, '', '', '', '', '', 'root', '', 'VALID_NOT_EMPTY', '', '', 1, 1, 0, 17, '2016-08-12 00:48:34', NULL),
(18, 2, 1, 'aaa', 'aaa', 'text', 'aaa', '', '', '', '', NULL, NULL, NULL, '', '', '', '', '', '', '', '', '', '', 0, 1, 0, 18, '2020-12-14 14:44:46', '2020-12-14 14:44:46');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_mail_messages`
--

CREATE TABLE `mysite_mail_messages` (
  `id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_mail_message_1`
--

CREATE TABLE `mysite_mail_message_1` (
  `id` int(8) NOT NULL,
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `name_1` text,
  `name_2` text,
  `sex` text,
  `email_1` text,
  `email_2` text,
  `tel` text,
  `zip` text,
  `address_1` text,
  `address_2` text,
  `address_3` text,
  `category` text,
  `message` text,
  `root` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_mail_message_2`
--

CREATE TABLE `mysite_mail_message_2` (
  `id` int(8) NOT NULL,
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `aaa` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_pages`
--

CREATE TABLE `mysite_pages` (
  `id` int(8) NOT NULL,
  `contents` longtext,
  `draft` longtext,
  `page_template` varchar(255) DEFAULT NULL,
  `code` text,
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_pages`
--

INSERT INTO `mysite_pages` (`id`, `contents`, `draft`, `page_template`, `code`, `modified`, `created`) VALUES
(1, '<h2>baserCMSサンプルテーマ</h2>\n\n<p>これは、baserCMSのサンプルテーマです。<br>\nトップページのこの部分は、固定ページで作られており、NEWS と baserCMS最新情報の部分は、テンプレートで作成されています。<br>\n変更する場合は、/Elements/top_info.php を変更してください。</p>\n\n<h3>グローバルメニューについて</h3>\n\n<p>グローバルメニューは、管理画面の [コンテンツ管理] のツリー構造と連動しています。ツリー構造上においてドラック＆ドロップで並び替えることができ、グローバルメニューに表示したくない場合は、対象コンテンツの編集画面より、[オプション] を開き、[公開ページのメニューより除外する] を選択し保存してください。</p>\n\n<p>また、グローバルメニュー部分を変更する場合は、/Elements/header.php を編集してください。</p>\n\n<h3>サイドバーについて</h3>\n\n<p>サイドバーはウィジェットエリア機能を利用して作られています。管理画面の [設定] &rarr; [ユーティリティ] &rarr; [ウィジェットエリア] より、カスタマイズが可能です。ブログでは「ブログサイドバー」を利用していますが、それ以外のコンテンツでは、「標準サイドバー」を利用しています。</p>\n\n<p>サイト基本設定は、各ブログの [設定] より利用するウィジェットエリアを変更することができます。</p>\n\n<p>また、サイドバー部分を変更する場合は、/Elements/widget_area.php を編集してください。</p>\n\n<h3>初期データについて</h3>\n\n<p>このサンプルテーマは２つの初期データを提供しています。現在利用しているものは「default」でサンプルのデータが入っているものになります。</p>\n\n<p>サンプルデータを入っていないものを利用したい場合は、テーマ管理より「empty」を選択し「初期データ読込」ボタンをクリックしてください。</p>', '', '', '', '2020-10-02 20:33:09', '2015-06-26 20:34:06'),
(2, '<h2>会社案内</h2>\n\n<h3>会社データ</h3>\n\n<table>\n	<tbody>\n		<tr>\n			<th>会社名</th>\n			<td>baserCMSサンプル</td>\n		</tr>\n		<tr>\n			<th>設立</th>\n			<td>2020年9月</td>\n		</tr>\n		<tr>\n			<th>所在地</th>\n			<td>福岡県福岡市</td>\n		</tr>\n		<tr>\n			<th>事業内容</th>\n			<td>\n			<ul>\n				<li>事業内容１</li>\n				<li>事業内容２</li>\n				<li>事業内容３</li>\n			</ul>\n			</td>\n		</tr>\n	</tbody>\n</table>\n\n<h3>アクセスマップ</h3>\n<?php $this->BcBaser->googleMaps([\"width\" => 585]) ?>', '', '', '', '2020-09-14 19:53:48', '2015-06-26 20:34:06'),
(3, '<h2>サービス１</h2>\n\n<p>サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。</p>\n\n<p>サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。</p>\n\n<p>サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。</p>', '', '', '', '2016-08-12 00:58:02', '2015-06-26 20:34:06'),
(5, '<h1>見出し１</h1>\n\n<p>段落の文章が入ります。<span>段落の文章が入ります。段落の文章が入ります。段落の文章が入ります。段落の文章が入ります。段落の文章が入ります。段落の文章が入ります。段落の文章が入ります。段落の文章が入ります。段落の文章が入ります。段落の文章が入ります。</span></p>\n\n<h2>見出し２</h2>\n\n<ul>\n	<li>リスト１</li>\n	<li>リスト２</li>\n	<li>リスト３</li>\n</ul>\n\n<h3>見出し３</h3>\n\n<ol>\n	<li>連番リスト１</li>\n	<li><span>連番リスト２</span></li>\n	<li><span>連番リスト３</span></li>\n</ol>\n\n<h4>見出し４</h4>\n\n<p><strong>太字文章が入ります。太字文章が入ります。太字文章が入ります。太字文章が入ります。</strong></p>\n\n<h5>見出し５</h5>\n\n<p><em>斜体文章が入ります。斜体文章が入ります。斜体文章が入ります。斜体文章が入ります。</em></p>\n\n<h6>見出し６</h6>\n\n<p><u>下線付文章が入ります。下線付文章が入ります。下線付文章が入ります。下線付文章が入ります。</u></p>\n\n<table>\n	<tbody>\n		<tr>\n			<th>表見出し</th>\n			<td><span>表の文章が入ります。</span></td>\n		</tr>\n		<tr>\n			<th>表見出し</th>\n			<td>表の文章が入ります。</td>\n		</tr>\n		<tr>\n			<th>表見出し</th>\n			<td><span>表の文章が入ります。</span></td>\n		</tr>\n	</tbody>\n</table>\n\n<p>&nbsp;</p>\n\n<p>&nbsp;</p>', '', '', '', '2020-09-14 22:30:21', '2015-06-27 12:40:09'),
(6, '<h2>サービス２</h2>\n\n<p>サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。</p>\n\n<p>サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。</p>\n\n<p>サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。</p>', '', '', '', '2016-08-12 00:58:58', '2015-06-27 15:36:22'),
(7, '<h2>サービス３</h2>\n\n<p>サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。</p>\n\n<p>サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。</p>\n\n<p>サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。</p>', '', '', '', '2016-08-12 00:59:06', '2015-06-27 15:36:53'),
(8, '<p>aaaaaaaaaaaaa</p>\r\n', '', '', '', '2020-12-14 14:29:33', '2020-12-14 14:24:35'),
(9, NULL, NULL, NULL, NULL, '2020-12-14 14:48:53', '2020-12-14 14:48:53');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_permissions`
--

CREATE TABLE `mysite_permissions` (
  `id` int(8) NOT NULL,
  `no` int(8) DEFAULT NULL,
  `sort` int(8) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `user_group_id` int(8) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `auth` tinyint(1) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_permissions`
--

INSERT INTO `mysite_permissions` (`id`, `no`, `sort`, `name`, `user_group_id`, `url`, `auth`, `status`, `modified`, `created`) VALUES
(1, 1, 1, 'システム管理', 2, '/admin/*', 0, 1, NULL, '2015-09-30 01:21:40'),
(2, 2, 2, 'よく使う項目', 2, '/admin/favorites/*', 1, 1, NULL, '2015-09-30 01:21:40'),
(3, 3, 3, 'ページ管理', 2, '/admin/pages/*', 1, 1, NULL, '2015-09-30 01:21:40'),
(4, 4, 4, 'ページテンプレート読込・書出', 2, '/admin/pages/*_page_files', 0, 1, NULL, '2015-09-30 01:21:40'),
(7, 7, 7, '新着情報記事管理', 2, '/admin/blog/blog_posts/*', 1, 1, '2016-08-16 19:29:56', '2015-09-30 01:21:40'),
(9, 9, 9, '新着情報カテゴリ管理', 2, '/admin/blog/blog_categories/*', 1, 1, '2016-08-16 19:30:12', '2015-09-30 01:21:40'),
(10, 10, 10, '新着情報コメント一覧', 2, '/admin/blog/blog_comments/*', 1, 1, '2016-08-16 19:30:19', '2015-09-30 01:21:40'),
(11, 11, 11, 'ブログタグ管理', 2, '/admin/blog/blog_tags/*', 1, 1, NULL, '2015-09-30 01:21:40'),
(13, 13, 13, 'お問い合わせ管理', 2, '/admin/mail/mail_fields/*', 1, 1, '2016-08-16 19:30:34', '2015-09-30 01:21:40'),
(14, 14, 14, 'お問い合わせ受信メール一覧', 2, '/admin/mail/mail_messages/*', 1, 1, '2016-08-16 19:29:11', '2015-09-30 01:21:40'),
(15, 15, 15, 'エディタテンプレート呼出', 2, '/admin/editor_templates/js', 1, 1, NULL, '2015-09-30 01:21:40'),
(16, 16, 16, 'アップローダー', 2, '/admin/uploader/*', 1, 1, NULL, '2015-09-30 01:21:40'),
(17, 17, 17, 'コンテンツ管理', 2, '/admin/contents/*', 1, 1, '2016-08-16 19:28:39', '2016-08-16 19:28:39'),
(18, 18, 18, 'リンク管理', 2, '/admin/content_links/*', 1, 1, '2016-08-16 19:28:56', '2016-08-16 19:28:56'),
(19, 19, 19, 'カスタムフィールドプラグイン 管理', 2, '/admin/cu_custom_field/*', 1, 1, '2020-12-04 12:38:26', '2020-12-04 12:38:26'),
(20, 20, 20, '静的HTML出力プラグイン 管理', 2, '/admin/cu_static/*', 1, 1, '2020-12-04 12:55:05', '2020-12-04 12:55:05');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_plugins`
--

CREATE TABLE `mysite_plugins` (
  `id` int(8) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `version` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `db_inited` tinyint(1) DEFAULT NULL,
  `priority` int(8) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_plugins`
--

INSERT INTO `mysite_plugins` (`id`, `name`, `title`, `version`, `status`, `db_inited`, `priority`, `created`, `modified`) VALUES
(1, 'Blog', 'ブログ', '4.4.3-dev', 1, 1, 1, '2020-12-03 14:41:41', '2020-12-03 14:41:46'),
(2, 'Feed', 'フィードリーダー', '4.4.3-dev', 1, 1, 2, '2020-12-03 14:41:41', '2020-12-03 14:41:46'),
(3, 'Mail', 'メールフォーム', '4.4.3-dev', 1, 1, 3, '2020-12-03 14:41:41', '2020-12-03 14:41:51'),
(4, 'Uploader', 'アップローダー', '4.4.3-dev', 1, 1, 4, '2020-12-03 14:41:41', '2020-12-03 14:41:52'),
(5, 'CuCustomField', 'カスタムフィールドプラグイン', '4.0.0', 1, 1, 5, '2020-12-04 12:38:25', '2020-12-05 08:08:36'),
(6, 'CuStatic', '静的HTML出力プラグイン', '1.0.0', 0, 1, 6, '2020-12-04 12:55:05', '2020-12-05 08:08:29');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_search_indices`
--

CREATE TABLE `mysite_search_indices` (
  `id` int(8) NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `model_id` int(8) DEFAULT NULL,
  `site_id` int(8) DEFAULT NULL,
  `content_id` int(8) DEFAULT NULL,
  `content_filter_id` int(8) DEFAULT NULL,
  `lft` int(8) DEFAULT NULL,
  `rght` int(8) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `detail` text,
  `url` text,
  `status` tinyint(1) DEFAULT NULL,
  `priority` varchar(3) DEFAULT NULL,
  `publish_begin` datetime DEFAULT NULL,
  `publish_end` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_search_indices`
--

INSERT INTO `mysite_search_indices` (`id`, `type`, `model`, `model_id`, `site_id`, `content_id`, `content_filter_id`, `lft`, `rght`, `title`, `detail`, `url`, `status`, `priority`, `publish_begin`, `publish_end`, `created`, `modified`) VALUES
(1, 'ページ', 'Page', 1, 0, 4, NULL, 26, 27, 'トップページ', 'baserCMSサンプルテーマこれは、baserCMSのサンプルテーマです。トップページのこの部分は、固定ページで作られており、NEWS と baserCMS最新情報の部分は、テンプレートで作成されています。変更する場合は、/Elements/top_info.php を変更してください。グローバルメニューについてグローバルメニューは、管理画面の [コンテンツ管理] のツリー構造と連動しています。ツリー構造上においてドラック＆ドロップで並び替えることができ、グローバルメニューに表示したくない場合は、対象コンテンツの編集画面より、[オプション] を開き、[公開ページのメニューより除外する] を選択し保存してください。また、グローバルメニュー部分を変更する場合は、/Elements/header.php を編集してください。サイドバーについてサイドバーはウィジェットエリア機能を利用して作られています。管理画面の [設定] &rarr; [ユーティリティ] &rarr; [ウィジェットエリア] より、カスタマイズが可能です。ブログでは「ブログサイドバー」を利用していますが、それ以外のコンテンツでは、「標準サイドバー」を利用しています。サイト基本設定は、各ブログの [設定] より利用するウィジェットエリアを変更することができます。また、サイドバー部分を変更する場合は、/Elements/widget_area.php を編集してください。', '/index', 1, '0.5', NULL, NULL, '2016-10-06 01:20:12', '2020-09-14 20:13:25'),
(2, 'ブログ', 'BlogContent', 1, 0, 10, NULL, 28, 29, 'NEWS', 'このコンテンツはブログ機能により作られており、この文章については管理画面の [NEWS] &rarr; [設定] より更新ができます。また、ブログは [コンテンツ管理] よりいくつでも作成することができます。', '/news/', 1, '0.5', NULL, NULL, '2016-10-06 01:20:25', '2020-09-14 19:27:58'),
(4, 'ブログ', 'BlogPost', 2, 0, 10, 1, 4, 5, 'ブログ機能について説明します', 'この文章はブログ記事の [概要] 欄に入力されています。ブログ記事の一覧にて概要だけを表示する場合に利用しますが、テーマの構成上で利用しない場合は、各ブログの [設定] より、 [概要] 欄を利用しないようにする事もできます。ちなみにこのサンプルテーマではブログ記事一覧において概要を利用していません。 ここからは、ブログ記事の [本文] 欄に入力されている文章となります。カテゴリ・タグ機能baserCMSでのカテゴリとタグは少し仕様が違います。一つの記事は複数のタグを付けることができますが、複数のカテゴリに属すことはできません。また、タグは全ブログ共通ですが、カテゴリは各ブログごとに分けて作ることができます。なお、タグやカテゴリを利用するにはテーマ側が対応している必要があります。このサンプルテーマでは、タグの利用を想定していません。ブログコメント機能ブログの各記事には一般ユーザーがコメントを付ける機能がありますが、利用しない場合は、各ブログの [設定] 画面より簡単に非表示にすることができます。', '/news/archives/2', 1, '0.5', NULL, NULL, '2016-10-06 01:20:25', '2020-12-05 10:15:43'),
(5, 'ページ', 'Page', 3, 0, 11, NULL, 31, 32, 'サービス１', 'サービス１サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。', '/service/service1', 1, '0.5', NULL, NULL, '2016-10-06 01:30:46', '2016-10-06 01:30:46'),
(6, 'ページ', 'Page', 6, 0, 12, NULL, 33, 34, 'サービス２', 'サービス２サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。', '/service/service2', 1, '0.5', NULL, NULL, '2016-10-06 01:30:57', '2016-10-06 01:30:57'),
(7, 'ページ', 'Page', 7, 0, 13, NULL, 35, 36, 'サービス３', 'サービス３サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。 サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。', '/service/service3', 1, '0.5', NULL, NULL, '2016-10-06 01:31:05', '2016-10-06 01:31:05'),
(8, 'ページ', 'Page', 5, 0, 7, NULL, 18, 19, 'サンプル', '見出し１段落の文章が入ります。段落の文章が入ります。段落の文章が入ります。段落の文章が入ります。段落の文章が入ります。段落の文章が入ります。段落の文章が入ります。段落の文章が入ります。段落の文章が入ります。段落の文章が入ります。段落の文章が入ります。見出し２リスト１リスト２リスト３見出し３連番リスト１連番リスト２連番リスト３見出し４太字文章が入ります。太字文章が入ります。太字文章が入ります。太字文章が入ります。見出し５斜体文章が入ります。斜体文章が入ります。斜体文章が入ります。斜体文章が入ります。見出し６下線付文章が入ります。下線付文章が入ります。下線付文章が入ります。下線付文章が入ります。表見出し表の文章が入ります。表見出し表の文章が入ります。表見出し表の文章が入ります。&nbsp;&nbsp;', '/sample', 1, '1.0', NULL, NULL, '2016-10-06 01:31:22', '2020-12-14 14:41:14'),
(9, 'ページ', 'Page', 2, 0, 5, NULL, 30, 31, '会社案内', '会社案内会社データ会社名baserCMSサンプル設立2020年9月所在地福岡県福岡市事業内容事業内容１事業内容２事業内容３アクセスマップGoogleマップを利用するには、Google Maps APIのキーの登録が必要です。キーを取得して、システム管理より設定してください。※ JavaScript を有効にしてください。var geo = new google.maps.Geocoder();var lat = \'\';var lng = \'\';if(!lat || !lng) {geo.geocode({ address: \'福岡県\' }, function(results, status) {if(status === \'OK\') {lat = results[0].geometry.location.lat();lng = results[0].geometry.location.lng();loadMap(lat, lng);}});} else {loadMap(lat, lng)}function loadMap(lat, lng){var latlng = new google.maps.LatLng(lat,lng);var options = {zoom: 16,center: latlng,mapTypeId: google.maps.MapTypeId.ROADMAP,navigationControl: true,mapTypeControl: true,scaleControl: true,scrollwheel: false,};var map = new google.maps.Map(document.getElementById(\"map\"), options);var marker = new google.maps.Marker({position: latlng,map: map,title:\"baserCMSサンプル\"});if(\'baserCMSサンプル福岡県\') {var infowindow = new google.maps.InfoWindow({content: \'baserCMSサンプル福岡県\'});infowindow.open(map,marker);google.maps.event.addListener(marker, \'click\', function() {infowindow.open(map,marker);});}}', '/about', 1, '0.5', NULL, NULL, '2016-10-06 01:31:31', '2020-09-14 19:53:48'),
(10, 'メール', 'MailContent', 1, 0, 9, NULL, 42, 43, 'お問い合わせ', 'このコンテンツはメールフォーム機能により作られており、この文章については管理画面の [お問い合わせ] &rarr; [設定] より更新ができます。また、メールフォームは [コンテンツ管理] よりいくつでも作成することができます。', '/contact/', 1, '0.5', NULL, NULL, '2016-10-06 01:31:39', '2020-09-14 19:37:12'),
(11, 'ページ', 'Page', 8, 0, 15, NULL, 6, 7, 'aaa', 'aaaaaaaaaaaaa', '/aaa', 1, '0.5', NULL, NULL, '2020-12-14 14:24:36', '2020-12-14 14:29:34'),
(12, 'ページ', 'Page', 9, 1, 19, NULL, 29, 30, 'index', '', '/en/index', 1, '0.5', NULL, NULL, '2020-12-14 14:48:53', '2020-12-14 14:48:57');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_sites`
--

CREATE TABLE `mysite_sites` (
  `id` int(8) NOT NULL,
  `main_site_id` int(8) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `display_name` varchar(50) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `alias` varchar(50) DEFAULT NULL,
  `theme` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `keyword` text,
  `description` text,
  `use_subdomain` tinyint(1) DEFAULT '0',
  `relate_main_site` tinyint(1) DEFAULT NULL,
  `device` varchar(50) DEFAULT NULL,
  `lang` varchar(50) DEFAULT NULL,
  `same_main_url` tinyint(1) DEFAULT '0',
  `auto_redirect` tinyint(1) DEFAULT '0',
  `auto_link` tinyint(1) DEFAULT '0',
  `domain_type` int(8) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_sites`
--

INSERT INTO `mysite_sites` (`id`, `main_site_id`, `name`, `display_name`, `title`, `alias`, `theme`, `status`, `keyword`, `description`, `use_subdomain`, `relate_main_site`, `device`, `lang`, `same_main_url`, `auto_redirect`, `auto_link`, `domain_type`, `created`, `modified`) VALUES
(1, 0, 'en', '英語サイト', '英語サイト', 'en', '', 1, '', '', 0, 0, NULL, NULL, 0, 0, 0, 0, '2020-12-14 14:48:33', '2020-12-14 14:48:33');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_site_configs`
--

CREATE TABLE `mysite_site_configs` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `value` text,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_site_configs`
--

INSERT INTO `mysite_site_configs` (`id`, `name`, `value`, `created`, `modified`) VALUES
(1, 'name', 'baserCMSサンプル', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(2, 'keyword', 'baser,CMS,コンテンツマネジメントシステム,開発支援', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(3, 'description', 'baserCMS（ベーサーシーエムエス）とは、直感的な操作と高いメンテナンス性を実現し、Webサイトを自由にカスタマイズできる国産CMS（コンテンツ・マネージメント・システム）です。日本人が日本人の為に、みんなで作っているオープンソース・ソフトウェアです。無料で利用でき、様々なサーバーで動作可能で、インストールも簡単です。', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(4, 'address', '福岡県', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(5, 'theme', 'bc_sample', '2019-06-11 12:24:32', '2020-12-14 14:48:53'),
(6, 'email', 'egashira@catchup.co.jp', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(7, 'widget_area', '1', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(8, 'maintenance', '0', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(9, 'mail_encode', 'UTF-8', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(10, 'smtp_host', '', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(11, 'smtp_user', 'cmsadmin', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(12, 'smtp_password', 'demodemo', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(13, 'smtp_port', '', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(14, 'formal_name', 'baserCMSサンプル', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(15, 'admin_list_num', '10', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(16, 'google_analytics_id', '', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(17, 'content_types', 'YTozOntzOjk6IuODluODreOCsCI7czo5OiLjg5bjg63jgrAiO3M6OToi44Oa44O844K4IjtzOjk6IuODmuODvOOCuCI7czo5OiLjg6Hjg7zjg6siO3M6OToi44Oh44O844OrIjt9', '2019-06-11 12:24:32', '2020-12-14 14:48:57'),
(18, 'category_permission', '', '2019-06-11 12:24:32', '2020-12-14 14:48:53'),
(19, 'admin_theme', 'admin-third', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(20, 'login_credit', '0', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(21, 'first_access', '', '2019-06-11 12:24:32', '2020-12-14 14:48:53'),
(22, 'editor', 'BcCkeditor', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(23, 'editor_styles', '#青見出し\r\nh3 {\r\ncolor:Blue;\r\n}\r\n#赤見出し\r\nh3 {\r\ncolor:Red;\r\n}\r\n#黄マーカー\r\nspan {\r\nbackground-color:Yellow;\r\n}\r\n#緑マーカー\r\nspan {\r\nbackground-color:Lime;\r\n}\r\n#大文字\r\nbig {}\r\n#小文字\r\nsmall {}\r\n#コード\r\ncode {}\r\n#削除文\r\ndel {}\r\n#挿入文\r\nins {}\r\n#引用\r\ncite {}\r\n#インライン\r\nq {}', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(24, 'editor_enter_br', '0', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(25, 'admin_side_banner', '0', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(26, 'smtp_tls', '0', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(27, 'main_site_display_name', 'メインサイト', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(28, 'use_site_device_setting', '0', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(29, 'use_site_lang_setting', '0', '2019-06-11 12:24:32', '2020-12-14 14:51:08'),
(30, 'google_maps_api_key', '', '2019-06-11 12:27:01', '2020-12-14 14:51:08'),
(31, 'use_universal_analytics', '', '2019-06-11 12:27:01', '2020-12-14 14:51:08'),
(33, 'contents_sort_last_modified', '2020-12-14 14:48:53|1', '2020-09-14 19:22:38', '2020-12-14 14:48:53'),
(34, 'mail_additional_parameters', '', '2020-09-14 19:35:06', '2020-12-14 14:51:08'),
(35, 'version', '4.4.3-dev', '2020-12-03 14:41:54', '2020-12-14 14:48:53');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_theme_configs`
--

CREATE TABLE `mysite_theme_configs` (
  `id` int(8) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `value` text,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_theme_configs`
--

INSERT INTO `mysite_theme_configs` (`id`, `name`, `value`, `created`, `modified`) VALUES
(1, 'logo', '', '2015-06-26 20:34:06', NULL),
(2, 'logo_alt', 'baserCMS', '2015-06-26 20:34:06', '2020-12-13 11:54:15'),
(3, 'logo_link', '/', '2015-06-26 20:34:06', '2020-12-13 11:54:15'),
(4, 'main_image_1', '', '2015-06-26 20:34:07', NULL),
(5, 'main_image_alt_1', 'コーポレートサイトにちょうどいい国産CMS', '2015-06-26 20:34:07', '2020-12-13 11:54:15'),
(6, 'main_image_link_1', '/', '2015-06-26 20:34:07', '2020-12-13 11:54:15'),
(7, 'main_image_2', '', '2015-06-26 20:34:07', NULL),
(8, 'main_image_alt_2', '全て日本語の国産CMSだから、設置も更新も簡単、わかりやすい。', '2015-06-26 20:34:07', '2020-12-13 11:54:15'),
(9, 'main_image_link_2', '/', '2015-06-26 20:34:07', '2020-12-13 11:54:15'),
(10, 'main_image_3', '', '2015-06-26 20:34:07', NULL),
(11, 'main_image_alt_3', '標準的なWebサイトに必要な基本機能を全て装備', '2015-06-26 20:34:07', '2020-12-13 11:54:15'),
(12, 'main_image_link_3', '/', '2015-06-26 20:34:07', '2020-12-13 11:54:15'),
(13, 'main_image_4', '', '2015-06-26 20:34:07', NULL),
(14, 'main_image_alt_4', 'デザインも自由自在にカスタマイズ可能！', '2015-06-26 20:34:07', '2020-12-13 11:54:15'),
(15, 'main_image_link_4', '/', '2015-06-26 20:34:07', '2020-12-13 11:54:15'),
(16, 'main_image_5', '', '2015-06-26 20:34:07', NULL),
(17, 'main_image_alt_5', '質問・ご相談はユーザーズフォーラムへ', '2015-06-26 20:34:07', '2020-12-13 11:54:15'),
(18, 'main_image_link_5', '/', '2015-06-26 20:34:07', '2020-12-13 11:54:15'),
(19, 'color_main', '2c3adb', '2015-06-26 20:34:07', '2020-12-13 11:54:15'),
(20, 'color_sub', '001800', '2015-06-26 20:34:07', '2020-12-13 11:54:15'),
(21, 'color_link', '2B7BB9', '2015-06-26 20:34:07', '2020-12-13 11:54:15'),
(22, 'color_hover', '2B7BB9', '2015-06-26 20:34:07', '2020-12-13 11:54:15');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_uploader_categories`
--

CREATE TABLE `mysite_uploader_categories` (
  `id` int(8) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_uploader_configs`
--

CREATE TABLE `mysite_uploader_configs` (
  `id` int(8) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_uploader_configs`
--

INSERT INTO `mysite_uploader_configs` (`id`, `name`, `value`, `created`, `modified`) VALUES
(1, 'large_width', '500', '2016-08-12 00:48:35', NULL),
(2, 'large_height', '500', '2016-08-12 00:48:35', NULL),
(3, 'midium_width', '300', '2016-08-12 00:48:35', NULL),
(4, 'midium_height', '300', '2016-08-12 00:48:35', NULL),
(5, 'small_width', '150', '2016-08-12 00:48:35', NULL),
(6, 'small_height', '150', '2016-08-12 00:48:35', NULL),
(7, 'small_thumb', '1', '2016-08-12 00:48:35', NULL),
(8, 'mobile_large_width', '240', '2016-08-12 00:48:35', NULL),
(9, 'mobile_large_height', '240', '2016-08-12 00:48:35', NULL),
(10, 'mobile_small_width', '100', '2016-08-12 00:48:35', NULL),
(11, 'mobile_small_height', '100', '2016-08-12 00:48:35', NULL),
(12, 'mobile_small_thumb', '1', '2016-08-12 00:48:35', NULL),
(13, 'use_permission', '0', '2016-08-12 00:48:35', NULL),
(14, 'layout_type', 'panel', '2016-08-12 00:48:35', NULL);

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_uploader_files`
--

CREATE TABLE `mysite_uploader_files` (
  `id` int(8) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `alt` text,
  `uploader_category_id` int(8) DEFAULT NULL,
  `user_id` int(8) DEFAULT NULL,
  `publish_begin` datetime DEFAULT NULL,
  `publish_end` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_users`
--

CREATE TABLE `mysite_users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `real_name_1` varchar(50) DEFAULT NULL,
  `real_name_2` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user_group_id` int(4) DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_users`
--

INSERT INTO `mysite_users` (`id`, `name`, `password`, `real_name_1`, `real_name_2`, `email`, `user_group_id`, `nickname`, `created`, `modified`) VALUES
(1, 'admin', 'a6af980fef22d99166be95f1dcefc2760ed9bd67', 'admin', NULL, 'egashira@catchup.co.jp', 1, NULL, '2020-12-03 14:41:39', '2020-12-03 14:41:39'),
(2, 'bbb', 'bc433e3fbd3c79e03422733c6e86ca8d40b638f8', 'bbb', '', '', 2, '', '2020-12-14 14:37:25', '2020-12-14 14:37:25');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_user_groups`
--

CREATE TABLE `mysite_user_groups` (
  `id` int(8) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `auth_prefix` varchar(20) DEFAULT NULL,
  `use_admin_globalmenu` tinyint(1) DEFAULT NULL,
  `default_favorites` text,
  `use_move_contents` tinyint(1) DEFAULT '0',
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_user_groups`
--

INSERT INTO `mysite_user_groups` (`id`, `name`, `title`, `auth_prefix`, `use_admin_globalmenu`, `default_favorites`, `use_move_contents`, `modified`, `created`) VALUES
(1, 'admins', 'システム管理', 'admin', 1, 'YToxOntpOjA7YToyOntzOjQ6Im5hbWUiO3M6MTU6IuOCr+ODrOOCuOODg+ODiCI7czozOiJ1cmwiO3M6MjA6ImphdmFzY3JpcHQ6Y3JlZGl0KCk7Ijt9fQ==', 1, '2019-06-11 12:28:04', '2015-06-26 20:34:07'),
(2, 'operators', 'サイト運営', 'admin', 0, '', 0, NULL, '2015-06-26 20:34:07');

-- --------------------------------------------------------

--
-- テーブルの構造 `mysite_widget_areas`
--

CREATE TABLE `mysite_widget_areas` (
  `id` int(4) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `widgets` text,
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mysite_widget_areas`
--

INSERT INTO `mysite_widget_areas` (`id`, `name`, `widgets`, `modified`, `created`) VALUES
(1, '標準サイドバー', 'YTo0OntpOjA7YToxOntzOjc6IldpZGdldDEiO2E6OTp7czoyOiJpZCI7czoxOiIxIjtzOjQ6InR5cGUiO3M6MTI6IuODhuOCreOCueODiCI7czo3OiJlbGVtZW50IjtzOjQ6InRleHQiO3M6NjoicGx1Z2luIjtzOjA6IiI7czo0OiJzb3J0IjtpOjQ7czo0OiJuYW1lIjtzOjk6IuODquODs+OCryI7czo0OiJ0ZXh0IjtzOjQ0MDoiPHAgc3R5bGU9Im1hcmdpbi1ib3R0b206MjBweDt0ZXh0LWFsaWduOiBjZW50ZXIiPiA8YSBocmVmPSJodHRwOi8vYmFzZXJjbXMubmV0IiB0YXJnZXQ9Il9ibGFuayI+PGltZyBzcmM9Imh0dHA6Ly9iYXNlcmNtcy5uZXQvaW1nL2Jucl9iYXNlcmNtcy5qcGciIGFsdD0i44Kz44O844Od44Os44O844OI44K144Kk44OI44Gr44Gh44KH44GG44Gp44GE44GEQ01T44CBYmFzZXJDTVMiLz48L2E+PC9wPjxwIGNsYXNzPSJjdXN0b21pemUtbmF2aSBjb3JuZXIxMCI+PHNtYWxsPuOBk+OBrumDqOWIhuOBr+OAgeeuoeeQhueUu+mdouOBriBb6Kit5a6aXSDihpIgW+ODpuODvOODhuOCo+ODquODhuOCo10g4oaSIFvjgqbjgqPjgrjjgqfjg4Pjg4jjgqjjg6rjgqJdIOKGkiBb5qiZ5rqW44K144Kk44OJ44OQ44O8XSDjgojjgornt6jpm4bjgafjgY3jgb7jgZnjgII8L3NtYWxsPjwvcD4iO3M6OToidXNlX3RpdGxlIjtzOjE6IjEiO3M6Njoic3RhdHVzIjtzOjE6IjEiO319aToxO2E6MTp7czo3OiJXaWRnZXQyIjthOjg6e3M6MjoiaWQiO3M6MToiMiI7czo0OiJ0eXBlIjtzOjE4OiLjgrXjgqTjg4jlhoXmpJzntKIiO3M6NzoiZWxlbWVudCI7czo2OiJzZWFyY2giO3M6NjoicGx1Z2luIjtzOjA6IiI7czo0OiJzb3J0IjtpOjM7czo0OiJuYW1lIjtzOjE4OiLjgrXjgqTjg4jlhoXmpJzntKIiO3M6OToidXNlX3RpdGxlIjtzOjE6IjEiO3M6Njoic3RhdHVzIjtzOjE6IjEiO319aToyO2E6MTp7czo3OiJXaWRnZXQzIjthOjk6e3M6MjoiaWQiO3M6MToiMyI7czo0OiJ0eXBlIjtzOjMzOiLjg63jg7zjgqvjg6vjg4rjg5PjgrLjg7zjgrfjg6fjg7MiO3M6NzoiZWxlbWVudCI7czoxMDoibG9jYWxfbmF2aSI7czo2OiJwbHVnaW4iO3M6MDoiIjtzOjQ6InNvcnQiO2k6MjtzOjQ6Im5hbWUiO3M6MzQ6IuODreODvOOCq+ODq+ODiuODk+OCsuODvOOCt+ODp+ODszEiO3M6NToiY2FjaGUiO3M6MToiMSI7czo5OiJ1c2VfdGl0bGUiO3M6MToiMSI7czo2OiJzdGF0dXMiO3M6MToiMSI7fX1pOjM7YToxOntzOjc6IldpZGdldDQiO2E6OTp7czoyOiJpZCI7czoxOiI0IjtzOjQ6InR5cGUiO3M6MTI6IuODhuOCreOCueODiCI7czo3OiJlbGVtZW50IjtzOjQ6InRleHQiO3M6NjoicGx1Z2luIjtzOjA6IiI7czo0OiJzb3J0IjtpOjE7czo0OiJuYW1lIjtzOjEzOiLjg4bjgq3jgrnjg4gyIjtzOjQ6InRleHQiO3M6MjQ6IuOBguOBguOBguOBguOBguOBguOBguOBgiI7czo5OiJ1c2VfdGl0bGUiO3M6MToiMSI7czo2OiJzdGF0dXMiO3M6MToiMSI7fX19', '2020-12-14 14:34:10', '2015-06-26 20:34:07'),
(2, 'ブログサイドバー', 'YTo2OntpOjA7YToxOntzOjc6IldpZGdldDEiO2E6OTp7czoyOiJpZCI7czoxOiIxIjtzOjQ6InR5cGUiO3M6MjQ6IuODluODreOCsOOCq+ODrOODs+ODgOODvCI7czo3OiJlbGVtZW50IjtzOjEzOiJibG9nX2NhbGVuZGFyIjtzOjY6InBsdWdpbiI7czo0OiJCbG9nIjtzOjQ6InNvcnQiO2k6MTtzOjQ6Im5hbWUiO3M6MjQ6IuODluODreOCsOOCq+ODrOODs+ODgOODvCI7czoxNToiYmxvZ19jb250ZW50X2lkIjtzOjE6IjEiO3M6OToidXNlX3RpdGxlIjtzOjE6IjAiO3M6Njoic3RhdHVzIjtzOjE6IjEiO319aToxO2E6MTp7czo3OiJXaWRnZXQyIjthOjEwOntzOjI6ImlkIjtzOjE6IjIiO3M6NDoidHlwZSI7czozMDoi44OW44Ot44Kw44Kr44OG44K044Oq44O85LiA6KanIjtzOjc6ImVsZW1lbnQiO3M6MjI6ImJsb2dfY2F0ZWdvcnlfYXJjaGl2ZXMiO3M6NjoicGx1Z2luIjtzOjQ6IkJsb2ciO3M6NDoic29ydCI7aToyO3M6NDoibmFtZSI7czoyMToi44Kr44OG44K044Oq44O85LiA6KanIjtzOjU6ImNvdW50IjtzOjE6IjEiO3M6MTU6ImJsb2dfY29udGVudF9pZCI7czoxOiIxIjtzOjk6InVzZV90aXRsZSI7czoxOiIxIjtzOjY6InN0YXR1cyI7czoxOiIxIjt9fWk6MjthOjE6e3M6NzoiV2lkZ2V0MyI7YToxMTp7czoyOiJpZCI7czoxOiIzIjtzOjQ6InR5cGUiO3M6Mjc6IuaciOWIpeOCouODvOOCq+OCpOODluS4gOimpyI7czo3OiJlbGVtZW50IjtzOjIxOiJibG9nX21vbnRobHlfYXJjaGl2ZXMiO3M6NjoicGx1Z2luIjtzOjQ6IkJsb2ciO3M6NDoic29ydCI7aTo1O3M6NDoibmFtZSI7czoyNzoi5pyI5Yil44Ki44O844Kr44Kk44OW5LiA6KanIjtzOjU6ImNvdW50IjtzOjI6IjEyIjtzOjEwOiJ2aWV3X2NvdW50IjtzOjE6IjEiO3M6MTU6ImJsb2dfY29udGVudF9pZCI7czoxOiIxIjtzOjk6InVzZV90aXRsZSI7czoxOiIxIjtzOjY6InN0YXR1cyI7czoxOiIxIjt9fWk6MzthOjE6e3M6NzoiV2lkZ2V0NCI7YToxMDp7czoyOiJpZCI7czoxOiI0IjtzOjQ6InR5cGUiO3M6MTU6IuacgOi/keOBruaKleeovyI7czo3OiJlbGVtZW50IjtzOjE5OiJibG9nX3JlY2VudF9lbnRyaWVzIjtzOjY6InBsdWdpbiI7czo0OiJCbG9nIjtzOjQ6InNvcnQiO2k6MztzOjQ6Im5hbWUiO3M6MTU6IuacgOi/keOBruaKleeovyI7czo1OiJjb3VudCI7czoxOiI1IjtzOjE1OiJibG9nX2NvbnRlbnRfaWQiO3M6MToiMSI7czo5OiJ1c2VfdGl0bGUiO3M6MToiMSI7czo2OiJzdGF0dXMiO3M6MToiMSI7fX1pOjQ7YToxOntzOjc6IldpZGdldDUiO2E6MTA6e3M6MjoiaWQiO3M6MToiNSI7czo0OiJ0eXBlIjtzOjI0OiLjg5bjg63jgrDmipXnqL/ogIXkuIDopqciO3M6NzoiZWxlbWVudCI7czoyMDoiYmxvZ19hdXRob3JfYXJjaGl2ZXMiO3M6NjoicGx1Z2luIjtzOjQ6IkJsb2ciO3M6NDoic29ydCI7aTo0O3M6NDoibmFtZSI7czoyNDoi44OW44Ot44Kw5oqV56i/6ICF5LiA6KanIjtzOjEwOiJ2aWV3X2NvdW50IjtzOjE6IjAiO3M6MTU6ImJsb2dfY29udGVudF9pZCI7czoxOiIxIjtzOjk6InVzZV90aXRsZSI7czoxOiIxIjtzOjY6InN0YXR1cyI7czoxOiIxIjt9fWk6NTthOjE6e3M6NzoiV2lkZ2V0NiI7YToxMTp7czoyOiJpZCI7czoxOiI2IjtzOjQ6InR5cGUiO3M6Mjc6IuW5tOWIpeOCouODvOOCq+OCpOODluS4gOimpyI7czo3OiJlbGVtZW50IjtzOjIwOiJibG9nX3llYXJseV9hcmNoaXZlcyI7czo2OiJwbHVnaW4iO3M6NDoiQmxvZyI7czo0OiJzb3J0IjtpOjY7czo0OiJuYW1lIjtzOjI3OiLlubTliKXjgqLjg7zjgqvjgqTjg5bkuIDopqciO3M6NToibGltaXQiO3M6MDoiIjtzOjEwOiJ2aWV3X2NvdW50IjtzOjE6IjAiO3M6MTU6ImJsb2dfY29udGVudF9pZCI7czoxOiIxIjtzOjk6InVzZV90aXRsZSI7czoxOiIxIjtzOjY6InN0YXR1cyI7czoxOiIxIjt9fX0=', '2020-09-14 20:16:49', '2015-06-26 20:34:07');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `mysite_blog_categories`
--
ALTER TABLE `mysite_blog_categories`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_blog_comments`
--
ALTER TABLE `mysite_blog_comments`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_blog_configs`
--
ALTER TABLE `mysite_blog_configs`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_blog_contents`
--
ALTER TABLE `mysite_blog_contents`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_blog_posts`
--
ALTER TABLE `mysite_blog_posts`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_blog_posts_blog_tags`
--
ALTER TABLE `mysite_blog_posts_blog_tags`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_blog_tags`
--
ALTER TABLE `mysite_blog_tags`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_contents`
--
ALTER TABLE `mysite_contents`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_content_folders`
--
ALTER TABLE `mysite_content_folders`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_content_links`
--
ALTER TABLE `mysite_content_links`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_dblogs`
--
ALTER TABLE `mysite_dblogs`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_editor_templates`
--
ALTER TABLE `mysite_editor_templates`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_favorites`
--
ALTER TABLE `mysite_favorites`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_feed_configs`
--
ALTER TABLE `mysite_feed_configs`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_feed_details`
--
ALTER TABLE `mysite_feed_details`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_mail_configs`
--
ALTER TABLE `mysite_mail_configs`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_mail_contents`
--
ALTER TABLE `mysite_mail_contents`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_mail_fields`
--
ALTER TABLE `mysite_mail_fields`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_mail_messages`
--
ALTER TABLE `mysite_mail_messages`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_mail_message_1`
--
ALTER TABLE `mysite_mail_message_1`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_mail_message_2`
--
ALTER TABLE `mysite_mail_message_2`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_pages`
--
ALTER TABLE `mysite_pages`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_permissions`
--
ALTER TABLE `mysite_permissions`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_plugins`
--
ALTER TABLE `mysite_plugins`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_search_indices`
--
ALTER TABLE `mysite_search_indices`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_sites`
--
ALTER TABLE `mysite_sites`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_site_configs`
--
ALTER TABLE `mysite_site_configs`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_theme_configs`
--
ALTER TABLE `mysite_theme_configs`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_uploader_categories`
--
ALTER TABLE `mysite_uploader_categories`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_uploader_configs`
--
ALTER TABLE `mysite_uploader_configs`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_uploader_files`
--
ALTER TABLE `mysite_uploader_files`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_users`
--
ALTER TABLE `mysite_users`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_user_groups`
--
ALTER TABLE `mysite_user_groups`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mysite_widget_areas`
--
ALTER TABLE `mysite_widget_areas`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルのAUTO_INCREMENT
--

--
-- テーブルのAUTO_INCREMENT `mysite_blog_categories`
--
ALTER TABLE `mysite_blog_categories`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- テーブルのAUTO_INCREMENT `mysite_blog_comments`
--
ALTER TABLE `mysite_blog_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `mysite_blog_configs`
--
ALTER TABLE `mysite_blog_configs`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `mysite_blog_contents`
--
ALTER TABLE `mysite_blog_contents`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- テーブルのAUTO_INCREMENT `mysite_blog_posts`
--
ALTER TABLE `mysite_blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- テーブルのAUTO_INCREMENT `mysite_blog_posts_blog_tags`
--
ALTER TABLE `mysite_blog_posts_blog_tags`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- テーブルのAUTO_INCREMENT `mysite_blog_tags`
--
ALTER TABLE `mysite_blog_tags`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- テーブルのAUTO_INCREMENT `mysite_contents`
--
ALTER TABLE `mysite_contents`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- テーブルのAUTO_INCREMENT `mysite_content_folders`
--
ALTER TABLE `mysite_content_folders`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- テーブルのAUTO_INCREMENT `mysite_content_links`
--
ALTER TABLE `mysite_content_links`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `mysite_dblogs`
--
ALTER TABLE `mysite_dblogs`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- テーブルのAUTO_INCREMENT `mysite_editor_templates`
--
ALTER TABLE `mysite_editor_templates`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- テーブルのAUTO_INCREMENT `mysite_favorites`
--
ALTER TABLE `mysite_favorites`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- テーブルのAUTO_INCREMENT `mysite_feed_configs`
--
ALTER TABLE `mysite_feed_configs`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- テーブルのAUTO_INCREMENT `mysite_feed_details`
--
ALTER TABLE `mysite_feed_details`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- テーブルのAUTO_INCREMENT `mysite_mail_configs`
--
ALTER TABLE `mysite_mail_configs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- テーブルのAUTO_INCREMENT `mysite_mail_contents`
--
ALTER TABLE `mysite_mail_contents`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- テーブルのAUTO_INCREMENT `mysite_mail_fields`
--
ALTER TABLE `mysite_mail_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- テーブルのAUTO_INCREMENT `mysite_mail_messages`
--
ALTER TABLE `mysite_mail_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `mysite_mail_message_1`
--
ALTER TABLE `mysite_mail_message_1`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `mysite_mail_message_2`
--
ALTER TABLE `mysite_mail_message_2`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `mysite_pages`
--
ALTER TABLE `mysite_pages`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- テーブルのAUTO_INCREMENT `mysite_permissions`
--
ALTER TABLE `mysite_permissions`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- テーブルのAUTO_INCREMENT `mysite_plugins`
--
ALTER TABLE `mysite_plugins`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- テーブルのAUTO_INCREMENT `mysite_search_indices`
--
ALTER TABLE `mysite_search_indices`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- テーブルのAUTO_INCREMENT `mysite_sites`
--
ALTER TABLE `mysite_sites`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- テーブルのAUTO_INCREMENT `mysite_site_configs`
--
ALTER TABLE `mysite_site_configs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- テーブルのAUTO_INCREMENT `mysite_theme_configs`
--
ALTER TABLE `mysite_theme_configs`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- テーブルのAUTO_INCREMENT `mysite_uploader_categories`
--
ALTER TABLE `mysite_uploader_categories`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `mysite_uploader_configs`
--
ALTER TABLE `mysite_uploader_configs`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- テーブルのAUTO_INCREMENT `mysite_uploader_files`
--
ALTER TABLE `mysite_uploader_files`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `mysite_users`
--
ALTER TABLE `mysite_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- テーブルのAUTO_INCREMENT `mysite_user_groups`
--
ALTER TABLE `mysite_user_groups`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- テーブルのAUTO_INCREMENT `mysite_widget_areas`
--
ALTER TABLE `mysite_widget_areas`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
