<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Users Community
 * @link          http://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\View\Helper;
use \Cake\View\Helper;
use \Cake\Core\Configure;
use BaserCore\View\Helper\BcHtmlHelper;

/**
 * Class BcBaserHelper
 * @package BaserCore\View\Helper
 */
class BcBaserHelper extends Helper
{
	public $helpers = ['Html'];
	// TODO 取り急ぎ
	public $siteConfig = [
		'formal_name' => 'baserCMS',
		'admin_side_banner' => true
	];
	public function js($url, $inline = true, $options = []) {
		$options = array_merge(['inline' => $inline], $options);
		$result = $this->Html->script($url, $options);
		if ($inline) {
			echo $result;
		}
	}

	public function element($name, $data = [], $options = []) {
		echo $this->getElement($name, $data, $options);
	}

	public function getElement($name, $data = [], $options = []) {
		$out = $this->_View->element($name, $data, $options);
		return $out;
	}

	public function getImg($path, $options = []) {
		return $this->Html->image($path, $options);
	}

	public function link($title, $url = null, $htmlAttributes = [], $confirmMessage = false) {
		echo $this->getLink($title, $url, $htmlAttributes, $confirmMessage);
	}

	public function getLink($title, $url = null, $options = [], $confirmMessage = false) {
		$out = $this->Html->link($title, $url, $options, $confirmMessage);
		return $out;
	}

	public function isAdminUser () {

	}
	public function existsEditLink() {

	}

	public function existsPublishLink() {

	}

	public function url() {

	}
    
    /**
     * ユーザー名を取得する
     * @todo 実装要
     * @param $user
     * @return string
     */
	public function getUserName($user) {
	    return 'basercms';
	}

}
