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

use Cake\Routing\Router;
use BaserCore\View\BcAdminAppView;
use BaserCore\Model\Entity\Content;
use BaserCore\Model\Entity\ContentFolder;

/**
 * [ADMIN] 統合コンテンツフォーム
 *
 * @var BcAdminAppView $this
 * @var bool $related 親サイトに連携する設定で、エイリアス、もしくはフォルダであるかどうか
 *                                        上記に一致する場合、URLに関わるコンテンツ名は編集できない
 * @var bool $editable コンテンツ編集不可かどうか
 * @var Content $content
 * @var ContentFolder $contentFolder
 * @var array $parentContents
 * @var string $fullUrl
 * @checked
 * @noTodo
 * @unitTest
 */

$this->BcBaser->js('admin/contents/edit.bundle', false, ['id' => 'AdminContentsEditScript',
  'data-previewurl' => Router::url(["plugin" => "BaserCore", "controller" => "preview", "action" => "view"]),
  'data-fullurl' => rawurldecode($fullUrl),
  'data-current' => $content,
  'data-settings' => $this->BcContents->getJsonItems()
]);
$this->BcBaser->i18nScript([
  'contentsEditConfirmMessage1' => __d('baser_core', 'コンテンツをゴミ箱に移動してもよろしいですか？'),
  'contentsEditConfirmMessage2' => __d('baser_core', "エイリアスを削除してもよろしいですか？\nエイリアスはゴミ箱に入らず完全に削除されます。"),
  'contentsEditConfirmMessage3' => __d('baser_core', 'このコンテンツを元に %s にエイリアスを作成します。よろしいですか？'),
  'contentsEditConfirmMessage4' => __d('baser_core', 'このコンテンツを元に %s にコピーを作成します。よろしいですか？'),
  'contentsEditInfoMessage1' => __d('baser_core', 'エイリアスを作成しました。作成先の編集画面に移動しますのでしばらくお待ち下さい。'),
  'contentsEditInfoMessage2' => __d('baser_core', 'コピーを作成しました。作成先の編集画面に移動しますのでしばらくお待ち下さい。'),
  'contentsEditAlertMessage1' => __d('baser_core', 'エイリアスの作成に失敗しました。'),
  'contentsEditAlertMessage2' => __d('baser_core', '指定したサイトの同じ階層上にフォルダではない同名のコンテンツが存在します。エイリアスの作成を実行する前に、指定したサイト上の同名コンテンツを確認し名称を変更してください。'),
  'contentsEditAlertMessage3' => __d('baser_core', '指定したサイトの同じ階層上にフォルダではない同名のコンテンツが存在します。コピーの作成を実行する前に、指定したサイト上の同名コンテンツを確認し名称を変更してください。'),
  'contentsEditAlertmessage4' => __d('baser_core', 'コピーの作成に失敗しました。')
]);
$isOmitViewAction = $this->BcContents->getConfig('items')[$content->type]['omitViewAction'];

// サブドメイン
if ($content->site->use_subdomain) {
  $contentsName = '';
  if (!$content->site_root) {
    $contentsName = $this->BcAdminForm->getSourceValue("content.name");
    if (!$isOmitViewAction && $content->url !== '/') {
      $contentsName .= '/';
    }
  }
} else {
  if ($this->request->getData('Site.same_main_url') && $content->site_root) {
    $contentsName = '';
  } else {
    $contentsName = rawurldecode($this->BcAdminForm->getSourceValue("content.name"));
  }
  if (!$isOmitViewAction && $content->url !== '/' && $contentsName) {
    $contentsName .= '/';
  }
}
$linkedFullUrl = $this->BcContents->getFolderLinkedUrl($content) . $contentsName;
$editable = $this->BcContents->isEditable($content);
?>


<?php echo $this->BcAdminForm->hidden("content.id") ?>
<?php echo $this->BcAdminForm->hidden("content.plugin") ?>
<?php echo $this->BcAdminForm->hidden("content.type") ?>
<?php echo $this->BcAdminForm->hidden("content.entity_id") ?>
<?php echo $this->BcAdminForm->hidden("content.url") ?>
<?php echo $this->BcAdminForm->hidden("content.alias_id") ?>
<?php echo $this->BcAdminForm->hidden("content.site_root") ?>
<?php echo $this->BcAdminForm->hidden("content.site_id") ?>
<?php echo $this->BcAdminForm->hidden("content.lft") ?>
<?php echo $this->BcAdminForm->hidden("content.rght") ?>
<?php echo $this->BcAdminForm->hidden("content.status") ?>
<?php echo $this->BcAdminForm->hidden("content.main_site_content_id") ?>
<?php echo $this->BcAdminForm->hidden("content.publish_begin") ?>


<?php if($fullUrl): ?>
<div class="bca-section bca-section__post-top">
  <span class="bca-post__url">
	  <a href="<?php echo h($fullUrl) ?>"
	    class="bca-text-url"
	    target="_blank"
	    data-toggle="tooltip"
      data-placement="top"
      title="<?php echo __d('baser_core', '公開URLを開きます') ?>">
      <i class="bca-icon--globe"></i><?php echo rawurldecode($fullUrl) ?>
    </a>
	  <?php echo $this->BcAdminForm->button('', [
      'id' => 'BtnCopyUrl',
      'class' => 'bca-btn',
      'type' => 'button',
      'data-bca-btn-type' => 'textcopy',
      'data-bca-btn-category' => 'text',
      'data-bca-btn-size' => 'sm'
    ]) ?>
</div>
<?php endif ?>

<section id="BasicSetting" class="bca-section">
  <table class="form-table bca-form-table" data-bca-table-type="type2">
    <tr>
      <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label("content.name", 'URL') ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php if (!$content->site_root): ?>
          <?php echo $this->BcAdminForm->control("content.parent_id", ['type' => 'select', 'options' => $parentContents, 'escape' => true]) ?>
        <?php endif ?>
        <?php if (!$content->site_root && !$related): ?>
          <?php echo rawurldecode($this->BcAdminForm->control("content.name", ['type' => 'text', 'size' => 20, 'autofocus' => true])) ?>
          <?php if (!$isOmitViewAction && $content->url !== '/'): ?>/<?php endif ?>
        <?php else: ?>
          <?php if (!$content->site_root): ?>
            <?php // サイトルートの場合はコンテンツ名を表示しない ?>
            <?php echo h($contentsName) ?>
<?php endif ?>
          <?php echo $this->BcAdminForm->hidden("content.name") ?>
        <?php endif ?>
        <?php echo $this->BcAdminForm->error("content.name") ?>
        <?php echo $this->BcAdminForm->error("content.parent_id") ?>
        <span class="bca-post__url">
          <?php echo strip_tags($linkedFullUrl, '<a>') ?>
        </span>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label("content.title", __d('baser_core', 'タイトル')) ?>
        &nbsp;
        <span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php if ($editable): ?>
          <?php echo $this->BcAdminForm->control("content.title", ['type' => 'text', 'size' => 50]) ?>
          <?php echo $this->BcAdminForm->error("content.title") ?>
        <?php else: ?>
          <?php echo h($this->BcAdminForm->getSourceValue("content.title")) ?>
          <?php echo $this->BcAdminForm->hidden("content.title") ?>
        <?php endif ?>
      </td>
    </tr>
    <tr>
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label("content.self_status", __d('baser_core', '公開状態')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php if ($editable): ?>
          <?php echo $this->BcAdminForm->control("content.self_status", ['type' => 'radio', 'options' => $this->BcText->booleanDoList(__d('baser_core', '公開'))]) ?>
        <?php else: ?>
          <?php echo $this->BcText->arrayValue($this->BcAdminForm->getSourceValue("content.self_status"), $this->BcText->booleanDoList(__d('baser_core', '公開'))) ?>
          <?php echo $this->BcAdminForm->hidden("content.self_status") ?>
        <?php endif ?>
        <br>
        <?php echo $this->BcAdminForm->error("content.self_status") ?>
        <?php if ((bool)$this->BcAdminForm->getSourceValue("content.status") != (bool)$this->BcAdminForm->getSourceValue("content.self_status")): ?>
          <p>※ <?php echo __d('baser_core', '親フォルダの設定を継承し非公開状態となっています') ?></p>
        <?php endif ?>
      </td>
    </tr>
    <tr>
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label("content.self_status", __d('baser_core', '公開日時')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php if ($editable): ?>
          <?php echo $this->BcAdminForm->control("content.self_publish_begin", [
            'type' => 'dateTimePicker',
            'size' => 12,
            'maxlength' => 10,
            'dateLabel' => ['text' => __d('baser_core', '開始日付')],
            'timeLabel' => ['text' => __d('baser_core', '開始時間')]
          ]) ?>
          &nbsp;〜&nbsp;
          <?php echo $this->BcAdminForm->control("content.self_publish_end", [
            'type' => 'dateTimePicker',
            'size' => 12, 'maxlength' => 10,
            'dateLabel' => ['text' => __d('baser_core', '終了日付')],
            'timeLabel' => ['text' => __d('baser_core', '終了時間')]
          ]) ?>
        <?php else: ?>
          <?php if ($this->BcAdminForm->getSourceValue("content.self_publish_begin") || $this->BcAdminForm->getSourceValue("content.self_publish_end")): ?>
            <?php echo $this->BcAdminForm->getSourceValue("content.self_publish_begin") ?>&nbsp;〜&nbsp;<?php echo $this->BcAdminForm->getSourceValue("content.self_publish_end") ?>
          <?php endif ?>
          <?php echo $this->BcAdminForm->hidden("content.self_publish_begin") ?>
          <?php echo $this->BcAdminForm->hidden("content.self_publish_end") ?>
        <?php endif ?>
        <br>
        <?php echo $this->BcAdminForm->error("content.self_publish_begin") ?>
        <?php echo $this->BcAdminForm->error("content.self_publish_end") ?>
        <?php if (($this->BcAdminForm->getSourceValue("content.publish_begin") != $this->BcAdminForm->getSourceValue("content.self_publish_begin")) ||
          ($this->BcAdminForm->getSourceValue("content.publish_end") != $this->BcAdminForm->getSourceValue("content.self_publish_end"))): ?>
          <p>※ <?php echo __d('baser_core', '親フォルダの設定を継承し公開期間が設定されている状態となっています') ?><br>
            （<?php echo $this->BcTime->format($content->publish_begin, 'yyyy/MM/dd HH:mm') ?>
            〜
            <?php echo $this->BcTime->format($content->publish_end, 'yyyy/MM/dd HH:mm') ?>）
          </p>
        <?php endif ?>
      </td>
    </tr>
  </table>
</section>


