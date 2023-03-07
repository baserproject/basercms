<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * [ADMIN] メールコンテンツ フォーム
 * @var \BcMail\View\MailAdminAppView $this
 * @var bool $editorEnterBr
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<?php echo $this->BcFormTable->dispatchBefore() ?>

<section class="bca-section bca-section__mail-description">
  <label for="MailContentDescriptionTmp" class="bca-form-table__label -label">
    <?php echo $this->BcAdminForm->label('description', __d('baser_core', 'メールフォーム説明文')) ?>
  </label>
  <span class="bca-form-table__input-wrap">
		<?php
    echo $this->BcAdminForm->ckeditor('description', [
      'editorWidth' => 'auto',
      'editorHeight' => '120px',
      'editorToolType' => 'simple',
      'editorEnterBr' => $editorEnterBr
    ])
    ?>
    <?php echo $this->BcAdminForm->error('description') ?>
   </span>
</section>

<div class="section">
  <table class="form-table bca-form-table" data-bca-table-type="type2">
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('sender_1', __d('baser_core', '送信先メールアドレス')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('sender_1_', [
          'type' => 'radio',
          'options' => ['0' => __d('baser_core', '管理者用メールアドレスに送信する'), '1' => __d('baser_core', '別のメールアドレスに送信する')],
          'legend' => false,
          'separator' => '<br>'
        ]) ?><br/>
        <?php echo $this->BcAdminForm->control('sender_1', ['type' => 'text', 'size' => 40, 'class' => 'bca-textbox__input bca-mailContentSender1', 'placeholder' => __d('baser_core', 'メールアドレスを入力してください')]) ?>
        <?php echo $this->BcAdminForm->error('sender_1') ?>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('sender_name', __d('baser_core', '送信者名')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('sender_name', ['type' => 'text', 'size' => 80, 'maxlength' => 255]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <?php echo $this->BcAdminForm->error('sender_name') ?>
        <div class="bca-helptext"><?php echo __d('baser_core', '自動返信メールの送信者に表示します。') ?></div>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('subject_user', __d('baser_core', '自動返信メール件名<br>[ユーザー宛]'), ['escape' => false]) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('subject_user', ['type' => 'text', 'size' => 80, 'maxlength' => 255]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <?php echo $this->BcAdminForm->error('subject_user') ?>
        <div class="bca-helptext"><?php echo __d('baser_core', 'ユーザー宛の自動返信メールの件名に表示します。') ?></div>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('subject_admin', __d('baser_core', '自動送信メール件名<br>[管理者宛]'), ['escape' => false]) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('subject_admin', ['type' => 'text', 'size' => 80, 'maxlength' => 255]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <?php echo $this->BcAdminForm->error('subject_admin') ?>
        <div class="bca-helptext"><?php echo __d('baser_core', '管理者宛の自動送信メールの件名に表示します。') ?></div>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('redirect_url', __d('baser_core', 'リダイレクトURL')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('redirect_url', ['type' => 'text', 'size' => 80, 'maxlength' => 255]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <?php echo $this->BcAdminForm->error('redirect_url') ?>
        <div class="bca-helptext">
          <ul>
            <li><?php echo __d('baser_core', 'メール送信後、別のURLにリダイレクトする場合、ここにURLを指定します。') ?></li>
            <li><?php echo __d('baser_core', 'https からの完全なURLを指定してください。') ?></li>
          </ul>
        </div>
      </td>
    </tr>
    <?php echo $this->BcAdminForm->dispatchAfterForm() ?>
  </table>
</div>


<div class="bca-section" data-bca-section-type='form-group'>
  <div class="bca-collapse__action">
    <button
      type="button"
      class="bca-collapse__btn"
      data-bca-collapse="collapse"
      data-bca-target="#formOptionBody"
      aria-expanded="false"
      aria-controls="formOptionBody">
      <?php echo __d('baser_core', '詳細設定') ?>&nbsp;&nbsp;
      <i class="bca-icon--chevron-down bca-collapse__btn-icon"></i>
    </button>
  </div>
  <div class="bca-collapse" id="formOptionBody" data-bca-state="">
    <table class="form-table bca-form-table" data-bca-table-type="type2">
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('publish_begin', __d('baser_core', 'フォーム受付期間')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          &nbsp;&nbsp;
          <?php echo $this->BcAdminForm->control('publish_begin', [
            'type' => 'dateTimePicker',
            'size' => 12,
            'maxlength' => 10,
            'dateLabel' => ['text' => __d('baser_core', '開始日付')],
            'timeLabel' => ['text' => __d('baser_core', '開始時間')]
          ]) ?>
          &nbsp;〜&nbsp;
          <?php echo $this->BcAdminForm->control('publish_end', [
            'type' => 'dateTimePicker',
            'size' => 12,
            'maxlength' => 10,
            'dateLabel' => ['text' => __d('baser_core', '終了日付')],
            'timeLabel' => ['text' => __d('baser_core', '終了時間')]
          ]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <div class="bca-helptext">
            <p><?php echo __d('baser_core', '公開期間とは別にフォームの受付期間を設定する事ができます。受付期間外にはエラーではなく受付期間外のページを表示します。') ?></p>
          </div>
          <?php echo $this->BcAdminForm->error('publish_begin') ?>
          <?php echo $this->BcAdminForm->error('publish_end') ?>
        </td>
      </tr>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('save_info', __d('baser_core', 'データベース保存')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('save_info', ['type' => 'radio', 'options' => [1 => __d('baser_core', '送信情報をデータベースに保存する'), 0 => __d('baser_core', '送信情報をデータベースに保存しない')]]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('save_info') ?>
          <div class="bca-helptext">
            <ul>
              <li><?php echo __d('baser_core', 'メールフォームから送信された情報をデータベースに保存するかどうかを指定できます。') ?></li>
              <li><?php echo __d('baser_core', 'メールフォームから送信された情報をデータベースに保存したくない場合は、保存しないを指定してください。') ?></li>
            </ul>
          </div>
        </td>
      </tr>

			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('auth_captcha', __d('baser', 'イメージ認証')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcAdminForm->control('auth_captcha', ['type' => 'checkbox', 'label' => __d('baser', '利用する')]) ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<?php echo $this->BcAdminForm->error('auth_captcha') ?>
					<div class="bca-helptext">
						<ul>
							<li><?php echo __d('baser', 'メールフォーム送信の際、表示された画像の文字入力させる事で認証を行ないます。') ?></li>
							<li><?php echo __d('baser', 'スパムなどいたずら送信が多いが多い場合に設定すると便利です。') ?></li>
						</ul>
					</div>
				</td>
			</tr>

      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('ssl_on', __d('baser_core', 'SSL通信')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('ssl_on', ['type' => 'checkbox', 'label' => __d('baser_core', '利用する')]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <div class="bca-helptext"><?php echo __d('baser_core', '管理者ページでSSLを利用する場合は、事前にSSLの申込、設定が必要です。また、SSL通信で利用するURLをシステム設定で指定している必要があります。') ?></div>
          <?php echo $this->BcAdminForm->error('ssl_on',
            __d('baser_core', 'SSL通信を利用するには、{0} で、事前にSSL通信用のWebサイトURLを指定してください。',
            $this->BcBaser->getLink(
              __d('baser_core', 'システム設定'),
              ['controller' => 'SiteConfigs', 'action' => 'index', 'plugin' => 'BaserCore'],
              ['target' => '_blank'])),
            ['escape' => false]
          ) ?>
        </td>
      </tr>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('sender_2', __d('baser_core', 'BCC用送信先メールアドレス')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('sender_2', ['type' => 'text', 'size' => 80, 'maxlength' => 255]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('sender_2') ?>
          <div class="bca-helptext">
            <ul>
              <li><?php echo __d('baser_core', 'BCC（ブラインドカーボンコピー）用のメールアドレスを指定します。') ?></li>
              <li><?php echo __d('baser_core', '複数の送信先を指定するには、カンマで区切って入力します。') ?></li>
            </ul>
          </div>
        </td>
      </tr>
      <?php if (\Cake\Core\Plugin::isLoaded('BcWidgetArea')): ?>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('widget_area', __d('baser_core', 'ウィジェットエリア')) ?>
          &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('widget_area', [
            'type' => 'select',
            'options' => $this->BcAdminForm->getControlsource('BcWidgetArea.WidgetAreas.id'),
            'empty' => __d('baser_core', 'サイト基本設定に従う')
          ]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('widget_area') ?>
          <div class="bca-helptext">
            <?php echo sprintf(__d('baser_core', 'メールコンテンツで利用するウィジェットエリアを指定します。<br>ウィジェットエリアは「%s」より追加できます。'), $this->BcBaser->getLink(__d('baser_core', 'ウィジェットエリア管理'), ['plugin' => 'BcWidgetArea', 'controller' => 'WidgetAreas', 'action' => 'index'])) ?>
          </div>
        </td>
      </tr>
      <?php endif ?>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('form_template', __d('baser_core', 'メールフォームテンプレート名')) ?>
          &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('form_template', [
            'type' => 'select',
            'options' => $this->Mail->getFormTemplates($this->BcAdminForm->getSourceValue('content.site_id'))
          ]) ?>
          <?php if ($this->getRequest()->getParam('action') === 'edit' && \Cake\Core\Plugin::isLoaded('BcThemeFile')): ?>
            <?php echo $this->BcAdminForm->control('edit_mail_form', ['type' => 'hidden']) ?>
            <?php $this->BcAdminForm->unlockField('edit_mail_form') ?>
            <?php $this->BcBaser->link('<i class="bca-icon--edit"></i>' . __d('baser_core', '編集する'), 'javascript:void(0)', ['id' => 'EditForm', 'escape' => false]) ?>
          <?php endif ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('form_template') ?>
          <div class="bca-helptext">
            <ul>
              <li><?php echo __d('baser_core', 'メールフォーム本体のテンプレートを指定します。') ?></li>
            </ul>
          </div>
        </td>
      </tr>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('mail_template', __d('baser_core', '送信メールテンプレート名')) ?>
          &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('mail_template', ['type' => 'select', 'options' => $this->Mail->getMailTemplates($this->BcAdminForm->getSourceValue('content.site_id'))]) ?>
          <?php if ($this->getRequest()->getParam('action') === 'edit' && \Cake\Core\Plugin::isLoaded('BcThemeFile')): ?>
            <?php echo $this->BcAdminForm->control('edit_mail', ['type' => 'hidden']) ?>
            <?php $this->BcAdminForm->unlockField('edit_mail') ?>
            <?php $this->BcBaser->link('<i class="bca-icon--edit"></i>' . __d('baser_core', '編集する'), 'javascript:void(0)', ['id' => 'EditMail', 'escape' => false]) ?>
          <?php endif ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('mail_template') ?>
          <div class="bca-helptext">
            <ul>
              <li><?php echo __d('baser_core', '送信するメールのテンプレートを指定します。') ?></li>
            </ul>
          </div>
        </td>
      </tr>
      <?php echo $this->BcAdminForm->dispatchAfterForm('option') ?>
    </table>
  </div>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>
