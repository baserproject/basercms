<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ページリスト
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$pages = $bcBaser->getPageList($categoryId);
$current = str_replace($this->base, '', $this->here);
?>
<ul class="clearfix">
<?php
if(!empty($pages)){
	foreach($pages as $key => $page){
		$class = '';
		$no = sprintf('%02d',$key+1);
		$classies = array('page-'.$no);
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
			echo '<li'.$class.'>'.str_replace('/index.php','',$bcBaser->getLink($page['title'],$page['url'])).'</li>';
		}else{
			echo '<li'.$class.'>'.$bcBaser->getLink($page['title'],$page['url']).'</li>';
		}
	}
}
?>
</ul>