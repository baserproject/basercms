<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] メールフィールド フォーム
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
<script type="text/javascript">
jQuery(function($) {

	// タイプを選択すると入力するフィールドが切り替わる
	$("#MailFieldType").change(function(){loadSetting($("#MailFieldType").val())});
    // 項目名を入力時に項目見出しを自動入力
    $("#MailFieldName").change(function(){
        if(!$("#MailFieldHead").val()){
            $("#MailFieldHead").val($("#MailFieldName").val());
        }
    });

	loadSetting($("#MailFieldType").val());

	/**
	 * タイプの値によってフィールドの表示設定を行う
	 */
	function loadSetting(value){

		switch ($("#MailFieldType").val()){
			case 'text':
				$("#rowSize").show();
				$("#rowRows").hide();$("#MailFieldRows").val('');
				$("#rowMaxlength").show();
				$("#rowSource").hide();
				$("#rowAutoConvert").show();
				$("#rowSeparator").hide();$("#MailFieldSeparator").val('');
				break;
			case 'textarea':
				$("#rowSize").show();
				$("#rowRows").show();
				$("#rowMaxlength").hide();$("#MailFieldMaxlength").val('');
				$("#rowSource").hide();$("#MailFieldSource").val('');
				$("#rowAutoConvert").show();
				$("#rowSeparator").hide();$("#MailFieldSeparator").val('');
				break;
			case 'radio':
			case 'multi':
				$("#rowSize").hide();$("#MailFieldSize").val('');
				$("#rowRows").hide();$("#MailFieldRows").val('');
				$("#rowMaxlength").hide();$("#MailFieldMaxlength").val('');
				$("#rowSource").show();
				$("#rowAutoConvert").hide();$("#MailFieldAutoConvert").val('');
				$("#rowSeparator").show();
				break;
			case 'select':
				$("#rowSize").hide();$("#MailFieldSize").val('');
				$("#rowRows").hide();$("#MailFieldRows").val('');
				$("#rowMaxlength").hide();$("#MailFieldMaxlength").val('');
				$("#rowSource").show();
				$("#rowAutoConvert").hide();$("#MailFieldAutoConvert").val('');
				$("#rowSeparator").hide();$("#MailFieldSeparator").val('');
				break;
			case 'pref':
			case 'date_time_birthday':
			case 'date_time_reserve':
				$("#rowSize").hide();$("#MailFieldSize").val('');
				$("#rowRows").hide();$("#MailFieldRows").val('');
				$("#rowMaxlength").hide();$("#MailFieldMaxlength").val('');
				$("#rowSource").hide();$("#MailFieldSource").val('');
				$("#rowAutoConvert").hide();$("#MailFieldAutoConvert").val('');
				$("#rowSeparator").hide();$("#MailFieldSeparator").val('');
                break;
            case 'autozip':
                $("#rowSize").show();
                $("#rowRows").hide();$("#MailFieldRows").val('');
                $("#rowMaxlength").show();$("#MailFieldMaxlength").val('7');
                $("#rowSource").show();
                $("#rowAutoConvert").show();$("#MailFieldAutoConvert").val('CONVERT_HANKAKU');
                $("#rowSeparator").hide();$("#MailFieldSeparator").val('');
				break;

		}

	}

});
</script>

<h2><?php $baser->contentsTitle() ?>&nbsp;<?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>メールフォームのフィールド（項目）の登録を行います。<br />
	項目のタイプをはじめ、入力チェックの設定等、便利な機能を用意しています。<br />
	各項目の詳細については、各ヘルプメッセージを確認してください。</p>
	<ul>
		<li>一時的に利用しない場合には、画面下のオプションをクリックし、フィールドの利用を「利用しない」に設定します。</li>
	</ul>
</div>


<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php /* MailContent.idを第一引数にしたいが為にURL直書き */ ?>
<?php if($this->action=='admin_add'): ?>
    <?php echo $formEx->create('MailField',array('url'=>'/admin/mail/mail_fields/add/'.$mailContent['MailContent']['id'])) ?>
<?php elseif($this->action=='admin_edit'): ?>
    <?php echo $formEx->create('MailField',array('url'=>'/admin/mail/mail_fields/edit/'.$mailContent['MailContent']['id'].'/'.$formEx->value('MailField.id'))) ?>
<?php endif; ?>
<?php echo $formEx->hidden('MailField.id') ?>
<h3>基本項目</h3>

<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('MailField.no', 'NO') ?></th>
		<td class="col-input">
			<?php echo $formEx->text('MailField.no', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>
		</td>
	</tr>
<?php endif; ?>
	<tr id="rowFieldName">
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailField.field_name', 'フィールド名') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailField.field_name', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpFieldName','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextFieldName" class="helptext">重複しない半角英数字で入力して下さい。</div>
            <?php echo $formEx->error('MailField.field_name') ?>
        </td>
	</tr>
	<tr id="rowName">
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailField.name', '項目名') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailField.name', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpName','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextName" class="helptext">項目を特定しやすいわかりやすい名前を入力して下さい。日本語可。</div>
            <?php echo $formEx->error('MailField.name') ?>
        </td>
	</tr>
	<tr id="rowType">
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailField.type', 'タイプ') ?></th>
		<td class="col-input">
            <?php echo $formEx->select('MailField.type', $controlSource['type']) ?>
            <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpType','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextType" class="helptext">
                自動補完郵便番号の場合は、選択リストに都道府県のフィールドと住所のフィールドのリストを指定します。
            </div>
			<?php echo $formEx->error('MailField.type') ?>
		</td>
	</tr>
	<tr id="rowHead">
		<th class="col-head"><?php echo $formEx->label('MailField.head', '項目見出し') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailField.head', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpHead','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextHead" class="helptext">
                グループの場合、２番目以降のフィールドは不要です。
            </div>
            <?php echo $formEx->error('MailField.head') ?>
        </td>
	</tr>
	<tr id="rowNotEmpty">
		<th class="col-head"><?php echo $formEx->label('MailField.not_empty', '必須マーク') ?></th>
		<td class="col-input"><?php echo $formEx->checkbox('MailField.not_empty', array('label'=>'項目見出しに必須マークを表示する')) ?>
            <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpNotEmpty','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextNotEmpty" class="helptext">グループの場合、２番目以降のフィールドは不要です。</div>
            <?php echo $formEx->error('MailField.not_empty') ?>
        </td>
	</tr>
	<tr id="rowValid">
		<th class="col-head"><?php echo $formEx->label('MailField.valid', '入力チェック') ?></th>
		<td class="col-input"><?php echo $formEx->select('MailField.valid', $controlSource['valid']) ?><?php echo $formEx->error('MailField.valid') ?>&nbsp;</td>
	</tr>
	<tr id="rowAttention">
		<th class="col-head"><?php echo $formEx->label('MailField.attention', '注意書き') ?></th>
		<td class="col-input"><?php echo $formEx->textarea('MailField.attention', array('cols'=>35,'rows'=>3)) ?><?php echo $formEx->error('MailField.attention') ?>&nbsp;</td>
	</tr>
	<tr id="rowBeforeAttachment">
		<th class="col-head"><?php echo $formEx->label('MailField.before_attachment', '前見出し') ?></th>
		<td class="col-input"><?php echo $formEx->textarea('MailField.before_attachment', array('cols'=>35,'rows'=>3)) ?><?php echo $formEx->error('MailField.before_attachment') ?>&nbsp;</td>
	</tr>
	<tr id="rowAfterAttachment">
		<th class="col-head"><?php echo $formEx->label('MailField.after_attachment', '後見出し') ?></th>
		<td class="col-input"><?php echo $formEx->textarea('MailField.after_attachment', array('cols'=>35,'rows'=>3)) ?><?php echo $formEx->error('MailField.after_attachment') ?>&nbsp;</td>
	</tr>
	<tr id="rowDescription">
		<th class="col-head"><?php echo $formEx->label('MailField.description', '説明文') ?></th>
		<td class="col-input"><?php echo $formEx->textarea('MailField.description', array('cols'=>35,'rows'=>3)) ?><?php echo $formEx->error('MailField.description') ?>&nbsp;</td>
	</tr>
	<tr id="rowSource">
		<th class="col-head"><?php echo $formEx->label('MailField.source', '選択リスト') ?></th>
		<td class="col-input"><?php echo $formEx->textarea('MailField.source',array('cols'=>35,'rows'=>4)) ?>
            <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpSource','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextSource" class="helptext">
                <ul>
                    <li>ラジオボタン、セレクトボックス、マルチチェックボックスの場合の選択リスト指定します。</li>
                    <li>自動補完郵便番号の場合は、都道府県のフィールドと住所のフィールドのリストを指定します。</li>
                    <li>リストは　|　で区切って入力します。</li>
                </ul>
            </div>
            <?php echo $formEx->error('MailField.source') ?>
        </td>
	</tr>
	<tr id="rowSize">
		<th class="col-head"><?php echo $formEx->label('MailField.size', '表示サイズ') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailField.size', array('size'=>10,'maxlength'=>255)) ?>
		<?php echo $formEx->error('MailField.size') ?>&nbsp;</td>
	</tr>
	<tr id="rowRows">
		<th class="col-head"><?php echo $formEx->label('MailField.rows', '行数') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailField.rows', array('size'=>10,'maxlength'=>255)) ?>
            <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpRows','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextRows" class="helptext">テキストボックスの場合の行数を指定します。</div>
            <?php echo $formEx->error('MailField.rows') ?>
        </td>
	</tr>
	<tr id="rowMaxlength">
		<th class="col-head"><?php echo $formEx->label('MailField.maxlength', '最大値') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailField.maxlength', array('size'=>10,'maxlength'=>255)) ?>文字
		<?php echo $formEx->error('MailField.maxlength') ?>&nbsp;</td>
	</tr>
</table>


<h3><a href="javascript:void(0)" id="formOption" class="slide-trigger">オプション</a></h3>


<table cellpadding="0" cellspacing="0" class="admin-row-table-01 slide-body" id="formOptionBody">
    <tr id="rowValidEx">
		<th class="col-head"><?php echo $formEx->label('MailField.valid_ex', '拡張入力チェック') ?></th>
		<td class="col-input"><?php echo $formEx->select('MailField.valid_ex', $controlSource['valid_ex']) ?>
		<?php echo $formEx->error('MailField.valid_ex') ?>&nbsp;</td>
	</tr>

	<tr id="rowGroupField">
		<th class="col-head"><?php echo $formEx->label('MailField.group_field', 'グループ名') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailField.group_field', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpGroupField','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextGroupField" class="helptext">
                <ul>
                    <li>各項目を同じグループとするには同じグループ名を入力します。</li>
                    <li>半角英数字で入力して下さい。</li>
                </ul>
            </div>
            <?php echo $formEx->error('MailField.group_field') ?>
        </td>
	</tr>
	<tr id="rowGroupValid">
		<th class="col-head"><?php echo $formEx->label('MailField.group_valid', 'グループ入力チェック') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailField.group_valid', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpGroupValid','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextGroupValid" class="helptext">
                <ul>
                    <li>グループで連帯して入力チェックを行うには同じグループ名を入力します。</li>
                    <li>グループ内の項目が一つでもエラーとなるとグループ内の全ての項目にエラーを意味する背景色が付きます。</li>
                    <li>半角英数字で入力して下さい。</li>
                </ul>
            </div>
            <?php echo $formEx->error('MailField.group_valid') ?>
        </td>
	</tr>
	<tr id="rowOptions">
		<th class="col-head"><?php echo $formEx->label('MailField.options', 'オプション') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailField.options', array('size'=>40,'maxlength'=>255)) ?>
		<?php echo $formEx->error('MailField.options') ?>&nbsp;</td>
	</tr>
	<tr id="rowClass">
		<th class="col-head"><?php echo $formEx->label('MailField.class', 'クラス名') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailField.class', array('size'=>40,'maxlength'=>255)) ?>
		<?php echo $formEx->error('MailField.class') ?>&nbsp;</td>
	</tr>
	<tr id="rowSeparator">
		<th class="col-head"><?php echo $formEx->label('MailField.separator', '区切り文字') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailField.separator', array('size'=>40,'maxlength'=>255)) ?>
		<?php echo $formEx->error('MailField.separator') ?>&nbsp;</td>
	</tr>
	<tr id="rowDefault">
		<th class="col-head"><?php echo $formEx->label('MailField.default_value', '初期値') ?></th>
		<td class="col-input"><?php echo $formEx->textarea('MailField.default_value', array('cols'=>35,'rows'=>2)) ?>
		<?php echo $formEx->error('MailField.default_value') ?>&nbsp;</td>
	</tr>

	<tr id="rowAutoConvert">
		<th class="col-head"><?php echo $formEx->label('MailField.auto_convert', '自動変換') ?></th>
		<td class="col-input"><?php echo $formEx->select('MailField.auto_convert', $controlSource['auto_convert']) ?>
		<?php echo $formEx->error('MailField.auto_convert') ?>&nbsp;</td>
	</tr>
	<tr id="rowUseField">
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailField.use_field', 'フィールドの利用') ?></th>
		<td class="col-input">
		<?php echo $formEx->radio('MailField.use_field', $textEx->booleanDoList("利用"),array("legend"=>false,"separator"=>"&nbsp;&nbsp;")) ?>
		<?php echo $formEx->error('MailField.use_field') ?>&nbsp;
		</td>
	</tr>
	<tr id="rowNoSend">
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailField.no_send', 'メール送信') ?></th>
		<td class="col-input">
		<?php echo $formEx->radio('MailField.no_send', array(0=>'送信する',1=>'送信しない'),array("legend"=>false,"separator"=>"&nbsp;&nbsp;")) ?>
		<?php echo $formEx->error('MailField.no_send') ?>&nbsp;
		</td>
	</tr>
</table>


<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->end(array('label'=>'登　録','div'=>false,'class'=>'btn-red button')) ?>
<?php else: ?>
	<?php echo $formEx->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
	<?php $baser->link('削　除',array('action'=>'delete', $mailContent['MailContent']['id'], $formEx->value('MailField.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $formEx->value('MailField.name')),false); ?>
<?php endif ?>
</div>