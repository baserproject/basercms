<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] スキーマ書き出しフォーム　ヘルプ
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<p>スキーマファイルは、データベースの構造を読み取り、CakePHPのスキーマファイルとして出力できます。</p>
<p>コアパッケージやプラグインの新規テーブル作成、テーブル構造変更の際に利用すると便利です。</p>
<p>新規インストール時に利用するファイルは、次のフォルダ内に配置します。</p>
<ul>
	<li>Baserコア・・・/baser/config/sql/</li>
	<li>プラグイン・・・/{プラグインフォルダ}/config/sql/</li>
</ul>

<p>アップデート時に利用するファイルは、次のフォルダ内に配置します。</p>
<ul>
	<li>Baserコア・・・/baser/config/update/{バージョン番号}/sql/</li>
	<li>プラグイン・・・/{プラグインフォルダ}/config/update/{バージョン番号}/sql/</li>
</ul>