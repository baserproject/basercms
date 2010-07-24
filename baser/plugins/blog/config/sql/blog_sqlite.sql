-- SVN FILE: $Id$
--
-- BaserCMS ブログプラグイン SQL（SQLite）
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
-- テーブルの構造 bc__blog_categories
--

CREATE TABLE bc__blog_categories (
  id integer NOT NULL PRIMARY KEY,
  blog_content_id integer default NULL,
  no integer default NULL,
  name text default NULL,
  title text default NULL,
  status integer default NULL,
  parent_id integer default NULL,
  lft integer default NULL,
  rght integer default NULL,
  created text default NULL,
  modified text default NULL
);

--
-- テーブルのデータをダンプしています bc__blog_categories
--

INSERT INTO bc__blog_categories (blog_content_id, no, name, title, status, parent_id, lft, rght, created, modified) VALUES
(1, 1, 'release', 'プレスリリース', 1, NULL, 1, 2, datetime('now', 'localtime'), datetime('now', 'localtime'));

-- --------------------------------------------------------

--
-- テーブルの構造 bc__blog_configs
--

CREATE TABLE bc__blog_configs (
  id integer NOT NULL PRIMARY KEY
);

--
-- テーブルのデータをダンプしています bc__blog_configs
--


-- --------------------------------------------------------

--
-- テーブルの構造 bc__blog_contents
--

CREATE TABLE bc__blog_contents (
  id integer NOT NULL PRIMARY KEY,
  name text default NULL,
  title text default NULL,
  description text default NULL,
  layout text default NULL,
  template text default NULL,
  status integer default NULL,
  list_count integer default NULL,
  list_direction text default NULL,
  feed_count integer default NULL,
  comment_use boolean default NULL,
  comment_approve boolean default NULL,
  created text default NULL,
  modified text default NULL
);

--
-- テーブルのデータをダンプしています bc__blog_contents
--

INSERT INTO bc__blog_contents (name, title, description, layout, template, status, list_count, list_direction, feed_count, comment_use, comment_approve, created, modified) VALUES
('news', 'ニュースリリース', 'Baser CMS inc. [デモ]の最新のニュースリリースをお届けします。', 'default', 'default', 1, 10, 'DESC', 10, 1, 0, datetime('now', 'localtime'), datetime('now', 'localtime'));

-- --------------------------------------------------------

--
-- テーブルの構造 bc__blog_posts
--

CREATE TABLE bc__blog_posts (
  id integer NOT NULL PRIMARY KEY,
  blog_content_id integer NOT NULL,
  no integer NOT NULL,
  name text default NULL,
  content text,
  detail text,
  blog_category_id integer default NULL,
  user_id integer default NULL,
  status integer default NULL,
  posts_date text default NULL,
  created text default NULL,
  modified text default NULL
);

--
-- テーブルのデータをダンプしています bc__blog_posts
--

INSERT INTO bc__blog_posts (blog_content_id, no, name, content, detail, blog_category_id, user_id, status, posts_date, created, modified) VALUES
(1, 1, 'ホームページをオープンしました。', 'テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。<br /><br />', 'テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。<br />', 1, 1, 1, datetime('now', 'localtime'), datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__blog_posts (blog_content_id, no, name, content, detail, blog_category_id, user_id, status, posts_date, created, modified) VALUES
(1, 2, '新商品を販売を開始しました。','新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。<br /><br />新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。<br />', '詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br /><br />詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br /><br />詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br /><br />', '1' , '1', '1', datetime('now', 'localtime'), datetime('now', 'localtime'), datetime('now', 'localtime'));


--
-- テーブルの構造 `bc__blog_comments`
--

CREATE TABLE bc__blog_comments (
  id integer NOT NULL PRIMARY KEY,
  blog_content_id integer NOT NULL,
  blog_post_id integer NOT NULL,
  no integer NOT NULL,
  status boolean default NULL,
  name text default NULL,
  email text default NULL,
  url text default NULL,
  message text,
  created text default NULL,
  modified text default NULL
);

--
-- テーブルのデータをダンプしています "bc__blog_comments"
--

INSERT INTO bc__blog_comments (blog_content_id,blog_post_id,no,status,name,email,url,message,created,modified) VALUES
(1, 1, 1, 1, 'BaserCMS', '', 'http://basercms.net', 'ホームページの開設おめでとうございます。', datetime('now', 'localtime'), datetime('now', 'localtime'));