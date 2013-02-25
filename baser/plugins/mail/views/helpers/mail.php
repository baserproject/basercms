<?php
/* SVN FILE: $Id$ */
/**
 * メールヘルパー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.mail.views.helpers
 * @since			baserCMS v 0.1.0
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
 * @package baser.plugins.mail.views.helpers
 *
 */
class MailHelper extends AppHelper {
/**
 * view
 * @var		View
 * @access	protected
 */
	var $_view = null;
/**
 * ヘルパー
 * @var array
 * @access public
 */
	var $helpers = array(BC_BASER_HELPER);
/**
 * コンストラクタ
 *
 * @return void
 * @access public
 */
	function __construct() {

		$this->_view =& ClassRegistry::getObject('view');
		$this->setMailContent();

	}
/**
 * メールコンテンツデータをセットする
 *
 * @param int $mailContentId
 */
	function setMailContent($mailContentId = null) {

		if(isset($this->mailContent) && !$mailContentId) {
			return;
		}
		if($mailContentId) {
			$MailContent = ClassRegistry::getObject('MailContent');
			$MailContent->expects(array());
			$this->mailContent = Set::extract('MailContent', $MailContent->read(null, $mailContentId));
		} elseif(isset($this->_view->viewVars['mailContent'])) {
			$this->mailContent = $this->_view->viewVars['mailContent']['MailContent'];
		}

	}
/**
 * 管理画面のメールフィールド一覧ページへのリンクを出力する
 *
 * @param string $mailContentId
 * @return void
 * @access public
 * @deprecated ツールバーに移行
 */
	function indexFields($mailContentId) {

		if(!empty($this->BcBaser->_view->viewVars['user']) && !Configure::read('BcRequest.agent')) {
			echo '<div class="edit-link">'.$this->BcBaser->getLink('≫ 編集する', array('prefix' => 'mail', 'controller' => 'mail_fields', 'action' => 'index', $mailContentId), array('target' => '_blank')).'</div>';
		}

	}
/**
 * レイアウトテンプレートを取得
 * コンボボックスのソースとして利用
 * TODO 他のヘルパーに移動する
 * @return array
 * @access public
 */
	function getLayoutTemplates() {

		$templatesPathes = array();
		if($this->BcBaser->siteConfig['theme']){
			$templatesPathes[] = WWW_ROOT.'themed'.DS.$this->BcBaser->siteConfig['theme'].DS.'layouts'.DS;
		}
		$templatesPathes[] = APP . 'plugins' . DS . 'mail'.DS.'views'.DS.'layouts'.DS;
		$templatesPathes = am($templatesPathes,array(
			BASER_PLUGINS.'mail'.DS.'views'.DS.'layouts'.DS,
			BASER_VIEWS.'layouts'.DS
		));

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

		$_templates = array_unique($_templates);
		$templates = array();
		$ext = Configure::read('BcApp.templateExt');
		foreach($_templates as $template){
			if($template != 'installations'.$ext){
				$template = basename($template, $ext);
				$templates[$template] = $template;
			}
		}
		return $templates;

	}
/**
 * フォームテンプレートを取得
 * コンボボックスのソースとして利用
 * TODO 他のヘルパーに移動する
 * @return array
 * @access public
 */
	function getFormTemplates() {

		$templatesPathes = array();
		if($this->BcBaser->siteConfig['theme']){
			$templatesPathes[] = WWW_ROOT.'themed'.DS.$this->BcBaser->siteConfig['theme'].DS.'mail'.DS;
		}
		$templatesPathes[] = APP . 'plugins' . DS . 'mail'.DS.'views'.DS.'mail'.DS;
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

		$excludes = Configure::read('BcAgent');
		$excludes = Set::extract('{.+?}.prefix', $excludes);
		$templates = array();
		foreach($_templates as $template){
			if(!in_array($template, $excludes)){
				$templates[$template] = $template;
			}
		}
		return $templates;

	}
/**
 * レイアウトテンプレートを取得
 * コンボボックスのソースとして利用
 * TODO 他のヘルパに移動する
 * @return array
 * @access public
 */
	function getMailTemplates() {

		$templatesPathes = array();
		if($this->BcBaser->siteConfig['theme']){
			$templatesPathes[] = WWW_ROOT.'themed'.DS.$this->BcBaser->siteConfig['theme'].DS.'elements'.DS.'email'.DS.'text'.DS;
		}
		$templatesPathes[] = APP . 'plugins' . DS . 'mail'.DS.'views'.DS.'elements'.DS.'email'.DS.'text'.DS;
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
			$ext = Configure::read('BcApp.templateExt');
			if($template != 'mail_data'.$ext){
				$template = basename($template, $ext);
				$templates[$template] = $template;
			}
		}
		return $templates;

	}
/**
 * メールの説明文を取得する
 * @return string
 */
	function getDescription() {
		return $this->mailContent['description'];
	}
/**
 * メールの説明文を表示する
 * @return void
 */
	function description() {
		echo $this->getDescription();
	}
/**
 * メールの説明文が指定されているかどうかを判定する
 *
 * @return boolean
 * @access public
 */
	function descriptionExists() {

		if(!empty($this->mailContent['description'])) {
			return true;
		}else {
			return false;
		}

	}

}