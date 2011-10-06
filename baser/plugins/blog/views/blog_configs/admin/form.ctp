<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログ設定 フォーム
 *
 * 1.6.10 では利用していない＆不完全
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<!-- title -->
<h2><?php $baser->contentsTitle() ?></h2>

<h3>WordPressデータの取り込み</h3>
<p>WordPressから出力したXMLデータを取込みます。（<a href="http://ja.wordpress.org/" target="_blank">WordPress</a> 2.8.4 のみ動作確認済）</p>

<div class="align-center">
	<?php echo $formEx->create('BlogPost', array('action' => 'import', 'enctype' => 'multipart/form-data')) ?>
	<?php echo $formEx->input('Import.blog_content_id', array('type' => 'select', 'options' => $blogContentList)) ?>
	<?php echo $formEx->input('Import.user_id', array('type' => 'select', 'options' => $userList)) ?>
	<?php echo $form->input('Import.file', array('type' => 'file')) ?>
	<?php echo $form->end(array('label' => '取り込む', 'div' => false, 'class' => 'btn-orange button')) ?>
</div>
