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

namespace BaserCore\View\Helper;
use Cake\ORM\Locator\TableLocator;
use Cake\ORM\TableRegistry;
use \Cake\View\Helper\FormHelper;

/**
 * FormHelper 拡張クラス
 *
 * @package Baser.View.Helper
 */
class BcFormHelper extends FormHelper
{
    /**
     * Other helpers used by FormHelper
     *
     * @var array
     */
    public $helpers = ['Url', 'Html', 'BcTime', 'BcText', 'Js', 'BcUpload', 'BcCkeditor'];

    public function dispatchAfterForm($type = '') {

    }

    /**
     * widget
     * @param string $name
     * @param array $data
     * @return string
     */
    public function widget(string $name, array $data = []): string
    {
        return parent::widget($name, $data);
    }

/**
 * コントロールソースを取得する
 * Model側でメソッドを用意しておく必要がある
 *
 * @param string $field フィールド名
 * @param array $options
 * @return array コントロールソース
 */
	public function getControlSource($field, $options = []) {
		$count = preg_match_all('/\./is', $field, $matches);
		if ($count === 1) {
			[$modelName, $field] = explode('.', $field);
			$plugin = $this->_View->getPlugin();
            if($plugin) {
                $modelName = $plugin . '.' . $modelName;
            }
		} elseif ($count === 2) {
			[$plugin, $modelName, $field] = explode('.', $field);
			$modelName = $plugin . '.' . $modelName;
		}
		if (empty($modelName)) {
			return [];
		}
		$model = TableRegistry::getTableLocator()->get($modelName);
		if ($model && method_exists($model, 'getControlSource')) {
			return $model->getControlSource($field, $options);
		} else {
			return [];
		}
	}

}
