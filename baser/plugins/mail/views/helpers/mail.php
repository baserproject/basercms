<?php
/* SVN FILE: $Id$ */
/**
 * メールヘルパー
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
/**
 * ヘルパー
 * @var		array
 * @access	public
 */
	var $helpers = array('Baser');
/**
 * メールフィールド一覧ページへのリンクを張る
 * @param string $mailContentId
 */
	function indexFields($mailContentId) {
		if(!empty($this->Baser->_view->viewVars['user']) && !Configure::read('Mobile.on')) {
			echo '<div class="edit-link">'.$this->Baser->getLink('≫ 編集する',array('admin'=>true,'prefix'=>'mail','controller'=>'mail_fields','action'=>'index',$mailContentId),array('target'=>'_blank')).'</div>';
		}
	}
/**
 * レイアウトテンプレートを取得
 * コンボボックスのソースとして利用
 * @return	array
 * @access	public
 */
	function getLayoutTemplates() {

		$templatesPathes = array();
		if($this->Baser->siteConfig['theme']){
			$templatesPathes[] = WWW_ROOT.'themed'.DS.$this->Baser->siteConfig['theme'].DS.'layouts'.DS;
		}
		$templatesPathes = am($templatesPathes,array(BASER_PLUGINS.'mail'.DS.'views'.DS.'layouts'.DS,
													BASER_VIEWS.'layouts'.DS));
		
		$_templates = array();
		foreach($templatesPathes as $templatesPath){
			$folder = new Folder($templatesPath);
			$files = $folder->read(true, true);
			$foler = null;
			if($files[1]){
				if($_templates){
					$_templates = am($_templates,$files[1]);
				}else{
					$_templates = $files[1];
				}
			}
		}
		$templates = array();
		foreach($_templates as $template){
			if($template != 'installations.ctp'){
				$template = basename($template, '.ctp');
				$templates[$template] = $template;
			}
		}
		return $templates;
	}
/**
 * フォームテンプレートを取得
 * コンボボックスのソースとして利用
 * @return	array
 * @access	public
 */
	function getFormTemplates() {

		$templatesPathes = array();
		if($this->Baser->siteConfig['theme']){
			$templatesPathes[] = WWW_ROOT.'themed'.DS.$this->Baser->siteConfig['theme'].DS.'mail'.DS;
		}
		$templatesPathes[] = BASER_PLUGINS.'mail'.DS.'views'.DS.'mail'.DS;
		
		$_templates = array();
		foreach($templatesPathes as $templatePath){
			$folder = new Folder($templatePath);
			$files = $folder->read(true, true);
			$foler = null;
			if($files[0]){
				if($_templates){
					$_templates = am($_templates,$files[0]);
				}else{
					$_templates = $files[0];
				}
			}
		}
		$templates = array();
		foreach($_templates as $template){
			if($template != 'mobile'){
				$templates[$template] = $template;
			}
		}
		return $templates;
	}
/**
 * レイアウトテンプレートを取得
 * コンボボックスのソースとして利用
 * @return	array
 * @access	public
 */
	function getMailTemplates() {

		$templatesPathes = array();
		if($this->Baser->siteConfig['theme']){
			$templatesPathes[] = WWW_ROOT.'themed'.DS.$this->Baser->siteConfig['theme'].DS.'elements'.DS.'email'.DS.'text'.DS;
		}
		$templatesPathes[] = BASER_PLUGINS.'mail'.DS.'views'.DS.'elements'.DS.'email'.DS.'text'.DS;

		$_templates = array();
		foreach($templatesPathes as $templatesPath){
			$folder = new Folder($templatesPath);
			$files = $folder->read(true, true);
			$foler = null;
			if($files[1]){
				if($_templates){
					$_templates = am($_templates,$files[1]);
				}else{
					$_templates = $files[1];
				}
			}
		}
		$templates = array();
		foreach($_templates as $template){
			if($template != 'mail_data.ctp'){
				$template = basename($template, '.ctp');
				$templates[$template] = $template;
			}
		}
		return $templates;
	}
}

?>