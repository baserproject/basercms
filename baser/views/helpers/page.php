<?php
/* SVN FILE: $Id$ */
/**
 * ページヘルパー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2009, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2009, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.view.helpers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * ページヘルパー
 *
 * @package			baser.views.helpers
 */
class PageHelper extends Helper {
    var $Page = null;
/**
 * ページ機能用URLを取得する
 * @param array $page
 * @return string
 */
    function url($page){

        if (!isset($this->Page)) {
            if(ClassRegistry::isKeySet('Page')){
                $this->Page = ClassRegistry::getObject('Page');
            }else{
                $this->Page =& ClassRegistry::init('Page','Model');
            }
        }
        return $this->Page->getPageUrl($page);
        
    }
}
?>