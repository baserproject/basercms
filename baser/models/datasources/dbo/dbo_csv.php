<?php
/* SVN FILE: $Id$ */
/**
 * CSV DBO Driver
 *
 * SQLベースでCSVファイルに読み書きをさせる為のドライバー
 *
 * ・dbo_datasourcesによって、一旦SQL文に変換された文字列をqueryDataとして復元した上で処理を行う。
 * ・復元したqueryDataは、CSVファイルを処理しやすいように独自拡張、仕様変更している。
 * ・機能として追加できていないものは空メソッドとして、CakeErrorを発生させる。
 * ・Order By は１フィールドのみ対応
 * ・アソシエイションは未実装
 *
 * [ CSV ファイル 仕様 ]
 * ・カンマは[\,]でエスケープする
 * ・ダブルコーテーションは、[""]でエスケープする（Cassavaは自動でエスケープしてくれる）
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.models.datasources.dbo
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 * @deprecated		DboBcCsv に移行
 */
trigger_error('/app/config/database.php の driver を bc_csv に書き換えてください。（２ヶ所）', E_USER_WARNING);
