<?php

/**
 * メールヘルパー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */

/**
 * メールヘルパー
 *
 * @package Mail.View.Helper
 *
 */
class MailHelper extends AppHelper {

/**
 * ヘルパー
 * @var array
 */
	public $helpers = array('BcBaser');

/**
 * コンストラクタ
 *
 * @param View $View Viewオブジェクト
 * @return void
 */
	public function __construct(View $View) {
		parent::__construct($View);
		$this->setMailContent();
	}

/**
 * メールコンテンツデータをセットする
 *
 * @param int $mailContentId メールコンテンツID
 * @return void
 */
	public function setMailContent($mailContentId = null) {
		if (isset($this->mailContent)) {
			return;
		}
		if ($mailContentId) {
			$MailContent = ClassRegistry::getObject('MailContent');
			$MailContent->expects(array());
			$this->mailContent = Hash::extract($MailContent->read(null, $mailContentId), 'MailContent');
		} elseif (isset($this->_View->viewVars['mailContent'])) {
			$this->mailContent = $this->_View->viewVars['mailContent']['MailContent'];
		}
	}

/**
 * 管理画面のメールフィールド一覧ページへのリンクを出力する
 *
 * @param string $mailContentId メールコンテンツID
 * @return void
 * @todo ツールバーに移行
 * @deprecated
 */
	public function indexFields($mailContentId) {
		if (!empty($this->BcBaser->_View->viewVars['user']) && !Configure::read('BcRequest.agent')) {
			echo '<div class="edit-link">' . $this->BcBaser->getLink('≫ 編集する', array('prefix' => 'mail', 'controller' => 'mail_fields', 'action' => 'index', $mailContentId), array('target' => '_blank')) . '</div>';
		}
	}

/**
 * レイアウトテンプレートを取得
 * 
 * コンボボックスのソースとして利用
 * 
 * @return array レイアウトテンプレート一覧データ
 * @todo 他のヘルパーに移動する
 */
	public function getLayoutTemplates() {
		$templatesPathes = array_merge(App::path('View', 'Mail'), App::path('View'));
		if ($this->BcBaser->siteConfig['theme']) {
			array_unshift($templatesPathes, WWW_ROOT . 'theme' . DS . $this->BcBaser->siteConfig['theme'] . DS);
		}

		$_templates = array();
		foreach ($templatesPathes as $templatesPath) {
			$templatesPath .= 'Layouts' . DS;
			$folder = new Folder($templatesPath);
			$files = $folder->read(true, true);
			$foler = null;
			if ($files[1]) {
				if ($_templates) {
					$_templates = am($_templates, $files[1]);
				} else {
					$_templates = $files[1];
				}
			}
		}

		$_templates = array_unique($_templates);
		$templates = array();
		$ext = Configure::read('BcApp.templateExt');
		foreach ($_templates as $template) {
			if ($template != 'installations' . $ext) {
				$template = basename($template, $ext);
				$templates[$template] = $template;
			}
		}
		return $templates;
	}

/**
 * フォームテンプレートを取得
 * 
 * コンボボックスのソースとして利用
 * 
 * @return array フォームテンプレート一覧データ
 * @todo 他のヘルパーに移動する
 */
	public function getFormTemplates() {
		$templatesPathes = array_merge(App::path('View', 'Mail'), App::path('View'));
		if ($this->BcBaser->siteConfig['theme']) {
			array_unshift($templatesPathes, WWW_ROOT . 'theme' . DS . $this->BcBaser->siteConfig['theme'] . DS);
		}

		$_templates = array();
		foreach ($templatesPathes as $templatePath) {
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
		$templates = array();
		foreach ($_templates as $template) {
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
	public function getMailTemplates() {
		$templatesPathes = array_merge(App::path('View', 'Mail'), App::path('View'));
		if ($this->BcBaser->siteConfig['theme']) {
			array_unshift($templatesPathes, WWW_ROOT . 'theme' . DS . $this->BcBaser->siteConfig['theme'] . DS);
		}

		$_templates = array();
		foreach ($templatesPathes as $templatesPath) {
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

		$templates = array();
		$ext = Configure::read('BcApp.templateExt');
		$excludes = array('empty', 'installed' . $ext, 'mail_data' . $ext);
		foreach ($_templates as $template) {
			if (!in_array($template, $excludes)) {
				$template = basename($template, $ext);
				$templates[$template] = $template;
			}
		}
		return $templates;
	}

/**
 * メールフォームの説明文を取得する
 * 
 * @return string メールフォームの説明文
 */
	public function getDescription() {
		return $this->mailContent['description'];
	}

/**
 * メールの説明文を出力する
 * 
 * @return void
 */
	public function description() {
		echo $this->getDescription();
	}

/**
 * メールの説明文が設定されているかどうかを判定する
 *
 * @return boolean 設定されている場合 true を返す
 */
	public function descriptionExists() {
		if (!empty($this->mailContent['description'])) {
			return true;
		} else {
			return false;
		}
	}

/**
 * メールフォームへのリンクを生成する
 * 
 * @param string $title リンクのタイトル
 * @param string $contentsName メールフォームのコンテンツ名
 * @param array $datas メールフォームに引き継ぐデータ（初期値 : array()）
 * @param array $options a タグの属性（初期値 : array()）
 *	※ オプションについては、HtmlHelper::link() を参照
 * @return void
 */
	public function link($title, $contentsName, $datas = array(), $options = array()) {
		if($datas && is_array($datas)) {
			foreach($datas as $key => $data) {
				$datas[$key] = base64UrlsafeEncode($data);
			}
		}
		$link = array_merge(array('plugin' => '', 'controller' => $contentsName,  'action' => 'index'), $datas);
		$this->BcBaser->link($title, $link, $options);
	}

/**
 * ブラウザの戻るボタン対応コードを作成
 * 
 * @return string
 */
	public function getToken() {
		return $this->BcBaser->element('mail_token');
	}

/**
 * ブラウザの戻るボタン対応コードを出力
 * 
 * @return void
 */
	public function token() {
		echo $this->getToken();
	}

}
