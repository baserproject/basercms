<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * システム基本設定 フォーム
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BaserCore\Model\Entity\SiteConfig $siteConfig
 * @var array $mailEncodeList
 * @var bool $isWritableEnv
 * @var array $modeList
 * @var array $adminThemeList
 * @var array $editorList
 * @checked
 * @unitTest
 */

$this->BcAdmin->setTitle(__d('baser_core', 'システム基本設定'));
$this->BcAdmin->setHelp('site_configs_form');
$this->BcBaser->i18nScript([
  'alertMessage1' => __d('baser_core', '管理システムをSSLに切り替える場合には、SSL用のURLを登録してください。'),
  'alertMessage2' => __d('baser_core', 'テストメールを送信に失敗しました。'),
  'confirmMessage1' => __d('baser_core', '管理システムをSSLに切り替えようとしています。よろしいですか？<br><br>サーバがSSLに対応していない場合、管理システムを表示する事ができなくなってしまいますのでご注意ください。<br><br>もし、表示する事ができなくなってしまった場合は、 /app/Config/install.php の、 BcEnv.sslUrl の値を調整するか、BcApp.adminSsl の値を false に書き換えて復旧してください。'),
  'confirmMessage2' => __d('baser_core', 'テストメールを送信します。いいですか？'),
  'infoMessage1' => __d('baser_core', 'テストメールを送信しました。'),
  'confirmTitle1' => __d('baser_core', '管理システムSSL設定確認')
], ['escape' => false]);
$this->BcBaser->js('admin/site_configs/index.bundle', false, ['id' => 'AdminSiteConfigsFormScript',
  'data-isAdminSsl' => (string)$siteConfig->admin_ssl
]);
?>


<section class="bca-section" data-bca-section-type='form-group'>
  <h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser_core', '基本項目') ?></h2>
  <?php echo $this->BcAdminForm->create($siteConfig, ['id' => 'SiteConfigFormForm']) ?>
  <?php echo $this->BcFormTable->dispatchBefore() ?>

  <table class="form-table bca-form-table section" data-bca-table-type="type2">
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('email', __d('baser_core', '管理者メールアドレス')) ?>
        &nbsp;<span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('email', ['type' => 'text', 'size' => 35, 'maxlength' => 255]) ?>
        <?php echo $this->BcAdminForm->error('email') ?>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('site_url', __d('baser_core', 'WebサイトURL')) ?>
        &nbsp;<span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <input type="password" name="dummy-site_url" style="display: none">
        <?php $this->BcAdminForm->unlockFields('dummy-site_url') ?>
        <?php echo $this->BcAdminForm->control('site_url', ['type' => 'text', 'size' => 35, 'maxlength' => 255, 'data-margin' => 'bottom', 'disabled' => !$isWritableEnv]) ?>
        <br>
        <input type="password" name="dummy-ssl_url" style="display: none">
        <?php $this->BcAdminForm->unlockFields('dummy-ssl_url') ?>
        <?php echo $this->BcAdminForm->control('ssl_url', [
            'type' => 'text',
            'size' => 35,
            'maxlength' => 255,
            'disabled' => !$isWritableEnv]
        ) ?> <small>[SSL]</small>
        <i class="bca-icon--question-circle bca-help"></i>
        <?php echo $this->BcAdminForm->error('site_url') ?>
        <?php echo $this->BcAdminForm->error('ssl_url') ?>
        <div class="bca-helptext">
          <?php echo __d('baser_core', 'baserCMSを設置しているURLを指定します。管理画面等でSSL通信を利用する場合は、SSL通信で利用するURLも指定します。') ?>
        </div>
      </td>
    </tr>

    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('maintenance', __d('baser_core', '公開状態')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('maintenance', [
          'type' => 'select',
          'options' => [0 => __d('baser_core', '公開中'), 1 => __d('baser_core', 'メンテナンス中')]
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser_core',
            '公開状態を指定します。<br>
            メンテナンス中の場合に、公開ページを確認するには、管理画面にログインする必要があります。<br>
            ただし、制作・開発モードがデバッグモードに設定されている場合は、メンテナンス中にしていても公開ページが表示されてしまいますので注意が必要です。'
          ) ?>
        </div>
      </td>
    </tr>

    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('mode', __d('baser_core', '制作・開発モード')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('mode', [
          'type' => 'select',
          'options' => $modeList,
          'disabled' => !$isWritableEnv
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser_core',
            '制作・開発時のモードを指定します。通常は、ノーマルモードを指定しておきます。<br>
            ※ CakePHPのデバッグモードを指します。<br>
            ※ インストールモードはbaserCMSを初期化する場合にしか利用しませんので普段は利用しないようにしてください。'
          ) ?>
        </div>
      </td>
    </tr>

    <?php echo $this->BcAdminForm->dispatchAfterForm() ?>

  </table>
</section>

<section class="bca-section" data-bca-section-type='form-group'>
  <div class="bca-collapse__action">
    <button type="button" class="bca-collapse__btn" data-bca-collapse="collapse"
            data-bca-target="#formAdminSettingBody" aria-expanded="false"
            aria-controls="formAdminSettingBody">
      <?php echo __d('baser_core', '管理画面設定') ?>&nbsp;&nbsp;
      <i class="bca-icon--chevron-down bca-collapse__btn-icon"></i>
    </button>
  </div>
  <div class="bca-collapse" id="formAdminSettingBody" data-bca-state="">
    <table class="form-table bca-form-table section" data-bca-table-type="type2">
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('admin_ssl', __d('baser_core', '管理画面SSL設定')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('admin_ssl', [
            'type' => 'radio',
            'options' => $this->BcText->booleanDoList(__d('baser_core', 'SSL通信を利用')),
            'separator' => '　',
            'legend' => false,
            'disabled' => !$isWritableEnv
          ]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('admin_ssl') ?>
          <div class="bca-helptext">
            <?php echo __d('baser_core', '管理者ページでSSLを利用する場合は、事前にSSLの申込、設定が必要です。<br>また、SSL用のWebサイトURLの指定が必要です。') ?>
          </div>
        </td>
      </tr>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('admin_list_num', __d('baser_core', '管理画面テーマ')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('admin_theme', ['type' => 'select', 'options' => $adminThemeList]) ?>
          <?php echo $this->BcAdminForm->error('admin_theme') ?>
        </td>
      </tr>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('admin_list_num', __d('baser_core', '初期一覧件数')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('admin_list_num', ['type' => 'select', 'options' => [
            10 => __d('baser_core', '10件'),
            20 => __d('baser_core', '20件'),
            50 => __d('baser_core', '50件'),
            100 => __d('baser_core', '100件')
          ]])
          ?>
          <?php echo $this->BcAdminForm->error('admin_list_num') ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <div class="bca-helptext">
            <?php echo __d('baser_core', 'ダッシュボードに表示される「最近の動き」などの表示件数を設定します') ?>
          </div>
        </td>
      </tr>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('login_credit', __d('baser_core', 'ログインページのクレジット表示')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('login_credit', [
            'type' => 'radio',
            'options' => $this->BcText->booleanDoList(__d('baser_core', '利用'))
          ]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <div class="bca-helptext"><?php echo __d('baser_core', 'ログインページに表示されているクレジット表示を利用するかどうか設定します。') ?></div>
          <?php echo $this->BcAdminForm->error('login_credit') ?>
        </td>
      </tr>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('admin_side_banner', __d('baser_core', 'サイドバーのバナー表示')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('admin_side_banner', [
            'type' => 'radio',
            'options' => $this->BcText->booleanDoList(__d('baser_core', '利用'))
          ]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <div class="bca-helptext"><?php echo __d('baser_core', '管理システムのサイド部分にバナーを表示するかどうか設定します。') ?></div>
          <?php echo $this->BcAdminForm->error('admin_side_banner') ?>
        </td>
      </tr>

      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('use_update_notice', __d('baser_core', 'アップデート通知')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('use_update_notice', [
            'type' => 'checkbox',
            'label' => __d('baser_core', '管理システムのアップデート通知を有効にする')
          ]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <div class="bca-helptext"><?php echo __d('baser_core', '管理システム自体のアップデートに関する通知を有効する場合にはチェックを入れます。
          左サイドメニューに更新ボタンが表示され、利用可能なアップデートが存在する場合にバッジが付きます。') ?></div>
          <?php echo $this->BcAdminForm->error('use_update_notice') ?>
        </td>
      </tr>

      <?php echo $this->BcAdminForm->dispatchAfterForm('Admin') ?>

    </table>
  </div>
</section>

<section class="bca-section" data-bca-section-type='form-group'>
  <div class="bca-collapse__action">
    <button type="button"
      class="bca-collapse__btn"
      data-bca-collapse="collapse"
      data-bca-target="#formOuterServiceSettingBody"
      aria-expanded="false"
      aria-controls="formOuterServiceSettingBody">
        <?php echo __d('baser_core', '外部サービス設定') ?>&nbsp;&nbsp;
        <i class="bca-icon--chevron-down bca-collapse__btn-icon"></i>
    </button>
  </div>

  <div class="bca-collapse" id="formOuterServiceSettingBody" data-bca-state="">
    <table class="form-table bca-form-table" data-bca-table-type="type2">
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('address', __d('baser_core', 'GoogleMaps住所')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('address', ['type' => 'text', 'size' => 35, 'maxlength' => 255, 'placeholder' => __d('baser_core', '住所'), 'data-margin' => 'bottom']) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <div class="bca-helptext">
            <?php echo __d('baser_core',
              'GoogleMapを利用する場合は地図を表示させたい住所を入力してください。郵便番号からでも大丈夫です。<br><br>
              入力例1) 福岡市中央区大名2-11-25<br>
              入力例2) 〒819-0041 福岡県福岡市中央区大名2-11-25<br><br>
              建物名を含めるとうまく表示されない場合があります。<br>
              その時は建物名を省略して試してください。<br>
              APIキーを入力しないと地図が表示されない場合があります。<br>
              <a href="https://developers.google.com/maps/web/" target="_blank">「ウェブ向け Google Maps API」</a>'
            ) ?>
          </div>
          <br>
          <?php echo $this->BcAdminForm->control('google_maps_api_key', [
            'type' => 'text',
            'size' => 35,
            'maxlength' => 255,
            'placeholder' => __d('baser_core', 'APIキー')
          ]) ?>
          <?php echo $this->BcAdminForm->error('address') ?>
          <?php echo $this->BcAdminForm->error('google_maps_api_key') ?>
        </td>
      </tr>

      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('google_analytics_id', __d('baser_core', 'Google Analytics<br>トラッキングID'), ['escape' => false]) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('google_analytics_id', ['type' => 'text', 'size' => 35, 'maxlength' => 16, 'data-margin' => 'bottom']) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <div class="bca-helptext">
            <?php echo __d('baser_core',
              'Googleの無料のアクセス解析サービス <a href="http://www.google.com/intl/ja/analytics/" target="_blank">Google Analytics</a> を
              利用される方は、取得したトラッキングID (G-0000000000 のような文字列）を入力してください。'
            ) ?>
            <br/>
            ※<?php echo __d('baser_core', '事前に<a href="http://www.google.com/intl/ja/analytics/" target="_blank">Google Analytics</a> で登録作業が必要です。') ?>
            <br/>
            <?php echo __d('baser_core', 'テンプレートで利用する場合は、 <pre>&lt;?php $this->BcBaser->googleAnalytics() ?&gt;</pre> で出力します。') ?>
          </div>
          <?php echo $this->BcAdminForm->error('google_analytics_id') ?><br/>
        </td>
      </tr>

      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('outer_service_output_header', __d('baser_core', 'ヘッダー埋め込みスクリプト')) ?>
        </th>

        <td class="col-input bca-form-table__input">
          <div class="bca-collapse-outer-service">
            <?php echo $this->BcAdminForm->control('outer_service_output_header', [
              'type' => 'textarea',
              'cols' => 36,
              'rows' => 5,
              'data-input-text-size' => 'full-counter'
            ]) ?>
          </div>
        </td>
      </tr>

      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('outer_service_output_footer', __d('baser_core', 'フッター埋め込みスクリプト')) ?>
        </th>

        <td class="col-input bca-form-table__input">
          <div class="bca-collapse-outer-service">
            <?php echo $this->BcAdminForm->control('outer_service_output_footer', [
              'type' => 'textarea',
              'cols' => 36,
              'rows' => 5,
              'data-input-text-size' => 'full-counter'
            ]) ?>
          </div>
        </td>
      </tr>

      <?php echo $this->BcAdminForm->dispatchAfterForm('OuterService') ?>

    </table>

  </div>

</section>

<section class="bca-section" data-bca-section-type='form-group'>
  <div class="bca-collapse__action">
    <button type="button"
      class="bca-collapse__btn"
      data-bca-collapse="collapse"
      data-bca-target="#formSubSiteSettingBody"
      aria-expanded="false"
      aria-controls="formSubSiteSettingBody">
        <?php echo __d('baser_core', 'サイト設定') ?>&nbsp;&nbsp;
        <i class="bca-icon--chevron-down bca-collapse__btn-icon"></i>
    </button>
  </div>
  <div class="bca-collapse" id="formSubSiteSettingBody" data-bca-state="">
    <table class="form-table bca-form-table section" data-bca-table-type="type2">

      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('use_site_device_setting', __d('baser_core', 'デバイス・言語設定')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('use_site_device_setting', [
            'type' => 'checkbox',
            'label' => __d('baser_core', 'サイト管理でデバイス設定を利用する')
          ]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <div class="bca-helptext">
            <?php echo __d('baser_core', 'サイトにデバイス属性を持たせ、サイトアクセス時、ユーザーエージェントを判定し適切なサイトを表示する機能を利用します。') ?>
          </div>
          <br>
          <?php echo $this->BcAdminForm->control('use_site_lang_setting', [
            'type' => 'checkbox',
            'label' => __d('baser_core', 'サイト管理で言語設定を利用する')
          ]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <div class="bca-helptext">
            <?php echo __d('baser_core', 'サイトに言語属性を持たせ、サイトアクセス時、ブラウザの言語設定を判定し適切なサイトを表示する機能を利用します。') ?>
          </div>
          <?php echo $this->BcAdminForm->error('use_site_device_setting') ?>
          <?php echo $this->BcAdminForm->error('use_site_lang_setting') ?>
        </td>
      </tr>

      <?php echo $this->BcAdminForm->dispatchAfterForm('Site') ?>

    </table>
  </div>
</section>

<section class="bca-section" data-bca-section-type='form-group'>
  <div class="bca-collapse__action">
    <button type="button"
      class="bca-collapse__btn"
      data-bca-collapse="collapse"
      data-bca-target="#formEditorSettingBody"
      aria-expanded="false"
      aria-controls="formEditorSettingBody">
        <?php echo __d('baser_core', 'エディタ設定') ?>&nbsp;&nbsp;
        <i class="bca-icon--chevron-down bca-collapse__btn-icon"></i>
    </button>
  </div>
  <div class="bca-collapse" id="formEditorSettingBody" data-bca-state="">
    <table class="form-table bca-form-table section" data-bca-table-type="type2">
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('editor_enter_br', __d('baser_core', 'エディタタイプ')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('editor', [
            'type' => 'radio',
            'options' => $editorList
          ]) ?>
        </td>
      </tr>
      <tr class="ckeditor-option">
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('editor_enter_br', __d('baser_core', '改行モード')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('editor_enter_br', [
            'type' => 'radio',
            'options' => [
              0 => __d('baser_core', '改行時に段落を挿入する'),
              1 => __d('baser_core', '改行時にBRタグを挿入する')
            ]])
          ?>
        </td>
      </tr>
      <tr class="ckeditor-option">
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('editor_styles', __d('baser_core', 'エディタスタイルセット')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('editor_styles', [
            'type' => 'textarea',
            'cols' => 36,
            'rows' => 10,
            'data-input-text-size' => 'full-counter'
          ]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('editor_styles') ?>
          <div class="bca-helptext">
            <p><?php echo __d('baser_core', '固定ページなどで利用するエディタのスタイルセットをCSS形式で記述する事ができます。') ?></p>
            <pre># <?php echo __d('baser_core', 'タイトル') ?>
              <?php echo __d('baser_core', 'タグ') ?> {
						<?php echo __d('baser_core', 'プロパティ名：プロパティ値') ?>
}

 《<?php echo __d('baser_core', '記述例') ?>》
 # <?php echo __d('baser_core', '見出し') ?>
 h2 {
	font-size:20px;
	color:#333;
 }
					</pre>
            <p><?php echo __d('baser_core', 'タグにプロパティを設定しない場合は次のように記述します。') ?></p>
            <pre>
# <?php echo __d('baser_core', '見出し') ?>
h2 {}
					</pre>
          </div>
        </td>
      </tr>

      <?php echo $this->BcAdminForm->dispatchAfterForm('Editor') ?>

    </table>
  </div>
</section>

<section class="bca-section" data-bca-section-type='form-group'>
  <div class="bca-collapse__action">
    <button type="button"
      class="bca-collapse__btn"
      data-bca-collapse="collapse"
      data-bca-target="#formMailSettingBody"
      aria-expanded="false"
      aria-controls="formMailSettingBody">
        <?php echo __d('baser_core', 'メール設定') ?>&nbsp;&nbsp;
        <i class="bca-icon--chevron-down bca-collapse__btn-icon"></i>
    </button>
  </div>
  <div class="bca-collapse" id="formMailSettingBody" data-bca-state="">
    <table class="form-table bca-form-table" data-bca-table-type="type2">

      <tr>
        <th class="bca-form-table__label">
          <?php echo $this->BcAdminForm->label('smtp_host', __d('baser_core', 'SMTP設定')) ?></th>
        <td class="col-input bca-form-table__input">
          <table class="bca-form-table__inner-table">
            <tr>
              <th class="bca-form-table__inner-table-label">
                <?php echo $this->BcAdminForm->label('smtp_host', __d('baser_core', 'ホスト')) ?>
              </th>
              <td class="bca-form-table__inner-table-input">
                <?php echo $this->BcAdminForm->control('smtp_host', [
                  'type' => 'text',
                  'size' => 35,
                  'maxlength' => 255,
                  'autocomplete' => 'off'
                ]) ?>
                <?php echo $this->BcAdminForm->error('smtp_host') ?>
                <i class="bca-icon--question-circle bca-help"></i>
                <div class="bca-helptext">
                  <?php echo __d('baser_core', 'メールの送信にSMTPサーバーを利用する場合指定します。') ?>
                </div>
              </td>
            </tr>
            <tr class="bca-form-table__inner-table-label">
              <th class="bca-form-table__inner-table-label">
                <?php echo $this->BcAdminForm->label('smtp_port', __d('baser_core', 'ポート')) ?>
              </th>
              <td class="bca-form-table__inner-table-input">
                <?php echo $this->BcAdminForm->control('smtp_port', ['type' => 'text', 'size' => 35, 'maxlength' => 255, 'autocomplete' => 'off']) ?>
                <?php echo $this->BcAdminForm->error('smtp_port') ?>
                <i class="bca-icon--question-circle bca-help"></i>
                <div class="bca-helptext">
                  <?php echo __d('baser_core', 'メールの送信にSMTPサーバーを利用する場合指定します。入力を省略した場合、25番ポートを利用します。') ?>
                </div>
              </td>
            </tr>
            <tr>
              <th class="bca-form-table__inner-table-label">
                <?php echo $this->BcAdminForm->label('smtp_user', __d('baser_core', 'ユーザー')) ?>
              </th>
              <td class="bca-form-table__inner-table-input">
                <?php echo $this->BcAdminForm->control('smtp_user', [
                  'type' => 'text',
                  'size' => 35,
                  'maxlength' => 255,
                  'autocomplete' => 'off'
                ]) ?>
                <?php echo $this->BcAdminForm->error('smtp_user') ?>
                <i class="bca-icon--question-circle bca-help"></i>
                <div class="bca-helptext">
                  <?php echo __d('baser_core', 'メールの送信にSMTPサーバーを利用する場合指定します。') ?>
                </div>
              </td>
            </tr>
            <tr>
              <th class="bca-form-table__inner-table-label">
                <?php echo $this->BcAdminForm->label('smtp_password', __d('baser_core', 'パスワード')) ?>
              </th>
              <td class="bca-form-table__inner-table-input">
                <?php echo $this->BcAdminForm->control('smtp_password', [
                  'type' => 'password',
                  'size' => 35,
                  'maxlength' => 255,
                  'autocomplete' => 'off'
                ]) ?>
                <?php echo $this->BcAdminForm->error('smtp_password') ?>
                <i class="bca-icon--question-circle bca-help"></i>
                <div class="bca-helptext">
                  <?php echo __d('baser_core', 'メールの送信にSMTPサーバーを利用する場合指定します。') ?>
                </div>
              </td>
            </tr>
            <tr>
              <th class="bca-form-table__inner-table-label">
                <?php echo $this->BcAdminForm->label('smtp_tls', __d('baser_core', 'TLS暗号化')) ?>
              </th>
              <td class="bca-form-table__inner-table-input">
                <?php echo $this->BcAdminForm->control('smtp_tls', [
                  'type' => 'radio',
                  'options' => $this->BcText->booleanDoList(__d('baser_core', 'TLS暗号化を利用'))
                ]) ?>
                <?php echo $this->BcAdminForm->error('smtp_tls') ?>
                <i class="bca-icon--question-circle bca-help"></i>
                <div class="bca-helptext">
                  <?php echo __d('baser_core', 'SMTPサーバーがTLS暗号化を利用する場合指定します。') ?>
                </div>
              </td>
            </tr>
          </table>
          <div class="bca-form-table__inner-submit">
            <?php echo $this->BcAdminForm->button(
              '<i class="bca-icon--mail"></i>' . __d('baser_core', 'メール送信テスト'),
              ['type' => 'button',
                'class' => 'bca-btn',
                'id' => 'BtnCheckSendmail',
                'escapeTitle' => false
              ]) ?>
            　<span id=ResultCheckSendmail></span>
            <?php $this->BcBaser->img('admin/ajax-loader-s.gif', [
              'id' => 'AjaxLoaderCheckSendmail',
              'style' => 'display:none',
              'class' => 'bca-small-loader'
            ]) ?>
          </div>
        </td>
      </tr>
      <tr>
        <th class="bca-form-table__label">
          <?php echo $this->BcAdminForm->label('mail_additional_parameters', __d('baser_core', 'additional_parameters（オプション）')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('mail_additional_parameters', [
            'type' => 'text',
            'size' => 35,
            'maxlength' => 255,
            'placeholder' => '-f webmaster@mail.example.com'
          ]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <div class="bca-helptext"><?php echo __d('baser_core', '標準機能によるメール送信時にオプションを追加します。') ?></div>
          <?php echo $this->BcAdminForm->error('mail_additional_parameters') ?>
        </td>
      </tr>

      <?php echo $this->BcAdminForm->dispatchAfterForm('Mail') ?>

    </table>
  </div>
</section>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="bca-actions">
  <?php echo $this->BcAdminForm->button(__d('baser_core', '保存'),
    [
      'type' => 'submit',
      'id' => 'BtnSave',
      'div' => false,
      'class' => 'button bca-btn',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',
    ]) ?>
</div>

<?php echo $this->BcAdminForm->end() ?>
