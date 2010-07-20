<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ブログ設定 フォーム
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
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<h2>
	<?php $baser->contentsTitle() ?>
</h2>
<h3>WordPressデータの取り込み</h3>
<p>WordPressから出力したXMLデータを取込みます。（<a href="http://ja.wordpress.org/" target="_blank">WordPress</a> 2.8.4 のみ動作確認済）</p>
<?php echo $form->create('BlogPost',array('action'=>'import','enctype'=>'multipart/form-data')) ?>
<div class="align-center"> <?php echo $form->select('Import.blog_content_id',$blogContentList) ?> <?php echo $form->select('Import.user_id',$userList) ?> <?php echo $form->file('Import.file') ?> <?php echo $form->end(array('label'=>'取り込む','div'=>false,'class'=>'btn-orange button')) ?> </div>
<!--
<p><small><span class="required">*</span> 印の項目は必須です。</small></p>
<?php echo $form->create('BlogConfig',array('action'=>'form')) ?>
<?php echo $form->hidden('BlogConfig.id') ?>

<div class="align-center">
<?php echo $form->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
</div>
-->
