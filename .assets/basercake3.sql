/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50542
 Source Host           : localhost
 Source Database       : basercake3

 Target Server Type    : MySQL
 Target Server Version : 50542
 File Encoding         : utf-8

 Date: 05/03/2017 20:00:35 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `content_folders`
-- ----------------------------
DROP TABLE IF EXISTS `content_folders`;
CREATE TABLE `content_folders` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `folder_template` varchar(255) DEFAULT NULL,
  `page_template` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `content_folders`
-- ----------------------------
BEGIN;
INSERT INTO `content_folders` VALUES ('1', '', '', '2017-05-03 14:21:45', '2017-05-03 14:51:52');
COMMIT;

-- ----------------------------
--  Table structure for `contents`
-- ----------------------------
DROP TABLE IF EXISTS `contents`;
CREATE TABLE `contents` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `name` text,
  `plugin` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `entity_id` int(8) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
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
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `contents`
-- ----------------------------
BEGIN;
INSERT INTO `contents` VALUES ('1', '', 'Core', 'ContentFolder', '1', '/', '0', null, null, null, '1', '18', '0', 'ryuring.com', '', '', '1', 'default', '1', null, null, '1', null, null, '0', '2017-05-03 14:22:08', '2017-05-03 14:22:08', '1', null, '0', '0', '0', '2016-07-29 18:02:53', '2017-05-03 14:51:52'), ('2', 'index', 'Core', 'Page', '1', '/index', '0', null, null, '1', '14', '15', '1', 'トップページ', '', '', '1', '', '1', null, null, '1', null, null, '0', '2017-05-03 14:22:08', '2017-05-03 14:22:08', '0', null, '0', '0', '0', '2016-07-29 18:13:03', '2017-05-03 15:12:27');
COMMIT;

-- ----------------------------
--  Table structure for `pages`
-- ----------------------------
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `contents` mediumtext,
  `draft` text,
  `page_template` varchar(255) DEFAULT NULL,
  `code` text,
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `pages`
-- ----------------------------
BEGIN;
INSERT INTO `pages` VALUES ('1', '<p>座右の銘「やるときゃやる」</p>\r\n', '', '', '', '2017-05-03 15:12:27', '2015-06-26 20:34:06');
COMMIT;

-- ----------------------------
--  Table structure for `user_groups`
-- ----------------------------
DROP TABLE IF EXISTS `user_groups`;
CREATE TABLE `user_groups` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `auth_prefix` varchar(20) DEFAULT NULL,
  `use_admin_globalmenu` tinyint(1) DEFAULT NULL,
  `default_favorites` text,
  `use_move_contents` tinyint(1) DEFAULT '0',
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `user_groups`
-- ----------------------------
BEGIN;
INSERT INTO `user_groups` VALUES ('1', 'admins', 'システム管理', 'admin', '1', 'YTo3OntpOjA7YToyOntzOjQ6Im5hbWUiO3M6MjE6IuOCs+ODs+ODhuODs+ODhOeuoeeQhiI7czozOiJ1cmwiO3M6MjE6Ii9hZG1pbi9jb250ZW50cy9pbmRleCI7fWk6MTthOjI6e3M6NDoibmFtZSI7czoxODoi5paw552A5oOF5aCx566h55CGIjtzOjM6InVybCI7czozMDoiL2FkbWluL2Jsb2cvYmxvZ19wb3N0cy9pbmRleC8xIjt9aToyO2E6Mjp7czo0OiJuYW1lIjtzOjMwOiLmlrDnnYDmg4XloLHjgrPjg6Hjg7Pjg4jkuIDopqciO3M6MzoidXJsIjtzOjMzOiIvYWRtaW4vYmxvZy9ibG9nX2NvbW1lbnRzL2luZGV4LzEiO31pOjM7YToyOntzOjQ6Im5hbWUiO3M6MjQ6IuOBiuWVj+OBhOWQiOOCj+OBm+ioreWumiI7czozOiJ1cmwiO3M6MzE6Ii9hZG1pbi9tYWlsL21haWxfZmllbGRzL2luZGV4LzEiO31pOjQ7YToyOntzOjQ6Im5hbWUiO3M6MjQ6IuOBiuWVj+OBhOWQiOOCj+OBm+S4gOimpyI7czozOiJ1cmwiO3M6MzM6Ii9hZG1pbi9tYWlsL21haWxfbWVzc2FnZXMvaW5kZXgvMSI7fWk6NTthOjI6e3M6NDoibmFtZSI7czoyNDoi44Ki44OD44OX44Ot44O844OJ566h55CGIjtzOjM6InVybCI7czozMToiL2FkbWluL3VwbG9hZGVyL3VwbG9hZGVyX2ZpbGVzLyI7fWk6NjthOjI6e3M6NDoibmFtZSI7czoxNToi44Kv44Os44K444OD44OIIjtzOjM6InVybCI7czoyMDoiamF2YXNjcmlwdDpjcmVkaXQoKTsiO319', '1', '2016-08-16 19:47:07', '2015-06-26 20:34:07'), ('2', 'operators', 'サイト運営', 'admin', '0', 'YTo3OntpOjA7YToyOntzOjQ6Im5hbWUiO3M6MjE6IuOCs+ODs+ODhuODs+ODhOeuoeeQhiI7czozOiJ1cmwiO3M6MjE6Ii9hZG1pbi9jb250ZW50cy9pbmRleCI7fWk6MTthOjI6e3M6NDoibmFtZSI7czoxODoi5paw552A5oOF5aCx566h55CGIjtzOjM6InVybCI7czozMDoiL2FkbWluL2Jsb2cvYmxvZ19wb3N0cy9pbmRleC8xIjt9aToyO2E6Mjp7czo0OiJuYW1lIjtzOjMwOiLmlrDnnYDmg4XloLHjgrPjg6Hjg7Pjg4jkuIDopqciO3M6MzoidXJsIjtzOjMzOiIvYWRtaW4vYmxvZy9ibG9nX2NvbW1lbnRzL2luZGV4LzEiO31pOjM7YToyOntzOjQ6Im5hbWUiO3M6MjQ6IuOBiuWVj+OBhOWQiOOCj+OBm+ioreWumiI7czozOiJ1cmwiO3M6MzE6Ii9hZG1pbi9tYWlsL21haWxfZmllbGRzL2luZGV4LzEiO31pOjQ7YToyOntzOjQ6Im5hbWUiO3M6MjQ6IuOBiuWVj+OBhOWQiOOCj+OBm+S4gOimpyI7czozOiJ1cmwiO3M6MzM6Ii9hZG1pbi9tYWlsL21haWxfbWVzc2FnZXMvaW5kZXgvMSI7fWk6NTthOjI6e3M6NDoibmFtZSI7czoyNDoi44Ki44OD44OX44Ot44O844OJ566h55CGIjtzOjM6InVybCI7czozMToiL2FkbWluL3VwbG9hZGVyL3VwbG9hZGVyX2ZpbGVzLyI7fWk6NjthOjI6e3M6NDoibmFtZSI7czoxNToi44Kv44Os44K444OD44OIIjtzOjM6InVybCI7czoyMDoiamF2YXNjcmlwdDpjcmVkaXQoKTsiO319', '0', null, '2015-06-26 20:34:07');
COMMIT;

-- ----------------------------
--  Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `real_name_1` varchar(50) DEFAULT NULL,
  `real_name_2` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user_group_id` int(4) DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `users`
-- ----------------------------
BEGIN;
INSERT INTO `users` VALUES ('1', 'basercake3', '$2y$10$QJANUS5uCoqk0msOXu0MjeE8DindYSrv6zWkDdMSXvKU0dZd35BOu', 'basercake3', '', 'basercake3@example.com', '1', '', '2017-05-03 14:22:08', '2017-05-03 10:59:12');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
