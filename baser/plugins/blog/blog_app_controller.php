<?php
/* SVN FILE: $Id$ */
/**
 * ブログコントローラー基底クラス
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
 * @package			baser.plugins.blog
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Controller', 'Plugins');
/**
 * ブログコントローラー基底クラス
 *
 * @package			baser.plugins.blog
 */
class BlogAppController extends PluginsController {
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
		$from = $this->siteConfigs['email'];
		$fromName = $this->siteConfigs['name'];
		$title = '【'.$this->siteConfigs['name'].'】コメントを受け付けました';
		$template = 'blog_comment';
		$this->sendmail($to, $from, $fromName, $title, $template, $data);

	}
}
?>