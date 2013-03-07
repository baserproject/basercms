<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ローカルナビゲーションウィジェット設定
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$title = 'ローカルナビゲーション';
$description = 'ページ機能で作成されたページで同一カテゴリ内のタイトルリストを表示します。';
echo $bcForm->hidden($key.'.cache',array('value'=>true));
?>
<br />
<small>タイトルを表示する場合、カテゴリ名を表示します。</small>