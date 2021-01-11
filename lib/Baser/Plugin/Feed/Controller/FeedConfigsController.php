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
 * フィード設定コントローラー
 *
 * @package Feed.Controller
 * @property Feed $Feed
 * @property FeedConfig $FeedConfig
 * @property FeedDetail $FeedDetail
 */
class FeedConfigsController extends FeedAppController
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'FeedConfigs';

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
	public $helpers = ['BcText', 'BcTime', 'BcForm', 'Feed.Feed'];

	/**
	 * コンポーネント
	 *
	 * @var array
	 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];

	/**
	 * サブメニューエレメント
	 *
	 * @var array
	 */
	public $subMenuElements = [];

	/**
	 * FeedConfigsController constructor.
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
	 * before_filter
	 *
	 * @return void
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();

		if ($this->params['prefix'] == 'admin') {
			$this->subMenuElements = ['feed_common'];
		}
	}

	/**
	 * [ADMIN] 一覧表示
	 *
	 * @return void
	 */
	public function admin_index()
	{
		$default = ['named' => ['num' => $this->siteConfigs['admin_list_num']]];
		$this->setViewConditions('FeedConfig', ['default' => $default]);
		// データを取得
		$this->paginate = ['conditions' => [],
			'fields' => [],
			'order' => 'FeedConfig.id',
			'limit' => $this->passedArgs['num']
		];
		$feedConfigs = $this->paginate('FeedConfig');

		if ($feedConfigs) {
			$this->set('feedConfigs', $feedConfigs);
		}

		// 表示設定
		$this->pageTitle = __d('baser', 'フィード設定一覧');
		$this->help = 'feed_configs_index';
	}

	/**
	 * [ADMIN] 登録
	 *
	 * @return void
	 */
	public function admin_add()
	{
		if (empty($this->request->data)) {

			$this->request->data = $this->FeedConfig->getDefaultValue();
		} else {

			$this->FeedConfig->create($this->request->data);

			// データを保存
			if ($this->FeedConfig->save()) {

				$id = $this->FeedConfig->getLastInsertId();
				$this->BcMessage->setSuccess(sprintf(__d('baser', 'フィード「%s」を追加しました。'), $this->request->data['FeedConfig']['name']));
				$this->redirect(['controller' => 'feed_configs', 'action' => 'edit', $id, '#' => 'headFeedDetail']);
			} else {

				$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			}
		}

		// 表示設定
		$this->pageTitle = __d('baser', '新規フィード設定登録');
		$this->help = 'feed_configs_form';
		$this->render('form');
	}

	/**
	 * [ADMIN] 編集
	 *
	 * @param int $id
	 * @return void
	 */
	public function admin_edit($id)
	{
		if (!$id && empty($this->request->data)) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			$this->redirect(['action' => 'index']);
		}

		if (empty($this->request->data)) {

			$this->request->data = $this->FeedConfig->read(null, $id);
			$this->set('feedConfig', $this->request->data);
		} else {

			// データを保存
			if ($this->FeedConfig->save($this->request->data)) {

				$this->_clearCache($this->request->data['FeedConfig']['id']);
				$this->BcMessage->setSuccess(sprintf(__d('baser', 'フィード「%s」を更新しました。'), $this->request->data['FeedConfig']['name']));

				if ($this->request->data['FeedConfig']['edit_template']) {
					$this->redirectEditTemplate($this->request->data['FeedConfig']['template']);
				} else {
					$this->redirect(['action' => 'index']);
				}
			} else {

				$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			}
		}

		// 表示設定
		$this->subMenuElements = array_merge($this->subMenuElements, ['feed_details']);
		$this->pageTitle = __d('baser', 'フィード設定編集');
		$this->help = 'feed_configs_form';
		$this->render('form');
	}

	/**
	 * テンプレート編集画面にリダイレクトする
	 *
	 * @param string $template
	 * @return void
	 */
	protected function redirectEditTemplate($template)
	{
		$path = 'Feed' . DS . $template . $this->ext;
		$target = WWW_ROOT . 'theme' . DS . $this->siteConfigs['theme'] . DS . $path;
		$sorces = [BASER_PLUGINS . 'Feed' . DS . 'View' . DS . $path];
		if ($this->siteConfigs['theme']) {
			if (!file_exists($target)) {
				foreach($sorces as $source) {
					if (file_exists($source)) {
						$folder = new Folder();
						$folder->create(dirname($target), 0777);
						copy($source, $target);
						chmod($target, 0666);
						break;
					}
				}
			}
			$this->redirect(array_merge(['plugin' => null, 'mail' => false, 'prefix' => false, 'controller' => 'theme_files', 'action' => 'edit', $this->siteConfigs['theme'], 'etc'], explode('/', $path)));
		} else {
			$this->BcMessage->setError(__d('baser', '現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。'));
			$this->redirect(['action' => 'index']);
		}
	}

	/**
	 * [ADMIN] 削除　(ajax)
	 *
	 * @param int $id
	 * @return void
	 */
	protected function _batch_del($ids)
	{
		if ($ids) {
			foreach($ids as $id) {
				// メッセージ用にデータを取得
				$feedConfig = $this->FeedConfig->read(null, $id);

				// 削除実行
				if ($this->FeedConfig->delete($id)) {
					$this->FeedConfig->saveDbLog(sprintf(__d('baser', 'フィード「%s」を削除しました。'), $feedConfig['FeedConfig']['name']));
				}
			}
		}
		return true;
	}

	/**
	 * [ADMIN] 削除　(ajax)
	 *
	 * @param int $id
	 * @return void
	 */
	public function admin_ajax_delete($id = null)
	{
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, __('無効な処理です。'));
		}

		// メッセージ用にデータを取得
		$feedConfig = $this->FeedConfig->read(null, $id);

		// 削除実行
		if ($this->FeedConfig->delete($id)) {
			$this->FeedConfig->saveDbLog(sprintf(__d('baser', 'フィード「%s」を削除しました。'), $feedConfig['FeedConfig']['name']));
			exit(true);
		}
		exit();
	}

	/**
	 * [ADMIN] 削除
	 *
	 * @param int $id
	 * @return void
	 */
	public function admin_delete($id = null)
	{
		$this->_checkSubmitToken();
		if (!$id) {

			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			$this->redirect(['action' => 'index']);
			return;
		}

		// メッセージ用にデータを取得
		$feedConfig = $this->FeedConfig->read(null, $id);

		// 削除実行
		if ($this->FeedConfig->delete($id)) {

			$this->BcMessage->setSuccess(sprintf(__d('baser', ' %sを削除しました。'), $feedConfig['FeedConfig']['name']));
		} else {

			$this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
		}

		$this->redirect(['action' => 'index']);
	}

	/**
	 * 読み込んだフィードをプレビュー表示する
	 *
	 * @param string $id
	 * @return void
	 */
	public function admin_preview($id)
	{
		if (!$id) {
			$this->notFound();
		}

		$feedConfig = $this->FeedConfig->findById($id);
		$this->pageTitle = __d('baser', 'プレビュー');
		$this->set('id', $id);
		$this->set('feedConfig', $feedConfig);
	}

	/**
	 * フィードのキャッシュを全て削除する
	 *
	 * @return void
	 */
	public function admin_delete_cache()
	{
		$this->_checkReferer();
		$this->_clearCache();
		$this->BcMessage->setInfo(__d('baser', 'フィードのキャッシュを削除しました。'));
		$this->redirect($this->referer());
	}

	/**
	 * フィードのキャッシュを削除する（requestAction用）
	 *
	 * @param string $feedConfigId
	 * @param string $url
	 * @return void
	 */
	public function admin_clear_cache($feedConfigId = '', $url = '')
	{
		$this->_clearCache($feedConfigId, $url);
	}

	/**
	 * フィードのキャッシュを削除する
	 * TODO 第2引き数がない場合、全てのRSSのキャッシュを削除してしまう仕様となっているので
	 * RSSキャッシュ保存名をURLのハッシュ文字列ではなく、feed_detail_idを元にした文字列に変更し、
	 * feed_detail_idで指定して削除できるようにする
	 *
	 * @param string $feedConfigId
	 * @param string $url
	 * @return    void
	 * @access    protected
	 */
	protected function _clearCache($feedConfigId = '', $url = '')
	{
		if ($feedConfigId) {
			clearViewCache('/feed/index/' . $feedConfigId);
			clearViewCache('/feed/ajax/' . $feedConfigId);
			clearViewCache('/feed/cachetime/' . $feedConfigId);
		} else {
			clearViewCache('/feed/index');
			clearViewCache('/feed/ajax');
			clearViewCache('/feed/cachetime');
		}
		if ($url) {
			if (strpos($url, 'http') === false) {
				// 実際のキャッシュではSSLを利用しているかどうかわからないので、両方削除する
				$host = Configure::read('BcEnv.host');
				clearCache($this->Feed->createCacheHash('', 'http://' . $host . $this->base . $url), 'views', '.rss');
				clearCache($this->Feed->createCacheHash('', 'https://' . $host . $this->base . $url), 'views', '.rss');
			} else {
				clearCache($this->Feed->createCacheHash('', $url), 'views', '.rss');
			}
		} else {
			clearViewCache(null, 'rss');
		}
	}

}
