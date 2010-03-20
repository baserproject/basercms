<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] メールフォーム共通メニュー
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
 * @package			baser.plugins.mail.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<div class="side-navi">
<h2>メールフォーム<br />共通メニュー</h2>
<ul>
<?php if(isset($mailContent) &&
			($this->params['controller'] == 'mail_fields' ||
			$this->params['controller'] == 'mail_contents')): ?>
<li><?php $baser->link('公開ページ確認',array('admin'=>false,'plugin'=>'','controller'=>$mailContent['MailContent']['name'],'action'=>'index'),array('target'=>'_blank')) ?></li>
<li><?php $baser->link($mailContent['MailContent']['title'].'基本設定',array('controller'=>'mail_contents','action'=>'edit',$mailContent['MailContent']['id'])) ?></li>
<?php endif ?>
<li><?php $baser->link('メールプラグイン基本設定',array('controller'=>'mail_configs','action'=>'form')) ?></li>
<li><?php $baser->link('メールフォーム一覧',array('controller'=>'mail_contents','action'=>'index')) ?></li>
<li><?php $baser->link('新規メールフォームを登録',array('controller'=>'mail_contents','action'=>'add')) ?></li>
</ul>
</div>