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

use BaserCore\View\BcAdminAppView;

/**
 * サブサイトフォーム
 *
 * @var BcAdminAppView $this
 * @var array $themes
 * @var \BaserCore\Model\Entity\Site $site
 * @var bool $useSiteDeviceSetting
 * @var bool $useSiteLangSetting
 * @var bool $isMainOnCurrentDisplay
 * @var array $siteList
 * @var array $selectableLangs
 * @var array $selectableDevices
 * @var array $selectableThemes
 * @checked
 * @unitTest
 * @noTodo
 */
$this->BcBaser->i18nScript([
  'confirmMessage1' => __d('baser_core', "サイトを削除してもよろしいですか？\nサイトに関連しているコンテンツは全てゴミ箱に入ります。"),
  'confirmMessage2' => __d('baser_core', 'エイリアスを本当に変更してもいいですか？<br><br>エイリアスを変更する場合、サイト全体のURLが変更となる為、保存に時間がかかりますのでご注意ください。'),
  'confirmTitle1' => __d('baser_core', 'エイリアス変更')
], ['escape' => false]);
$this->BcBaser->js('admin/sites/form.bundle', false, [
  'defer' => true
]);
?>


<?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']) ?>

<table class="form-table bca-form-table">
  <?php if ($this->request->getParam('action') === 'edit'): ?>
    <tr>
      <th class="bca-form-table__label"><?php echo $this->BcAdminForm->label('id', 'No') ?></th>
      <td class=" bca-form-table__input">
        <?php echo $this->BcAdminForm->getSourceValue('id') ?>
      </td>
    </tr>
  <?php endif ?>
  <?php if(!$isMainOnCurrentDisplay): ?>
  <tr>
    <th class="bca-form-table__label"><?php echo $this->BcAdminForm->label('name', __d('baser_core', '識別名称')) ?>
      &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span></th>
    <td class=" bca-form-table__input">
      <?php echo $this->BcAdminForm->control('name', ['type' => 'text', 'size' => '30', 'autofocus' => true]) ?>
      <i class="bca-icon--question-circle bca-help"></i>
      <div
        class="bca-helptext"><?php echo __d('baser_core', 'サイトを特定する事ができる識別名称を入力します。半角英数とハイフン（-）・アンダースコア（_）のみが利用できます。') ?></div>
      　<span
        style="white-space: nowrap;"><small>[<?php echo $this->BcAdminForm->label('alias', __d('baser_core', 'エイリアス')) ?>]</small>
			<?php echo $this->BcAdminForm->control('alias', ['type' => 'text', 'size' => '10']) ?></span>
      <i class="bca-icon--question-circle bca-help"></i>
      <div
        class="bca-helptext"><?php echo __d('baser_core', 'サイトのURLに利用します。エイリアスは半角英数に加えハイフン（-）・アンダースコア（_）・スラッシュ（/）・ドット（.）が利用できます。空欄に設定した場合、自動的に識別名称と同じものを設定します。') ?></div>
      <?php echo $this->BcAdminForm->error('name') ?>
      <?php echo $this->BcAdminForm->error('alias') ?>
    </td>
  </tr>
  <?php endif ?>
  <tr>
    <th class="bca-form-table__label"><?php echo $this->BcAdminForm->label('display_name', __d('baser_core', 'サイト名')) ?>
      &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span></th>
    <td class=" bca-form-table__input">
      <?php echo $this->BcAdminForm->control('display_name', ['type' => 'text', 'size' => '60']) ?>
      <i class="bca-icon--question-circle bca-help"></i>
      <div
        class="bca-helptext"><?php echo __d('baser_core', 'サイト名を入力します。管理システムでの表示に利用されます。日本語の入力が可能ですのでわかりやすい名前をつけてください。') ?></div>
      <?php echo $this->BcAdminForm->error('display_name') ?>
    </td>
  </tr>
  <tr>
    <th class="bca-form-table__label"><?php echo $this->BcAdminForm->label('title', __d('baser_core', 'サイトタイトル')) ?>
      &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span></th>
    <td class="bca-form-table__input">
      <?php echo $this->BcAdminForm->control('title', ['type' => 'text', 'size' => '60']) ?>
      <i class="bca-icon--question-circle bca-help"></i>
      <div class="bca-helptext"><?php echo __d('baser_core', 'サイトのタイトルを入力します。タイトルタグに利用されます。') ?></div>
      <?php echo $this->BcAdminForm->error('title') ?>
    </td>
  </tr>
  <tr>
    <th
      class="bca-form-table__label"><?php echo $this->BcAdminForm->label('keyword', __d('baser_core', 'サイト基本キーワード')) ?></th>
    <td
      class="bca-form-table__input"><?php echo $this->BcAdminForm->control('keyword', ['type' => 'text', 'size' => 55, 'maxlength' => 255, 'counter' => true, 'class' => 'bca-textbox__input full-width']) ?>
      <i class="bca-icon--question-circle bca-help"></i>
      <div class="bca-helptext"><?php echo __d('baser_core', 'テンプレートで利用する場合は、<br>&lt;?php $this->BcBaser->metaKeywords() ?&gt; で出力します。') ?></div>
      <?php echo $this->BcAdminForm->error('keyword') ?>
    </td>
  </tr>
  <tr>
    <th
      class="bca-form-table__label"><?php echo $this->BcAdminForm->label('description', __d('baser_core', 'サイト基本説明文')) ?></th>
    <td
      class="bca-form-table__input"><?php echo $this->BcAdminForm->control('description', ['type' => 'textarea', 'cols' => 20, 'rows' => 6, 'maxlength' => 255, 'counter' => true]) ?>
      <i class="bca-icon--question-circle bca-help"></i>
      <div class="bca-helptext"><?php echo __d('baser_core', 'テンプレートで利用する場合は、<br>&lt;?php $this->BcBaser->metaDescription() ?&gt; で出力します。') ?></div>
      <?php echo $this->BcAdminForm->error('description') ?>
    </td>
  </tr>
  <?php if(!$isMainOnCurrentDisplay): ?>
  <tr>
    <th
      class="bca-form-table__label"><?php echo $this->BcAdminForm->label('main_site_id', __d('baser_core', 'メインサイト')) ?></th>
    <td class=" bca-form-table__input">
      <?php echo $this->BcAdminForm->control('main_site_id', ['type' => 'select', 'options' => $siteList]) ?>
      <?php echo $this->BcAdminForm->control('relate_main_site', ['type' => 'checkbox', 'label' => __d('baser_core', 'エイリアスを利用してメインサイトと自動連携する')]) ?>
      <i class="bca-icon--question-circle bca-help"></i>
      <div class="bca-helptext">
        <p><?php echo __d('baser_core', '対象サイトの主として連携させたいサイトを選択します。') ?></p>
        <p><?php echo __d('baser_core', '「エイリアスを利用してメインサイトと自動連携する」にチェックを入れておくと、メインサイトでコンテンツの追加や削除が発生した場合、エイリアスを利用して自動的にサブサイトで同様の処理を実行します。') ?></p>
      </div>
      <?php echo $this->BcAdminForm->error('main_site_id') ?>
    </td>
  </tr>
  <?php endif ?>
  <?php if ($useSiteDeviceSetting || $useSiteLangSetting): ?>
    <tr>
      <th class="bca-form-table__label"><?php echo $this->BcAdminForm->label('device', __d('baser_core', 'デバイス・言語')) ?></th>
      <td class=" bca-form-table__input">
        <?php if ($useSiteDeviceSetting): ?>
          <small><?php echo __d('baser_core', '[デバイス]') ?></small>&nbsp;<?php echo $this->BcAdminForm->control('device', ['type' => 'select', 'options' => $selectableDevices]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <div
            class="bca-helptext"><?php echo __d('baser_core', 'サイトにデバイス属性を持たせ、サイトアクセス時、ユーザーエージェントを判定し適切なサイトを表示する機能を利用します。') ?></div>
        <?php else: ?>
          <?php echo $this->BcAdminForm->control('device', ['type' => 'hidden']) ?>
        <?php endif ?>
        <?php if ($useSiteLangSetting): ?>
          <small><?php echo __d('baser_core', '[言語]') ?></small><?php echo $this->BcAdminForm->control('lang', ['type' => 'select', 'options' => $selectableLangs]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <div
            class="bca-helptext"><?php echo __d('baser_core', 'サイトに言語属性を持たせ、サイトアクセス時、ブラウザの言語設定を判定し適切なサイトを表示する機能を利用します。') ?></div>
        <?php else: ?>
          <?php echo $this->BcAdminForm->control('lang', ['type' => 'hidden']) ?>
        <?php endif ?>
        <div id="SectionAccessType" style="display:none">
          <small><?php echo __d('baser_core', '[アクセス設定]') ?></small>
          <br>
          <span
            id="SpanSiteSameMainUrl"><?php echo $this->BcAdminForm->control('same_main_url', ['type' => 'checkbox', 'label' => __d('baser_core', 'メインサイトと同一URLでアクセス')]) ?>&nbsp;
					<i class="bca-icon--question-circle bca-help"></i>
					<div
            class="bca-helptext"><?php echo __d('baser_core', 'メインサイトと同一URLでアクセスし、デバイス設定や言語設定を判定し、適切なサイトを表示します。このオプションをオフにした場合は、エイリアスを利用した別URLを利用したアクセスとなります。') ?></div>
				</span>
          <br>
          <span
            id="SpanSiteAutoRedirect"><?php echo $this->BcAdminForm->control('auto_redirect', ['type' => 'checkbox', 'label' => __d('baser_core', 'メインサイトから自動的にリダイレクト')]) ?>&nbsp;
					<i class="bca-icon--question-circle bca-help"></i>
					<span
            class="bca-helptext"><?php echo __d('baser_core', 'メインサイトと別URLでアクセスする際、デバイス設定や言語設定を判定し、適切なサイトへリダイレクトします。') ?></span>
				</span>
          <br>
          <span
            id="SpanSiteAutoLink"><?php echo $this->BcAdminForm->control('auto_link', ['type' => 'checkbox', 'label' => __d('baser_core', '全てのリンクをサイト用に変換する')]) ?>&nbsp;
					<i class="bca-icon--question-circle bca-help"></i>
					<span
            class="bca-helptext"><?php echo __d('baser_core', 'メインサイトと別URLでアクセスし、エイリアスを利用して同一コンテンツを利用する場合、コンテンツ内の全てのリンクをサイト用に変換します。') ?></span>
				</span>
        </div>
        <?php echo $this->BcAdminForm->error('device') ?>
        <?php echo $this->BcAdminForm->error('lang') ?>
      </td>
    </tr>
  <?php endif ?>
  <tr>
    <th class="bca-form-table__label"><?php echo $this->BcAdminForm->label('theme', __d('baser_core', 'テーマ')) ?></th>
    <td class=" bca-form-table__input">
      <?php echo $this->BcAdminForm->control('theme', ['type' => 'select', 'options' => $selectableThemes]) ?>
      <i class="bca-icon--question-circle bca-help"></i>
      <div
        class="bca-helptext"><?php echo __d('baser_core', 'サイトのテンプレートは、各テンプレートの配置フォルダ内にサイト名のサブフォルダを作成する事で別途配置する事ができますが、テーマフォルダ自体を別にしたい場合はここでテーマを指定します。') ?></div>
      <?php echo $this->BcAdminForm->error('theme') ?>
    </td>
  </tr>
  <tr>
    <th class="bca-form-table__label"><?php echo $this->BcAdminForm->label('status', __d('baser_core', '公開状態')) ?></th>
    <td class=" bca-form-table__input">
      <?php echo $this->BcAdminForm->control('status', ['type' => 'radio', 'options' => [0 => __d('baser_core', '公開しない'), 1 => __d('baser_core', '公開する')]]) ?>
      <?php echo $this->BcAdminForm->error('status') ?>
    </td>
  </tr>
  <?php echo $this->BcAdminForm->dispatchAfterForm() ?>
</table>

<?php if(!$isMainOnCurrentDisplay): ?>
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
      <th class="bca-form-table__label"><?php echo $this->BcAdminForm->label('use_subdomain', __d('baser_core', '外部ドメイン利用')) ?></th>
      <td class=" bca-form-table__input">
        <?php echo $this->BcAdminForm->control('use_subdomain', [
          'type' => 'radio',
          'options' => [0 => __d('baser_core', '利用しない'), 1 => __d('baser_core', '利用する')],
          'default' => 0
        ]) ?>
        <?php echo $this->BcAdminForm->error('use_subdomain') ?>
      </td>
    </tr>
    <tr id="DomainType">
      <th class="bca-form-table__label"><?php echo $this->BcAdminForm->label('domain_type', __d('baser_core', 'ドメインタイプ')) ?></th>
      <td class=" bca-form-table__input">
        <?php echo $this->BcAdminForm->control('domain_type', [
          'type' => 'radio',
          'options' => [1 => __d('baser', 'サブドメイン'), 2 => __d('baser', '外部ドメイン')],
          'default' => 0
        ]) ?>
        <?php echo $this->BcAdminForm->error('domain_type') ?>
      </td>
    </tr>
    </table>
  </div>
</div>
<?php endif ?>
