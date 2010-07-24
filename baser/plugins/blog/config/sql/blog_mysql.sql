-- SVN FILE: $Id$
--
-- BaserCMS ブログプラグイン SQL（MySQL）
--
-- BaserCMS :  Based Website Development Project <http://basercms.net>
-- Copyright 2008 - 2010, Catchup, Inc.
--								9-5 nagao 3-chome, fukuoka-shi
--								fukuoka, Japan 814-0123
--
-- @copyright		Copyright 2008 - 2010, Catchup, Inc.
-- @link			http://basercms.net BaserCMS Project
-- @version			$Revision$
-- @modifiedby		$LastChangedBy$
-- @lastmodified	$Date$
-- @license			http://basercms.net/license/index.html

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- テーブルの構造 `bc__blog_categories`
--

CREATE TABLE IF NOT EXISTS `bc__blog_categories` (
  `id` int(8) NOT NULL auto_increment,
  `blog_content_id` int(8) NOT NULL,
  `no` int(8) NOT NULL,
  `name` varchar(50) default NULL,
  `title` varchar(50) default NULL,
  `status` tinyint(2) default NULL,
  `parent_id` int(8) default NULL,
  `lft` int(8) default NULL,
  `rght` int(8) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- テーブルのデータをダンプしています `bc__blog_categories`
--

INSERT INTO `bc__blog_categories` (`blog_content_id`, `no`, `name`, `title`, `status`, `parent_id`, `lft`, `rght`, `created`, `modified`) VALUES
(1, 1, 'release', 'プレスリリース', 1, NULL, 1, 2, NOW(), NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 `bc__blog_configs`
--

CREATE TABLE IF NOT EXISTS `bc__blog_configs` (
  `id` int(2) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- テーブルのデータをダンプしています `bc__blog_configs`
--


-- --------------------------------------------------------

--
-- テーブルの構造 `bc__blog_contents`
--

CREATE TABLE IF NOT EXISTS `bc__blog_contents` (
  `id` int(8) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `title` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `layout` varchar(20) default NULL,
  `template` varchar(20) default NULL,
  `status` tinyint(2) default NULL,
  `list_count` int(4) default NULL,
  `list_direction` varchar(4) default NULL,
  `feed_count` int(4) default NULL,
  `comment_use` tinyint(2) default NULL,
  `comment_approve` tinyint(2) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- テーブルのデータをダンプしています `bc__blog_contents`
--

INSERT INTO `bc__blog_contents` (`name`, `title`, `description`, `layout`, `template`, `status`, `list_count`, `list_direction`, `feed_count`, `comment_use`, `comment_approve`, `created`, `modified`) VALUES
('news', 'ニュースリリース', 'Baser CMS inc. [デモ]の最新のニュースリリースをお届けします。', 'default', 'default', 1, 10, 'DESC', 10, 1, 0, NOW(), NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 `bc__blog_posts`
--

CREATE TABLE IF NOT EXISTS `bc__blog_posts` (
  `id` int(11) NOT NULL auto_increment,
  `blog_content_id` int(8) NOT NULL,
  `no` int(11) NOT NULL,
  `name` varchar(50) default NULL,
  `content` text,
  `detail` text,
  `blog_category_id` int(8) default NULL,
  `user_id` int(8) default NULL,
  `status` tinyint(2) default NULL,
  `posts_date` datetime default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- テーブルのデータをダンプしています `bc__blog_posts`
--

INSERT INTO `bc__blog_posts` (`blog_content_id`, `no`, `name`, `content`, `detail`, `blog_category_id`, `user_id`, `status`, `posts_date`, `created`, `modified`) VALUES
(1, 1, 'ホームページをオープンしました。', 'テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。<br /><br />', 'テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。<br />', 1, 1, 1, NOW(), NOW(), NOW()),
(1, 2, '新商品を販売を開始しました。', '新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。<br /><br />新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。<br />', '詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br /><br />詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br /><br />詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br /><br />', '1' , '1', '1', NOW(), NOW(), NOW());


--
-- テーブルの構造 `bc__blog_comments`
--

CREATE TABLE IF NOT EXISTS `bc__blog_comments` (
  `id` int(11) NOT NULL auto_increment,
  `blog_content_id` int(8) NOT NULL,
  `blog_post_id` int(8) NOT NULL,
  `no` int(11) NOT NULL,
  `status` tinyint(2) default NULL,
  `name` varchar(50) default NULL,
  `email` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `message` text,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- テーブルのデータをダンプしています `bc__blog_comments`
--

INSERT INTO `bc__blog_comments` (`blog_content_id`,`blog_post_id`,`no`,`status`,`name`,`email`,`url`,`message`,`created`,`modified`) VALUES
(1, 1, 1, 1, 'BaserCMS', '', 'http://basercms.net', 'ホームページの開設おめでとうございます。', '', '');