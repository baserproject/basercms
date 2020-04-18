<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Users Community
 * @link          http://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

use Cake\Core\Configure;
/**
 * @var Cake\View\View $this
 */
$this->assign('title', $title);
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
		<?php
		echo $this->Html->css([
      		'../js/admin/vendors/bootstrap-4.1.3/bootstrap.min',
			'admin/style.css',
			'admin/jquery-ui/jquery-ui.min'
		]) ?>
<?php //echo $this->Html->script(['https://unpkg.com/vue', 'https://unpkg.com/axios/dist/axios.min.js', 'admin/app']) ?>
	</head>

	<body class="normal">

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

							</div>

							<div class="bca-main__header-menu">

							</div>

						</div>

						<div id="BcMessageBox"><div id="BcSystemMessage" class="notice-message"></div></div>

						<div class="bca-main__contents clearfix">
							<?= $this->fetch('content') ?>
						</div>

					<!-- / bca-main__body --></article>

				<!-- / .bca-main --></main>

			<!-- / #Wrap --></div>

<?php echo $this->element('Admin/footer') ?>

	<!-- / #Page --></div>

	</body>

</html>
