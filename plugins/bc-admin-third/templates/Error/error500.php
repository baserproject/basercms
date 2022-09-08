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

/**
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $message エラーメッセージ
 * @var string $url URL
 */

$this->layout = 'error';
$this->BcAdmin->setTitle(__d('baser', '内部エラーが発生しました'));
if (Configure::read('debug')) :
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error500.php');

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
<?php if ($error instanceof Error) : ?>
    <strong>Error in: </strong>
    <?= sprintf('%s, line %s', str_replace(ROOT, 'ROOT', $error->getFile()), $error->getLine()) ?>
<?php endif; ?>
<?php
    echo $this->element('auto_table_warning');

    $this->end();
endif;
?>


<h2><?php echo $message ?></h2>
<p class="error">
  <strong><?php echo __d('baser', 'エラー') ?>: </strong>
  <?php printf(
    __d('baser', 'アドレス %s に送信されたリクエストは無効です。'),
    "<strong>'{$url}'</strong>"
  ); ?>
</p>
