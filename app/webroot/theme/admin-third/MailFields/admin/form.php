<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Mail.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] メールフィールド フォーム
 */
$this->BcBaser->js('Mail.admin/mail_fields/form', false);
?>


<?php if ($this->action == 'admin_add'): ?>
	<?php echo $this->BcForm->create('MailField', ['url' => ['controller' => 'mail_fields', 'action' => 'add', $mailContent['MailContent']['id']]]) ?>
<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $this->BcForm->create('MailField', ['url' => ['controller' => 'mail_fields', 'action' => 'edit', $mailContent['MailContent']['id'], $this->BcForm->value('MailField.id'), 'id' => false]]) ?>
<?php endif; ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php echo $this->BcForm->hidden('MailField.id') ?>

<h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser', '基本項目') ?></h2>

<section class="bca-section" data-bca-section-type='form-group'>
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table bca-form-table">
		<?php if ($this->action == 'admin_edit'): ?>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.no', 'No') ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo h($this->BcForm->value('MailField.no')) ?>
					<?php echo $this->BcForm->input('MailField.no', ['type' => 'hidden']) ?>
				</td>
			</tr>
		<?php endif; ?>
		<tr id="RowFieldName">
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.field_name', __d('baser', 'フィールド名')) ?>
				&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailField.field_name', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'autofocus' => true]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('MailField.field_name') ?>
				<div id="helptextFieldName" class="helptext"><?php echo __d('baser', '重複しない半角英数字で入力してください。') ?></div>
			</td>
		</tr>
		<tr id="RowName">
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.name', __d('baser', '項目名')) ?>
				&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailField.name', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('MailField.name') ?>
				<div id="helptextName"
					 class="helptext"><?php echo __d('baser', '項目を特定しやすいわかりやすい名前を入力してください。日本語可。') ?></div>
			</td>
		</tr>
		<tr id="RowType">
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.type', __d('baser', 'タイプ')) ?>
				&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailField.type', ['type' => 'select', 'options' => $this->BcForm->getControlSource('type')]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('MailField.type') ?>
				<div id="helptextType" class="helptext">
					<ul>
						<li><?php echo __d('baser', 'Eメールを選択すると、メールフォーム送信の際、入力されたEメール宛に自動返信メールを送信します。') ?><br/>
							<small>※ 前バージョンとの互換性の為、フィールド名を「email_1」とした場合、Eメールを選択しなくても自動返信メールを送信します。</small></li>
						<li><?php echo __d('baser', '自動補完郵便番号の場合は、選択リストに都道府県のフィールドと住所のフィールドのリストを指定します。') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<tr id="RowHead">
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.head', __d('baser', '項目見出し')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailField.head', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('MailField.head') ?>
				<div id="helptextHead"
					 class="helptext"><?php echo __d('baser', ' グループとして設定する場合、同グループの２番目以降のフィールドについてこの項目の入力は不要です。 ') ?></div>
			</td>
		</tr>
		<tr id="RowNotEmpty">
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.not_empty', __d('baser', '必須マーク')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailField.not_empty', ['type' => 'checkbox', 'label' => __d('baser', '項目見出しに必須マークを表示する')]) ?>
				<?php echo $this->BcForm->error('MailField.not_empty') ?>
			</td>
		</tr>
		<tr id="RowValid">
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.valid', __d('baser', '入力チェック')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailField.valid', ['type' => 'select', 'options' => $this->BcForm->getControlSource('valid'), 'empty' => __d('baser', 'なし')]) ?>
				<?php echo $this->BcForm->error('MailField.valid') ?>
			</td>
		</tr>
		<tr id="RowAttention">
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.attention', __d('baser', '注意書き')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailField.attention', ['type' => 'textarea', 'cols' => 35, 'rows' => 3]) ?>
				<?php echo $this->BcForm->error('MailField.attention') ?>
			</td>
		</tr>
		<tr id="RowBeforeAttachment">
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.before_attachment', __d('baser', '前見出し')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailField.before_attachment', ['type' => 'textarea', 'cols' => 35, 'rows' => 3]) ?>
				<?php echo $this->BcForm->error('MailField.before_attachment') ?>
			</td>
		</tr>
		<tr id="RowAfterAttachment">
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.after_attachment', __d('baser', '後見出し')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailField.after_attachment', ['type' => 'textarea', 'cols' => 35, 'rows' => 3]) ?>
				<?php echo $this->BcForm->error('MailField.after_attachment') ?>
			</td>
		</tr>
		<tr id="RowDescription">
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.description', __d('baser', '説明文')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailField.description', ['type' => 'textarea', 'cols' => 35, 'rows' => 3]) ?>
				<?php echo $this->BcForm->error('MailField.description') ?>
			</td>
		</tr>
		<tr id="RowSource">
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.source', __d('baser', '選択リスト')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailField.source', ['type' => 'textarea', 'cols' => 35, 'rows' => 4]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('MailField.source') ?>
				<div id="helptextSource" class="helptext">
					<ul>
						<li><?php echo __d('baser', 'ラジオボタン、セレクトボックス、マルチチェックボックスの場合の選択リスト指定します。') ?></li>
						<li><?php echo __d('baser', '自動補完郵便番号の場合は、都道府県のフィールドと住所のフィールドのリストを指定します。') ?></li>
						<li><?php echo __d('baser', 'リストは　|　で区切って入力します。') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<tr id="RowSize">
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.size', __d('baser', '表示サイズ')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailField.size', ['type' => 'text', 'size' => 10, 'maxlength' => 255]) ?>
				<?php echo $this->BcForm->error('MailField.size') ?>
			</td>
		</tr>
		<tr id="RowRows">
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.rows', __d('baser', '行数')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailField.rows', ['type' => 'text', 'size' => 10, 'maxlength' => 255]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('MailField.rows') ?>
				<div id="helptextRows" class="helptext"><?php echo __d('baser', 'テキストボックスの場合の行数を指定します。') ?></div>
			</td>
		</tr>
		<tr id="RowMaxlength">
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.maxlength', __d('baser', '最大値')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailField.maxlength', ['type' => 'text', 'size' => 10, 'maxlength' => 255]) ?>
				&nbsp;<?php echo __d('baser', '文字') ?>
				<?php echo $this->BcForm->error('MailField.maxlength') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</section>

<section class="bca-section" data-bca-section-type="form-group">
	<div class="bca-collapse__action">
		<button type="button" class="bca-collapse__btn" data-bca-collapse="collapse"
				data-bca-target="#mailFieldSettingBody" aria-expanded="false" aria-controls="mailFieldSettingBody"><?php echo __d('baser', '詳細設定') ?>&nbsp;&nbsp;<i
				class="bca-icon--chevron-down bca-collapse__btn-icon"></i></button>
	</div>
	<div class="bca-collapse" id="mailFieldSettingBody" data-bca-state="">
		<table cellpadding="0" cellspacing="0" class="form-table bca-form-table" id="formOptionBody">
			<tr id="RowValidEx">
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.valid_ex', __d('baser', '拡張入力チェック')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('MailField.valid_ex', ['type' => 'select', 'multiple' => 'checkbox', 'options' => $this->BcForm->getControlSource('valid_ex')]) ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<?php echo $this->BcForm->error('MailField.valid_ex') ?>
					<div id="helptextValidEx" class="helptext">
						<ul>
							<li><?php echo __d('baser', 'Eメール比較チェック：利用するには「Eメール」タイプのフィールドを二つ作成し、グループ入力チェックに任意の同じ値を入力します。') ?></li>
							<li><?php echo __d('baser', 'グループチェック：グループで連帯して入力チェックを行うには同じグループ名を入力します。') ?></li>
							<li><?php echo __d('baser', '日付チェック：日付形式かどうかのチェックです。') ?></li>
							<li><?php echo __d('baser', 'ファイルアップロードサイズ制限：利用するには、「ファイル」タイプを選択し、オプション項目に、上限となるサイズを次の形式のように | 区切りで入力します。「maxFileSize|10（単位：MB）」') ?></li>
							<li><?php echo __d('baser', 'ファイル拡張子チェック：利用するには、「ファイル」タイプを選択し、オプション項目に、アップロードを許可する拡張子を次の形式のように | 区切りで入力します。「fileExt|jpg,pdf」') ?></li>
							<li><?php echo __d('baser', '正規表現チェック：利用するには、オプション項目に、正規表現を次の形式のように | 区切りで入力します。「regex|\d+」 入力した正規表現は以下の書式で実行されます。 /\A○○○\z/us') ?></li>
						</ul>
					</div>
				</td>
			</tr>
			<tr id="RowGroupField">
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.group_field', __d('baser', 'グループ名')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('MailField.group_field', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<?php echo $this->BcForm->error('MailField.group_field') ?>
					<div id="helptextGroupField" class="helptext">
						<ul>
							<li><?php echo __d('baser', '各項目を同じグループとするには同じグループ名を入力します。') ?></li>
							<li><?php echo __d('baser', '半角英数字で入力してください。') ?></li>
						</ul>
					</div>
				</td>
			</tr>
			<tr id="RowGroupValid">
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.group_valid', __d('baser', 'グループ入力チェック')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('MailField.group_valid', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<?php echo $this->BcForm->error('MailField.group_valid') ?>
					<div id="helptextGroupValid" class="helptext">
						<ul>
							<li><?php echo __d('baser', 'グループで連帯して入力チェックを行うには同じグループ名を入力します。') ?></li>
							<li><?php echo __d('baser', 'グループ内の項目が一つでもエラーとなるとグループ内の全ての項目にエラーを意味する背景色が付きます。') ?></li>
							<li><?php echo __d('baser', '半角英数字で入力してください。') ?></li>
						</ul>
					</div>
				</td>
			</tr>
			<tr id="RowOptions">
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.options', __d('baser', 'オプション')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('MailField.options', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
					<?php echo $this->BcForm->error('MailField.options') ?>
				</td>
			</tr>
			<tr id="RowType">
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.type', __d('baser', 'オートコンプリート')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('MailField.auto_complete', ['type' => 'select', 'options' => $autoCompleteOptions]) ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<div id="helpSeparator" class="helptext">
						<ul>
							<li><?php echo __d('baser', 'オートコンプリート属性については<a href="https://developer.mozilla.org/ja/docs/Web/HTML/Attributes/autocomplete" target="_blank">リファレンス</a>を参照してください。') ?></li>
						</ul>
					</div>
					<?php echo $this->BcForm->error('MailField.auto_complete') ?>
				</td>
			</tr>
			<tr id="RowClass">
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.class', __d('baser', 'クラス名')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('MailField.class', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
					<?php echo $this->BcForm->error('MailField.class') ?>
				</td>
			</tr>
			<tr id="RowSeparator">
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.separator', __d('baser', '区切り文字')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('MailField.separator', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<div id="helpSeparator" class="helptext">
						<ul>
							<li><?php echo __d('baser', '空白の場合は自動で「＆nbsp;＆nbsp;」が挿入されます') ?></li>
							<li><?php echo __d('baser', '空にしたいときは半角スペースを入力してください。') ?></li>
						</ul>
					</div>
					<?php echo $this->BcForm->error('MailField.separator') ?>
				</td>
			</tr>
			<tr id="RowDefault">
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.default_value', __d('baser', '初期値')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('MailField.default_value', ['type' => 'textarea', 'cols' => 35, 'rows' => 2]) ?>
					<?php echo $this->BcForm->error('MailField.default_value') ?>
				</td>
			</tr>
			<tr id="RowAutoConvert">
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.auto_convert', __d('baser', '自動変換')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('MailField.auto_convert', ['type' => 'select', 'options' => $this->BcForm->getControlSource('auto_convert'), 'empty' => __d('baser', 'なし')]) ?>
					<?php echo $this->BcForm->error('MailField.auto_convert') ?>
				</td>
			</tr>
			<tr id="RowUseField">
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.use_field', __d('baser', '利用状態')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('MailField.use_field', ['type' => 'checkbox', 'label' => __d('baser', '利用中')]) ?>
					<?php echo $this->BcForm->error('MailField.use_field') ?>
				</td>
			</tr>
			<tr id="RowNoSend">
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailField.no_send', __d('baser', 'メール送信')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('MailField.no_send', ['type' => 'radio', 'options' => [__d('baser', '送信する'), __d('baser', '送信しない')]]) ?>
					<?php echo $this->BcForm->error('MailField.no_send') ?>
				</td>
			</tr>
			<?php echo $this->BcForm->dispatchAfterForm('option') ?>
		</table>
	</div>
</section>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit bca-actions">
	<div class="bca-actions__main">
		<?php echo $this->BcForm->button(__d('baser', '保存'), ['div' => false, 'class' => 'button bca-btn bca-actions__item', 'id' => 'BtnSave',
			'data-bca-btn-type' => 'save',
			'data-bca-btn-size' => 'lg',
			'data-bca-btn-width' => 'lg',
		]) ?>
	</div>
	<?php if ($this->action == 'admin_edit'): ?>
		<div class="bca-actions__sub">
			<?php $this->BcBaser->link(__d('baser', '削除'), ['action' => 'delete', $mailContent['MailContent']['id'], $this->BcForm->value('MailField.id')], ['class' => 'submit-token button bca-btn bca-actions__item', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'sm'], sprintf('%s を本当に削除してもいいですか？', $this->BcForm->value('MailField.name')), false); ?>
		</div>
	<?php endif ?>
</div>

<?php echo $this->BcForm->end() ?>
