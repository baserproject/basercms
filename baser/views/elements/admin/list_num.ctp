<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] リスト設定リンク
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$currentNum = '';
if(empty($nums)){
	$nums = array('10','20','50','100');
}
if(!is_array($nums)){
	$nums = array($nums);
}
if(!empty($this->passedArgs['num'])){
	$currentNum = $this->passedArgs['num'];
}
$links = array();
foreach($nums as $num){
	if($currentNum != $num){
		$links[] = '<span>'.$baser->getLink($num, am($this->passedArgs,array('num'=>$num))).'</span>';
	} else {
		$links[] = '<span class="current">'. $num .'</span>';
	}
}
if ($links) {
	$link = implode('｜',$links);
}
?>
<?php if($link): ?>
<div class="list-num">
	<p>表示件数：<?php echo $link ?></p>
</div>
<?php endif ?>