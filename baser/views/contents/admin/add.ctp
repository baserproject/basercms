<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] 検索インデックス登録フォーム
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
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<h2><?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>

<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>baserCMSの管理システムで管理できないコンテンツの登録に利用します。</p>
	<ul>
		<li>「管理できないコンテンツ」とは、baserCMSをカスタマイズした場合の新しいURLや、検索インデックスへの自動登録をサポートしていないプラグインのコンテンツURLを指します。</li>
		<li>画像ファイルや、静的HTMLは登録できません。</li>
	</ul>
</div>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php echo $formEx->create('Content') ?>

<!-- form -->
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('Page.url', 'URL') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('Content.url', array('type' => 'text', 'size' => 60, 'maxlength' => 255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpUrl', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('Content.url') ?>
			<div id="helptextUrl" class="helptext">
				<ul>
					<li>サイト内で検索インデックスとして登録したいURLを指定します。</li>
					<li>baserCMSの設置URL部分は省略する事ができます。<br />
						http://{baserCMS設置URL}/company/index<br />
						→ /company/index<br />
						<small>※ 省略時、スマートURLオフの場合、URL上の「/index.php」 は含めないようにします。</small>
					</li>
				</ul>
			</div>
		</td>
	</tr>
</table>

<div class="submit">
	<?php echo $formEx->submit('登　録', array('div' => false, 'class' => 'btn-red button', 'id' => 'btnSave')) ?>
</div>

<?php echo $formEx->end() ?>