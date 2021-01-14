<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Feed.Controller
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * フィードコントローラー
 *
 * @package Feed.Controller
 */
class FeedController extends FeedAppController
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'Feed';

	/**
	 * コンポーネント
	 * @var array
	 */
	public $components = ['RequestHandler', 'Cookie', 'BcAuth', 'BcAuthConfigure'];

	/**
	 * モデル
	 *
	 * @var array
	 */
	public $uses = ["Feed.FeedConfig", "Feed.FeedDetail", "Feed.Feed"];

	/**
	 * ヘルパー
	 *
	 * @var array
	 */
	public $helpers = ['BcText', 'Feed.Feed', 'BcArray'];

	/**
	 * beforeFilter
	 *
	 * @return void
	 */
	public function beforeFilter()
	{
		/* 認証設定 */
		$this->BcAuth->allow('index', 'mobile_index', 'smartphone_index', 'ajax', 'smartphone_ajax');
		parent::beforeFilter();
	}

	/**
	 * [PUBLIC] フィードを一覧表示する
	 *
	 * @param int $id
	 * @return void
	 */
	public function index($id = null)
	{

		$this->navis = [];

		// IDの指定がなかった場合はエラーとする
		if (!$id) {
			$this->render('error');
			return;
		}

		// feed設定データ取得
		$feedConfig = $this->FeedConfig->read(null, $id);
		$feedDetails = $this->FeedDetail->find('all', ['conditions' => ["FeedDetail.feed_config_id" => $id]]);

		// データが取得できなかった場合はエラーとする
		if (!$feedConfig || !$feedDetails) {
			$this->render('error');
			return;
		}
		$cachetime = 0;
		$itemExists = false;
		foreach($feedDetails as $feedDetail) {

			// フィードを取得する
			if (strpos($feedDetail['FeedDetail']['category_filter'], '|') !== false) {
				$categoryFilter = explode('|', $feedDetail['FeedDetail']['category_filter']);
			} else {
				$categoryFilter = $feedDetail['FeedDetail']['category_filter'];
			}

			$url = '';
			if (strpos($feedDetail['FeedDetail']['url'], 'http') !== false) {
				$url = $feedDetail['FeedDetail']['url'];
			} else {
				if (empty($_SERVER['HTTPS'])) {
					$protocol = 'http';
				} else {
					$protocol = 'https';
				}
				if ($protocol) {
					$url = $protocol . '://' . Configure::read('BcEnv.host') . $this->base . $feedDetail['FeedDetail']['url'];
				}
			}

			$feed = $this->Feed->getFeed($url, null, $feedDetail['FeedDetail']['cache_time'], $categoryFilter);
			$feeds[] = $feed;

			if ($cachetime < (strtotime($feedDetail['FeedDetail']['cache_time']) - time())) {
				$cachetime = (strtotime($feedDetail['FeedDetail']['cache_time']) - time());
			}

			if ($feed['Items']) {
				$itemExists = true;
			}
		}
		// データが取得できなかった場合はレンダリングして終了
		if (!$itemExists) {
			$this->render($feedConfig['FeedConfig']['template']);
			return;
		}

		// フィードタイトルをtitle_noとしてインデックス番号に変換する
		if ($feedConfig['FeedConfig']['feed_title_index']) {
			$titleIndex = explode("|", $feedConfig['FeedConfig']['feed_title_index']);
			foreach($feeds as $key => $feed) {
				foreach($titleIndex as $key2 => $title) {
					if ($title == $feed['Channel']['title']['value']) {
						foreach($feed['Items'] as $key3 => $item) {
							$feeds[$key]['Items'][$key3]['feed_title_no']['value'] = $key2 + 1;
							$feeds[$key]['Items'][$key3]['feed_title']['value'] = $title;
						}
					}
				}
			}
		}

		// アイテムをマージ
		$items = [];
		foreach($feeds as $feed) {
			if (!empty($feed['Items'])) {
				$items = array_merge($items, $feed['Items']);
			}
		}

		// カテゴリをcategory_noとしてインデックス番号に変換する
		if ($feedConfig['FeedConfig']['category_index']) {
			$categoryIndex = explode("|", $feedConfig['FeedConfig']['category_index']);
			foreach($items as $key => $item) {
				foreach($categoryIndex as $key2 => $category) {
					if ($category == $item['category']['value']) {
						$items[$key]['category_no']['value'] = $key2 + 1;
					}
				}
			}
		}
		// 日付を秒数に変換
		foreach($items as $key => $item) {
			if (!empty($item['pubDate']['value'])) {
				$items[$key]['timestamp'] = strtotime($item['pubDate']['value']);
			}
		}

		// 日付で並び替え
		usort($items, [$this, "_sortDescByTimestamp"]);

		// 件数で絞り込み
		$items = array_slice($items, 0, $feedConfig['FeedConfig']['display_number']);

		/* キャッシュを設定 */
		if (!isset($_SESSION['Auth'][Configure::read('BcAuthPrefix.admin.sessionKey')])) {
			// モバイルの場合、BcMobileHelper より CacheHelper が先に読み込まれた場合、
			// nocacheが正常に動作しなくなる為、ここでヘルパの設定を行う
			// ※ Shift-JISでキャッシュを保存する為先にBcMobileHelperのイベントを実行する必要がある為
			$this->helpers[] = 'Cache';
			$this->cacheAction = $cachetime;
			// Ajaxのcacheをオフにした場合のクエリ文字列がキャッシュファイル名に影響を与えるので除去
			unset($this->request->query['_']);
		}

		if (!empty($this->request->query['admin_theme']) && $this->adminTheme) {
			$this->theme = $this->adminTheme;
		}

		$this->set('cachetime', $cachetime);
		$this->set('items', $items);
		$this->render($feedConfig['FeedConfig']['template']);
	}

	/**
	 * [SMARTPHONE] フィードを一覧表示する
	 *
	 * @param int $id
	 * @return void
	 */
	public function smartphone_index($id)
	{
		$this->setAction('index', $id);
	}

	/**
	 * [PUBLIC] フィードをAJAXで読み込む為のJavascriptを生成する
	 *
	 * @param int $id
	 * @return void
	 */
	public function ajax($id)
	{
		if (strpos($id, '.js') !== false) {
			$id = str_replace('.js', '', $id);
		}
		if (!empty($this->request->query['admin_theme'])) {
			$this->theme = '';
		}
		$this->cacheAction = Configure::read('BcCache.duration');
		$this->layout = "ajax";
		$this->set('id', $id);
		$this->set('feedAdminTheme', (!empty($this->request->query['admin_theme'])));
		$this->render('ajax');
	}

	/**
	 * [PUBLIC] フィードをAJAXで読み込む為のJavascriptを生成する
	 *
	 * @param int $id
	 * @return void
	 */
	public function smartphone_ajax($id)
	{
		$this->setAction('ajax', $id);
	}

	/**
	 * タイムスタンプを元に降順に並び替える
	 *
	 * @param array $a
	 * @param array $b
	 * @return array
	 */
	protected function _sortDescByTimestamp($a, $b)
	{
		if ($a['timestamp'] == $b['timestamp']) {
			return 0;
		}
		return ($a['timestamp'] > $b['timestamp'])? -1 : 1;
	}

	/**
	 * バブルソート
	 *
	 * @param array $val = ソートする配列
	 * @param string $flag = ソート対象の配列要素
	 * @param string $order = ソートの昇順・降順 デフォルトは昇順
	 * @return array 並び替え後の配列
	 */
	protected function _bsort(&$val, $flag = "", $order = "ASC")
	{
		$count = count($val);
		for($i = 0; $i < $count - 1; $i++) {
			for($j = $count - 1; $j > $i; $j--) {
				if ($flag) {
					if ($order == "DESC") {
						if ($val[$j]["" . $flag . ""] > $val[$j - 1]["" . $flag . ""]) {
							$t = $val[$j];
							$val[$j] = $val[$j - 1];
							$val[$j - 1] = $t;
						}
					} else {
						if ($val[$j]["" . $flag . ""] < $val[$j - 1]["" . $flag . ""]) {
							$t = $val[$j];
							$val[$j] = $val[$j - 1];
							$val[$j - 1] = $t;
						}
					}
				} else {
					if ($order == "DESC") {
						if ($val[$j] > $val[$j - 1]) {
							$t = $val[$j];
							$val[$j] = $val[$j - 1];
							$val[$j - 1] = $t;
						}
					} else {
						if ($val[$j] < $val[$j - 1]) {
							$t = $val[$j];
							$val[$j] = $val[$j - 1];
							$val[$j - 1] = $t;
						}
					}
				}
			}
		}
	}

}
