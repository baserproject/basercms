<?php
/**
 * デフォルトレイアウト
 */
?>
<?php $this->BcBaser->xmlHeader() ?>
<?php $this->BcBaser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
	<head>
		<?php $this->BcBaser->charset() ?>
		<?php $this->BcBaser->title() ?>
		<?php $this->BcBaser->metaDescription() ?>
		<?php $this->BcBaser->metaKeywords() ?>
		<?php $this->BcBaser->icon() ?>
		<?php $this->BcBaser->rss('ニュースリリース RSS 2.0', '/news/index.rss') ?>
		<?php $this->BcBaser->css('style') ?>
<?php $this->BcBaser->js(array(
			'admin/jquery-1.7.2.min',
	'admin/functions')) ?>
<?php $this->BcBaser->scripts() ?>
<?php $this->BcBaser->element('google_analytics') ?>
	</head>
	<body id="<?php $this->BcBaser->contentsName() ?>">

		<div id="Page">

			<?php $this->BcBaser->header() ?>

			<div id="Wrap" class="clearfix">

				<div id="Alfa" >
					<div id="ContentsBody" class="clearfix">
						<?php $this->BcBaser->flash() ?>
						<?php $this->BcBaser->content() ?>
						<?php $this->BcBaser->element('contents_navi') ?>
					</div>
				</div>

				<div id="Beta">
					<?php $this->BcBaser->widgetArea() ?>
				</div>

			</div>

			<div class="to-top"> <a href="#Page">このページの先頭へ戻る</a> </div>

			<?php $this->BcBaser->footer() ?>

		</div>

		<?php $this->BcBaser->func() ?>
	</body>
</html>
