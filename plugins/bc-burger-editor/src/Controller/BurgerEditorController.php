<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.1.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcBurgerEditor\Controller;

use BaserCore\Controller\BcFrontAppController;
use Cake\Event\EventInterface;
use BcBurgerEditor\Lib\BurgerEditorUtil;
use BcBurgerEditor\View\Helper\BurgerEditorHelper;
use BcBurgerEditor\Service\BurgerEditorService;

/**
 * BurgerEditorController
 */
class BurgerEditorController extends BcFrontAppController
{
	/**
	 * Constructor
	 *
	 * @param $request
	 * @param $response
	 */
	public function __construct($request = null, $response = null)
	{
		parent::__construct($request, $response);

		$this->BurgerEditorService = new BurgerEditorService();
	}

	/**
	 * initialize
	 *
	 * ログインページ認証除外
	 *
	 * @return void
	 * @checked
	 * @unitTest
	 * @noTodo
	 */
	public function initialize(): void
	{
		parent::initialize();
		if(isset($this->Authentication)) {
			$this->Authentication->allowUnauthenticated([
				'dl',
				'panel'
			]);
		}
	}

	/**
	 * before filter
	 *
	 * @param EventInterface $event
	 * @return \Cake\Http\Response|void|null
	 */
	public function beforeFilter(EventInterface $event)
	{
		if ($this->getRequest()->getParam('action') === 'panel') {
			$this->setRequest($this->getRequest()->withAttribute('requested', true));
		}
		$result = parent::beforeFilter($event);

		BurgerEditorHelper::setSelfValue();
		$this->set("addonDir", BurgerEditorUtil::getAddonPath());
		return $result;
	}

	/**
	 * ファイルダウンロード
	 */
	public function dl()
	{
		$this->viewBuilder()->disableAutoLayout();
		$this->disableAutoRender();
		$params = func_get_args();
		// path階層は2階層まで許可
		if (empty($params) || count($params) > 2) {
			$this->notfound();
		}
		// ユーザIDを利用する場合1階層目は数値のみ許可
		if (count($params) === 2) {
			$paramsPath = (int)$params[0] . DS . $params[1];
		} else {
			$paramsPath = $params[0];
		}

		$filePath = WWW_ROOT . 'files' . DS . 'bgeditor' . DS . 'other' . DS . $paramsPath;
		$filename = preg_replace("/^\d+__/", "", BurgerEditorUtil::mb_basename($filePath));
		$basename = BurgerEditorUtil::getFileNameNoExtension($filename);
		if ($filename != BurgerEditorUtil::mb_basename($filePath)) {
			$filename = BurgerEditorUtil::b64d($basename) . "." . BurgerEditorUtil::getExtension($filePath);
		}

		$mimeType = $this->response->getMimeType(BurgerEditorUtil::getExtension($filePath));
		if ($mimeType === false) {
			$mimeType = 'application/octet-stream';
		} elseif (is_array($mimeType)) {
			$mimeType = $mimeType[0];
		}

		if (!file_exists($filePath) || is_dir($filePath)) {
			$this->notfound();
		}

		// RFC6266 のヘッダエンコードに従いUTF-8で出力 (http://tools.ietf.org/html/rfc6266)
		header("Content-Disposition: inline; filename*=UTF-8''" . rawurlencode($filename));
		header("Content-Type: " . $mimeType);
		header('Content-Length: ' . filesize($filePath));
		readfile($filePath);
		exit;
	}
}

