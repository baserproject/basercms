<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.View.Helper
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('AppHelper', 'View/Helper');

/**
 * メールヘルパー
 *
 * @package Mail.View.Helper
 *
 */
class MailHelper extends AppHelper
{

	/**
	 * ヘルパー
	 * @var array
	 */
	public $helpers = ['BcBaser'];

	/**
	 * コンストラクタ
	 *
	 * @param View $View Viewオブジェクト
	 * @return void
	 */
	public function __construct(View $View)
	{
		parent::__construct($View);
		$this->setMailContent();
	}

	/**
	 * メールコンテンツデータをセットする
	 *
	 * @param int $mailContentId メールコンテンツID
	 * @return void
	 */
	public function setMailContent($mailContentId = null)
	{
		if (isset($this->mailContent)) {
			return;
		}
		if ($mailContentId) {
			$MailContent = ClassRegistry::init('Mail.MailContent');
			$MailContent->reduceAssociations([]);
			$this->mailContent = Hash::extract($MailContent->read(null, $mailContentId), 'MailContent');
		} elseif (isset($this->_View->viewVars['mailContent'])) {
			$this->mailContent = $this->_View->viewVars['mailContent']['MailContent'];
		}
	}

	/**
	 * フォームテンプレートを取得
	 *
	 * コンボボックスのソースとして利用
	 *
	 * @return array フォームテンプレート一覧データ
	 * @todo 他のヘルパーに移動する
	 */
	public function getFormTemplates($siteId = 0)
	{
		$site = BcSite::findById($siteId);
		$theme = $this->BcBaser->siteConfig['theme'];
		if ($site->theme) {
			$theme = $site->theme;
		}
		$templatesPathes = array_merge(App::path('View', 'Mail'), App::path('View'));
		if ($theme) {
			array_unshift($templatesPathes, WWW_ROOT . 'theme' . DS . $theme . DS);
		}

		$_templates = [];
		foreach($templatesPathes as $templatePath) {
			$templatePath .= 'Mail' . DS;
			$folder = new Folder($templatePath);
			$files = $folder->read(true, true);
			$foler = null;
			if ($files[0]) {
				if ($_templates) {
					$_templates = am($_templates, $files[0]);
				} else {
					$_templates = $files[0];
				}
			}
		}

		$excludes = Configure::read('BcAgent');
		$excludes = Hash::extract($excludes, '{s}.prefix');
		$templates = [];
		foreach($_templates as $template) {
			if (!in_array($template, $excludes)) {
				$templates[$template] = $template;
			}
		}
		return $templates;
	}

	/**
	 * メールテンプレートを取得
	 *
	 * コンボボックスのソースとして利用
	 *
	 * @return array メールテンプレート一覧データ
	 * @todo 他のヘルパに移動する
	 */
	public function getMailTemplates($siteId = 0)
	{
		$site = BcSite::findById($siteId);
		$theme = $this->BcBaser->siteConfig['theme'];
		if ($site->theme) {
			$theme = $site->theme;
		}
		$templatesPathes = array_merge(App::path('View', 'Mail'), App::path('View'));
		if ($theme) {
			array_unshift($templatesPathes, WWW_ROOT . 'theme' . DS . $theme . DS);
		}

		$_templates = [];
		foreach($templatesPathes as $templatesPath) {
			$templatesPath .= 'Emails' . DS . 'text' . DS;
			$Folder = new Folder($templatesPath);
			$files = $Folder->read(true, true);
			$Folder = null;
			if ($files[1]) {
				if ($_templates) {
					$_templates = am($_templates, $files[1]);
				} else {
					$_templates = $files[1];
				}
			}
		}

		$templates = [];
		$ext = Configure::read('BcApp.templateExt');
		$excludes = ['empty', 'installed' . $ext, 'mail_data' . $ext];
		foreach($_templates as $template) {
			if (!in_array($template, $excludes)) {
				$template = basename($template, $ext);
				$templates[$template] = $template;
			}
		}
		return $templates;
	}

	/**
	 * メールフォームの説明文を取得する
	 * @return string メールフォームの説明文
	 */
	public function getDescription()
	{
		return $this->mailContent['description'];
	}

	/**
	 * メールの説明文を出力する
	 *
	 * @return void
	 */
	public function description()
	{
		echo $this->getDescription();
	}

	/**
	 * メールの説明文が設定されているかどうかを判定する
	 *
	 * @return boolean 設定されている場合 true を返す
	 */
	public function descriptionExists()
	{
		if (empty($this->mailContent['description'])) {
			return false;
		}

		return true;
	}

	/**
	 * メールフォームへのリンクを生成する
	 *
	 * @param string $title リンクのタイトル
	 * @param string $contentsName メールフォームのコンテンツ名
	 * @param array $datas メールフォームに引き継ぐデータ（初期値 : array()）
	 * @param array $options a タグの属性（初期値 : array()）
	 *    ※ オプションについては、HtmlHelper::link() を参照
	 * @return void
	 */
	public function link($title, $contentsName, $datas = [], $options = [])
	{
		if ($datas && is_array($datas)) {
			foreach($datas as $key => $data) {
				$datas[$key] = base64UrlsafeEncode($data);
			}
		}
		$link = array_merge(['plugin' => '', 'controller' => $contentsName, 'action' => 'index'], $datas);
		$this->BcBaser->link($title, $link, $options);
	}

	/**
	 * ブラウザの戻るボタン対応コードを作成
	 *
	 * @return string
	 */
	public function getToken()
	{
		return $this->BcBaser->getElement('Mail.mail_token');
	}

	/**
	 * ブラウザの戻るボタン対応コードを出力
	 *
	 * @return void
	 */
	public function token()
	{
		echo $this->getToken();
	}

	/**
	 * メールフォームを取得する
	 *
	 * @param $id
	 * @return mixed
	 */
	public function getForm($id = null)
	{
		$MailContent = ClassRegistry::init('Mail.MailContent');
		$conditions = [];
		if ($id) {
			$conditions = [
				'MailContent.id' => $id
			];
		}
		$mailContent = $MailContent->findPublished('first', ['conditions' => $conditions]);
		if (!$mailContent) {
			return false;
		}
		$url = $mailContent['Content']['url'];
		return $this->requestAction($url, ['return' => true]);
	}

	/**
	 * beforeRender
	 *
	 * @param string $viewFile
	 */
	public function beforeRender($viewFile)
	{
		if ($this->request->params['controller'] === 'mail' && in_array($this->request->params['action'], ['index', 'confirm', 'submit'])) {
			// メールフォームをショートコードを利用する際、ショートコードの利用先でキャッシュを利用している場合、
			// セキュリティコンポーネントで発行するトークンが更新されない為、強制的にキャッシュをオフにする
			if (!empty($this->request->params['requested'])) {
				Configure::write('Cache.disable', true);
			}
			$this->_View->BcForm->request->params['_Token']['unlockedFields'] = $this->_View->get('unlockedFields');
		}
	}

}
