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

use BaserCore\View\AppView;
/**
 * レイアウト
 * 呼出箇所：全ページ
 * @var AppView $this
 */
$request = $this->getRequest();
$attributes = $request->getAttributes();
$base = $attributes['base'];
?>
<!DOCTYPE html>
<html>
<head>
	<?php $this->BcBaser->charset() ?>
	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
	<?php $this->BcBaser->title() ?>
	<?php $this->BcBaser->metaDescription() ?>
	<?php $this->BcBaser->metaKeywords() ?>
	<?php $this->BcBaser->icon() ?>
	<?php $this->BcBaser->css([
		'style',
		'jquery-ui/jquery-ui-1.11.4',
		'colorbox/colorbox-1.6.1',
		'editor'
	]) ?>
  <?= $this->BcBaser->declarationI18n() ?>
	<?php $this->BcBaser->js([
		'vendor/jquery-1.11.3.min',
		'vendor/jquery-ui-1.11.4.min',
		'vendor/jquery.bxslider-4.12.min',
		'vendor/jquery.colorbox-1.6.1.min',
		'vendor/i18n/ui.datepicker-ja',
		'vendor/jquery-accessibleMegaMenu',
	]); ?>
  <?php $this->BcBaser->js('common.bundle', true, [
	  'id' => 'AdminScript',
    'data-baseUrl' => h($base),
    'data-baserCorePrefix' => \Cake\Utility\Inflector::underscore(\BaserCore\Utility\BcUtil::getBaserCorePrefix()),
	]) ?>
	<?php $this->BcBaser->js([
		'startup.bundle'
	]); ?>
	<?php $this->BcBaser->scripts() ?>
	<!-- /Elements/google_analytics.php -->
	<?php $this->BcBaser->googleAnalytics() ?>
</head>
<body id="<?php $this->BcBaser->contentsName() ?>">

<?php $this->BcBaser->func() ?>

<div class="bs-container">

	<!-- /Elements/header.php -->
	<?php $this->BcBaser->header() ?>

	<?php if ($this->BcBaser->isHome()): ?>
		<?php $this->BcBaser->mainImage(['all' => true, 'num' => 5, 'width' => '100%', 'class' => 'bs-main-image']) ?>
	<?php else: ?>
		<!-- /Elements/crumbs.php -->
		<?php $this->BcBaser->crumbsList(['onSchema' => true]); ?>
	<?php endif ?>

	<div class="bs-wrap clearfix">

		<main class="bs-main-contents">
			<?php $this->BcBaser->flash() ?>

			<?php if($this->BcBaser->isHome()): ?>
				<!-- /Elements/top_info.php -->
				<?php $this->BcBaser->element('top_info') ?>
			<?php endif ?>

			<?php $this->BcBaser->content() ?>

			<!-- /Elements/contents_navi.php -->
			<?php $this->BcBaser->contentsNavi() ?>
		</main>

		<section class="bs-sub-contents">
			<!-- /Elements/widget_area.php -->
			<?php $this->BcBaser->widgetArea() ?>
		</section>

	</div>

	<!-- /Elements/footer.php -->
	<?php $this->BcBaser->footer() ?>

</div>

</body>
</html>
