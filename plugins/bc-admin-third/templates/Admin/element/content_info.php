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
 * コンテンツ情報
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<?php if (in_array($this->request->getParam('action'), ['edit', 'edit_alias'])): ?>
  <section id="EtcSetting" class="bca-section" data-bca-section-type="form-group">
    <div class="bca-collapse__action">
      <button type="button" class="bca-collapse__btn" data-bca-collapse="collapse"
              data-bca-target="#formContentsInfoBody" aria-expanded="false"
              aria-controls="formOptionBody"><?php echo __d('baser_core', 'その他情報') ?>&nbsp;&nbsp;<i
          class="bca-icon--chevron-down bca-collapse__btn-icon"></i></button>
    </div>
    <div class="bca-collapse" id="formContentsInfoBody" data-bca-state="">
      <div class="bca-box">
        <ul class="bca-list" data-bca-list-layout="horizon" data-bca-list-type='circle'>
          <li class="bca-list__item">
            <span><?php echo __d('baser_core', 'コンテンツID') ?></span>：<?php echo $this->BcAdminForm->getSourceValue("content.id"); ?>
          </li>
          <li class="bca-list__item">
            <span><?php echo __d('baser_core', '実体ID') ?></span>：<?php echo $this->BcAdminForm->getSourceValue("content.entity_id"); ?>
          </li>
          <li class="bca-list__item">
            <span><?php echo __d('baser_core', 'プラグイン') ?></span>：<?php echo $this->BcAdminForm->getSourceValue("content.plugin"); ?>
          </li>
          <li class="bca-list__item">
            <span><?php echo __d('baser_core', 'コンテンツタイプ') ?></span>：<?php echo $this->BcAdminForm->getSourceValue("content.type"); ?>
          </li>
          <li class="bca-list__item">
            <span><?php echo __d('baser_core', 'データ作成日') ?></span>：<?php echo $this->BcAdminForm->getSourceValue("content.created"); ?>
          </li>
          <li class="bca-list__item">
            <span><?php echo __d('baser_core', 'データ更新日') ?></span>：<?php echo $this->BcAdminForm->getSourceValue("content.modified"); ?>
          </li>
          <li class="bca-list__item">
            <span><?php echo __d('baser_core', 'サイト') ?></span>：<?php echo h($this->BcAdminForm->getSourceValue("content.site.display_name")) ?>
          </li>
          <li class="bca-list__item"><span><?php echo __d('baser_core', 'タイプ') ?></span>：
            <?php if (!$this->BcAdminForm->getSourceValue("content.alias_id")): ?>
              <?php if (!empty($this->BcContents->getConfig('items')[$this->BcAdminForm->getSourceValue("content.type")])): ?>
                <?php echo h($this->BcContents->getConfig('items')[$this->BcAdminForm->getSourceValue("content.type")]['title']) ?>
              <?php else: ?>
                <?php echo __d('baser_core', 'デフォルト') ?>
              <?php endif ?>
            <?php else: ?>
              <?php echo __d('baser_core', 'エイリアス') ?>
            <?php endif ?>
            <?php if (empty($this->BcContents->getConfig('items')[$this->BcAdminForm->getSourceValue("content.type")])): ?>
              <p
                class="bca-notice"><?php echo __d('baser_core', 'タイプ「デフォルト」は、プラグインの無効処理等が理由となり、タイプとの関連付けが外れてしまっている状態です。<br>プラグインがまだ存在する場合は有効にしてください。') ?></p>
            <?php endif ?>
          </li>
        </ul>
      </div>
    </div>
  </section>
<?php endif ?>
