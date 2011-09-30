<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] スキーマ生成 フォーム
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
<h2>
	<?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?>
</h2>

<!-- help -->
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
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
</div>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<!-- list -->
<?php echo $formEx->create('Tool', array('action' => 'write_schema')) ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('Tool.baser', 'コアテーブル名') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('Tool.baser', array(
				'type'		=> 'select',
				'options'	=> $formEx->getControlSource('Tool.baser'),
				'multiple'	=> true,
				'style'		=> 'width:400px;height:250px')) ?>
			<?php echo $formEx->error('Tool.baser') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('Tool.plugin', 'プラグインテーブル名') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('Tool.plugin', array(
				'type'		=> 'select',
				'options'	=> $formEx->getControlSource('Tool.plugin'),
				'multiple'	=> true,
				'style'		=> 'width:400px;height:250px')) ?>
			<?php echo $formEx->error('Tool.plugin') ?>
		</td>
	</tr>
</table>

<div class="align-center"><?php echo $formEx->submit('生　成', array('div' => false, 'class' => 'btn-red button')) ?></div>

<?php echo $formEx->end() ?>