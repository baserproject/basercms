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
 * メールフォーム非公開時表示ページ
 *
 * 呼出箇所：メールフォーム
 *
 * @var \BaserCore\View\BcFrontAppView $this
 */
?>


<h2 class="bs-mail-title"><?php $this->BcBaser->contentsTitle() ?></h2>

<h3 class="bs-mail-title-sub"><?php echo __d('baser_core', '受付中止') ?></h3>

<div class="bs-mail-form"><p><?php echo __d('baser_core', '現在、受付を中止しています。') ?></p></div>
