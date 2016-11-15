<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Uploader.Model
 * @since			baserCMS v 3.0.10
 * @license			http://basercms.net/license/index.html
 */

/**
 * ファイルカテゴリモデル
 *
 * @package			Uploader.Model
 */
class UploaderCategory extends AppModel {

/**
 * プラグイン名
 *
 * @var		string
 * @access	public
 */
	public $plugin = 'Uploader';
/**
 * バリデート
 *
 * @var		array
 * @access	public
 */
	public $validate = array(
		'name' => array(
			array(
				'rule'		=> array('notBlank'),
				'message'	=> 'カテゴリ名を入力してください。')
			)
		);
/**
 * コピーする
 * 
 * @param int $id
 * @param array $data
 * @return mixed page Or false
 */
	public function copy($id = null, $data = array()) {
		
		$data = array();
		if($id) {
			$data = $this->find('first', array('conditions' => array('UploaderCategory.id' => $id)));
		}
		
		$data['UploaderCategory']['name'] .= '_copy';
		$data['UploaderCategory']['id'] = $this->getMax('id', array('UploaderCategory.id' => $data['UploaderCategory']['id'])) + 1;
		
		unset($data['UploaderCategory']['id']);
		unset($data['UploaderCategory']['created']);
		unset($data['UploaderCategory']['modified']);
		
		$this->create($data);
		$result = $this->save();
		if($result) {
			return $result;
		} else {
			if(isset($this->validationErrors['name'])) {
				return $this->copy(null, $data);
			} else {
				return false;
			}
		}
		
	}

}
