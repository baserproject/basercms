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

/**
 * コンテンツ情報
 * @var string $mainSiteDisplayName メインサイト表示名称
 */
?>


<?php if (in_array($this->request->getParam('action'), ['edit', 'edit_alias'])): ?>
  <section id="EtcSetting" class="bca-section" data-bca-section-type="form-group">
    <div class="bca-collapse__action">
      <button type="button" class="bca-collapse__btn" data-bca-collapse="collapse"
              data-bca-target="#formContentsInfoBody" aria-expanded="false"
              aria-controls="formOptionBody"><?php echo __d('baser', 'その他情報') ?>&nbsp;&nbsp;<i
          class="bca-icon--chevron-down bca-collapse__btn-icon"></i></button>
    </div>
    <div class="bca-collapse" id="formContentsInfoBody" data-bca-state="">
      <div class="bca-box">
        <ul class="bca-list" data-bca-list-layout="horizon" data-bca-list-type='circle'>
          <li class="bca-list__item">
            <span><?php echo __d('baser', 'コンテンツID') ?></span>：<?php echo $this->request->getData('Content.id') ?>
          </li>
          <li class="bca-list__item">
            <span><?php echo __d('baser', '実体ID') ?></span>：<?php echo $this->request->getData('Content.entity_id') ?>
          </li>
          <li class="bca-list__item">
            <span><?php echo __d('baser', 'プラグイン') ?></span>：<?php echo $this->request->getData('Content.plugin') ?>
          </li>
          <li class="bca-list__item">
            <span><?php echo __d('baser', 'コンテンツタイプ') ?></span>：<?php echo $this->request->getData('Content.type') ?>
          </li>
          <li class="bca-list__item">
            <span><?php echo __d('baser', 'データ作成日') ?></span>：<?php echo $this->BcTime->format($this->request->getData('Content.created'), 'YYYY/MM/DD H:i:s') ?>
          </li>
          <li class="bca-list__item">
            <span><?php echo __d('baser', 'データ更新日') ?></span>：<?php echo $this->BcTime->format($this->request->getData('Content.modified'), 'YYYY/MM/DD H:i:s') ?>
          </li>
          <li class="bca-list__item">
            <span><?php echo __d('baser', 'サイト') ?></span>：<?php echo h($this->BcText->noValue($this->request->getData('Site.display_name'), $mainSiteDisplayName)) ?>
          </li>
          <li class="bca-list__item"><span><?php echo __d('baser', 'タイプ') ?></span>：
            <?php if (!$this->BcAdminForm->value($contentPath . 'alias_id')): ?>
              <?php if (!empty($this->BcContents->getConfig('items')[$this->BcAdminForm->value($contentPath . 'type')])): ?>
                <?php echo h($this->BcContents->getConfig('items')[$this->BcAdminForm->value($contentPath . 'type')]['title']) ?>
              <?php else: ?>
                <?php echo __d('baser', 'デフォルト') ?>
              <?php endif ?>
            <?php else: ?>
              <?php echo __d('baser', 'エイリアス') ?>
            <?php endif ?>
            <?php if (empty($this->BcContents->getConfig('items')[$this->BcAdminForm->value($contentPath . 'type')])): ?>
              <p
                class="bca-notice"><?php echo __d('baser', 'タイプ「デフォルト」は、プラグインの無効処理等が理由となり、タイプとの関連付けが外れてしまっている状態です。<br>プラグインがまだ存在する場合は有効にしてください。') ?></p>
            <?php endif ?>
          </li>
        </ul>
      </div>
    </div>
  </section>
<?php endif ?>
