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
 * @var BcAdminAppView $this
 * @checked
 * @unitTest
 * @noTodo
 */
$this->loadHelper('BcMail.Mail');
?>


<h2 class="bca-panel-box__title"><?php echo __d('baser_core', '受信メール') ?></h2>
<div id="ContentInfo">
    <div class="bca-content-info">
      <?php foreach($this->BcContents->getPublishedSites() as $site): ?>
        <h3 class="bca-content-info__title"><?php echo h($site->display_name) ?></h3>
        <ul class="bca-content-info__list">
          <li class="bca-content-info__list-item">
            <?php foreach($this->Mail->getPublishedMailContents($site->id) as $mailContent): ?>
              <?php echo $mailContent->content->title ?>：
              <?php $this->BcBaser->link(
                __d('baser_core', '{0} 件', $mailContent->getNumberOfMessages()),
                ['plugin' => 'BcMail', 'controller' => 'MailMessages', 'action' => 'index', $mailContent->id]
              ) ?>
            <?php endforeach ?>
          </li>
        </ul>
      <?php endforeach ?>
    </div>
</div>
