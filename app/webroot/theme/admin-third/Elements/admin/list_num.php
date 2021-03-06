<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] リスト設定リンク
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
foreach($nums as $num) {
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
	<dl class="list-num bca-list-num">
		<dt class="bca-list-num__title"><?php echo __d('baser', '表示件数') ?></dt>
		<dd class="bca-list-num__data"><?php echo $link ?></dd>
	</dl>
<?php endif ?>
