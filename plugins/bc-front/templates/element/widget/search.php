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
 * サイト検索フォーム
 *
 * 呼出箇所：ウィジェット
 * $this->BcBaser->widgetArea('ウィジェットエリアNO') で呼び出す
 * 管理画面で設定されたウィジェットエリアNOは、 $widgetArea で参照できる
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var int $id ウィジェットID
 */

if ($this->getRequest()->is('maintenance')) return;
$siteId = 1;
if (!empty($this->request->getAttribute('currentSite')->id)) {
    $siteId = $this->request->getAttribute('currentSite')->id;
}
if (!empty($this->getRequest()->getQuery('num'))) {
    $url = ['plugin' => 'BcSearchIndex', 'controller' => 'SearchIndexes', 'action' => 'search', '?' => ['num' => $this->getRequest()->getQuery('num')]];
} else {
    $url = ['plugin' => 'BcSearchIndex', 'controller' => 'SearchIndexes', 'action' => 'search'];
}
$folders = $this->BcContents->getContentFolderList($siteId, ['excludeId' => $this->BcContents->getSiteRootId($siteId)]);
if(empty($searchIndexesFront)) $searchIndexesFront = null;
?>


<div class="bs-widget bs-widget-search-box bs-widget-search-box-<?php echo $id ?>">
    <h2 class="bs-widget-head"><?php echo __d('baser_core', 'サイト内検索') ?></h2>
    <div class="bs-widget-form">
        <?php echo $this->BcForm->create($searchIndexesFront, ['type' => 'get', 'url' => $url]) ?>
        <?php if ($folders): ?>
            <?php echo $this->BcForm->label('f', __d('baser_core', 'カテゴリ')) ?><br>
            <?php echo $this->BcForm->control('f', ['type' => 'select', 'options' => $folders, 'empty' => __d('baser_core', '指定しない')]) ?>
            <br>
        <?php endif ?>
        <?php echo $this->BcForm->control('q', ['placeholder' => __d('baser_core', 'キーワード'), 'escape' => false]) ?>
        <?php echo $this->BcForm->control('s', ['type' => 'hidden', 'value' => $siteId]) ?>
        <?php echo $this->BcForm->submit(__d('baser_core', '検索'), ['div' => false, 'class' => 'bs-button-small']) ?>
        <?php echo $this->BcForm->end() ?>
    </div>
</div>
