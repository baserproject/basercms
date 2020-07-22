<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\View\Helper;
use \Cake\View\Helper;

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

	public function url($url = null, $full = false, $sessionId = true) {

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

    public function i18nScript($data, $options = []) {

    }

    public function flash() {

    }

    public function getContentsTitle() {

    }

/**
 * コンテンツを特定する文字列を出力する
 *
 * URL を元に、第一階層までの文字列をキャメルケースで取得する
 * ※ 利用例、出力例については BcBaserHelper::getContentsName() を参照
 *
 * @param bool $detail 詳細モード true にした場合は、ページごとに一意となる文字列をキャメルケースで出力する（初期値 : false）
 * @param array $options オプション（初期値 : array()）
 *	※ オプションの詳細については、BcBaserHelper::getContentsName() を参照
 * @return void
 */
	public function contentsName($detail = false, $options = []) {
		echo $this->getContentsName($detail, $options);
	}


/**
 * コンテンツを特定する文字列を取得する
 *
 * URL を元に、第一階層までの文字列をキャメルケースで取得する
 *
 * 《利用例》
 * $this->BcBaser->contentsName()
 *
 * 《出力例》
 * - トップページの場合 : Home
 * - about ページの場合 : About
 *
 * @param bool $detail 詳細モード true にした場合は、ページごとに一意となる文字列をキャメルケースで取得する（初期値 : false）
 * @param array $options オプション（初期値 : array()）
 *	- `home` : トップページの場合に出力する文字列（初期値 : Home）
 *	- `default` : ルート直下の下層ページの場合に出力する文字列（初期値 : Default）
 *	- `error` : エラーページの場合に出力する文字列（初期値 : Error）
 *  - `underscore` : キャメルケースではなく、アンダースコア区切りで出力する（初期値 : false）
 * @return string
 */
	public function getContentsName($detail = false, $options = []) {
	    return 'AdminUsersLogin';
	}

}
