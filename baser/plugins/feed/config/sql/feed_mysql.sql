-- SVN FILE: $Id$
--
-- BaserCMS フィードプラグイン SQL（MySQL）
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
-- テーブルの構造 `bc__feed_configs`
--

CREATE TABLE IF NOT EXISTS `bc__feed_configs` (
  `id` int(8) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `feed_title_index` varchar(255) default NULL,
  `category_index` varchar(255) default NULL,
  `template` varchar(50) default NULL,
  `display_number` int(3) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- テーブルのデータをダンプしています `bc__feed_configs`
--

INSERT INTO `bc__feed_configs` (`name`, `feed_title_index`, `category_index`, `template`, `display_number`, `created`, `modified`) VALUES
('新着情報', NULL, NULL, 'default', 5, NOW(), NOW());
INSERT INTO `bc__feed_configs` (`name`, `feed_title_index`, `category_index`, `template`, `display_number`, `created`, `modified`) VALUES
('BaserCMSニュース', NULL, NULL, 'default', 5, NOW(), NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 `bc__feed_details`
--

CREATE TABLE IF NOT EXISTS `bc__feed_details` (
  `id` int(8) NOT NULL auto_increment,
  `feed_config_id` int(8) default NULL,
  `name` varchar(50) default NULL,
  `url` varchar(255) default NULL,
  `category_filter` varchar(255) default NULL,
  `cache_time` varchar(20) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- テーブルのデータをダンプしています `bc__feed_details`
--

INSERT INTO `bc__feed_details` (`feed_config_id`, `name`, `url`, `category_filter`, `cache_time`, `created`, `modified`) VALUES
(1, '新着情報', '/news/index.rss', '', '+30 minutes', NOW(), NOW());
INSERT INTO `bc__feed_details` (`feed_config_id`, `name`, `url`, `category_filter`, `cache_time`, `created`, `modified`) VALUES
(2, 'BaserCMSニュース', 'http://basercms.net/news/index.rss', '', '+30 minutes', NOW(), NOW());
