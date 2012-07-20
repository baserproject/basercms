<?php
/* SVN FILE: $Id$ */
/**
 * ブログコントローラー基底クラス
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog
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
 * ブログコントローラー基底クラス
 *
 * @package			baser.plugins.blog
 */
class BlogAppController extends BaserPluginAppController {
/**
 * コメントを管理者メールへメール送信する
 * @param array $data
 */
	function _sendComment() {

		if(!$this->data || empty($this->siteConfigs['email'])) {
			return false;
		}else {
			$data = $this->data;
			$data['SiteConfig'] = $this->siteConfigs;
		}
		$to = $this->siteConfigs['email'];
		$title = '【'.$this->siteConfigs['name'].'】コメントを受け付けました';
		$this->sendMail($to, $title, $data, array('template' => 'blog_comment'));

	}
/**
 * beforeFilter
 *
 * @return	void
 * @access 	public
 */
	function beforeFilter() {
		
		parent::beforeFilter();
		$user = $this->BcAuth->user();
		$userModel = $this->getUserModel();
		if(!$user || !$userModel) {
			return;
		}
		$newCatAddable = $this->BlogCategory->checkNewCategoryAddable(
				$user[$userModel]['user_group_id'], 
				$this->checkRootEditable()
		);
		$this->set('newCatAddable', $newCatAddable);
		
	}
	
}