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
 * 関連コンテンツ
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $mainSiteDisplayName メインサイト表示名称
 * @var array $relatedContents 関連コンテンツ
 * @var array $sites サイトリスト
 * @var int $currentSiteId 現在のサイトID
 * @var \BaserCore\Model\Entity\Content $content
 * @checked
 * @noTodo
 * @unitTest
 */
if(!$content->url) return;
$pureUrl = $this->BcContents->getPureUrl($content->url, $content->site_id);
?>


<?php if (count($relatedContents) > 1): ?>
  <section id="RelatedContentsSetting" class="bca-section" data-bca-section-type="form-group">
    <div class="bca-collapse__action">
      <button type="button" class="bca-collapse__btn" data-bca-collapse="collapse"
              data-bca-target="#formRelatedContentsBody" aria-expanded="false"
              aria-controls="formOptionBody"><?php echo __d('baser_core', '関連コンテンツ') ?>&nbsp;&nbsp;<i
          class="bca-icon--chevron-down bca-collapse__btn-icon"></i></button>
    </div>
    <div class="bca-collapse" id="formRelatedContentsBody" data-bca-state="">
      <table class="list-table bca-table-listup"
      ">
      <thead class="bca-table-listup__thead">
      <tr>
        <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'サイト名') ?></th>
        <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'メインサイト') ?></th>
        <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'タイトル') ?></th>
        <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'エイリアス') ?></th>
        <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'アクション') ?></th>
      </tr>
      </thead>
      <tbody class="bca-table-listup__tbody">
      <?php foreach($relatedContents as $relatedContent): ?>
        <?php
        $class = $editUrl = $checkUrl = '';
        $current = false;
        if (!empty($relatedContent['Content'])) {
          if (!$relatedContent['Content']->alias_id) {
            $editUrl = $this->BcContents->getConfig('items')[$relatedContent['Content']->type]['url']['edit'];
            if ($relatedContent['Content']->entity_id) {
              $editUrl .= '/' . $relatedContent['Content']->entity_id;
            }
            $editUrl .= '/content_id:' . $relatedContent['Content']->id . '#RelatedContentsSetting';
          } else {
            $editUrl = '/' . \BaserCore\Utility\BcUtil::getAdminPrefix() . '/contents/edit_alias/' . $relatedContent['Content']->id . '#RelatedContentsSetting';
          }
          if ($this->BcAdminForm->getSourceValue('content.id') === $relatedContent['Content']->id) {
            $current = true;
            $class = ' class="bca-currentrow"';
          }
        } else {
          $class = ' class="bca-disablerow"';
        }

        $prefix = $relatedContent['Site']->name;
        if ($relatedContent['Site']->alias) {
          $prefix = $relatedContent['Site']->alias;
        }
        $targetUrl = '/' . $prefix . $pureUrl;
        $mainSiteId = $relatedContent['Site']->main_site_id;
        if ($mainSiteId === '') {
          $mainSiteId = 1;
        }
        ?>
        <tr<?php echo $class ?> id="Row<?php echo $relatedContent['Site']->id ?>">
          <td class="cel2 bca-table-listup__tbody-td"><?php echo h($relatedContent['Site']->display_name) ?></td>
          <td class="cel3 bca-table-listup__tbody-td">
            <?php echo h($this->BcText->arrayValue($relatedContent['Site']->main_site_id, $sites, $mainSiteDisplayName)) ?>
          </td>
          <td class="cel4 bca-table-listup__tbody-td">
            <?php if (!empty($relatedContent['Content'])): ?>
              <?php echo h($relatedContent['Content']->title) ?>
              <?php if (!empty($relatedContent['Content'])): ?>
                <small>（<?php echo h($this->BcContents->getConfig('items')[$relatedContent['Content']->type]['title']) ?>
                  ）</small>
              <?php endif ?>
            <?php else: ?>
              <small><?php echo __d('baser_core', '未登録') ?></small>
            <?php endif ?>
          </td>
          <td class="cel5 bca-table-listup__tbody-td">
            <?php if (!empty($relatedContent['Content']) && !empty($relatedContent['Content']->alias_id)): ?>
              <i class="fa fa-check-square" aria-hidden="true"></i>
            <?php endif ?>
          </td>
          <td class="cel1 bca-table-listup__tbody-td">
            <?php if (!$current): ?>
              <?php if (!empty($relatedContent['Content'])): ?>
                <?php $this->BcBaser->link('', $relatedContent['Content']->url, [
                  'title' => __d('baser_core', '確認'),
                  'target' => '_blank',
                  'class' => 'btn-check bca-btn-icon',
                  'data-bca-btn-type' => 'preview',
                  'data-bca-btn-size' => 'lg'
                ]) ?>
                <?php $this->BcBaser->link('', $editUrl, [
                  'title' => __d('baser_core', '編集'),
                  'class' => 'btn-edit bca-btn-icon',
                  'data-bca-btn-type' => 'edit',
                  'data-bca-btn-size' => 'lg'
                ]) ?>
              <?php elseif ($currentSiteId == $mainSiteId && $this->BcAdminForm->getSourceValue("content.type") !== 'ContentFolder'): ?>
                <?php $this->BcBaser->link('<span class="icon-add-layerd"></span>', 'javascript:void(0)', [
                  'class' => 'create-alias btn-alias bca-btn-icon',
                  'data-bca-btn-type' => 'alias',
                  'data-bca-btn-size' => 'lg',
                  'title' => __d('baser_core', 'エイリアス作成'),
                  'target' => '_blank',
                  'data-site-id' => $relatedContent['Site']->id,
                  'escape' => false,
                ]) ?>
                <?php $this->BcBaser->link('<span class="icon-add-layerd"></span>', 'javascript:void(0)', [
                  'class' => 'create-copy btn-copy bca-btn-icon',
                  'data-bca-btn-type' => 'copy',
                  'data-bca-btn-size' => 'lg',
                  'title' => __d('baser_core', 'コピー作成'),
                  'target' => '_blank',
                  'data-site-id' => $relatedContent['Site']->id,
                  'escape' => false
                ]) ?>
              <?php endif ?>
            <?php endif ?>
            <?php echo $this->BcAdminForm->control('Sites.display_name' . $relatedContent['Site']->id, ['type' => 'hidden', 'value' => $relatedContent['Site']->display_name]) ?>
            <?php echo $this->BcAdminForm->control('Sites.target_url' . $relatedContent['Site']->id, ['type' => 'hidden', 'value' => $targetUrl]) ?>
          </td>
        </tr>
      <?php endforeach ?>
      </tbody>
      </table>
    </div>
  </section>
<?php endif ?>
