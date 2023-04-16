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
 * PHPテンプレート
 *
 * 呼出箇所：ウィジェット
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var int $id ウィジェットID
 * @var string $name タイトル
 * @var bool $use_title タイトルを利用するかどうか
 * @var string $template テンプレートファイル名
 */
if(!isset($subDir)) {
	$subDir = true;
}
?>


<div class="bs-widget bs-widget-php-template bs-widget-php-template-<?php echo $id ?>">
	<?php if ($name && $use_title): ?>
		<h2 class="bs-widget-list"><?php echo $name ?></h2>
	<?php endif ?>
	<?php if($this->_getElementFileName('widgets/' . basename($template, '.php'))): ?>
	<?php $this->BcBaser->element('widgets' . DS . $template) ?>
	<?php else: ?>
	  <?php if(\Cake\Core\Configure::read('debug')) echo __d('baser_core', 'エラー：テンプレートが正常な場所に配置されていません。') ?>
	<?php endif ?>
</div>
