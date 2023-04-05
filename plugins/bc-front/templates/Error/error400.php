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

use Cake\Core\Configure;
use Cake\Error\Debugger;

/**
 * 400エラーページ
 *
 * 呼出箇所：エラー発生時
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var string $message エラーメッセージ
 * @var string $url URL
 */
if (Configure::read('debug')) :
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error400.php');

    $this->start('file');
?>


<?php if (!empty($error->queryString)) : ?>
    <p class="notice">
        <strong>SQL Query: </strong>
        <?= h($error->queryString) ?>
    </p>
<?php endif; ?>
<?php if (!empty($error->params)) : ?>
        <strong>SQL Query Params: </strong>
        <?php Debugger::dump($error->params) ?>
<?php endif; ?>
<?= $this->element('auto_table_warning') ?>
<?php

$this->end();
endif;
?>


<h2 class="bs-error-title"><?php echo $message ?></h2>
<div class="bs-error-body">
	<strong><?php echo __d('baser_core', 'エラー'); ?>: </strong>
	<?php printf(
		__d('baser_core', 'アドレス %s に送信されたリクエストは無効です。'),
		"<strong>'{$url}'</strong>"
	); ?>
</div>
