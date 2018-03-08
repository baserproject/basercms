<?php
/**
 * リスト表示件数設定（スマホ用）
 * 呼出箇所：サイト内検索結果一覧、ブログトップ、カテゴリ別ブログ記事一覧、タグ別ブログ記事一覧、年別ブログ記事一覧、月別ブログ記事一覧、日別ブログ記事一覧
 *
 * BcBaserHelper::listNum() で呼び出す
 * （例）<?php $this->BcBaser->listNum() ?>
 */
$this->request->params['action'] = str_replace('smartphone_', '', $this->request->params['action']);
$currentNum = '';
if (empty($nums)) {
	$nums = array('10', '20', '50', '100');
}
if (!is_array($nums)) {
	$nums = array($nums);
}
if (!empty($this->passedArgs['num'])) {
	$currentNum = $this->passedArgs['num'];
}
$links = array();
foreach ($nums as $num) {
	if ($currentNum != $num) {
		$links[] = '<span>' . $this->BcBaser->getLink($num, am($this->passedArgs, array('num' => $num, 'page' => null))) . '</span>';
	} else {
		$links[] = '<span class="current">' . $num . '</span>';
	}
}
if ($links) {
	$link = implode('｜', $links);
}
?>
<?php if ($link): ?>
	<div class="list-num">
		<strong><?php echo __('表示件数') ?></strong><p><?php echo $link ?></p>
	</div>
<?php endif ?>
<?php
$this->request->params['action'] = 'smartphone_' . $this->request->params['action'];