<?php
/**
 * [ADMIN] ローカルナビゲーションウィジェット設定
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$title = 'ローカルナビゲーション';
$description = 'ページ機能で作成されたページで同一カテゴリ内のタイトルリストを表示します。';
echo $this->BcForm->hidden($key . '.cache', array('value' => true));
?>
<br />
<small>タイトルを表示する場合、カテゴリ名を表示します。</small>