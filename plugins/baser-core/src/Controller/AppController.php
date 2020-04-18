<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Users Community
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
namespace BaserCore\Controller;
use Cake\Event\EventInterface;
use Cake\Utility\Inflector;
use App\Controller\AppController as BaseController;

/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Users Community
 * @link          http://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

class AppController extends BaseController
{

	public function beforeRender(EventInterface $event)
	{
		$this->viewBuilder()->setTheme('BcAdminThird');
	}

	/**
	 * 画面の情報をセットする
	 *
	 * @param array $filterModels
	 * @param array $options オプション
	 * @return    void
	 * @access    public
	 */
	protected function setViewConditions($filterModels = [], $options = [])
	{
		$_options = ['type' => 'post', 'session' => true];
		$options = array_merge($_options, $options);
		extract($options);
		if ($type == 'post' && $session == true) {
			$this->_saveViewConditions($filterModels, $options);
		} elseif ($type == 'get') {
			$options['session'] = false;
		}
		$this->_loadViewConditions($filterModels, $options);
	}

	/**
	 * 画面の情報をセッションに保存する
	 *
	 * @param array $filterModels
	 * @param array $options オプション
	 * @return    void
	 * @access    protected
	 */
	protected function _saveViewConditions($filterModels = [], $options = [])
	{
		$_options = ['action' => '', 'group' => ''];
		$options = array_merge($_options, $options);
		extract($options);

		if (!is_array($filterModels)) {
			$filterModels = [$filterModels];
		}

		if (!$action) {
			$action = $this->request->getParam('action');
		}

		$contentsName = $this->name . Inflector::classify($action);
		if ($group) {
			$contentsName .= "." . $group;
		}

		foreach($filterModels as $model) {
			if ($this->request->getData($model)) {
				$this->Session->write("Baser.viewConditions.{$contentsName}.filter.{$model}", $this->request->getData($model));
			}
		}

		if (!empty($this->request->getParam('named'))) {
			if ($this->Session->check("Baser.viewConditions.{$contentsName}.named")) {
				$named = array_merge($this->Session->read("Baser.viewConditions.{$contentsName}.named"), $this->request->getParams('named'));
			} else {
				$named = $this->request->getParams['named'];
			}
			$this->Session->write("Baser.viewConditions.{$contentsName}.named", $named);
		}
	}

	/**
	 * 画面の情報をセッションから読み込む
	 *
	 * @param array $filterModels
	 * @param array|string $options オプション
	 * @return void
	 * @access    protected
	 */
	protected function _loadViewConditions($filterModels = [], $options = [])
	{
		$_options = ['default' => [], 'action' => '', 'group' => '', 'type' => 'post', 'session' => true];
		$options = array_merge($_options, $options);
		$named = [];
		$filter = [];
		extract($options);

		if (!is_array($filterModels)) {
			$model = (string)$filterModels;
			$filterModels = [$filterModels];
		} else {
			$model = (string)$filterModels[0];
		}

	}

}
