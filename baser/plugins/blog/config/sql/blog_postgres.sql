-- SVN FILE: $Id$
--
-- BaserCMS ブログプラグイン SQL（PostgreSQL）
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
-- テーブルの構造 "bc__blog_categories"
--

CREATE SEQUENCE bc__blog_categories_id_seq;
CREATE TABLE "public"."bc__blog_categories" (
  "id" int8 NOT NULL default nextval('bc__blog_categories_id_seq'),
  "blog_content_id" int8 NOT NULL,
  "no" int8 NOT NULL,
  "name" varchar(50) default NULL,
  "title" varchar(50) default NULL,
  "status" int2 default NULL,
  "parent_id" int8 default NULL,
  "lft" int8 default NULL,
  "rght" int8 default NULL,
  "created" timestamp default NULL,
  "modified" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc__blog_categories" SET WITHOUT CLUSTER;


--
-- テーブルのデータをダンプしています "bc__blog_categories"
--

INSERT INTO "bc__blog_categories" ("blog_content_id", "no", "name", "title", "status", "parent_id", "lft", "rght", "created", "modified") VALUES
(1, 1, 'release', 'プレスリリース', 1, NULL, 1, 2, NOW(), NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 "bc__blog_configs"
--

CREATE SEQUENCE bc__blog_configs_id_seq;
CREATE TABLE "public"."bc__blog_configs" (
  "id" int2 NOT NULL default nextval('bc__blog_configs_id_seq'),
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc__blog_configs" SET WITHOUT CLUSTER;


--
-- テーブルのデータをダンプしています "bc__blog_configs"
--


-- --------------------------------------------------------

--
-- テーブルの構造 "bc__blog_contents"
--

CREATE SEQUENCE bc__blog_contents_id_seq;
CREATE TABLE "public"."bc__blog_contents" (
  "id" int8 NOT NULL default nextval('bc__blog_contents_id_seq'),
  "name" varchar(50) default NULL,
  "title" varchar(255) default NULL,
  "description" varchar(255) default NULL,
  "layout" varchar(20) default NULL,
  "template" varchar(20) default NULL,
  "theme" varchar(20) default NULL,
  "status" boolean default NULL,
  "list_count" int4 default NULL,
  "list_direction" varchar(4) default NULL,
  "comment_use" boolean default NULL,
  "comment_approve" boolean default NULL,
  "created" timestamp default NULL,
  "modified" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc__blog_contents" SET WITHOUT CLUSTER;


--
-- テーブルのデータをダンプしています "bc__blog_contents"
--

INSERT INTO "bc__blog_contents" ("name", "title", "description", "layout", "template", "theme", "status", "list_count", "list_direction", "comment_use", "comment_approve", "created", "modified") VALUES
('news', 'ニュースリリース', 'Baser CMS inc. [デモ]の最新のニュースリリースをお届けします。', 'default', 'default', '', true, 10, 'DESC', true, false, NOW(), NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 "bc__blog_posts"
--

CREATE SEQUENCE bc__blog_posts_id_seq;
CREATE TABLE "public"."bc__blog_posts" (
  "id" int8 NOT NULL default nextval('bc__blog_posts_id_seq'),
  "blog_content_id" int8 NOT NULL,
  "no" int8 NOT NULL,
  "name" varchar(50) default NULL,
  "content" text,
  "detail" text,
  "blog_category_id" int8 default NULL,
  "user_id" int8 default NULL,
  "status" int2 default NULL,
  "posts_date" timestamp default NULL,
  "created" timestamp default NULL,
  "modified" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc__blog_posts" SET WITHOUT CLUSTER;


--
-- テーブルのデータをダンプしています "bc__blog_posts"
--

INSERT INTO "bc__blog_posts" ("blog_content_id", "no", "name", "content", "detail", "blog_category_id", "user_id", "status", "posts_date", "created", "modified") VALUES
(1, 1, 'ホームページをオープンしました。', 'テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。<br /><br />', 'テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。テキストが入ります。<br />', 1, 1, 1, NOW(), NOW(), NOW()),
(1, 2, '新商品を販売を開始しました。', '新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。<br /><br />新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。新商品を販売を開始しました。<br />', '詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br /><br />詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br /><br />詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。詳細が入ります。<br /><br />', '1' , '1', '1', NOW(), NOW(), NOW());


--
-- テーブルの構造 `bc__blog_comments`
--

CREATE SEQUENCE bc__blog_comments_id_seq;
CREATE TABLE "public"."bc__blog_comments" (
  "id" int8 NOT NULL default nextval('bc__blog_comments_id_seq'),
  "blog_content_id" int8 default NULL,
  "blog_post_id" int8 default NULL,
  "no" int8 default NULL,
  "status" boolean default NULL,
  "name" varchar(50) default NULL,
  "email" varchar(255) default NULL,
  "url" varchar(255) default NULL,
  "message" text,
  "created" timestamp default NULL,
  "modified" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc__blog_comments" SET WITHOUT CLUSTER;

--
-- テーブルのデータをダンプしています "bc__blog_comments"
--

INSERT INTO "bc__blog_comments" ("blog_content_id","blog_post_id","no","status","name","email","url","message","created","modified") VALUES
(1, 1, 1, true, 'BaserCMS', '', 'http://basercms.net', 'ホームページの開設おめでとうございます。', NOW(), NOW());