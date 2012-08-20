<?php
/* SVN FILE: $Id$ */
/**
 * プラグインコンテンツビヘイビア
 *
 * 一つのプラグインに複数のコンテンツを持つ場合に、一つのコンテンツに対し
 * [http://example/コンテンツ名/コントローラー/アクション]形式のURLでアクセスする為のビヘイビア
 * プラグインコンテンツテーブルへの自動的なデータの追加と削除を実装する。
 *
 * 以下が必須項目
 * ◆ /app/config/plugin.php
 * ◆ /app/models/plugin_content.php
 * ◆ plugin_contents テーブル
 * 詳しくは、/app/config/plugin.php を参照
 *
 * 【注意点】
 * このビヘイビアを実装するモデルはプラグイン名と同じモデルもしくは、[プラグイン名Content]である必要がある
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.models.behaviors
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 * @deprecated		BcPluginContentBehavior に移行
 */
trigger_error('PluginContentBehavior は非推奨です。BcPluginContentBehavior を利用してください。', E_USER_WARNING);
?>