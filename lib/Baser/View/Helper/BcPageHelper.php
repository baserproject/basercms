<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View.Helper
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('Helper', 'View');

/**
 * ページヘルパー
 *
 * @package Baser.View.Helper
 * @property BcContentsHelper $BcContents
 * @property BcBaserHelper $BcBaser
 * @property BcAppView $_View
 */
class BcPageHelper extends Helper
{

	/**
	 * ページモデル
	 *
	 * @var Page
	 */
	public $Page = null;

	/**
	 * data
	 * @var array
	 */
	public $data = [];

	/**
	 * ヘルパー
	 *
	 * @var array
	 */
	public $helpers = ['BcBaser', 'BcContents'];

	/**
	 * construct
	 *
	 * @param View $View
	 */
	public function __construct(View $View)
	{

		parent::__construct($View);
		if (ClassRegistry::isKeySet('Page')) {
			$this->Page = ClassRegistry::getObject('Page');
		} else {
			$this->Page = ClassRegistry::init('Page', 'Model');
		}
	}

	/**
	 * ページ機能用URLを取得する
	 *
	 * @param array $page 固定ページデータ
	 * @return string URL
	 */
	public function getUrl($page)
	{
		if (isset($page['Content'])) {
			$page = $page['Content'];
		}
		if (!isset($page['url'])) {
			return '';
		}
		return $page['url'];
	}

	/**
	 * ページリストを取得する
	 * 戻り値は、固定ページ、または、コンテンツフォルダが対象
	 *
	 * @param int $pageCategoryId カテゴリID
	 * @param int $recursive 関連データの階層
	 * @return array
	 */
	public function getPageList($id, $level = null, $options = [])
	{
		$options['type'] = 'Page';
		return $this->BcContents->getTree($id, $level, $options);
	}

	/**
	 * 公開状態を取得する
	 *
	 * @param array データリスト
	 * @return boolean 公開状態
	 */
	public function allowPublish($data)
	{

		if (isset($data['Page'])) {
			$data = $data['Page'];
		}

		$allowPublish = (int)$data['status'];

		// 期限を設定している場合に条件に該当しない場合は強制的に非公開とする
		if (($data['publish_begin'] != 0 && $data['publish_begin'] >= date('Y-m-d H:i:s')) ||
			($data['publish_end'] != 0 && $data['publish_end'] <= date('Y-m-d H:i:s'))) {
			$allowPublish = false;
		}

		return $allowPublish;
	}

	/**
	 * ページカテゴリ間の次の記事へのリンクを取得する
	 *
	 * MEMO: BcRequest.(agent).aliasは廃止
	 *
	 * @param string $title
	 * @param array $options オプション（初期値 : array()）
	 *    - `class` : CSSのクラス名（初期値 : 'next-link'）
	 *    - `arrow` : 表示文字列（初期値 : ' ≫'）
	 *    - `overCategory` : 固定ページのカテゴリをまたいで次の記事のリンクを取得するかどうか（初期値 : false）
	 *        ※ overCategory が true の場合は、BcPageHelper::contentsNaviAvailable() が false だとしても強制的に出力する
	 *    - `escape` : エスケープするかどうか
	 * @return mixed コンテンツナビが無効かつオプションoverCategoryがtrueでない場合はfalseを返す
	 */
	public function getNextLink($title = '', $options = [])
	{

		if (empty($this->request->params['Content']['id']) || empty($this->request->params['Content']['parent_id'])) {
			return false;
		}
		$options = array_merge([
			'class' => 'next-link',
			'arrow' => ' ≫',
			'overCategory' => false,
			'escape' => true
		], $options);

		$arrow = $options['arrow'];
		$overCategory = $options['overCategory'];
		unset($options['arrow']);
		unset($options['overCategory']);

		$content = $this->_getPageByNextOrPrev($this->request->params['Content']['lft'], $this->request->params['Content']['parent_id'], 'next', $overCategory);

		if ($content) {
			if (!$title) {
				$title = $content['Content']['title'] . $arrow;
			}
			$url = $content['Content']['url'];
			return $this->BcBaser->getLink($title, $url, $options);
		} else {
			return false;
		}
	}

	/**
	 * ページカテゴリ間の次の記事へのリンクを出力する
	 *
	 * @param string $title
	 * @param array $options オプション（初期値 : array()）
	 *    - `class` : CSSのクラス名（初期値 : 'next-link'）
	 *    - `arrow` : 表示文字列（初期値 : ' ≫'）
	 *    - `overCategory` : 固定ページのカテゴリをまたいで次の記事のリンクを取得するかどうか（初期値 : false）
	 *        ※ overCategory が true の場合は、BcPageHelper::contentsNaviAvailable() が false だとしても強制的に出力する
	 * @return @return void コンテンツナビが無効かつオプションoverCategoryがtrueでない場合はfalseを出力する
	 */
	public function nextLink($title = '', $options = [])
	{
		echo $this->getNextLink($title, $options);
	}

	/**
	 * ページカテゴリ間の前の記事へのリンクを取得する
	 *
	 * @param string $title
	 * @param array $options オプション（初期値 : array()）
	 *    - `class` : CSSのクラス名（初期値 : 'prev-link'）
	 *    - `arrow` : 表示文字列（初期値 : ' ≫'）
	 *    - `overCategory` : 固定ページのカテゴリをまたいで次の記事のリンクを取得するかどうか（初期値 : false）
	 *    - `escape` : エスケープするかどうか
	 * @return string|false
	 */
	public function getPrevLink($title = '', $options = [])
	{
		if (empty($this->request->params['Content']['id']) || empty($this->request->params['Content']['parent_id'])) {
			return false;
		}
		$options = array_merge([
			'class' => 'prev-link',
			'arrow' => '≪ ',
			'overCategory' => false,
			'escape' => true
		], $options);

		$arrow = $options['arrow'];
		$overCategory = $options['overCategory'];
		unset($options['arrow']);
		unset($options['overCategory']);

		$content = $this->_getPageByNextOrPrev($this->request->params['Content']['lft'], $this->request->params['Content']['parent_id'], 'prev', $overCategory);

		if ($content) {
			if (!$title) {
				$title = $arrow . $content['Content']['title'];
			}
			$url = $content['Content']['url'];
			return $this->BcBaser->getLink($title, $url, $options);
		} else {
			return false;
		}
	}

	/**
	 * ページカテゴリ間の前の記事へのリンクを出力する
	 *
	 * @param string $title
	 * @param array $options オプション（初期値 : array()）
	 *    - `class` : CSSのクラス名（初期値 : 'prev-link'）
	 *    - `arrow` : 表示文字列（初期値 : ' ≫'）
	 *    - `overCategory` : 固定ページのカテゴリをまたいで次の記事のリンクを取得するかどうか（初期値 : false）
	 *        ※ overCategory が true の場合は、BcPageHelper::contentsNaviAvailable() が false だとしても強制的に出力する
	 * @return void コンテンツナビが無効かつオプションoverCategoryがtrueでない場合はfalseを返す
	 */
	public function prevLink($title = '', $options = [])
	{
		echo $this->getPrevLink($title, $options);
	}

	/**
	 * 指定した固定ページデータの次、または、前のデータを取得する
	 *
	 * @param array $page 固定ページデータ
	 * @param string $type next Or prev
	 * @param bool $overCategory カテゴリをまたがるかどうか
	 * @return array 次、または、前の固定ページデータ
	 */
	protected function _getPageByNextOrPrev($lft, $parentId, $type, $overCategory = false)
	{
		$Content = ClassRegistry::init('Content');
		$conditions = array_merge($Content->getConditionAllowPublish(), [
			'Content.type <>' => 'ContentFolder',
			'Content.site_id' => $this->request->params['Content']['site_id']
		]);
		if ($overCategory !== true) {
			$conditions['Content.parent_id'] = $parentId;
		}
		$data = $Content->find('neighbors', [
			'field' => 'lft',
			'value' => $lft,
			'conditions' => $conditions,
			'order' => ['Content.lft'],
			'recursive' => 0,
			'cache' => false
		]);
		if ($data && !empty($data[$type])) {
			return $data[$type];
		} else {
			return false;
		}
	}

	/**
	 * 固定ページのコンテンツを出力する
	 *
	 * @return void
	 */
	public function content()
	{
		$previewTemplate = $this->_View->get('previewTemplate');
		if ($previewTemplate) {
			$path = $previewTemplate;
		} else {
			$path = APP . 'View' . DS . 'Pages' . DS . $this->_View->get('pagePath') . $this->_View->ext;
		}
		echo $this->_View->evaluate($path, $this->_View->viewVars);
	}

}
