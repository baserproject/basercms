<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Feed.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * フィードヘルパー
 *
 * @package Feed.View.Helper
 *
 */
class FeedHelper extends AppHelper {

/**
 * ヘルパー
 * 
 * @var array
 */
	public $helpers = array('BcBaser');

/**
 * レイアウトテンプレートを取得
 * コンボボックスのソースとして利用
 * 
 * @return array レイアウトの一覧データ
 */
	public function getTemplates() {
		$templatesPathes = array();
		if ($this->BcBaser->siteConfig['theme']) {
			$templatesPathes[] = WWW_ROOT . 'theme' . DS . $this->BcBaser->siteConfig['theme'] . DS . 'Feed' . DS;
		}
		$templatesPathes[] = BASER_PLUGINS . 'Feed' . DS . 'View' . DS . 'Feed' . DS;

		$_templates = array();
		foreach ($templatesPathes as $templatesPath) {
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
		$templates = array();
		foreach ($_templates as $template) {
			$ext = Configure::read('BcApp.templateExt');
			if ($template != 'ajax' . $ext && $template != 'error' . $ext) {
				$template = basename($template, $ext);
				$templates[$template] = $template;
			}
		}
		return $templates;
	}

/**
 * @deprecated 4.1.0 since 4.0.0 
 */
	public function saveCachetime() {
		trigger_error(deprecatedMessage(__d('baser', 'メソッド') . '：FeedHelper::saveCachetime()', '4.0.0', '4.1.0', __d('baser', 'このメソッドは非推奨となりました。代替機能はありません。この行を削除してください。')), E_USER_DEPRECATED);
	}
/**
 * @deprecated 4.1.0 since 4.0.0
 */
	public function cacheHeader() {
		trigger_error(deprecatedMessage(__d('baser', 'メソッド') . '：FeedHelper::cacheHeader()', '4.0.0', '4.1.0', __d('baser', 'このメソッドは非推奨となりました。代替機能はありません。この行を削除してください。')), E_USER_DEPRECATED);
	}

}
