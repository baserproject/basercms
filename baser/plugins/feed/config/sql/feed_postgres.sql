-- SVN FILE: $Id$
--
-- BaserCMS フィードプラグイン SQL（PostgreSQL）
--
-- BaserCMS :  Based Website Development Project <http://basercms.net>
-- Copyright 2008 - 2009, Catchup, Inc.
--								9-5 nagao 3-chome, fukuoka-shi
--								fukuoka, Japan 814-0123
--
-- @copyright		Copyright 2008 - 2009, Catchup, Inc.
-- @link			http://basercms.net BaserCMS Project
-- @version			$Revision$
-- @modifiedby		$LastChangedBy$
-- @lastmodified	$Date$
-- @license			http://basercms.net/license/index.html

--
-- テーブルの構造 "bc__feed_configs"
--

CREATE SEQUENCE bc__feed_configs_id_seq;
CREATE TABLE "public"."bc__feed_configs" (
  "id" int8 NOT NULL default nextval('bc__feed_configs_id_seq'),
  "name" varchar(50) default NULL,
  "feed_title_index" varchar(255) default NULL,
  "category_index" varchar(255) default NULL,
  "template" varchar(50) default NULL,
  "display_number" int4 default NULL,
  "created" timestamp default NULL,
  "modified" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc__feed_configs" SET WITHOUT CLUSTER;

--
-- テーブルのデータをダンプしています "bc__feed_configs"
--

INSERT INTO "bc__feed_configs" ("name", "feed_title_index", "category_index", "template", "display_number", "created", "modified") VALUES
('新着情報', NULL, NULL, 'default', 5, NOW(), NOW());
INSERT INTO "bc__feed_configs" ("name", "feed_title_index", "category_index", "template", "display_number", "created", "modified") VALUES
('BaserCMSニュース', NULL, NULL, 'default', 5, NOW(), NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 "bc__feed_details"
--

CREATE SEQUENCE bc__feed_details_id_seq;
CREATE TABLE "public"."bc__feed_details" (
  "id" int8 NOT NULL default nextval('bc__feed_details_id_seq'),
  "feed_config_id" int8 default NULL,
  "name" varchar(50) default NULL,
  "url" varchar(255) default NULL,
  "category_filter" varchar(255) default NULL,
  "cache_time" varchar(20) default NULL,
  "created" timestamp default NULL,
  "modified" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc__feed_details" SET WITHOUT CLUSTER;

--
-- テーブルのデータをダンプしています "bc__feed_details"
--

INSERT INTO "bc__feed_details" ("feed_config_id", "name", "url", "category_filter", "cache_time", "created", "modified") VALUES
(1, '新着情報', '/news/index.rss', '', '+30 minutes', NOW(), NOW());
INSERT INTO "bc__feed_details" ("feed_config_id", "name", "url", "category_filter", "cache_time", "created", "modified") VALUES
(2, 'BaserCMSニュース', 'http://basercms.net/news/index.rss', '', '+30 minutes', NOW(), NOW());
