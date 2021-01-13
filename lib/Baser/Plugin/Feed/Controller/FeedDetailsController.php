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
 * フィード詳細コントローラー
 *
 * @package Feed.Controller
 * @property Feed $Feed
 * @property FeedConfig $FeedConfig
 * @property FeedDetail $FeedDetail
 */
class FeedDetailsController extends FeedAppController
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'FeedDetails';

	/**
	 * モデル
	 *
	 * @var array
	 */
	public $uses = ['Feed.FeedDetail', 'Feed.FeedConfig', 'Feed.Feed'];

	/**
	 * ヘルパー
	 *
	 * @var array
	 */
	public $helpers = ['BcForm'];

	/**
	 * コンポーネント
	 *
	 * @var array
	 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];

	/**
	 * FeedDetailsController constructor.
	 *
	 * @param \CakeRequest $request
	 * @param \CakeRequest $response
	 */
	public function __construct($request = null, $response = null)
	{
		parent::__construct($request, $response);
		$this->crumbs = [
			['name' => __d('baser', 'フィード管理'), 'url' => ['controller' => 'feed_configs', 'action' => 'index']]
		];
	}

	/**
	 * サブメニューエレメント
	 *
	 * @var array
	 */
	public $subMenuElements = [];

	/**
	 * beforeFilter
	 *
	 * @return void
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();

		$feedConfig = $this->FeedConfig->read(null, $this->params['pass'][0]);
		$this->crumbs[] = ['name' => __d('baser', 'フィード設定情報') . '： ' . $feedConfig['FeedConfig']['name'], 'url' => ['controller' => 'feed_configs', 'action' => 'edit', $this->params['pass'][0]]];

		if ($this->params['prefix'] == 'admin') {
			$this->subMenuElements = ['feed_details'];
			$this->help = 'feed_details_form';
		}
	}

	/**
	 * [ADMIN] 登録
	 *
	 * @param int feed_config_id
	 * @return void
	 */
	public function admin_add($feedConfigId)
	{
		/* 除外処理 */
		if (!$feedConfigId) {
			$this->BcMessage->setError(__d('baser', '無効なIDです'));
			$this->redirect(['controller' => 'feed_configs', 'action' => 'index']);
		}

		if (empty($this->request->data)) {

			$this->request->data = $this->FeedDetail->getDefaultValue($feedConfigId);
		} else {

			if (!preg_match('/^http/is', $this->request->data['FeedDetail']['url']) && !preg_match('/^\//is', $this->request->data['FeedDetail']['url'])) {
				$this->request->data['FeedDetail']['url'] = '/' . $this->request->data['FeedDetail']['url'];
			}

			$this->FeedDetail->create($this->request->data);

			// データを保存
			if ($this->FeedDetail->save()) {

				$id = $this->FeedDetail->getLastInsertId();
				$this->BcMessage->setSuccess(sprintf(__d('baser', 'フィード「%s」を追加しました。'), $this->request->data['FeedDetail']['name']));
				$this->redirect(['controller' => 'feed_configs', 'action' => 'edit', $feedConfigId, $id, '#' => 'headFeedDetail']);
			} else {

				$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			}
		}

		// 表示設定
		$this->pageTitle = __d('baser', '新規フィード情報登録');
		$this->render('form');
	}

	/**
	 * [ADMIN] 編集
	 *
	 * @param int $feedConfigId
	 * @param int $id
	 * @return    void
	 * @access    public
	 */
	public function admin_edit($feedConfigId, $id)
	{
		if (!$id && empty($this->request->data)) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			$this->redirect(['controller' => 'feed_configs', 'action' => 'index']);
		}

		if (empty($this->request->data)) {

			$this->request->data = $this->FeedDetail->read(null, $id);
		} else {

			if (!preg_match('/^http/is', $this->request->data['FeedDetail']['url']) && !preg_match('/^\//is', $this->request->data['FeedDetail']['url'])) {
				$this->request->data['FeedDetail']['url'] = '/' . $this->request->data['FeedDetail']['url'];
			}

			$this->FeedDetail->set($this->request->data);

			// データを保存
			if ($this->FeedDetail->save()) {

				$this->requestAction(['controller' => 'feed_configs', 'action' => 'clear_cache'], ['pass' => [$this->request->data['FeedDetail']['feed_config_id'], $this->request->data['FeedDetail']['url']]]);
				$this->BcMessage->setSuccess(sprintf(__d('baser', 'フィード詳細「%s」を更新しました。'), $this->request->data['FeedDetail']['name']));
				$this->redirect(['controller' => 'feed_configs', 'action' => 'edit', $feedConfigId, $id, '#' => 'headFeedDetail']);
			} else {

				$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			}
		}

		// 表示設定
		$this->pageTitle = __d('baser', 'フィード情報編集');
		$this->render('form');
	}

	/**
	 * フィードのキャッシュを削除する
	 *
	 * @param string $feedConfigId
	 * @param string $url
	 * @return    void
	 * @access    protected
	 */
	protected function _clearViewCatch($feedConfigId, $url)
	{
		clearViewCache('/feed/index/' . $feedConfigId);
		clearViewCache('/feed/ajax/' . $feedConfigId);
		clearViewCache('/feed/cachetime/' . $feedConfigId);
		if (strpos($url, 'http') === false) {
			// 実際のキャッシュではSSLを利用しているかどうかわからないので、両方削除する
			$host = Configure::read('BcEnv.host');
			clearCache($this->Feed->createCacheHash('', 'http://' . $host . $this->base . $url), 'views', '.rss');
			clearCache($this->Feed->createCacheHash('', 'https://' . $host . $this->base . $url), 'views', '.rss');
		} else {
			clearCache($this->Feed->createCacheHash('', $url), 'views', '.rss');
		}
	}

	/**
	 * [ADMIN] 削除　(ajax)
	 *
	 * @param int $feedConfigId
	 * @param int $id
	 * @return void
	 * @access    public
	 */
	public function admin_ajax_delete($feedConfigId, $id = null)
	{
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, __('無効な処理です。'));
		}
		if ($this->_del($id)) {
			exit(true);
		}
		exit();
	}

	/**
	 * [ADMIN] 削除
	 *
	 * @param int $feedConfigId
	 * @param int $id
	 * @return void
	 * @access    public
	 */
	public function admin_delete($feedConfigId, $id = null)
	{
		$this->_checkSubmitToken();
		/* 除外処理 */
		if (!$id) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			$this->redirect(['controller' => 'feed_configs', 'action' => 'index']);
		}

		// メッセージ用にデータを取得
		$FeedDetail = $this->FeedDetail->read(null, $id);

		// 削除実行
		if ($this->FeedDetail->delete($id)) {
			$this->BcMessage->setSuccess(sprintf(__d('baser', '%s を削除しました。'), $FeedDetail['FeedDetail']['name']));
		} else {
			$this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
		}

		$this->redirect(['controller' => 'feed_configs', 'action' => 'edit', $feedConfigId, $id, '#' => 'headFeedDetail']);
	}

	/**
	 * 一括削除
	 *
	 * @param array $ids
	 * @return boolean
	 */
	protected function _batch_del($ids)
	{
		if ($ids) {
			foreach($ids as $id) {
				$this->_del($id);
			}
		}
		return true;
	}

	/**
	 * データを削除する
	 *
	 * @param int $id
	 * @return boolean
	 */
	protected function _del($id)
	{
		$data = $this->FeedDetail->read(null, $id);
		if ($this->FeedDetail->delete($id)) {
			$this->FeedDetail->saveDbLog(sprintf(__d('baser', 'フィード「%s」を削除しました。'), $data['FeedDetail']['name']));
			return true;
		} else {
			return false;
		}
	}

}
