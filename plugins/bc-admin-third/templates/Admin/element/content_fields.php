<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

use BaserCore\Model\Entity\Content;
use BaserCore\Model\Entity\ContentFolder;
use BaserCore\View\BcAdminAppView;
use Cake\Utility\Inflector;
use BaserCore\Utility\BcUtil;

/**
 * [ADMIN] 統合コンテンツフォーム
 *
 * @var BcAdminAppView $this
 * @var array $parentContents
 * @var bool $related 親サイトに連携する設定で、エイリアス、もしくはフォルダであるかどうか
 *                                        上記に一致する場合、URLに関わるコンテンツ名は編集できない
 * @var bool $disableEditContent コンテンツ編集不可かどうか
 * @var Content $content
 * @var ContentFolder $contentFolder
 */

$site = $content->site;
$options = [];
if ($this->getName() === 'ContentFolders') {
    $options['excludeId'] = $content->id;
}
$parentContents = $this->BcAdminContent->getContentFolderList($content->site_id, $options);

if ($this->request->getData('Site.use_subdomain')) {
  $targetSite = $this->BcAdminSite->findByUrl($content->url);
  $previewUrl = $this->BcBaser->getUrl($targetSite->getPureUrl($content->url) . '?host=' . $targetSite->host);
} else {
  $previewUrl = $this->BcBaser->getUrl($this->BcContents->getUrl($content->url, false, false, false));
}
$fullUrl = $this->BcContents->getUrl($content->url, true, $site->use_subdomain);
// TODO ucmits data-current が何に使われているか確認
// $this->request->getData() では Content は取得できないため
$this->BcBaser->js('admin/contents/edit', false, ['id' => 'AdminContentsEditScript',
  'data-previewurl' => $previewUrl,
  'data-fullurl' => $fullUrl,
  'data-current' => json_encode($this->request->getData()),
  'data-settings' => $this->BcContents->getJsonItems()
]);
$this->BcBaser->i18nScript([
  'contentsEditConfirmMessage1' => __d('baser', 'コンテンツをゴミ箱に移動してもよろしいですか？'),
  'contentsEditConfirmMessage2' => __d('baser', "エイリアスを削除してもよろしいですか？\nエイリアスはゴミ箱に入らず完全に削除されます。"),
  'contentsEditConfirmMessage3' => __d('baser', 'このコンテンツを元に %s にエイリアスを作成します。よろしいですか？'),
  'contentsEditConfirmMessage4' => __d('baser', 'このコンテンツを元に %s にコピーを作成します。よろしいですか？'),
  'contentsEditInfoMessage1' => __d('baser', 'エイリアスを作成しました。作成先の編集画面に移動しますのでしばらくお待ち下さい。'),
  'contentsEditInfoMessage2' => __d('baser', 'コピーを作成しました。作成先の編集画面に移動しますのでしばらくお待ち下さい。'),
  'contentsEditAlertMessage1' => __d('baser', 'エイリアスの作成に失敗しました。'),
  'contentsEditAlertMessage2' => __d('baser', '指定したサイトの同じ階層上にフォルダではない同名のコンテンツが存在します。エイリアスの作成を実行する前に、指定したサイト上の同名コンテンツを確認し名称を変更してください。'),
  'contentsEditAlertMessage3' => __d('baser', '指定したサイトの同じ階層上にフォルダではない同名のコンテンツが存在します。コピーの作成を実行する前に、指定したサイト上の同名コンテンツを確認し名称を変更してください。'),
  'contentsEditAlertmessage4' => __d('baser', 'コピーの作成に失敗しました。')
]);
$isOmitViewAction = $this->BcContents->getConfig('items')[$content->type]['omitViewAction'];

// サブドメイン
if ($site->use_subdomain) {
  $contentsName = '';
  if (!$content->site_root) {
    $contentsName = $this->BcAdminForm->value($contentPath . 'name');
    if (!$isOmitViewAction && $content->url !== '/') {
      $contentsName .= '/';
    }
  }
} else {
  if ($this->request->getData('Site.same_main_url') && $content->site_root) {
    $contentsName = '';
  } else {
    $contentsName = $this->BcAdminForm->value($contentPath . 'name');
  }
  if (!$isOmitViewAction && $content->url !== '/' && $contentsName) {
    $contentsName .= '/';
  }
}
$linkedFullUrl = $this->BcContents->getCurrentFolderLinkedUrl($content) . $contentsName;
$disableEdit = false;
// TODO: エラーが出るため一時的にコメントアウト
// if ($this->BcContents->isEditable()) {
//   $disableEdit = true;
// }
?>


<?php echo $this->BcAdminForm->hidden($contentPath . 'id') ?>
<?php echo $this->BcAdminForm->hidden($contentPath . 'plugin') ?>
<?php echo $this->BcAdminForm->hidden($contentPath . 'type') ?>
<?php echo $this->BcAdminForm->hidden($contentPath . 'entity_id') ?>
<?php echo $this->BcAdminForm->hidden($contentPath . 'url') ?>
<?php echo $this->BcAdminForm->hidden($contentPath . 'alias_id') ?>
<?php echo $this->BcAdminForm->hidden($contentPath . 'site_root') ?>
<?php echo $this->BcAdminForm->hidden($contentPath . 'site_id') ?>
<?php echo $this->BcAdminForm->hidden($contentPath . 'lft') ?>
<?php echo $this->BcAdminForm->hidden($contentPath . 'rght') ?>
<?php echo $this->BcAdminForm->hidden($contentPath . 'status') ?>
<?php echo $this->BcAdminForm->hidden($contentPath . 'main_site_content_id') ?>
<?php echo $this->BcAdminForm->hidden($contentPath . 'publish_begin') ?>


<div class="bca-section bca-section__post-top">
  <span class="bca-post__url">
	  <a href="<?php echo h($fullUrl) ?>" class="bca-text-url" target="_blank" data-toggle="tooltip"
       data-placement="top" title="<?php echo __d('baser', '公開URLを開きます') ?>"><i
        class="bca-icon--globe"></i><?php echo urldecode($fullUrl) ?></a>
	  <?php echo $this->BcAdminForm->button('', [
      'id' => 'BtnCopyUrl',
      'class' => 'bca-btn',
      'type' => 'button',
      'data-bca-btn-type' => 'textcopy',
      'data-bca-btn-category' => 'text',
      'data-bca-btn-size' => 'sm'
    ]) ?>
</div>

<section id="BasicSetting" class="bca-section">
  <table class="form-table bca-form-table" data-bca-table-type="type2">
    <tr>
      <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label($contentPath . 'name', 'URL') ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php if (!$content->site_root): ?>
          <?php echo $this->BcAdminForm->control($contentPath . 'parent_id', ['type' => 'select', 'options' => $parentContents, 'escape' => true]) ?>
        <?php endif ?>
        <?php if (!$content->site_root && !$related): ?>
          <?php echo $this->BcAdminForm->control($contentPath . 'name', ['type' => 'text', 'size' => 20, 'autofocus' => true]) ?>
          <?php if (!$isOmitViewAction && $content->url !== '/'): ?>/<?php endif ?>
        <?php else: ?>
          <?php if (!$content->site_root): ?>
            <?php // サイトルートの場合はコンテンツ名を表示しない ?>
            <?php echo h($contentsName) ?>
          <?php endif ?>
          <?php echo $this->BcAdminForm->hidden($contentPath . 'name') ?>
        <?php endif ?>
        <?php echo $this->BcAdminForm->error($contentPath . 'name') ?>
        <?php echo $this->BcAdminForm->error($contentPath . 'parent_id') ?>
        <span class="bca-post__url">
          			<?php echo strip_tags($linkedFullUrl, '<a>') ?>
        		</span>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label($contentPath . 'title', __d('baser', 'タイトル')) ?>&nbsp;<span class="bca-label"
                                                                                            data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php if (!$disableEdit): ?>
          <?php echo $this->BcAdminForm->control($contentPath . 'title', ['type' => 'text', 'size' => 50]) ?>
          <?php echo $this->BcAdminForm->error($contentPath . 'title') ?>
        <?php else: ?>
          <?php echo h($this->BcAdminForm->value($contentPath . 'title')) ?>
          <?php echo $this->BcAdminForm->hidden($contentPath . 'title') ?>
        <?php endif ?>
      </td>
    </tr>
    <tr>
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label($contentPath . 'self_status', __d('baser', '公開状態')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php if (!$disableEdit): ?>
          <?php echo $this->BcAdminForm->control($contentPath . 'self_status', ['type' => 'radio', 'options' => $this->BcText->booleanDoList('公開')]) ?>
        <?php else: ?>
          <?php echo $this->BcText->arrayValue($this->BcAdminForm->value($contentPath . 'self_status'), $this->BcText->booleanDoList('公開')) ?>
          <?php echo $this->BcAdminForm->hidden($contentPath . 'self_status') ?>
        <?php endif ?>
        <br>
        <?php echo $this->BcAdminForm->error($contentPath . 'self_status') ?>
        <?php if ((bool)$this->BcAdminForm->value($contentPath . 'status') != (bool)$this->BcAdminForm->value($contentPath . 'self_status')): ?>
          <p>※ <?php echo __d('baser', '親フォルダの設定を継承し非公開状態となっています') ?></p>
        <?php endif ?>
      </td>
    </tr>
    <tr>
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label($contentPath . 'self_status', __d('baser', '公開日時')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php if (!$disableEdit): ?>
          <?php echo $this->BcAdminForm->control($contentPath . 'self_publish_begin', [
            'type' => 'dateTimePicker',
            'size' => 12,
            'maxlength' => 10,
            'dateLabel' => ['text' => '開始日付'],
            'timeLabel' => ['text' => '開始時間']
          ]) ?>
          &nbsp;〜&nbsp;
          <?php echo $this->BcAdminForm->control($contentPath . 'self_publish_end', [
            'type' => 'dateTimePicker',
            'size' => 12, 'maxlength' => 10,
            'dateLabel' => ['text' => '終了日付'],
            'timeLabel' => ['text' => '終了時間']
          ]) ?>
        <?php else: ?>
          <?php if ($this->BcAdminForm->value($contentPath . 'self_publish_begin') || $this->BcAdminForm->value($contentPath . 'self_publish_end')): ?>
            <?php echo $this->BcAdminForm->value($contentPath . 'self_publish_begin') ?>&nbsp;〜&nbsp;<?php echo $this->BcAdminForm->value($contentPath . 'self_publish_end') ?>
          <?php endif ?>
          <?php echo $this->BcAdminForm->hidden($contentPath . 'self_publish_begin') ?>
          <?php echo $this->BcAdminForm->hidden($contentPath . 'self_publish_end') ?>
        <?php endif ?>
        <br>
        <?php echo $this->BcAdminForm->error($contentPath . 'self_publish_begin') ?>
        <?php echo $this->BcAdminForm->error($contentPath . 'self_publish_end') ?>
        <?php if (($this->BcAdminForm->value($contentPath . 'publish_begin') != $this->BcAdminForm->value($contentPath . 'self_publish_begin')) ||
          ($this->BcAdminForm->value($contentPath . 'publish_end') != $this->BcAdminForm->value($contentPath . 'self_publish_end'))): ?>
          <p>※ <?php echo __d('baser', '親フォルダの設定を継承し公開期間が設定されている状態となっています') ?><br>
            （<?php echo $this->BcTime->format($content->publish_begin, 'YYYY/MM/DD H:i') ?>
            〜
            <?php echo $this->BcTime->format($content->publish_end, 'YYYY/MM/DD H:i') ?>）
          </p>
        <?php endif ?>
      </td>
    </tr>
  </table>
</section>


