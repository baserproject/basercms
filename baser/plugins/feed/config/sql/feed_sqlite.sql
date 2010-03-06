-- SVN FILE: $Id$
--
-- BaserCMS フィードプラグイン SQL（SQLite）
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
-- テーブルの構造 bc__feed_configs
--

CREATE TABLE bc__feed_configs (
  id integer NOT NULL PRIMARY KEY,
  name text default NULL,
  feed_title_index text default NULL,
  category_index text default NULL,
  template text default NULL,
  display_number integer default NULL,
  created text default NULL,
  modified text default NULL
);

--
-- テーブルのデータをダンプしています bc__feed_configs
--

INSERT INTO bc__feed_configs (name, feed_title_index, category_index, template, display_number, created, modified) VALUES
('新着情報', NULL, NULL, 'default', 5, datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__feed_configs (name, feed_title_index, category_index, template, display_number, created, modified) VALUES
('BaserCMSニュース', NULL, NULL, 'default', 5, datetime('now', 'localtime'), datetime('now', 'localtime'));

-- --------------------------------------------------------

--
-- テーブルの構造 bc__feed_details
--

CREATE TABLE bc__feed_details (
  id integer NOT NULL PRIMARY KEY,
  feed_config_id integer default NULL,
  name text default NULL,
  url text default NULL,
  category_filter text default NULL,
  cache_time text default NULL,
  created text default NULL,
  modified text default NULL
);

--
-- テーブルのデータをダンプしています bc__feed_details
--

INSERT INTO bc__feed_details (feed_config_id, name, url, category_filter, cache_time, created, modified) VALUES
(1, '新着情報', '/news/index.rss', '', '+30 minutes', datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__feed_details (feed_config_id, name, url, category_filter, cache_time, created, modified) VALUES
(2, 'BaserCMSニュース', 'http://basercms.net/news/index.rss', '', '+30 minutes', datetime('now', 'localtime'), datetime('now', 'localtime'));
