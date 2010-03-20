<?php
/* SVN FILE: $Id$ */
/**
 * インストール警告ページ
 * 
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<p>インストールを開始するにはデバッグモードが 0 以外である必要があります。</p>
<p>デバッグモードを変更するには次の手順のとおり操作してください。</p>

<ul>
    <li>次のファイルを開きます。<br />
        <pre>/app/config/core.php</pre>
    <li>
    <li>core.phpより次の行を見つけます。<br />
        <pre>Configure::write('debug', 0);</pre></li>
    <li>0 の部分を、 -1 / 1 / 2 / 3 のどれかに書き換えます。</li>
    <li>編集したファイルをサーバーにアップロードします。</li>
</ul>

<p><small><?php $baser->link('≫ BaserCMSを初期化するにはコチラから','/installations/reset') ?></small></p>