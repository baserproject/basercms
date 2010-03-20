-- SVN FILE: $Id$
--
-- BaserCMS インストール SQL（SQLite）
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
-- テーブルの構造 bc_dblogs
--
CREATE TABLE bc_dblogs (
  id integer NOT NULL PRIMARY KEY,
  name text default NULL,
  created text default NULL,
  modified text default NULL
);

--
-- テーブルのデータをダンプしています bc_dblogs
--


-- --------------------------------------------------------


-- --------------------------------------------------------

--
-- テーブルの構造 bc_global_menus
--

CREATE TABLE bc_global_menus (
  id integer NOT NULL PRIMARY KEY,
  no integer default NULL,
  name text default NULL,
  link text default NULL,
  menu_type text default NULL,
  sort integer default NULL,
  status boolean default NULL,
  created text default NULL,
  modified text default NULL
);

--
-- テーブルのデータをダンプしています bc_global_menus
--

INSERT INTO bc_global_menus (no, sort, status, name, link, menu_type, created, modified) VALUES
('1','1','1','ホーム', '/', 'default', datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc_global_menus (no, sort, status, name, link, menu_type, created, modified) VALUES
('2','2','1','会社案内', '/about.html', 'default', datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc_global_menus (no, sort, status, name, link, menu_type, created, modified) VALUES
('3','3','1','サービス', '/service.html', 'default', datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc_global_menus (no, sort, status, name, link, menu_type, created, modified) VALUES
('4','4','1','ニュースリリース', '/news/index', 'default', datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc_global_menus (no, sort, status, name, link, menu_type, created, modified) VALUES
('5','5','1','お問い合せ', '/contact/index', 'default', datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc_global_menus (no, sort, status, name, link, menu_type, created, modified) VALUES
('6','6','1','サイトマップ', '/sitemap.html', 'default', datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc_global_menus (no, sort, status, name, link, menu_type, created, modified) VALUES
('1','1','1','ダッシュボード', '/admin/dashboard/index', 'admin', datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc_global_menus (no, sort, status, name, link, menu_type, created, modified) VALUES
('2','2','1','ユーザー管理', '/admin/users/index', 'admin', datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc_global_menus (no, sort, status, name, link, menu_type, created, modified) VALUES
('3','3','1','ニュース管理', '/admin/blog/blog_posts/index/1', 'admin', datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc_global_menus (no, sort, status, name, link, menu_type, created, modified) VALUES
('4','4','1','ページ管理', '/admin/pages/index', 'admin', datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc_global_menus (no, sort, status, name, link, menu_type, created, modified) VALUES
('5','5','1','お問合せ管理', '/admin/mail/mail_fields/index/1', 'admin', datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc_global_menus (no, sort, status, name, link, menu_type, created, modified) VALUES
('6','6','1','フィード管理', '/admin/feed/feed_configs/index', 'admin', datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc_global_menus (no, sort, status, name, link, menu_type, created, modified) VALUES
('7','7','1','システム設定', '/admin/site_configs/form', 'admin', datetime('now', 'localtime'), datetime('now', 'localtime'));

-- --------------------------------------------------------

--
-- テーブルの構造 bc_plugins
--

CREATE TABLE bc_plugins (
  id integer NOT NULL PRIMARY KEY,
  name text default NULL,
  title text default NULL,
  admin_link text default NULL,
  created text default NULL,
  modified text default NULL
);

--
-- テーブルのデータをダンプしています bc_plugins
--

INSERT INTO bc_plugins (name, title, admin_link, created, modified) VALUES
('mail', 'メールフォーム', '/admin/mail/mail_contents/index', datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc_plugins (name, title, admin_link, created, modified) VALUES
('feed', 'フィードリーダー', '/admin/feed/feed_configs/index', datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc_plugins (name, title, admin_link, created, modified) VALUES
('blog', 'ブログ', '/admin/blog/blog_contents/index', datetime('now', 'localtime'), datetime('now', 'localtime'));

-- --------------------------------------------------------

--
-- テーブルの構造 bc_plugin_contents
--

CREATE TABLE bc_plugin_contents (
  id integer NOT NULL PRIMARY KEY,
  content_id integer default NULL,
  name text default NULL,
  plugin text default NULL,
  created text default NULL,
  modified text default NULL
);

--
-- テーブルのデータをダンプしています bc_plugin_contents
--

INSERT INTO bc_plugin_contents (content_id, name, plugin, created, modified) VALUES
(1, 'news', 'blog', datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc_plugin_contents (content_id, name, plugin, created, modified) VALUES
(1, 'contact', 'mail', datetime('now', 'localtime'), datetime('now', 'localtime'));

-- --------------------------------------------------------

--
-- テーブルの構造 bc_site_configs
--

CREATE TABLE bc_site_configs (
  id integer NOT NULL PRIMARY KEY,
  name text default NULL,
  value text default NULL,
  created text default NULL,
  modified text default NULL
);

--
-- テーブルのデータをダンプしています bc_site_configs
--

INSERT INTO bc_site_configs (name, value, created, modified) VALUES
('name','BaserCMS inc. [デモ]',datetime('now', 'localtime'),datetime('now', 'localtime'));
INSERT INTO bc_site_configs (name, value, created, modified) VALUES
('keyword','Baser,CMS,コンテンツマネジメントシステム,開発支援',datetime('now', 'localtime'),datetime('now', 'localtime'));
INSERT INTO bc_site_configs (name, value, created, modified) VALUES
('description','BaserCMSは、CakePHPを利用し、環境準備の素早さに重点を置いた基本開発支援プロジェクトです。WEBサイトに最低限必要となるプラグイン、そしてそのプラグインを組み込みやすい管理画面、認証付きのメンバーマイページを最初から装備しています。',datetime('now', 'localtime'),datetime('now', 'localtime'));
INSERT INTO bc_site_configs (name, value, created, modified) VALUES
('address', NULL,datetime('now', 'localtime'),datetime('now', 'localtime'));
INSERT INTO bc_site_configs (name, value, created, modified) VALUES
('googlemaps_key', NULL,datetime('now', 'localtime'),datetime('now', 'localtime'));
INSERT INTO bc_site_configs (name, value, created, modified) VALUES
('theme', 'demo',datetime('now', 'localtime'),datetime('now', 'localtime'));
INSERT INTO bc_site_configs (name, value, created, modified) VALUES
('email', '',datetime('now', 'localtime'),datetime('now', 'localtime'));

-- --------------------------------------------------------

--
-- テーブルの構造 bc_users
--

CREATE TABLE bc_users (
  id integer NOT NULL PRIMARY KEY,
  name text default NULL,
  password text default NULL,
  real_name_1 text default NULL,
  real_name_2 text default NULL,
  email text default NULL,
  user_group_id integer default NULL,
  created text default NULL,
  modified text default NULL
);

-- --------------------------------------------------------

--
-- テーブルの構造 bc_pages
--

CREATE TABLE bc_pages (
  id integer NOT NULL PRIMARY KEY,
  no integer default NULL,
  sort integer default NULL,
  name text default NULL,
  title text default NULL,
  description text default NULL,
  contents text default NULL,
  page_category_id integer default NULL,
  theme text default NULL,
  status boolean default NULL,
  url text default NULL,
  modified datetime default NULL,
  created datetime default NULL
);

--
-- テーブルのデータをダンプしています bc_pages
--

INSERT into bc_pages (no, sort, name, title, description, contents, page_category_id, theme, status, url, created, modified) VALUES
 ('1','1','index',null,null,'<?php echo $html->css(''top'',null,null,false) ?>


<div id="news" class="clearfix">
<div class="news" style="margin-right:28px;">
<h2 id="newsHead01">NEWS RELEASE</h2>
<div class="body">
<script type="text/javascript" src="<?php $baser->root() ?>feed/ajax/1"></script>
</div>
</div>


<div class="news">
<h2 id="newsHead02">BaserCMS NEWS</h2>
<div class="body">
<script type="text/javascript" src="<?php $baser->root() ?>feed/ajax/2"></script>
</div>
</div>
</div>',null,'demo','1', '/index.html', datetime('now', 'localtime'), datetime('now', 'localtime'));

INSERT into bc_pages (no, sort, name, title, description, contents, page_category_id, theme, status, url, created, modified) VALUES
 ('2','2','about','会社案内','BaserCMS inc.の会社案内ページ','<h2 class="contents-head">会社案内</h2>

<h3 class="contents-head">会社データ</h3>

<div class="section">
<table class="row-table-01" cellspacing="0" cellpadding="0">
<tr><th width="150">会社名</th><td>BaserCMS inc.  [デモ]</td></tr>
<tr><th>設立</th><td>2009年11月</td></tr>
<tr><th>所在地</th><td>福岡県福岡市博多区博多駅前（ダミー）</td></tr>
<tr><th>事業内容</th><td>インターネットサービス業（ダミー）<br />
WEBサイト制作事業（ダミー）<br />
WEBシステム開発事業（ダミー）</td></tr>
</table>
</div>


<h3 class="contents-head">アクセスマップ</h3>
<?php $baser->element(''googlemaps'') ?>
',null,'demo','1', '/about.html', datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT into bc_pages (no, sort, name, title, description, contents, page_category_id, theme, status, url, created, modified) VALUES
 ('3','3','service','サービス','BaserCMS inc.のサービス紹介ページ。','<h2 class="contents-head">サービス</h2>

<div class="section">
<p>
サービスの案内文がはります。サービスの案内文がはります。サービスの案内文がはります。
サービスの案内文がはります。サービスの案内文がはります。サービスの案内文がはります。
サービスの案内文がはります。サービスの案内文がはります。サービスの案内文がはります。
サービスの案内文がはります。サービスの案内文がはります。サービスの案内文がはります。
</p>
</div>

<div class="section">
<p>
サービスの案内文がはります。サービスの案内文がはります。サービスの案内文がはります。
サービスの案内文がはります。サービスの案内文がはります。サービスの案内文がはります。
サービスの案内文がはります。サービスの案内文がはります。サービスの案内文がはります。
サービスの案内文がはります。サービスの案内文がはります。サービスの案内文がはります。
</p>
</div>

<div class="section">
<p>
サービスの案内文がはります。サービスの案内文がはります。サービスの案内文がはります。
サービスの案内文がはります。サービスの案内文がはります。サービスの案内文がはります。
サービスの案内文がはります。サービスの案内文がはります。サービスの案内文がはります。
サービスの案内文がはります。サービスの案内文がはります。サービスの案内文がはります。
</p>
</div>',null,'demo','1', '/service.html', datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT into bc_pages (no, sort, name, title, description, contents, page_category_id, theme, status, url, created, modified) VALUES
 ('4','4','sitemap','サイトマップ','BaserCMS inc.のサイトマップページ','<h2 class="contents-head">
	サイトマップ</h2>
<h3 class="contents-head">
	公開ページ</h3>
<ul class="section">
	<li>
<?php echo $html->link(''ホーム'',''/'') ?>	</li>
	<li>
<?php echo $html->link(''会社案内'',''/about.html'') ?>	</li>
	<li>
<?php echo $html->link(''サービス'',''/service.html'') ?>	</li>
	<li>
<?php echo $html->link(''新着情報'',''/news/'') ?>	</li>
	<li>
<?php echo $html->link(''お問い合わせ'',''/contact/index'') ?>	</li>
	<li>
<?php echo $html->link(''サイトマップ'',''/sitemap.html'') ?>	</li>
</ul>
<h3 class="contents-head">
	非公開ページ</h3>
<ul class="section">
<li>
	<?php echo $html->link(''管理者ログイン'',''/admin/users/login'') ?>	</li>
</ul>
<p class="customize-navi corner10">
	<small>公開する際には非公開ページは削除をおすすめします。</small>
</p>',null,'demo','1', '/service.html', datetime('now', 'localtime'), datetime('now', 'localtime'));


-- --------------------------------------------------------

--
-- テーブルの構造 bc_page_categories
--

CREATE TABLE bc_page_categories (
  id integer NOT NULL PRIMARY KEY,
  no integer default NULL,
  parent_id integer default NULL,
  lft integer default NULL,
  rght integer default NULL,
  name text default NULL,
  title text default NULL,
  sort integer default NULL,
  theme text default NULL,
  modified datetime default NULL,
  created datetime default NULL
);