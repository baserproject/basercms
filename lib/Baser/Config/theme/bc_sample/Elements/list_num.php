<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * リスト表示件数設定
 * 呼出箇所：サイト内検索結果一覧、ブログトップ、カテゴリ別ブログ記事一覧、タグ別ブログ記事一覧、年別ブログ記事一覧、月別ブログ記事一覧、日別ブログ記事一覧
 *
 * BcBaserHelper::listNum() で呼び出す
 * （例）<?php $this->BcBaser->listNum() ?>
 *
 * @var BcAppView $this
 */
$currentNum = '';
if (empty($nums)) {
	$nums = ['10', '20', '50', '100'];
}
if (!is_array($nums)) {
	$nums = [$nums];
}
if (!empty($this->passedArgs['num'])) {
	$currentNum = $this->passedArgs['num'];
}
$links = [];
foreach ($nums as $num) {
	if ($currentNum != $num) {
		$links[] = '<span>' . $this->BcBaser->getLink($num, am($this->passedArgs, ['num' => $num, 'page' => null])) . '</span>';
	} else {
		$links[] = '<span class="current">' . $num . '</span>';
	}
}
if ($links) {
	$link = implode('｜', $links);
}
?>
<?php if ($link): ?>
	<div class="bs-list-num">
		<strong><?php echo __('表示件数') ?></strong><span class="bs-list-num__number"><?php echo $link ?></span>
	</div>
<?php endif ?>

