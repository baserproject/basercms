<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ページリスト
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
$pages = $baser->getPageList($categoryId);
$current = str_replace($this->base, '', $this->here);
?>
<ul class="clearfix">
<?php
if(!empty($pages)){
	foreach($pages as $key => $page){
		$class = '';
		$classies = array('page-'.$no);
		$no = sprintf('%02d',$key+1);
		if($key == 0){
			$classies[] = 'first';
		}elseif($key == count($pages) - 1){
			$classies[] = 'last';
		}
		if($current == $page['url']) {
			$classies[] = 'current';
		}
		if($classies) {
			$class = ' class="'.implode(' ', $classies).'"';
		}
		if($this->base == '/index.php' && $page['url'] == '/'){
			echo '<li'.$class.'>'.str_replace('/index.php','',$baser->getLink($page['title'],$page['url'])).'</li>';
		}else{
			echo '<li'.$class.'>'.$baser->getLink($page['title'],$page['url']).'</li>';
		}
	}
}
?>
</ul>