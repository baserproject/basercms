-- SVN FILE: $Id$
--
-- BaserCMS インストール SQL（PostgreSQL）
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

--
-- テーブルの構造 "bc_dblogs"
--

CREATE SEQUENCE bc_dblogs_id_seq;
CREATE TABLE "public"."bc_dblogs" (
  "id" int8 default nextval('bc_dblogs_id_seq'),
  "name" varchar(255) default NULL,
  "created" timestamp default NULL,
  "modified" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc_dblogs" SET WITHOUT CLUSTER;

--
-- テーブルのデータをダンプしています "bc_dblogs"
--


-- --------------------------------------------------------


-- --------------------------------------------------------

--
-- テーブルの構造 "bc_global_menus"
--

CREATE SEQUENCE bc_global_menus_id_seq;
CREATE TABLE "public"."bc_global_menus" (
  "id" int4 NOT NULL default nextval('bc_global_menus_id_seq'),
  "no" int4 default NULL,
  "name" varchar(20) default NULL,
  "link" varchar(255) default NULL,
  "menu_type" varchar(20) default NULL,
  "sort" int4 default NULL,
  "status" boolean default NULL,
  "created" timestamp default NULL,
  "modified" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc_global_menus" SET WITHOUT CLUSTER;

--
-- テーブルのデータをダンプしています "bc_global_menus"
--

INSERT INTO "bc_global_menus" ("no", "sort", "status", "name", "link", "menu_type", "created", "modified") VALUES
('1','1',true,'ホーム', '/', 'default', NOW(), NOW()),
('2','2',true,'会社案内', '/about.html', 'default', NOW(), NOW()),
('3','3',true,'サービス', '/service.html', 'default', NOW(), NOW()),
('4','4',true,'ニュースリリース', '/news/index', 'default', NOW(), NOW()),
('5','5',true,'お問い合せ', '/contact/index', 'default', NOW(), NOW()),
('6','6',true,'サイトマップ', '/sitemap.html', 'default', NOW(), NOW()),
('1','1',true,'ダッシュボード', '/admin/dashboard/index', 'admin', NOW(), NOW()),
('2','2',true,'ユーザー管理', '/admin/users/index', 'admin', NOW(), NOW()),
('3','3',true,'ニュース管理', '/admin/blog/blog_posts/index/1', 'admin', NOW(), NOW()),
('4','4',true,'ページ管理', '/admin/pages/index', 'admin', NOW(), NOW()),
('5','5',true,'お問合せ管理', '/admin/mail/mail_fields/index/1', 'admin', NOW(), NOW()),
('6','6',true,'フィード管理', '/admin/feed/feed_configs/index', 'admin', NOW(), NOW()),
('7','7',true,'システム設定', '/admin/site_configs/form', 'admin', NOW(), NOW());


-- --------------------------------------------------------

--
-- テーブルの構造 "bc_plugins"
--

CREATE SEQUENCE bc_plugins_id_seq;
CREATE TABLE "public"."bc_plugins" (
  "id" int8 NOT NULL default nextval('bc_plugins_id_seq'),
  "name" varchar(50) default NULL,
  "title" varchar(50) default NULL,
  "admin_link" varchar(255) default NULL,
  "created" timestamp default NULL,
  "modified" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc_plugins" SET WITHOUT CLUSTER;

--
-- テーブルのデータをダンプしています "bc_plugins"
--

INSERT INTO "bc_plugins" ("name", "title", "admin_link", "created", "modified") VALUES
('mail', 'メールフォーム', '/admin/mail/mail_contents/index', NOW(), NOW()),
('feed', 'フィードリーダー', '/admin/feed/feed_configs/index', NOW(), NOW()),
('blog', 'ブログ', '/admin/blog/blog_contents/index', NOW(), NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 "bc_plugin_contents"
--

CREATE SEQUENCE bc_plugin_contents_id_seq;
CREATE TABLE "public"."bc_plugin_contents" (
  "id" int8 NOT NULL default nextval('bc_plugin_contents_id_seq'),
  "content_id" int8 default NULL,
  "name" varchar(50) default NULL,
  "plugin" varchar(20) default NULL,
  "created" timestamp default NULL,
  "modified" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc_plugin_contents" SET WITHOUT CLUSTER;

--
-- テーブルのデータをダンプしています "bc_plugin_contents"
--

INSERT INTO "bc_plugin_contents" ("content_id", "name", "plugin", "created", "modified") VALUES
(1, 'news', 'blog', NOW(), NOW()),
(1, 'contact', 'mail', NOW(), NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 "bc_site_configs"
--

CREATE SEQUENCE bc_site_configs_id_seq;
CREATE TABLE "public"."bc_site_configs" (
  "id" int8 NOT NULL default nextval('bc_site_configs_id_seq'),
  "name" varchar(255) default NULL,
  "value" text default NULL,
  "created" timestamp default NULL,
  "modified" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc_site_configs" SET WITHOUT CLUSTER;

--
-- テーブルのデータをダンプしています "bc_site_configs"
--

INSERT INTO "bc_site_configs" ("name", "value", "created", "modified") VALUES
('name','BaserCMS inc. [デモ]', NOW(), NOW()),
('keyword','Baser,CMS,コンテンツマネジメントシステム,開発支援', NOW(), NOW()),
('description','BaserCMSは、CakePHPを利用し、環境準備の素早さに重点を置いた基本開発支援プロジェクトです。WEBサイトに最低限必要となるプラグイン、そしてそのプラグインを組み込みやすい管理画面、認証付きのメンバーマイページを最初から装備しています。', NOW(), NOW()),
('address',NULL, NOW(), NOW()),
('googlemaps_key', NULL, NOW(), NOW()),
('theme','demo', NOW(), NOW()),
('email','', NOW(), NOW()),
('twitter_username','basercms', NOW(), NOW()),
('twitter_count','3', NOW(), NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 "bc_users"
--

CREATE SEQUENCE bc_users_id_seq;
CREATE TABLE "public"."bc_users" (
  "id" int8 NOT NULL default nextval('bc_users_id_seq'),
  "name" varchar(255) default NULL,
  "password" varchar(255) default NULL,
  "real_name_1" varchar(50) default NULL,
  "real_name_2" varchar(50) default NULL,
  "email" varchar(255) default NULL,
  "user_group_id" int4 default NULL,
  "created" timestamp default NULL,
  "modified" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc_users" SET WITHOUT CLUSTER;

-- --------------------------------------------------------

--
-- テーブルの構造 "bc_pages"
--

CREATE SEQUENCE bc_pages_id_seq;
CREATE TABLE "public"."bc_pages" (
  "id" int8 NOT NULL default nextval('bc_pages_id_seq'),
  "no" int8 default NULL,
  "sort" int8 default NULL,
  "name" varchar(50) default NULL,
  "title" varchar(255) default NULL,
  "description" varchar(255) default NULL,
  "contents" text default NULL,
  "page_category_id" int8 default NULL,
  "theme" varchar(50) default NULL,
  "status" boolean default NULL,
  "url" text default NULL,
  "modified" timestamp default NULL,
  "created" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc_pages" SET WITHOUT CLUSTER;

--
-- テーブルのデータをダンプしています "bc_pages"
--

INSERT INTO "bc_pages" ("no", "sort", "name", "title", "description", "contents", "page_category_id", "theme", "status", "url", "created", "modified") VALUES
 ('1','1','index',null,null,'<?php echo $html->css(\'top\',null,null,false) ?>\n\n\n<div id=\"news\" class=\"clearfix\">\n<div class=\"news\" style=\"margin-right:28px;\">\n<h2 id=\"newsHead01\">NEWS RELEASE</h2>\n<div class=\"body\">\n<script type=\"text/javascript\" src=\"<?php $baser->root() ?>feed/ajax/1\"></script>\n</div>\n</div>\n\n\n<div class=\"news\">\n<h2 id=\"newsHead02\">BaserCMS NEWS</h2>\n<div class=\"body\">\n<script type=\"text/javascript\" src=\"<?php $baser->root() ?>feed/ajax/2\"></script>\n</div>\n</div>\n</div>',null,'',true, '/index.html', NOW(), NOW()),
 ('2','2','about','会社案内','BaserCMS inc.の会社案内ページ','<h2 class=\"contents-head\">会社案内</h2>\n\n<h3 class=\"contents-head\">会社データ</h3>\n\n<div class=\"section\">\n<table class=\"row-table-01\" cellspacing=\"0\" cellpadding=\"0\">\n<tr><th width=\"150\">会社名</th><td>BaserCMS inc.  [デモ]</td></tr>\n<tr><th>設立</th><td>2009年11月</td></tr>\n<tr><th>所在地</th><td>福岡県福岡市博多区博多駅前（ダミー）</td></tr>\n<tr><th>事業内容</th><td>インターネットサービス業（ダミー）<br />\nWEBサイト制作事業（ダミー）<br />\nWEBシステム開発事業（ダミー）</td></tr>\n</table>\n</div>\n\n\n<h3 class=\"contents-head\">アクセスマップ</h3>\n<?php $baser->element(\'googlemaps\') ?>\n',null,'',true, '/about.html', NOW(), NOW()),
 ('3','3','service','サービス','BaserCMS inc.のサービス紹介ページ。','<h2 class=\"contents-head\">サービス</h2>\n\n<div class=\"section\">\n<p>\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\n</p>\n</div>\n\n<div class=\"section\">\n<p>\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\n</p>\n</div>\n\n<div class=\"section\">\n<p>\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\n</p>\n</div>',null,'',true, '/service.html', NOW(), NOW()),
 ('4','4','sitemap','サイトマップ','BaserCMS inc.のサイトマップページ','<h2 class=\"contents-head\">\n	サイトマップ</h2>\n<h3 class=\"contents-head\">\n	公開ページ</h3>\n<ul class=\"section\">\n	<li>\n<?php $baser->link(\'ホーム\',\'/\') ?>	</li>\n	<li>\n<?php $baser->link(\'会社案内\',\'/about.html\') ?>	</li>\n	<li>\n<?php $baser->link(\'サービス\',\'/service.html\') ?>	</li>\n	<li>\n<?php $baser->link(\'新着情報\',\'/news/\') ?>	</li>\n	<li>\n<?php $baser->link(\'お問い合わせ\',\'/contact/index\') ?>	</li>\n	<li>\n<?php $baser->link(\'サイトマップ\',\'/sitemap.html\') ?>	</li>\n</ul>\n<h3 class=\"contents-head\">\n	非公開ページ</h3>\n<ul class=\"section\">\n<li>\n	<?php $baser->link(\'管理者ログイン\',\'/admin/users/login\') ?>	</li>\n</ul>\n<p class=\"customize-navi corner10\">\n	<small>公開する際には非公開ページは削除をおすすめします。</small>\n</p>',null,'',true, '/sitemap.html', NOW(), NOW()),
 ('1','1','index',null,null,'<?php echo $html->css(\'top\',null,null,false) ?>\n\n\n<div id=\"news\" class=\"clearfix\">\n<div class=\"news\" style=\"margin-right:28px;\">\n<h2 id=\"newsHead01\">NEWS RELEASE</h2>\n<div class=\"body\">\n<script type=\"text/javascript\" src=\"<?php $baser->root() ?>feed/ajax/1\"></script>\n</div>\n</div>\n\n\n<div class=\"news\">\n<h2 id=\"newsHead02\">BaserCMS NEWS</h2>\n<div class=\"body\">\n<script type=\"text/javascript\" src=\"<?php $baser->root() ?>feed/ajax/2\"></script>\n</div>\n</div>\n</div>',null,'demo',true, '/index.html', NOW(), NOW()),
 ('2','2','about','会社案内','BaserCMS inc.の会社案内ページ','<h2 class=\"contents-head\">会社案内</h2>\n\n<h3 class=\"contents-head\">会社データ</h3>\n\n<div class=\"section\">\n<table class=\"row-table-01\" cellspacing=\"0\" cellpadding=\"0\">\n<tr><th width=\"150\">会社名</th><td>BaserCMS inc.  [デモ]</td></tr>\n<tr><th>設立</th><td>2009年11月</td></tr>\n<tr><th>所在地</th><td>福岡県福岡市博多区博多駅前（ダミー）</td></tr>\n<tr><th>事業内容</th><td>インターネットサービス業（ダミー）<br />\nWEBサイト制作事業（ダミー）<br />\nWEBシステム開発事業（ダミー）</td></tr>\n</table>\n</div>\n\n\n<h3 class=\"contents-head\">アクセスマップ</h3>\n<?php $baser->element(\'googlemaps\') ?>\n',null,'demo',true, '/about.html', NOW(), NOW()),
 ('3','3','service','サービス','BaserCMS inc.のサービス紹介ページ。','<h2 class=\"contents-head\">サービス</h2>\n\n<div class=\"section\">\n<p>\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\n</p>\n</div>\n\n<div class=\"section\">\n<p>\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\n</p>\n</div>\n\n<div class=\"section\">\n<p>\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\nサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。\n</p>\n</div>',null,'demo',true, '/service.html', NOW(), NOW()),
 ('4','4','sitemap','サイトマップ','BaserCMS inc.のサイトマップページ','<h2 class=\"contents-head\">\n	サイトマップ</h2>\n<h3 class=\"contents-head\">\n	公開ページ</h3>\n<ul class=\"section\">\n	<li>\n<?php $baser->link(\'ホーム\',\'/\') ?>	</li>\n	<li>\n<?php $baser->link(\'会社案内\',\'/about.html\') ?>	</li>\n	<li>\n<?php $baser->link(\'サービス\',\'/service.html\') ?>	</li>\n	<li>\n<?php $baser->link(\'新着情報\',\'/news/\') ?>	</li>\n	<li>\n<?php $baser->link(\'お問い合わせ\',\'/contact/index\') ?>	</li>\n	<li>\n<?php $baser->link(\'サイトマップ\',\'/sitemap.html\') ?>	</li>\n</ul>\n<h3 class=\"contents-head\">\n	非公開ページ</h3>\n<ul class=\"section\">\n<li>\n	<?php $baser->link(\'管理者ログイン\',\'/admin/users/login\') ?>	</li>\n</ul>\n<p class=\"customize-navi corner10\">\n	<small>公開する際には非公開ページは削除をおすすめします。</small>\n</p>',null,'demo',true, '/sitemap.html', NOW(), NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 "bc_page_categories"
--

CREATE SEQUENCE bc_page_categories_id_seq;
CREATE TABLE "public"."bc_page_categories" (
  "id" int8 NOT NULL default nextval('bc_page_categories_id_seq'),
  "no" int8 default NULL,
  "parent_id" int8 default NULL,
  "lft" int8 default NULL,
  "rght" int8 default NULL,
  "name" varchar(50) default NULL,
  " title" varchar(255) default NULL,
  "sort" int8 default NULL,
  "theme" varchar(50) default NULL,
  "modified" timestamp default NULL,
  "created" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc_page_categories" SET WITHOUT CLUSTER;

--
-- テーブルの構造 "bc_user_groups"
--
CREATE SEQUENCE bc_user_groups_id_seq;
CREATE TABLE "public"."bc_user_groups" (
  "id" int8 NOT NULL default nextval('bc_user_groups_id_seq'),
  "name" varchar(50) default NULL,
  "title" varchar(50) default NULL,
  "modified" timestamp default NULL,
  "created" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc_user_groups" SET WITHOUT CLUSTER;

--
-- テーブルのデータをダンプしています "bc_user_groups"
--

INSERT INTO "bc_user_groups" ("name","title", "created", "modified") VALUES
('admins','管理者',NOW(),NOW()),
('operators','運営者',NOW(),NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 "bc_permissions"
--
CREATE SEQUENCE bc_permissions_id_seq;
CREATE TABLE "public"."bc_permissions" (
  "id" int8 NOT NULL default nextval('bc_permissions_id_seq'),
  "no" int8 default NULL,
  "sort" int8 default NULL,
  "name" varchar(255) default NULL,
  "user_group_id" int8 default NULL,
  "url" varchar(255) default NULL,
  "auth" boolean default NULL,
  "status" boolean default NULL,
  "modified" timestamp default NULL,
  "created" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc_permissions" SET WITHOUT CLUSTER;

--
-- テーブルのデータをダンプしています "bc_permissions"
--

INSERT INTO "bc_permissions" ("no","sort", "name", "user_group_id" ,"url", "auth", "status", "created", "modified") VALUES
('1','1','アクセス制限設定','2','/admin/permissions*',false,true,NOW(),NOW()),
('2','2','システム設定','2','/admin/site_configs*',false,true,NOW(),NOW()),
('3','3','グローバルメニュー管理','2','/admin/global_menus*',false,true,NOW(),NOW()),
('4','4','プラグイン管理','2','/admin/plugins*',false,true,NOW(),NOW()),
('5','5','ユーザー管理','2','/admin/users*',false,true,NOW(),NOW()),
('6','6','ユーザー編集','2','/admin/users/edit*',true,true,NOW(),NOW()),
('7','7','ユーザー編集','2','/admin/users/logout',true,true,NOW(),NOW()),
('8','8','ブログ管理','2','/admin/blog/blog_contents*',false,true,NOW(),NOW()),
('9','9','ブログ編集','2','/admin/blog/blog_contents/edit*',true,true,NOW(),NOW()),
('10','10','メールフォーム基本設定','2','/admin/mail/mail_configs*',false,true,NOW(),NOW()),
('11','11','メールフォーム管理','2','/admin/mail/mail_contents*',false,true,NOW(),NOW()),
('12','12','メールフォーム編集','2','/admin/mail/mail_contents/edit*',true,true,NOW(),NOW()),
('13','13','フィード管理','2','/admin/feed/feed_configs*',false,true,NOW(),NOW()),
('14','14','ページテンプレート読込','2','/admin/pages/entry_page_files',false,true,NOW(),NOW());