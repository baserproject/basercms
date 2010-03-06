<?php
/* SVN FILE: $Id$ */
/**
 * メールヘルパー
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
 * @package			baser.plugins.mail.views.helpers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * メールヘルパー
 *
 * @package			baser.plugins.mail.views.helpers
 *
 */
class MailHelper extends TextExHelper {
    var $helpers = array('Baser');
/**
 * メールフィールド一覧ページへのリンクを張る
 * @param string $mailContentId
 */
    function indexFields($mailContentId){
        if(!empty($this->Baser->_view->viewVars['user']) && !Configure::read('Mobile.on')){
            echo '<div class="edit-link">'.$this->Baser->getLink('≫ 編集する',array('admin'=>true,'prefix'=>'mail','controller'=>'mail_fields','action'=>'index',$mailContentId),array('target'=>'_blank')).'</div>';
        }
    }
}

?>