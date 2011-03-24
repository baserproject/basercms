<?php
/* SVN FILE: $Id$ */
/**
 * [MOBILE] ページネーション
 * 
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
if(!empty($paginator)){
	$this->passedArgs['action'] = str_replace('mobile_','',$this->passedArgs['action']);
	$this->passedArgs['plugin'] = '';
	$paginator->options = array('url' => $this->passedArgs);
	if($paginator->counter(array('format'=>'%pages%'))>1){
		echo $paginator->prev('<<', null, null, null).'&nbsp;';
		echo $paginator->numbers(array('separator'=>'&nbsp;','modulus'=>4)).'&nbsp;';
		echo $paginator->next('>>', null, null, null);
	}
}
?>