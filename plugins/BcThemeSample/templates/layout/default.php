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
?>
<?php $this->BcBaser->docType('html5') ?>
<html>
<head>
	<?php $this->BcBaser->charset() ?>
	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
	<?php $this->BcBaser->title() ?>
	<?php $this->BcBaser->metaDescription() ?>
	<?php $this->BcBaser->metaKeywords() ?>
	<?php $this->BcBaser->icon() ?>
	<?php $this->BcBaser->scripts() ?>
	<?php $this->BcBaser->googleAnalytics() ?>
</head>
<body id="<?php $this->BcBaser->contentsName() ?>">

<div class="bs-container">
	<div class="bs-wrap clearfix">
		<main class="bs-main-contents">
			<?php $this->BcBaser->flash() ?>
			<?php $this->BcBaser->content() ?>
			<?php $this->BcBaser->contentsNavi() ?>
		</main>
		<section class="bs-sub-contents">
			<?php $this->BcBaser->widgetArea() ?>
		</section>
	</div>
</div>

<?php $this->BcBaser->func() ?>
</body>
</html>
