<?php
/* SVN FILE: $Id$ */
/**
 * [モバイル] トップページ
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$baser->setTitle('');
$baser->setDescription('');
?>

<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<div style="text-align:center;background-color:#FF6600;"> <span style="color:white;">メインメニュー</span> </div>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<span style="color:#FF6600">◆</span>
<?php $baser->link('ニュースリリース',array('controller'=>'news','action'=>'index')) ?>
<br />
<span style="color:#FF6600">◆</span>
<?php $baser->link('お問い合わせ',array('controller'=>'contact','action'=>'index')) ?>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<div style="text-align:center;background-color:#FF6600;"> <span style="color:white;">NEWS RELEASE</span> </div>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<?php echo mb_convert_encoding(file_get_contents('http://'.$_SERVER['HTTP_HOST'].$baser->getUrl('/'.Configure::read('AgentSettings.mobile.alias').'/feed/index/1')),'UTF-8','SJIS'); ?> <br />
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<div style="text-align:center;background-color:#FF6600;"> <span style="color:white;">baserCMS NEWS</span> </div>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<?php echo mb_convert_encoding(file_get_contents('http://'.$_SERVER['HTTP_HOST'].$baser->getUrl('/'.Configure::read('AgentSettings.mobile.alias').'/feed/index/2')),'UTF-8','SJIS'); ?>