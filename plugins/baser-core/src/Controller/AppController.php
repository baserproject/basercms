<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller;

use Cake\Http\Session;
use Cake\Utility\Inflector;
use App\Controller\AppController as BaseController;
use Exception;

/**
 * Class AppController
 * @package BaserCore\Controller
 * @property Session $Session
 */
class AppController extends BaseController
{
    public function initialize(): void
    {
        parent::initialize();
        try {
            $this->loadComponent('BaserCore.BcMessage');
        } catch (Exception $e) {
        }
        $this->Session = $this->request->getSession();
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
		$options = array_merge([
		    'type' => 'post',
		    'session' => true
		], $options);

		if ($options['type'] == 'post' && $options['session'] == true) {
			$this->_saveViewConditions($filterModels, $options);
		} elseif ($options['type'] == 'get') {
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
		$options = array_merge([
		    'action' => '',
		    'group' => ''
		], $options);

		if (!is_array($filterModels)) {
			$filterModels = [$filterModels];
		}

		if (!$options['action']) {
			$options['action'] = $this->request->getParam('action');
		}

		$contentsName = $this->name . Inflector::classify($options['action']);
		if ($options['group']) {
			$contentsName .= "." . $options['group'];
		}

		foreach($filterModels as $model) {
			if ($this->request->getData($model)) {
				$this->Session->write("Baser.viewConditions.{$contentsName}.filter.{$model}", $this->request->getData($model));
			}
		}

		if (!empty($this->request->getParam('named'))) {
			if ($this->Session->check("Baser.viewConditions.{$contentsName}.named")) {
				$named = array_merge($this->Session->read("Baser.viewConditions.{$contentsName}.named"), $this->request->getParam('named'));
			} else {
				$named = $this->request->getParam('named');
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
	    // TODO : 未実装
	}

}
