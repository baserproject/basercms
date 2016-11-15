<?php
/**
 * コンテンツナビ
 * 呼出箇所：固定ページ
 *
 * BcBaserHelper::contentsNavi() で呼び出す
 * （例）<?php $this->BcBaser->contentsNavi() ?>
 */
?>


<?php if(!$this->BcBaser->isHome()): ?>
	<div class="contents-navi">
		<?php $this->BcPage->prevLink() ?>
		&nbsp;｜&nbsp;
		<?php $this->BcPage->nextLink() ?>
	</div>
<?php endif ?>