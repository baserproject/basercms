<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Model
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class SiteConfig
 *
 * システム設定モデル
 *
 * @package Baser.Model
 */
class SiteConfig extends AppModel
{

	/**
	 * ビヘイビア
	 *
	 * @var array
	 */
	public $actsAs = ['BcCache'];

	/**
	 * SiteConfig constructor.
	 *
	 * @param bool $id
	 * @param null $table
	 * @param null $ds
	 */
	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);
		$this->validate = [
			'formal_name' => [
				'rule' => ['notBlank'], 'message' => __d('baser', 'Webサイト名を入力してください。'), 'required' => true],
			'name' => [
				'rule' => ['notBlank'], 'message' => __d('baser', 'Webサイトタイトルを入力してください。'), 'required' => true],
			'email' => [
				['rule' => ['emails'], 'message' => __d('baser', '管理者メールアドレスの形式が不正です。')],
				['rule' => ['notBlank'], 'message' => __d('baser', '管理者メールアドレスを入力してください。')]],
			'mail_encode' => [
				'rule' => ['notBlank'], 'message' => __d('baser', 'メール送信文字コードを入力してください。初期値は「ISO-2022-JP」です。'), 'required' => true],
			'site_url' => [
				'rule' => ['notBlank'], 'message' => __d('baser', 'WebサイトURLを入力してください。'), 'required' => true],
			'admin_ssl' => [
				'rule' => ['sslUrlExists'], 'message' => __d('baser', '管理画面をSSLで利用するには、SSL用のWebサイトURLを入力してください。')],
			'main_site_display_name' => [
				'rule' => ['notBlank'], 'message' => __d('baser', 'メインサイト表示名を入力してください。'), 'required' => false]
		];
	}

	/**
	 * テーマの一覧を取得する
	 *
	 * @return array
	 */
	public function getThemes()
	{
		$themes = [];
		$themeFolder = new Folder(APP . 'View' . DS . 'theme' . DS);
		$_themes = $themeFolder->read(true, true);
		foreach($_themes[0] as $theme) {
			$themes[$theme] = Inflector::camelize($theme);
		}
		$themeFolder = new Folder(WWW_ROOT . 'theme' . DS);
		$_themes = array_merge($themes, $themeFolder->read(true, true));
		foreach($_themes[0] as $theme) {
			$themes[$theme] = Inflector::camelize($theme);
		}
		return $themes;
	}

	/**
	 * コントロールソースを取得する
	 *
	 * @param string $field
	 * @return mixed array | false
	 */
	public function getControlSource($field = null)
	{
		$controlSources['mode'] = [-1 => __d('baser', 'インストールモード'), 0 => __d('baser', 'ノーマルモード'), 1 => __d('baser', 'デバッグモード１'), 2 => __d('baser', 'デバッグモード２')];
		if (isset($controlSources[$field])) {
			return $controlSources[$field];
		} else {
			return false;
		}
	}

	/**
	 * SSL用のURLが設定されているかチェックする
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function sslUrlExists($check)
	{
		$sslOn = $check[key($check)];
		if ($sslOn && empty($this->data['SiteConfig']['ssl_url'])) {
			return false;
		}
		return true;
	}

	/**
	 * コンテンツ一覧を表示してから、コンテンツの並び順が変更されていないかどうか
	 *
	 * @param $listDisplayed
	 * @return bool
	 */
	public function isChangedContentsSortLastModified($listDisplayed)
	{
		$siteConfigs = $this->findExpanded();
		$changed = false;
		if (!empty($siteConfigs['contents_sort_last_modified'])) {
			$user = BcUtil::loginUser();
			$lastModified = $siteConfigs['contents_sort_last_modified'];
			list($lastModified, $userId) = explode('|', $lastModified);
			$lastModified = strtotime($lastModified);
			if ($user['id'] != $userId) {
				$listDisplayed = strtotime($listDisplayed);
				// 60秒はブラウザのロード時間を加味したバッファ
				if ($lastModified >= ($listDisplayed - 60)) {
					$changed = true;
				}
			}
		}
		return $changed;
	}

	/**
	 * コンテンツ並び順変更時間を更新する
	 */
	public function updateContentsSortLastModified()
	{
		$siteConfigs = $this->findExpanded();
		$user = BcUtil::loginUser();
		$siteConfigs['contents_sort_last_modified'] = date('Y-m-d H:i:s') . '|' . $user['id'];
		$this->saveKeyValue($siteConfigs);
	}

	/**
	 * コンテンツ並び替え順変更時間をリセットする
	 */
	public function resetContentsSortLastModified()
	{
		$siteConfigs['contents_sort_last_modified'] = '';
		$this->saveKeyValue($siteConfigs);
	}

	/**
	 * 指定したフィールドの値がDBのデータと比較して変更状態か確認
	 *
	 * @param string $field フィールド名
	 * @param string $value 値
	 * @return bool
	 */
	public function isChange($field, $value)
	{
		$siteConfig = $this->findExpanded();
		if (isset($siteConfig[$field])) {
			return !($siteConfig[$field] === $value);
		} else {
			return false;
		}
	}

}
