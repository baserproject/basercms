-- SVN FILE: $Id$
--
-- BaserCMS メールプラグイン SQL（PostgreSQL）
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
-- テーブルの構造 "bc__mail_configs"
--

CREATE SEQUENCE bc__mail_configs_id_seq;
CREATE TABLE "public"."bc__mail_configs" (
  "id" int8 NOT NULL default nextval('bc__mail_configs_id_seq'),
  "site_name" varchar(50) default NULL,
  "site_url" varchar(255) default NULL,
  "site_email" varchar(50) default NULL,
  "site_tel" varchar(20) default NULL,
  "site_fax" varchar(20) default NULL,
  "encode" varchar(20) default NULL,
  "smtp_host" varchar(20) default NULL,
  "smtp_username" varchar(20) default NULL,
  "smtp_password" varchar(50) default NULL,
  "created" timestamp default NULL,
  "modified" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc__mail_configs" SET WITHOUT CLUSTER;

--
-- テーブルのデータをダンプしています "bc__mail_configs"
--

INSERT INTO "bc__mail_configs" ("site_name", "site_url", "site_email", "site_tel", "site_fax", "encode", "smtp_host", "smtp_username", "smtp_password", "created", "modified") VALUES
('BaserCMS - Based Website Development Project -', 'http://basercms.net/', 'info@basercms.net', NULL, NULL, 'ISO-2022-JP', NULL, NULL, NULL, NOW(), NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 "bc__mail_contents"
--

CREATE SEQUENCE bc__mail_contents_id_seq;
CREATE TABLE "public"."bc__mail_contents" (
  "id" int8 NOT NULL default nextval('bc__mail_contents_id_seq'),
  "name" varchar(20) default NULL,
  "title" varchar(50) default NULL,
  "sender_1" varchar(255) default NULL,
  "sender_2" varchar(255) default NULL,
  "sender_name" varchar(50) default NULL,
  "subject_user" varchar(50) default NULL,
  "subject_admin" varchar(50) default NULL,
  "layout_template" varchar(20) default NULL,
  "form_template" varchar(20) default NULL,
  "mail_template" varchar(20) default NULL,
  "redirect_url" varchar(255) default NULL,
  "status" int2 default NULL,
  "auth_captcha" boolean default NULL,
  "widget_area" int4 default NULL,
  "created" timestamp default NULL,
  "modified" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc__mail_contents" SET WITHOUT CLUSTER;

--
-- テーブルのデータをダンプしています "bc__mail_contents"
--

INSERT INTO "bc__mail_contents" ("name", "title", "sender_1", "sender_2", "sender_name", "subject_user", "subject_admin", "layout_template", "form_template", "mail_template", "redirect_url", "status", "auth_captcha", "widget_area", "created", "modified") VALUES
('contact', 'お問い合わせ',  NULL, NULL, 'BaserCMS - Based Website Development Project -', '【BaserCMS】お問い合わせ頂きありがとうございます。', '【BaserCMS】お問い合わせを受け付けました', 'default', 'default', 'mail_default', 'http://basercms.net/', 1, true, NULL, NOW(), NOW());
-- --------------------------------------------------------

--
-- テーブルの構造 "bc__mail_fields"
--

CREATE SEQUENCE bc__mail_fields_id_seq;
CREATE TABLE "public"."bc__mail_fields" (
  "id" int8 NOT NULL default nextval('bc__mail_fields_id_seq'),
  "mail_content_id" int8 default NULL,
  "no" int8 default NULL,
  "name" varchar(255) default NULL,
  "field_name" varchar(255) default NULL,
  "type" varchar(255) default NULL,
  "head" varchar(255) default NULL,
  "attention" varchar(255) default NULL,
  "before_attachment" varchar(255) default NULL,
  "after_attachment" varchar(255) default NULL,
  "source" varchar(255) default NULL,
  "size" int8 default NULL,
  "rows" int8 default NULL,
  "maxlength" int8 default NULL,
  "options" varchar(255) default NULL,
  "class" varchar(255) default NULL,
  "separator" varchar(20) default NULL,
  "default_value" varchar(255) default NULL,
  "description" varchar(255) default NULL,
  "group_field" varchar(255) default NULL,
  "group_valid" varchar(255) default NULL,
  "valid" varchar(255) default NULL,
  "valid_ex" varchar(255) default NULL,
  "auto_convert" varchar(255) default NULL,
  "not_empty" boolean default NULL,
  "use_field" boolean default NULL,
  "no_send" boolean default NULL,
  "sort" int8 default NULL,
  "created" timestamp default NULL,
  "modified" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc__mail_fields" SET WITHOUT CLUSTER;

--
-- テーブルのデータをダンプしています "bc__mail_fields"
--

INSERT INTO "bc__mail_fields" ( "mail_content_id", "no", "name", "field_name", "type", "head", "attention", "before_attachment", "after_attachment", "source", "size", "rows", "maxlength", "options", "class", "separator", "default_value", "description", "group_field", "group_valid", "valid", "valid_ex", "auto_convert", "not_empty", "use_field", "no_send", "sort", "created", "modified") VALUES
(1, 1,'姓漢字', 'name_1', 'text', 'お名前', '', '[姓]', '', '', 8, 0, 255, '', '', '', '', '', 'name', 'name', 'VALID_NOT_EMPTY', '', '', true, true, false, 1, NOW(), NOW()),
(1, 2,'名漢字', 'name_2', 'text', 'お名前', NULL, '[名]', NULL, NULL, 8, 0, 255, NULL, NULL, NULL, NULL, NULL, 'name', 'name', 'VALID_NOT_EMPTY', NULL, NULL, true, true, false, 2, NOW(), NOW()),
(1, 3,'姓カナ', 'name_kana_1', 'text', 'フリガナ', '', '[姓]', '', '', 8, 0, 255, '', '', '', '', '', 'name_kana', 'name_kana', '', '', '', false, true, false, 3, NOW(), NOW()),
(1, 4,'名カナ', 'name_kana_2', 'text', 'フリガナ', '', '[名]', '', '', 8, 0, 255, '', '', '', '', '', 'name_kana', 'name_kana', '', '', '', false, true, false, 4, NOW(), NOW()),
(1, 5,'性別', 'sex', 'radio', '性別', '', '', '', '男性|女性', 0, 0, 0, '', '', '', '', '', '', '', '', '', '', false, true, false, 5, NOW(), NOW()),
(1, 6,'メールアドレス', 'email_1', 'text', 'メールアドレス', '', '', '<br />', '', 25, 0, 50, '', '', '', '', '確認の為、２回入力して下さい。', 'email', 'email', 'VALID_EMAIL', 'VALID_EMAIL_CONFIRM', '', true, true, false, 6, NOW(), NOW()),
(1, 7,'メールアドレス確認', 'email_2', 'text', 'メールアドレス（確認）', '[確認]', '', '', '', 25, 0, 50, '', '', '', '', '', 'email', 'email', 'VALID_EMAIL', 'VALID_EMAIL_CONFIRM', '', true, true, true, 7, NOW(), NOW()),
(1, 8,'電話番号１', 'tel_1', 'text', '電話番号', '', '', '-', '', 5, 0, 5, '', '', '', '', '', 'tel', 'tel', '', 'VALID_GROUP_COMPLATE', 'CONVERT_HANKAKU', false, true, false, 8, NOW(), NOW()),
(1, 9,'電話番号２', 'tel_2', 'text', '電話番号', '', '', '-', '', 5, 0, 5, '', '', '', '', '', 'tel', 'tel', '', 'VALID_GROUP_COMPLATE', 'CONVERT_HANKAKU', false, true, false, 9, NOW(), NOW()),
(1, 10,'電話番号３', 'tel_3', 'text', '電話番号', '', '', '', '', 5, 0, 5, '', '', '', '', '', 'tel', 'tel', '', 'VALID_GROUP_COMPLATE', 'CONVERT_HANKAKU', false, true, false, 10, NOW(), NOW()),
(1, 11,'郵便番号', 'zip', 'autozip', '住所', '[半角数字]<br />', '〒', '', 'address_1|address_2', 10, 0, 8, '', '', '', '', '', 'address', '', '', '', 'CONVERT_HANKAKU', false, true, false, 11, NOW(), NOW()),
(1, 12,'都道府県', 'address_1', 'pref', '住所', '', '', '<br />', '', 0, 0, 0, '', '', '', '', '', 'address', '', '', '', '', false, true, false, 12, NOW(), NOW()),
(1, 13,'市区町村・番地', 'address_2', 'text', '住所', '', '', '<br />', '', 30, 0, 200, '', '', '', '', '', 'address', '', '', '', '', false, true, false, 13, NOW(), NOW()),
(1, 14,'建物名', 'address_3', 'text', '住所', '', '', '', '', 30, 0, 200, '', '', '', '', '', 'address', '', '', '', '', false, true, false, 14, NOW(), NOW()),
(1, 15,'お問い合わせ項目', 'category', 'multi_check', 'お問い合わせ項目', '', '', '', '資料請求|問い合わせ|その他', 0, 0, 0, '', '', '', '', '', '', '', '', 'VALID_NOT_UNCHECKED', '', true, true, false, 15, NOW(), NOW()),
(1, 16,'お問い合わせ内容', 'message', 'textarea', 'お問い合わせ内容', '', '', '', '', 48, 12, NULL, '', '', '', '', '', '', '', '', '', '', false, true, false, 16, NOW(), NOW()),
(1, 17,'ルート', 'root', 'select', 'どうやってこのサイトをお知りになりましたか？', '', '', '<br />', '検索エンジン|web広告|紙面広告|求人案内|その他', 0, 0, 0, '', '', '', '', '', 'root', '', 'VALID_NOT_EMPTY', '', '', true, true, false, 17, NOW(), NOW()),
(1, 18,'ルートその他', 'root_etc', 'text', '', '<br />[その他を選択された場合は内容をご入力下さい。]', '', '', '', 30, 0, 50, '', '', '', '', '', 'root', '', '', '', '', false, true, false, 18, NOW(), NOW());

-- --------------------------------------------------------

--
-- テーブルの構造 "bc__messages"
--

CREATE SEQUENCE bc__messages_id_seq;
CREATE TABLE "public"."bc__messages" (
  "id" int8 NOT NULL default nextval('bc__messages_id_seq'),
  "created" timestamp default NULL,
  "modified" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc__messages" SET WITHOUT CLUSTER;

--
-- テーブルのデータをダンプしています "bc__messages"
--

-- --------------------------------------------------------

--
-- テーブルの構造 "bc__contact_messages"
--

CREATE SEQUENCE bc__contact_messages_id_seq;
CREATE TABLE "public"."bc__contact_messages" (
  "id" int8 NOT NULL default nextval('bc__contact_messages_id_seq'),
  "name_1" varchar(255) default NULL,
  "name_2" varchar(255) default NULL,
  "name_kana_1" varchar(255) default NULL,
  "name_kana_2" varchar(255) default NULL,
  "sex" varchar(255) default NULL,
  "email_1" varchar(255) default NULL,
  "email_2" varchar(255) default NULL,
  "tel_1" varchar(255) default NULL,
  "tel_2" varchar(255) default NULL,
  "tel_3" varchar(255) default NULL,
  "zip" varchar(255) default NULL,
  "address_1" varchar(255) default NULL,
  "address_2" varchar(255) default NULL,
  "address_3" varchar(255) default NULL,
  "category" varchar(255) default NULL,
  "message" text,
  "root" varchar(255) default NULL,
  "root_etc" varchar(255) default NULL,
  "created" timestamp default NULL,
  "modified" timestamp default NULL,
  PRIMARY KEY  ("id")
) WITHOUT OIDS;
ALTER table "public"."bc__contact_messages" SET WITHOUT CLUSTER;

--
-- テーブルのデータをダンプしています "bc__contact_messages"
--

