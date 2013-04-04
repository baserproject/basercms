<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログ設定 フォーム
 *
 * 1.6.10 では利用していない＆不完全
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
?>


<!-- title -->
<h2><?php $bcBaser->contentsTitle() ?></h2>

<h3>WordPressデータの取り込み</h3>
<p>WordPressから出力したXMLデータを取込みます。（<a href="http://ja.wordpress.org/" target="_blank">WordPress</a> 2.8.4 のみ動作確認済）</p>

<div class="align-center">
	<?php echo $bcForm->create('BlogPost', array('action' => 'import', 'enctype' => 'multipart/form-data')) ?>
	<?php echo $bcForm->input('Import.blog_content_id', array('type' => 'select', 'options' => $blogContentList)) ?>
	<?php echo $bcForm->input('Import.user_id', array('type' => 'select', 'options' => $userList)) ?>
	<?php echo $form->input('Import.file', array('type' => 'file')) ?>
	<?php echo $form->end(array('label' => '取り込む', 'div' => false, 'class' => 'btn-orange button')) ?>
</div>
