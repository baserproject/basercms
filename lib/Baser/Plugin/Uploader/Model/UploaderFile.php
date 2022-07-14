<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader.Model
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */

/**
 * ファイルアップローダーモデル
 *
 * @package        Uploader.Model
 */
class UploaderFile extends AppModel
{

	/**
	 * プラグイン名
	 *
	 * @var string
	 */
	public $plugin = 'Uploader';

	/**
	 * behaviors
	 *
	 * @var array
	 */
	public $actsAs = [
		'BcUpload' => [
			'saveDir' => 'uploads',
			'existsCheckDirs' => ['uploads/limited'],
			'fields' => [
				'name' => ['type' => 'all']
			]]];

	/**
	 * 公開期間をチェックする
	 *
	 * @return bool
	 */
	public function checkPeriod()
	{
		if (!empty($this->data['UploaderFile']['publish_begin']) && !empty($this->data['UploaderFile']['publish_end'])) {
			if (strtotime($this->data['UploaderFile']['publish_begin']) > strtotime($this->data['UploaderFile']['publish_end'])) {
				return false;
			}
		}
		return true;
	}

	/**
	 * コンストラクタ
	 *
	 * @param int $id
	 * @param string $table
	 * @param string $ds
	 */
	public function __construct($id = false, $table = null, $ds = null)
	{
		$this->validate = [
			'publish_begin' => [
				'checkPeriod' => [
					'rule' => 'checkPeriod',
					'message' => __d('baser', '公開期間が不正です。')
				]
			],
			'publish_end' => [
				'checkPeriod' => [
					'rule' => 'checkPeriod',
					'message' => __d('baser', '公開期間が不正です。')
				]
			]
		];
		if (!BcUtil::isAdminUser() || !Configure::read('Uploader.allowedAdmin')) {
			$this->validate['name'] = [
				'fileExt' => [
					'rule' => ['fileExt', Configure::read('Uploader.allowedExt')],
					'message' => __d('baser', '許可されていないファイル形式です。')
				]
			];
		}
		parent::__construct($id, $table, $ds);
		$sizes = ['large', 'midium', 'small', 'mobile_large', 'mobile_small'];
		$UploaderConfig = ClassRegistry::init('Uploader.UploaderConfig');
		$uploaderConfigs = $UploaderConfig->findExpanded();
		$imagecopy = [];

		foreach($sizes as $size) {
			if (!isset($uploaderConfigs[$size . '_width']) || !isset($uploaderConfigs[$size . '_height'])) {
				continue;
			}
			$imagecopy[$size] = ['suffix' => '__' . $size];
			$imagecopy[$size]['width'] = $uploaderConfigs[$size . '_width'];
			$imagecopy[$size]['height'] = $uploaderConfigs[$size . '_height'];
			if (isset($uploaderConfigs[$size . '_thumb'])) {
				$imagecopy[$size]['thumb'] = $uploaderConfigs[$size . '_thumb'];
			}
		}

		$settings = $this->actsAs['BcUpload'];
		$settings['fields']['name']['imagecopy'] = $imagecopy;
		$this->Behaviors->attach('BcUpload', $settings);

		// BcUploadBehavior より優先順位をあげる為登録、イベントを登録しなおす
		$this->getEventManager()->detach([$this, 'beforeDelete'], 'Model.beforeDelete');
		$this->getEventManager()->attach([$this, 'beforeDelete'], 'Model.beforeDelete', ['priority' => 5]);

	}

	/**
	 * Before Save
	 *
	 * @param array $options
	 * @return bool
	 */
	public function beforeSave($options = [])
	{
		parent::beforeSave($options);

		if (!empty($this->data['UploaderFile']['id'])) {

			$savePath = WWW_ROOT . 'files' . DS . $this->actsAs['BcUpload']['saveDir'] . DS;
			$sizes = ['large', 'midium', 'small', 'mobile_large', 'mobile_small'];
			$pathinfo = pathinfo($this->data['UploaderFile']['name']);

			if (!empty($this->data['UploaderFile']['publish_begin']) || !empty($this->data['UploaderFile']['publish_end'])) {
				if (file_exists($savePath . $this->data['UploaderFile']['name'])) {
					rename($savePath . $this->data['UploaderFile']['name'], $savePath . 'limited' . DS . $this->data['UploaderFile']['name']);
				}
				foreach($sizes as $size) {
					$file = $pathinfo['filename'] . '__' . $size . '.' . $pathinfo['extension'];
					if (file_exists($savePath . $file)) {
						rename($savePath . $file, $savePath . 'limited' . DS . $file);
					}
				}
			} else {
				if (file_exists($savePath . 'limited' . DS . $this->data['UploaderFile']['name'])) {
					rename($savePath . 'limited' . DS . $this->data['UploaderFile']['name'], $savePath . $this->data['UploaderFile']['name']);
				}
				foreach($sizes as $size) {
					$file = $pathinfo['filename'] . '__' . $size . '.' . $pathinfo['extension'];
					if (file_exists($savePath . 'limited' . DS . $file)) {
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
	 * @param string $fileName
	 * @return    bool
	 */
	public function fileExists($fileName, $limited = false)
	{

		if ($limited) {
			$savePath = WWW_ROOT . 'files' . DS . $this->actsAs['BcUpload']['saveDir'] . DS . 'limited' . DS . $fileName;
		} else {
			$savePath = WWW_ROOT . 'files' . DS . $this->actsAs['BcUpload']['saveDir'] . DS . $fileName;
		}
		return file_exists($savePath);

	}

	/**
	 * 複数のファイルの存在チェックを行う
	 *
	 * @param string $fileName
	 * @return    array
	 */
	public function filesExists($fileName, $limited = null)
	{
		if (is_null($limited)) {
			$data = $this->find('first', ['conditions' => ['UploaderFile.name' => $fileName], 'recursive' => -1]);
			$limited = false;
			if (!empty($data['UploaderFile']['publish_begin']) || !empty($data['UploaderFile']['publish_end'])) {
				$limited = true;
			}
		}
		$pathinfo = pathinfo($fileName);
		$ext = $pathinfo['extension'];
		$basename = mb_basename($fileName, '.' . $ext);
		$files['small'] = $this->fileExists($basename . '__small' . '.' . $ext, $limited);
		$files['midium'] = $this->fileExists($basename . '__midium' . '.' . $ext, $limited);
		$files['large'] = $this->fileExists($basename . '__large' . '.' . $ext, $limited);
		return $files;
	}

	/**
	 * コントロールソースを取得する
	 *
	 * @param string $field フィールド名
	 * @param array $options
	 * @return    mixed    $controlSource    コントロールソース
	 */
	public function getControlSource($field = null, $options = [])
	{
		switch($field) {
			case 'user_id':
				$User = ClassRegistry::getObject('User');
				return $User->getUserList($options);
			case 'uploader_category_id':
				$UploaderCategory = ClassRegistry::init('Uploader.UploaderCategory');
				return $UploaderCategory->find('list', ['order' => 'UploaderCategory.id']);
		}
		return false;
	}

	/**
	 * ソースファイルの名称を取得する
	 * @param $fileName
	 * @return mixed
	 */
	public function getSourceFileName($fileName)
	{
		$sizes = ['large', 'midium', 'small', 'mobile_large', 'mobile_small'];
		return preg_replace('/__(' . implode('|', $sizes) . ')\./', '.', $fileName);
	}

	/**
	 * Before Delete
	 *
	 * @param bool $cascade
	 * @return bool
	 */
	public function beforeDelete($cascade = true)
	{
		$data = $this->read(null, $this->id);
		if (!empty($data['UploaderFile']['publish_begin']) || !empty($data['UploaderFile']['publish_end'])) {
			$this->Behaviors->BcUpload->BcFileUploader['UploaderFile']->savePath .= 'limited' . DS;
		} else {
			$this->Behaviors->BcUpload->BcFileUploader['UploaderFile']->savePath = preg_replace('/' . preg_quote('limited' . DS, '/') . '$/', '', $this->Behaviors->BcUpload->BcFileUploader['UploaderFile']->savePath);
		}
		return parent::beforeDelete($cascade);
	}

}

