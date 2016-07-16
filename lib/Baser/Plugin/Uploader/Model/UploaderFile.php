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
 * Include files
 */
/**
 * ファイルアップローダーモデル
 *
 * @package		uploader.models
 */
class UploaderFile extends BcPluginAppModel {
/**
 * モデル名
 * 
 * @var     string
 * @access  public
 */
	public $name = 'UploaderFile';
/**
 * データソース
 *
 * @var		string
 * @access 	public
 */
	public $useDbConfig = 'plugin';
/**
 * プラグイン名
 *
 * @var		string
 * @access 	public
 */
	public $plugin = 'Uploader';
/**
 * behaviors
 *
 * @var 	array
 * @access 	public
 */
	public $actsAs = array('BcUpload' => array(
		'saveDir'	=> "uploads",
		'fields'	=> array(
				'name'	=> array('type'	=> 'all')
	)));
/**
 * コンストラクタ
 *
 * @param	int		$id
 * @param	string	$table
 * @param	string	$ds
 */
	public function __construct($id = false, $table = null, $ds = null) {
		
		parent::__construct($id, $table, $ds);
		$sizes = array('large', 'midium', 'small', 'mobile_large', 'mobile_small');
		$UploaderConfig = ClassRegistry::init('Uploader.UploaderConfig');
		$uploaderConfigs = $UploaderConfig->findExpanded();
		$imagecopy = array();
		
		foreach($sizes as $size) {
			if(!isset($uploaderConfigs[$size.'_width']) || !isset($uploaderConfigs[$size.'_height'])) {
				continue;
			}
			$imagecopy[$size] = array('suffix'	=> '__'.$size);
			$imagecopy[$size]['width'] = $uploaderConfigs[$size.'_width'];
			$imagecopy[$size]['height'] = $uploaderConfigs[$size.'_height'];
			if(isset($uploaderConfigs[$size.'_thumb'])) {
				$imagecopy[$size]['thumb'] = $uploaderConfigs[$size.'_thumb'];
			}
		}
		
		$settings = $this->actsAs['BcUpload'];
		$settings['fields']['name']['imagecopy'] = $imagecopy;
		$this->Behaviors->attach('BcUpload', $settings);

	}
	public function beforeSave($options = array()) {
		
		parent::beforeSave($options);
		
		if(!empty($this->data['UploaderFile']['id'])) {
			
			$savePath = WWW_ROOT . 'files' . DS . $this->actsAs['BcUpload']['saveDir'] . DS;
			$sizes = array('large', 'midium', 'small', 'mobile_large', 'mobile_small');
			$pathinfo = pathinfo($this->data['UploaderFile']['name']);
			
			if(!empty($this->data['UploaderFile']['publish_begin']) || !empty($this->data['UploaderFile']['publish_end'])) {
				if(file_exists($savePath . $this->data['UploaderFile']['name'])) {
					rename($savePath . $this->data['UploaderFile']['name'], $savePath . 'limited' . DS . $this->data['UploaderFile']['name']);
				}
				foreach($sizes as $size) {
					$file = $pathinfo['filename'] . '__' . $size . '.' . $pathinfo['extension'];
					if(file_exists($savePath . $file)) {
						rename($savePath . $file, $savePath . 'limited' . DS . $file);
					}
				}	
			} else {
				if(file_exists($savePath . 'limited' . DS . $this->data['UploaderFile']['name'])) {
					rename($savePath . 'limited' . DS . $this->data['UploaderFile']['name'], $savePath . $this->data['UploaderFile']['name']);
				}
				foreach($sizes as $size) {
					$file = $pathinfo['filename'] . '__' . $size . '.' . $pathinfo['extension'];
					if(file_exists($savePath . 'limited' . DS . $file)) {
						rename($savePath . 'limited' . DS . $file, $savePath . $file);
					}
				}	
			}
		}
		
		return true;
		
	}
/**
 * ファイルの存在チェックを行う
 *
 * @param	string	$fileName
 * @return	void
 * @access	boolean
 */
	public function fileExists($fileName, $limited = false) {
		
		if($limited) {
			$savePath = WWW_ROOT . 'files' . DS . $this->actsAs['BcUpload']['saveDir'] . DS . 'limited' . DS . $fileName;
		} else {
			$savePath = WWW_ROOT . 'files' . DS . $this->actsAs['BcUpload']['saveDir'] . DS . $fileName;
		}
		return file_exists($savePath);

	}
/**
 * 複数のファイルの存在チェックを行う
 * 
 * @param	string	$fileName
 * @return	array
 * @access	void
 */
	public function filesExists($fileName, $limited = null) {

		if(is_null($limited)) {
			$data = $this->find('first', array('conditions' => array('UploaderFile.name' => $fileName), 'recursive' => -1));
			$limited = false;
			if(!empty($data['UploaderFile']['publish_begin']) || !empty($data['UploaderFile']['publish_end'])) {
				$limited = true;
			}
		}
		$pathinfo = pathinfo($fileName);
		$ext = $pathinfo['extension'];
		$basename = mb_basename($fileName,'.'.$ext);
		$files['small'] = $this->fileExists($basename.'__small'.'.'.$ext, $limited);
		$files['midium'] = $this->fileExists($basename.'__midium'.'.'.$ext, $limited);
		$files['large'] = $this->fileExists($basename.'__large'.'.'.$ext, $limited);
		return $files;

	}
/**
 * コントロールソースを取得する
 *
 * @param	string	$field			フィールド名
 * @param	array	$options
 * @return	mixed	$controlSource	コントロールソース
 * @access	public
 */
	public function getControlSource($field = null, $options = array()) {

		switch ($field) {
			case 'user_id':
				$User = ClassRegistry::getObject('User');
				return $User->getUserList($options);
			case 'uploader_category_id':
				$UploaderCategory = ClassRegistry::init('Uploader.UploaderCategory');
				return $UploaderCategory->find('list', array('order' => 'UploaderCategory.id'));
		}
		return false;

	}
	public function getSourceFileName($fileName) {
		
		$sizes = array('large', 'midium', 'small', 'mobile_large', 'mobile_small');
		return preg_replace('/__(' . implode('|', $sizes) . ')\./', '.', $fileName);
		
	}
	public function beforeDelete($cascade = true) {
		
		$data = $this->read(null, $this->id);
		if(!empty($data['UploaderFile']['publish_begin']) || !empty($data['UploaderFile']['publish_end'])) {
			$this->Behaviors->BcUpload->savePath .= 'limited' . DS;
		} else {
			$this->Behaviors->BcUpload->savePath = preg_replace('/' . preg_quote('limited' . DS, '/') . '$/', '', $this->Behaviors->BcUpload->savePath);
		}
		parent::beforeDelete($cascade);
		
		return true;
		
	}
	
}

