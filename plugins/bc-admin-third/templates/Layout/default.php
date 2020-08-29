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

use BaserCore\View\AppView;

/**
 * @var AppView $this
 * @var string $title
 */

$this->assign('title', $title);
$request = $this->getRequest();
$attributes = $request->getAttributes();
$base = $attributes['base'];
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="UTF-8">
		<meta name="robots" content="noindex,nofollow" />
		<title><?= h($this->fetch('title')) ?></title>
		<?php echo $this->fetch('meta') ?>
		<?php echo $this->fetch('css') ?>
		<?php echo $this->fetch('script') ?>
		<?php echo $this->BcBaser->js([
            'admin/vendor.bundle',
            'vendor/vue.min'
		]) ?>
		<?php echo $this->BcBaser->js('admin/common.bundle', true, [
		    'id' => 'AdminScript',
		    'data-baseUrl' => h($base)
        ]) ?>
		<?php echo $this->Html->css([
			'admin/style.css',
			'admin/jquery-ui/jquery-ui.min'
		]) ?>
<?php //echo $this->Html->script(['https://unpkg.com/vue', 'https://unpkg.com/axios/dist/axios.min.js', 'admin/app']) ?>
	</head>

	<body id="<?php $this->BcBaser->contentsName(true) ?>" class="normal">

	<div id="Page" class="bca-app">
<?php echo $this->element('Admin/header') ?>

		<div id="Wrap" class="bca-container">

<?php // TMP cake3 ?>
<?php //if ($this->name != 'Installations' && $this->name != 'Updaters' && ('/' . $this->request->url != Configure::read('BcAuthPrefix.admin.loginAction')) && !empty($user)): ?>
			<?php $this->BcBaser->element('Admin/sidebar') ?>
<?php //endif ?>

				<main id="Contents" class="bca-main">

					<article id="ContentsBody" class="contents-body bca-main__body">

						<div class="bca-main__header">

							<h1 class="bca-main__header-title"><?= h($this->fetch('title')) ?></h1>

							<div class="bca-main__header-actions">
                                <?php $this->BcBaser->element('Admin/main_body_header_links'); ?>
							</div>

							<div class="bca-main__header-menu">

							</div>

						</div>

						<div id="BcMessageBox"><div id="BcSystemMessage" class="notice-message"></div></div>

                        <?= $this->Flash->render() ?>

						<div class="bca-main__contents clearfix">
                            <?= $this->Flash->render() ?>
							<?= $this->fetch('content') ?>
						</div>

					<!-- / bca-main__body --></article>

				<!-- / .bca-main --></main>

			<!-- / #Wrap --></div>

<?php echo $this->element('Admin/footer') ?>

	<!-- / #Page --></div>

	</body>

</html>
