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

use Cake\Utility\Inflector;

/**
 * [管理画面] クレジット
 * @var \BaserCore\View\BcAdminAppView
 * @var array $credits
 * @checked
 * @noTodo
 * @unitTest
 */

if (empty($credits)) {
  return;
}
$types = ['designers', 'developers', 'supporters', 'publishers'];
?>

<div id="Credit">
  <div id="CreditInner">
    <div id="CreditScroller">
      <div id="CreditScrollerInner">

        <h1>Special Thanks Credit</h1>
        <?php foreach($types as $type) : ?>
          <?php if (isset($credits->{$type})): ?>
            <div class="section">
              <h2><?php echo Inflector::camelize($type) ?></h2>
              <?php $i = 0 ?>
              <?php foreach($credits->{$type} as $key => $contributor): ?>
                <?php $i++ ?>
                <?php if ($i % 6 == 1): ?>
                  <ul>
                <?php endif ?>
                <li>
                  <?php if (!empty($contributor->siteUrl)): ?>
                    <?php $this->BcBaser->link($contributor->alphabet, $contributor->siteUrl, ['target' => '_blank']) ?>
                  <?php elseif (!empty($contributor->affiliationUrl)): ?>
                    <?php $this->BcBaser->link($contributor->alphabet, $contributor->affiliationUrl, ['target' => '_blank']) ?>
                  <?php else: ?>
                    <?php echo $contributor->alphabet ?>
                  <?php endif ?>
                  <?php if (!empty($contributor->twitter)): ?>
                    (<?php $this->BcBaser->link($contributor->twitter, 'http://twitter.com/' . $contributor->twitter, ['target' => '_blank']) ?>)
                  <?php endif ?>
                </li>
                <?php if ($i % 6 == 0 || $this->BcArray->last($credits->{$type}, $key)): ?>
                  </ul>
                <?php endif ?>
              <?php endforeach ?>
            </div>
          <?php endif ?>
        <?php endforeach ?>

        <h1 style="margin-top:400px;">baserCMS Users Community</h1>

      </div>
    </div>
  </div>
</div>
