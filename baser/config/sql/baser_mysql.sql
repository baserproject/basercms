-- SVN FILE: $Id$
--
-- BaserCMS インストール SQL（MySQL）
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
-- テーブルの構造 `bc_dblogs`
--

CREATE TABLE IF NOT EXISTS `bc_dblogs` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- テーブルのデータをダンプしています `bc_dblogs`
--


-- --------------------------------------------------------


-- --------------------------------------------------------

--
-- テーブルの構造 `bc_global_menus`
--

CREATE TABLE IF NOT EXISTS `bc_global_menus` (
  `id` int(3) NOT NULL auto_increment,
  `no` int(3) default NULL,
  `name` varchar(20) default NULL,
  `link` varchar(255) default NULL,
  `menu_type` varchar(20) default NULL,
  `sort` int(3) default NULL,
  `status` tinyint(1) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- テーブルのデータをダンプしています `bc_global_menus`
--

INSERT INTO `bc_global_menus` (`no`, `sort`, `status`, `name`, `link`, `menu_type`, `created`, `modified`) VALUES
('1','1','1','ホーム', '/', 'default', NOW(), NOW()),
('2','2','1','会社案内', '/about', 'default', NOW(), NOW()),
('3','3','1','サービス', '/service', 'default', NOW(), NOW()),
('4','4','1','ニュースリリース', '/news/index', 'default', NOW(), NOW()),
('5','5','1','お問い合せ', '/contact/index', 'default', NOW(), NOW()),
('6','6','1','サイトマップ', '/sitemap', 'default', NOW(), NOW()),
('1','1','1','ダッシュボード', '/admin/dashboard/index', 'admin', NOW(), NOW()),
('2','2','1','ユーザー管理', '/admin/users/index', 'admin', NOW(), NOW()),
('3','3','1','ニュース管理', '/admin/blog/blog_posts/index/1', 'admin', NOW(), NOW()),
('4','4','1','ページ管理', '/admin/pages/index', 'admin', NOW(), NOW()),
('5','5','1','お問合せ管理', '/admin/mail/mail_fields/index/1', 'admin', NOW(), NOW()),
('6','6','1','フィード管理', '/admin/feed/feed_configs/index', 'admin', NOW(), NOW()),
('7','7','1','システム設定', '/admin/site_configs/form', 'admin', NOW(), NOW());


-- --------------------------------------------------------

--
-- テーブルの構造 `bc_plugins`
--

CREATE TABLE IF NOT EXISTS `bc_plugins` (
  `id` int(8) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `title` varchar(50) default NULL,
  `admin_link` varchar(255) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- テーブルのデータをダンプしています `bc_plugins`
--

INSERT INTO `bc_plugins` (`name`, `title`, `admin_link`, `created`, `modified`) VALUES
('mail', 'メールフォーム', '/admin/mail/mail_contents/index', NOW(), NOW()),
('feed', 'フィードリーダー', '/admin/feed/feed_configs/index', NOW(), NOW()),
('blog', 'ブログ', '/admin/blog/blog_contents/index', NOW(), NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 `bc_plugin_contents`
--

CREATE TABLE IF NOT EXISTS `bc_plugin_contents` (
  `id` int(11) NOT NULL auto_increment,
  `content_id` int(8) default NULL,
  `name` varchar(50) default NULL,
  `plugin` varchar(20) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- テーブルのデータをダンプしています `bc_plugin_contents`
--

INSERT INTO `bc_plugin_contents` (`content_id`, `name`, `plugin`, `created`, `modified`) VALUES
(1, 'news', 'blog', NOW(), NOW()),
(1, 'contact', 'mail', NOW(), NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 `bc_site_configs`
--

CREATE TABLE IF NOT EXISTS `bc_site_configs` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `value` text collate utf8_unicode_ci,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- テーブルのデータをダンプしています `bc_site_configs`
--

INSERT INTO `bc_site_configs` (`name`, `value`, `created`, `modified`) VALUES
('name','BaserCMS inc. [デモ]', NOW(), NOW()),
('keyword','Baser,CMS,コンテンツマネジメントシステム,開発支援', NOW(), NOW()),
('description','BaserCMSは、CakePHPを利用し、環境準備の素早さに重点を置いた基本開発支援プロジェクトです。WEBサイトに最低限必要となるプラグイン、そしてそのプラグインを組み込みやすい管理画面、認証付きのメンバーマイページを最初から装備しています。', NOW(), NOW()),
('address',NULL, NOW(), NOW()),
('googlemaps_key', NULL, NOW(), NOW()),
('theme','demo', NOW(), NOW()),
('email','', NOW(), NOW()),
('maintenance','', NOW(), NOW()),
('widget_area','1', NOW(), NOW());


-- --------------------------------------------------------

--
-- テーブルの構造 `bc_users`
--

CREATE TABLE IF NOT EXISTS `bc_users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `password` varchar(255) default NULL,
  `real_name_1` varchar(50) default NULL,
  `real_name_2` varchar(50) default NULL,
  `email` varchar(255) default NULL,
  `user_group_id` int(4) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `bc_pages`
--

CREATE TABLE IF NOT EXISTS `bc_pages` (
  `id` int(8) NOT NULL auto_increment,
  `sort` int(8) default NULL,
  `name` varchar(50) default NULL,
  `title` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `contents` text collate utf8_unicode_ci,
  `page_category_id` int(8) default NULL,
  `status` tinyint(1) default NULL,
  `url` text collate utf8_unicode_ci,
  `modified` datetime default NULL,
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- テーブルのデータをダンプしています `bc_pages`
--

INSERT INTO `bc_pages` (`sort`,`name`, `title`, `description`, `contents`, `page_category_id`, `status`,`url`, `created`, `modified`) VALUES
 ('1','index',null,null,'<?php echo $html->css(\'top\',null,null,false) ?>\n\n\n<div id=\"news\" class=\"clearfix\">\n<div class=\"news\" style=\"margin-right:28px;\">\n<h2 id=\"newsHead01\">NEWS RELEASE</h2>\n<div class=\"body\">\n<script type=\"text/javascript\" src=\"<?php $baser->root() ?>feed/ajax/1\"></script>\n</div>\n</div>\n\n\n<div class=\"news\">\n<h2 id=\"newsHead02\">BaserCMS NEWS</h2>\n<div class=\"body\">\n<script type=\"text/javascript\" src=\"<?php $baser->root() ?>feed/ajax/2\"></script>\n</div>\n</div>\n</div>',null,'1', '/index', NOW(), NOW()),
 ('2','about','会社案内','BaserCMS inc.の会社案内ページ','<h2 class=\"contents-head\">会社案内</h2>\n\n<h3 class=\"contents-head\">会社データ</h3>\n\n<div class=\"section\">\n<table class=\"row-table-01\" cellspacing=\"0\" cellpadding=\"0\">\n<tr><th width=\"150\">会社名</th><td>BaserCMS inc.  [デモ]</td></tr>\n<tr><th>設立</th><td>2009年11月</td></tr>\n<tr><th>所在地</th><td>福岡県福岡市博多区博多駅前（ダミー）</td></tr>\n<tr><th>事業内容</th><td>インターネットサービス業（ダミー）<br />\nWEBサイト制作事業（ダミー）<br />\nWEBシステム開発事業（ダミー）</td></tr>\n</table>\n</div>\n\n\n<h3 class=\"contents-head\">アクセスマップ</h3>\n<?php $baser->element(\'googlemaps\') ?>\n',null,'1', '/about', NOW(), NOW()),
 ('3','service','サービス','BaserCMS inc.のサービス紹介ページ。','<h2 class=\"contents-head\">サービス</h2>\n\n<div class=\"section\">\n<p>\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\n</p>\n</div>\n\n<div class=\"section\">\n<p>\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\n</p>\n</div>\n\n<div class=\"section\">\n<p>\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\n</p>\n</div>',null,'1', '/service', NOW(), NOW()),
 ('4','sitemap','サイトマップ','BaserCMS inc.のサイトマップページ','<h2 class=\"contents-head\">\n	サイトマップ</h2>\n<h3 class=\"contents-head\">\n	公開ページ</h3>\n<ul class=\"section\">\n	<li>\n<?php $baser->link(\'ホーム\',\'/\') ?>	</li>\n	<li>\n<?php $baser->link(\'会社案内\',\'/about\') ?>	</li>\n	<li>\n<?php $baser->link(\'サービス\',\'/service\') ?>	</li>\n	<li>\n<?php $baser->link(\'新着情報\',\'/news/\') ?>	</li>\n	<li>\n<?php $baser->link(\'お問い合わせ\',\'/contact/index\') ?>	</li>\n	<li>\n<?php $baser->link(\'サイトマップ\',\'/sitemap\') ?>	</li>\n</ul>\n<h3 class=\"contents-head\">\n	非公開ページ</h3>\n<ul class=\"section\">\n<li>\n	<?php $baser->link(\'管理者ログイン\',\'/admin/users/login\') ?>	</li>\n</ul>\n<p class=\"customize-navi corner10\">\n	<small>公開する際には非公開ページは削除をおすすめします。</small>\n</p>',null,'1', '/sitemap', NOW(), NOW()),
 ('5','index',null,null,'<hr size=\"1\" style=\"width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;\" />\n<div style=\"text-align:center;background-color:#8ABE08;\"> <span style=\"color:white;\">メインメニュー</span> </div>\n<hr size=\"1\" style=\"width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;\" />\n<span style=\"color:#8ABE08\">◆</span>\n<?php $baser->link(\'ニュースリリース\',array(\'controller\'=>\'news\',\'action\'=>\'index\')) ?>\n<br />\n<span style=\"color:#8ABE08\">◆</span>\n<?php $baser->link(\'お問い合わせ\',array(\'controller\'=>\'contact\',\'action\'=>\'index\')) ?>\n<hr size=\"1\" style=\"width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;\" />\n<div style=\"text-align:center;background-color:#8ABE08;\"> <span style=\"color:white;\">NEWS RELEASE</span> </div>\n<hr size=\"1\" style=\"width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;\" />\n<?php $baser->feed(1) ?> <div>&nbsp;</div>\n<hr size=\"1\" style=\"width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;\" />\n<div style=\"text-align:center;background-color:#8ABE08;\"> <span style=\"color:white;\">BaserCMS NEWS</span> </div>\n<hr size=\"1\" style=\"width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;\" />\n<?php $baser->feed(2) ?>','1','0', '/mobile/index', NOW(), NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 `bc_page_categories`
--

CREATE TABLE IF NOT EXISTS `bc_page_categories` (
  `id` int(8) NOT NULL auto_increment,
  `parent_id` int(8) default NULL,
  `lft` int(8) default NULL,
  `rght` int(8) default NULL,
  `name` varchar(50) default NULL,
  `title` varchar(255) default NULL,
  `sort` int(8) default NULL,
  `modified` datetime default NULL,
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- テーブルのデータをダンプしています bc_page_categories
--

INSERT INTO `bc_page_categories` (`parent_id`, `lft`, `rght`, `name`, `title`, `sort`, `created`, `modified`) VALUES
 (null, '1', '2', 'mobile', 'モバイル', '1', NOW(), NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 `bc_user_groups`
--

CREATE TABLE IF NOT EXISTS `bc_user_groups` (
  `id` int(8) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `title` varchar(50) default NULL,
  `modified` datetime default NULL,
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- テーブルのデータをダンプしています `bc_user_groups`
--

INSERT INTO `bc_user_groups` (`name`,`title`, `created`, `modified`) VALUES
('admins','管理者',NOW(),NOW()),
('operators','運営者',NOW(),NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 `bc_permissions`
--

CREATE TABLE IF NOT EXISTS `bc_permissions` (
  `id` int(8) NOT NULL auto_increment,
  `no` int(8) default NULL,
  `sort` int(8) default NULL,
  `name` varchar(255) default NULL,
  `user_group_id` int(8) default NULL,
  `url` varchar(255) default NULL,
  `auth` tinyint(1) default NULL,
  `status` tinyint(1) default NULL,
  `modified` datetime default NULL,
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- テーブルのデータをダンプしています `bc_permissions`
--

INSERT INTO `bc_permissions` (`no`,`sort`, `name`, `user_group_id` ,`url`, `auth`, `status`, `created`, `modified`) VALUES
('1','1','アクセス制限設定','2','/admin/permissions*','0','1',NOW(),NOW()),
('2','2','システム設定','2','/admin/site_configs*','0','1',NOW(),NOW()),
('3','3','グローバルメニュー管理','2','/admin/global_menus*','0','1',NOW(),NOW()),
('4','4','プラグイン管理','2','/admin/plugins*','0','1',NOW(),NOW()),
('5','5','ユーザー管理','2','/admin/users*','0','1',NOW(),NOW()),
('6','6','ユーザー編集','2','/admin/users/edit*','1','1',NOW(),NOW()),
('7','7','ユーザー編集','2','/admin/users/logout','1','1',NOW(),NOW()),
('8','8','ブログ管理','2','/admin/blog/blog_contents*','0','1',NOW(),NOW()),
('9','9','ブログ編集','2','/admin/blog/blog_contents/edit*','1','1',NOW(),NOW()),
('10','10','メールフォーム基本設定','2','/admin/mail/mail_configs*','0','1',NOW(),NOW()),
('11','11','メールフォーム管理','2','/admin/mail/mail_contents*','0','1',NOW(),NOW()),
('12','12','メールフォーム編集','2','/admin/mail/mail_contents/edit*','1','1',NOW(),NOW()),
('13','13','フィード管理','2','/admin/feed/feed_configs*','0','1',NOW(),NOW()),
('14','14','ページテンプレート読込','2','/admin/pages/entry_page_files','0','1',NOW(),NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 `bc_widget_areas`
--

CREATE TABLE IF NOT EXISTS `bc_widget_areas` (
  `id` int(4) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `widgets` text collate utf8_unicode_ci,
  `modified` datetime default NULL,
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- テーブルのデータをダンプしています `bc_widget_areas`
--

INSERT INTO `bc_widget_areas` (`name`, `widgets`, `created`, `modified`) VALUES
('標準サイドバー','a:1:{i:0;a:1:{s:7:\"Widget1\";a:9:{s:2:\"id\";s:1:\"1\";s:4:\"type\";s:12:\"テキスト\";s:7:\"element\";s:4:\"text\";s:6:\"plugin\";s:0:\"\";s:4:\"sort\";i:1;s:4:\"name\";s:9:\"リンク\";s:4:\"text\";s:358:\"<p style=\"margin-bottom:20px;text-align: center\"> <a href=\"http://basercms.net\" target=\"_blank\"><img src=\"http://basercms.net/img/bnr_basercms.jpg\" alt=\"コーポレートサイトにちょうどいいCMS、BaserCMS\"/></a></p><p class=\"customize-navi corner10\"><small>この部分は、ウィジェットエリア管理より編集できます。</small></p>\";s:9:\"use_title\";s:1:\"1\";s:6:\"status\";s:1:\"1\";}}}',NOW(),NOW()),
('ブログサイドバー','a:4:{i:0;a:1:{s:7:\"Widget1\";a:9:{s:2:\"id\";s:1:\"1\";s:4:\"type\";s:24:\"ブログカレンダー\";s:7:\"element\";s:13:\"blog_calendar\";s:6:\"plugin\";s:4:\"blog\";s:4:\"sort\";i:1;s:4:\"name\";s:24:\"ブログカレンダー\";s:15:\"blog_content_id\";s:1:\"1\";s:9:\"use_title\";s:1:\"0\";s:6:\"status\";s:1:\"1\";}}i:1;a:1:{s:7:\"Widget2\";a:9:{s:2:\"id\";s:1:\"2\";s:4:\"type\";s:30:\"ブログカテゴリー一覧\";s:7:\"element\";s:22:\"blog_category_archives\";s:6:\"plugin\";s:4:\"blog\";s:4:\"sort\";i:2;s:4:\"name\";s:21:\"カテゴリー一覧\";s:15:\"blog_content_id\";s:1:\"1\";s:9:\"use_title\";s:1:\"1\";s:6:\"status\";s:1:\"1\";}}i:2;a:1:{s:7:\"Widget3\";a:9:{s:2:\"id\";s:1:\"3\";s:4:\"type\";s:27:\"月別アーカイブ一覧\";s:7:\"element\";s:21:\"blog_monthly_archives\";s:6:\"plugin\";s:4:\"blog\";s:4:\"sort\";i:3;s:4:\"name\";s:27:\"月別アーカイブ一覧\";s:15:\"blog_content_id\";s:1:\"1\";s:9:\"use_title\";s:1:\"1\";s:6:\"status\";s:1:\"1\";}}i:3;a:1:{s:7:\"Widget4\";a:9:{s:2:\"id\";s:1:\"4\";s:4:\"type\";s:15:\"最近の投稿\";s:7:\"element\";s:19:\"blog_recent_entries\";s:6:\"plugin\";s:4:\"blog\";s:4:\"sort\";i:4;s:4:\"name\";s:15:\"最近の投稿\";s:15:\"blog_content_id\";s:1:\"1\";s:9:\"use_title\";s:1:\"1\";s:6:\"status\";s:1:\"1\";}}}',NOW(),NOW());