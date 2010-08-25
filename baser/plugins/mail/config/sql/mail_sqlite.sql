-- SVN FILE: $Id$
--
-- BaserCMS メールプラグイン SQL（SQLite）
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
-- テーブルの構造 bc__mail_configs
--

CREATE TABLE bc__mail_configs (
  id integer NOT NULL PRIMARY KEY,
  site_name text default NULL,
  site_url text default NULL,
  site_email text default NULL,
  site_tel text default NULL,
  site_fax text default NULL,
  encode text default NULL,
  smtp_host text default NULL,
  smtp_username text default NULL,
  smtp_password text default NULL,
  created text default NULL,
  modified text default NULL
);

--
-- テーブルのデータをダンプしています bc__mail_configs
--

INSERT INTO bc__mail_configs (site_name, site_url, site_email, site_tel, site_fax, encode, smtp_host, smtp_username, smtp_password, created, modified) VALUES
('BaserCMS - Based Website Development Project -', 'http://basercms.net/', 'info@basercms.net', NULL, NULL, 'ISO-2022-JP', NULL, NULL, NULL, datetime('now', 'localtime'), datetime('now', 'localtime'));

-- --------------------------------------------------------

--
-- テーブルの構造 bc__mail_contents
--

CREATE TABLE bc__mail_contents (
  id integer NOT NULL PRIMARY KEY,
  name text default NULL,
  title text default NULL,
  sender_1 text default NULL,
  sender_2 text default NULL,
  sender_name text default NULL,
  subject_user text default NULL,
  subject_admin text default NULL,
  layout_template text default NULL,
  form_template text default NULL,
  mail_template text default NULL,
  redirect_url text default NULL,
  status integer default NULL,
  auth_captcha bloolan default NULL,
  widget_area integer default NULL,
  created text default NULL,
  modified text default NULL
);

--
-- テーブルのデータをダンプしています bc__mail_contents
--

INSERT INTO bc__mail_contents (name, title, sender_1, sender_2, sender_name, subject_user, subject_admin, layout_template, form_template, mail_template, redirect_url, status, auth_captcha, widget_area, created, modified) VALUES
('contact', 'お問い合わせ',  NULL, NULL, 'BaserCMS - Based Website Development Project -', '【BaserCMS】お問い合わせ頂きありがとうございます。', '【BaserCMS】お問い合わせを受け付けました', 'default', 'default', 'mail_default', 'http://basercms.net/', 1, 1, NULL, datetime('now', 'localtime'), datetime('now', 'localtime'));
-- --------------------------------------------------------

--
-- テーブルの構造 bc__mail_fields
--

CREATE TABLE bc__mail_fields (
  id integer NOT NULL PRIMARY KEY,
  mail_content_id integer default NULL,
  no integer default NULL,
  name text default NULL,
  field_name text default NULL,
  type text default NULL,
  head text default NULL,
  attention text default NULL,
  before_attachment text default NULL,
  after_attachment text default NULL,
  source text default NULL,
  size integer default NULL,
  rows integer default NULL,
  maxlength integer default NULL,
  options text default NULL,
  class text default NULL,
  separator text default NULL,
  default_value text default NULL,
  description text default NULL,
  group_field text default NULL,
  group_valid text default NULL,
  valid text default NULL,
  valid_ex text default NULL,
  auto_convert text default NULL,
  not_empty boolean default NULL,
  use_field boolean default NULL,
  no_send boolean default NULL,
  sort int default NULL,
  created text default NULL,
  modified text default NULL
);

--
-- テーブルのデータをダンプしています bc__mail_fields
--

INSERT INTO bc__mail_fields (mail_content_id, no, name, field_name, type, head, attention, before_attachment, after_attachment, source, size, rows, maxlength, options, class, separator, default_value, description, group_field, group_valid, valid, valid_ex, auto_convert, not_empty, use_field, no_send, sort, created, modified) VALUES
(1, 1,'姓漢字', 'name_1', 'text', 'お名前', '', '[姓]', '', '', 8, 0, 255, '', '', '', '', '', 'name', 'name', 'VALID_NOT_EMPTY', '', '', 1, 1, 0, 1, datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__mail_fields (mail_content_id, no, name, field_name, type, head, attention, before_attachment, after_attachment, source, size, rows, maxlength, options, class, separator, default_value, description, group_field, group_valid, valid, valid_ex, auto_convert, not_empty, use_field, no_send, sort, created, modified) VALUES
(1, 2,'名漢字', 'name_2', 'text', 'お名前', NULL, '[名]', NULL, NULL, 8, 0, 255, NULL, NULL, NULL, NULL, NULL, 'name', 'name', 'VALID_NOT_EMPTY', NULL, NULL, 1, 1, 0, 2, datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__mail_fields (mail_content_id, no, name, field_name, type, head, attention, before_attachment, after_attachment, source, size, rows, maxlength, options, class, separator, default_value, description, group_field, group_valid, valid, valid_ex, auto_convert, not_empty, use_field, no_send, sort, created, modified) VALUES
(1, 3,'姓カナ', 'name_kana_1', 'text', 'フリガナ', '', '[姓]', '', '', 8, 0, 255, '', '', '', '', '', 'name_kana', 'name_kana', '', '', '', 0, 1, 0, 3, datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__mail_fields (mail_content_id, no, name, field_name, type, head, attention, before_attachment, after_attachment, source, size, rows, maxlength, options, class, separator, default_value, description, group_field, group_valid, valid, valid_ex, auto_convert, not_empty, use_field, no_send, sort, created, modified) VALUES
(1, 4,'名カナ', 'name_kana_2', 'text', 'フリガナ', '', '[名]', '', '', 8, 0, 255, '', '', '', '', '', 'name_kana', 'name_kana', '', '', '', 0, 1, 0, 4, datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__mail_fields (mail_content_id, no, name, field_name, type, head, attention, before_attachment, after_attachment, source, size, rows, maxlength, options, class, separator, default_value, description, group_field, group_valid, valid, valid_ex, auto_convert, not_empty, use_field, no_send, sort, created, modified) VALUES
(1, 5,'性別', 'sex', 'radio', '性別', '', '', '', '男性|女性', 0, 0, 0, '', '', '', '', '', '', '', '', '', '', 0, 1, 0, 5, datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__mail_fields (mail_content_id, no, name, field_name, type, head, attention, before_attachment, after_attachment, source, size, rows, maxlength, options, class, separator, default_value, description, group_field, group_valid, valid, valid_ex, auto_convert, not_empty, use_field, no_send, sort, created, modified) VALUES
(1, 6,'メールアドレス', 'email_1', 'text', 'メールアドレス', '', '', '<br />', '', 25, 0, 50, '', '', '', '', '確認の為、２回入力して下さい。', 'email', 'email', 'VALID_EMAIL', 'VALID_EMAIL_CONFIRM', '', 1, 1, 0, 6, datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__mail_fields (mail_content_id, no, name, field_name, type, head, attention, before_attachment, after_attachment, source, size, rows, maxlength, options, class, separator, default_value, description, group_field, group_valid, valid, valid_ex, auto_convert, not_empty, use_field, no_send, sort, created, modified) VALUES
(1, 7,'メールアドレス確認', 'email_2', 'text', 'メールアドレス（確認）', '[確認]', '', '', '', 25, 0, 50, '', '', '', '', '', 'email', 'email', 'VALID_EMAIL', 'VALID_EMAIL_CONFIRM', '', 1, 1, 1, 7, datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__mail_fields (mail_content_id, no, name, field_name, type, head, attention, before_attachment, after_attachment, source, size, rows, maxlength, options, class, separator, default_value, description, group_field, group_valid, valid, valid_ex, auto_convert, not_empty, use_field, no_send, sort, created, modified) VALUES
(1, 8,'電話番号１', 'tel_1', 'text', '電話番号', '', '', '-', '', 5, 0, 5, '', '', '', '', '', 'tel', 'tel', '', 'VALID_GROUP_COMPLATE', 'CONVERT_HANKAKU', 0, 1, 0, 8, datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__mail_fields (mail_content_id, no, name, field_name, type, head, attention, before_attachment, after_attachment, source, size, rows, maxlength, options, class, separator, default_value, description, group_field, group_valid, valid, valid_ex, auto_convert, not_empty, use_field, no_send, sort, created, modified) VALUES
(1, 9,'電話番号２', 'tel_2', 'text', '電話番号', '', '', '-', '', 5, 0, 5, '', '', '', '', '', 'tel', 'tel', '', 'VALID_GROUP_COMPLATE', 'CONVERT_HANKAKU', 0, 1, 0, 9, datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__mail_fields (mail_content_id, no, name, field_name, type, head, attention, before_attachment, after_attachment, source, size, rows, maxlength, options, class, separator, default_value, description, group_field, group_valid, valid, valid_ex, auto_convert, not_empty, use_field, no_send, sort, created, modified) VALUES
(1, 10,'電話番号３', 'tel_3', 'text', '電話番号', '', '', '', '', 5, 0, 5, '', '', '', '', '', 'tel', 'tel', '', 'VALID_GROUP_COMPLATE', 'CONVERT_HANKAKU', 0, 1, 0, 10, datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__mail_fields (mail_content_id, no, name, field_name, type, head, attention, before_attachment, after_attachment, source, size, rows, maxlength, options, class, separator, default_value, description, group_field, group_valid, valid, valid_ex, auto_convert, not_empty, use_field, no_send, sort, created, modified) VALUES
(1, 11,'郵便番号', 'zip', 'autozip', '住所', '[半角数字]<br />', '〒', '', 'address_1|address_2', 10, 0, 8, '', '', '', '', '', 'address', '', '', '', 'CONVERT_HANKAKU', 0, 1, 0, 11, datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__mail_fields (mail_content_id, no, name, field_name, type, head, attention, before_attachment, after_attachment, source, size, rows, maxlength, options, class, separator, default_value, description, group_field, group_valid, valid, valid_ex, auto_convert, not_empty, use_field, no_send, sort, created, modified) VALUES
(1, 12,'都道府県', 'address_1', 'pref', '住所', '', '', '<br />', '', 0, 0, 0, '', '', '', '', '', 'address', '', '', '', '', 0, 1, 0, 12, datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__mail_fields (mail_content_id, no, name, field_name, type, head, attention, before_attachment, after_attachment, source, size, rows, maxlength, options, class, separator, default_value, description, group_field, group_valid, valid, valid_ex, auto_convert, not_empty, use_field, no_send, sort, created, modified) VALUES
(1, 13,'市区町村・番地', 'address_2', 'text', '住所', '', '', '<br />', '', 30, 0, 200, '', '', '', '', '', 'address', '', '', '', '', 0, 1, 0, 13, datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__mail_fields (mail_content_id, no, name, field_name, type, head, attention, before_attachment, after_attachment, source, size, rows, maxlength, options, class, separator, default_value, description, group_field, group_valid, valid, valid_ex, auto_convert, not_empty, use_field, no_send, sort, created, modified) VALUES
(1, 14,'建物名', 'address_3', 'text', '住所', '', '', '', '', 30, 0, 200, '', '', '', '', '', 'address', '', '', '', '', 0, 1, 0, 14, datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__mail_fields (mail_content_id, no, name, field_name, type, head, attention, before_attachment, after_attachment, source, size, rows, maxlength, options, class, separator, default_value, description, group_field, group_valid, valid, valid_ex, auto_convert, not_empty, use_field, no_send, sort, created, modified) VALUES
(1, 15,'お問い合わせ項目', 'category', 'multi_check', 'お問い合わせ項目', '', '', '', '資料請求|問い合わせ|その他', 0, 0, 0, '', '', '', '', '', '', '', '', 'VALID_NOT_UNCHECKED', '', 1, 1, 0, 15, datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__mail_fields (mail_content_id, no, name, field_name, type, head, attention, before_attachment, after_attachment, source, size, rows, maxlength, options, class, separator, default_value, description, group_field, group_valid, valid, valid_ex, auto_convert, not_empty, use_field, no_send, sort, created, modified) VALUES
(1, 16,'お問い合わせ内容', 'message', 'textarea', 'お問い合わせ内容', '', '', '', '', 48, 12, NULL, '', '', '', '', '', '', '', '', '', '', 0, 1, 0, 16, datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__mail_fields (mail_content_id, no, name, field_name, type, head, attention, before_attachment, after_attachment, source, size, rows, maxlength, options, class, separator, default_value, description, group_field, group_valid, valid, valid_ex, auto_convert, not_empty, use_field, no_send, sort, created, modified) VALUES
(1, 17,'ルート', 'root', 'select', 'どうやってこのサイトをお知りになりましたか？', '', '', '<br />', '検索エンジン|web広告|紙面広告|求人案内|その他', 0, 0, 0, '', '', '', '', '', 'root', '', 'VALID_NOT_EMPTY', '', '', 1, 1, 0, 17, datetime('now', 'localtime'), datetime('now', 'localtime'));
INSERT INTO bc__mail_fields (mail_content_id, no, name, field_name, type, head, attention, before_attachment, after_attachment, source, size, rows, maxlength, options, class, separator, default_value, description, group_field, group_valid, valid, valid_ex, auto_convert, not_empty, use_field, no_send, sort, created, modified) VALUES
(1, 18,'ルートその他', 'root_etc', 'text', '', '<br />[その他を選択された場合は内容をご入力下さい。]', '', '', '', 30, 0, 50, '', '', '', '', '', 'root', '', '', '', '', 0, 1, 0, 18, datetime('now', 'localtime'), datetime('now', 'localtime'));

-- --------------------------------------------------------

--
-- テーブルの構造 bc__messages
--

CREATE TABLE bc__messages (
  id integer NOT NULL PRIMARY KEY,
  name_1 text default NULL,
  name_2 text default NULL,
  name_kana_1 text default NULL,
  name_kana_2 text default NULL,
  sex text default NULL,
  email_1 text default NULL,
  email_2 text default NULL,
  tel_1 text default NULL,
  tel_2 text default NULL,
  tel_3 text default NULL,
  zip text default NULL,
  address_1 text default NULL,
  address_2 text default NULL,
  address_3 text default NULL,
  category text default NULL,
  message text default NULL,
  root text default NULL,
  root_etc text default NULL,
  created text default NULL,
  modified text default NULL
);

--
-- テーブルのデータをダンプしています bc__messages
--

-- --------------------------------------------------------

--
-- テーブルの構造 bc__contact_messages
--

CREATE TABLE bc__contact_messages (
  id integer NOT NULL PRIMARY KEY,
  name_1 text default NULL,
  name_2 text default NULL,
  name_kana_1 text default NULL,
  name_kana_2 text default NULL,
  sex text default NULL,
  email_1 text default NULL,
  email_2 text default NULL,
  tel_1 text default NULL,
  tel_2 text default NULL,
  tel_3 text default NULL,
  zip text default NULL,
  address_1 text default NULL,
  address_2 text default NULL,
  address_3 text default NULL,
  category text default NULL,
  message text default NULL,
  root text default NULL,
  root_etc text default NULL,
  created text default NULL,
  modified text default NULL
);

--
-- テーブルのデータをダンプしています bc__contact_messages
--

