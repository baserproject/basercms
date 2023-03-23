<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Model.Behavior
 * @since           baserCMS v 1.5.3
 * @license         https://basercms.net/license/index.html
 */

App::uses('Imageresizer', 'Vendor');
App::uses('BcFileUploader', 'Lib');

/**
 * Class BcUploadBehavior
 *
 * ファイルアップロードビヘイビア
 *
 * 《設定例》
 * public $actsAs = array(
 *  'BcUpload' => array(
 *     'saveDir'  => "editor",
 *     'fields'  => array(
 *       'image'  => array(
 *         'type'      => 'image',
 *         'namefield'    => 'id',
 *         'nameadd'    => false,
 *            'subdirDateFormat'    => 'Y/m'    // Or false
 *         'imageresize'  => array('prefix' => 'template', 'width' => '100', 'height' => '100'),
 *                'imagecopy'        => array(
 *                    'thumb'            => array('suffix' => 'template', 'width' => '150', 'height' => '150'),
 *                    'thumb_mobile'    => array('suffix' => 'template', 'width' => '100', 'height' => '100')
 *                )
 *       ),
 *       'pdf' => array(
 *         'type'      => 'pdf',
 *         'namefield'    => 'id',
 *         'nameformat'  => '%d',
 *         'nameadd'    => false
 *       )
 *     )
 *   )
 * );
 *
 * @package Baser.Model.Behavior
 * @property BcFileUploader[] $BcFileUploader
 */
class BcUploadBehavior extends ModelBehavior
{

    /**
     * BcFileUploader
     * @var BcFileUploader[]
     */
    public $BcFileUploader = [];

	/**
	 * oldEntity
	 * @var array
	 */
	public $oldEntity = [];

	/**
	 * バリデーション中のロック
	 * @var bool
	 */
	public $validatingLock = [];

	/**
	 * セットアップ
	 *
	 * @param Model $Model
	 * @param array $settings actsAsの設定
	 */
	public function setup(Model $Model, $settings = [])
	{
		$this->BcFileUploader[$Model->alias] = new BcFileUploader();
        $this->BcFileUploader[$Model->alias]->initialize($settings, $Model);
		$this->validatingLock[$Model->alias] = false;
        // @deprecated 後方互換 since v4.5.6, v5.0.0 で削除予定
        // CuApprover-4.3.2以下に対応するための処理
        // >>>
        $this->settings[$Model->alias] = $this->BcFileUploader[$Model->alias]->settings;
        // <<<

	}

	/**
	 * @param $modelName
	 * @return BcFileUploader|false
	 */
	public function getFileUploader(Model $model)
	{
		return (isset($this->BcFileUploader[$model->alias]))? $this->BcFileUploader[$model->alias] : false;
	}

	/**
	 * After Validate
	 *
	 * @param Model $Model
	 * @param array $options
	 * @return void
	 */
	public function afterValidate(Model $Model, $options = [])
	{
		if ($Model->validationErrors) {
			return;
		}
		if ($this->validatingLock[$Model->alias]) {
			return;
		}
		$Model->data[$Model->alias] = $this->BcFileUploader[$Model->alias]->setupRequestData(isset($Model->data[$Model->alias])? $Model->data[$Model->alias] : $Model->data);
		$Model->data[$Model->alias] = $this->BcFileUploader[$Model->alias]->setupTmpData(isset($Model->data[$Model->alias])? $Model->data[$Model->alias] : $Model->data);
		$this->oldEntity[$Model->alias] = (!empty($Model->data[$Model->alias]['id']))? $this->getOldEntity($Model, $Model->data[$Model->alias]['id']) : [];
		$this->validatingLock[$Model->alias] = true;

		// @deprecated 後方互換 since v4.5.6, v5.0.0 で削除予定
		// CuApprover-4.3.2以下に対応するための処理
		// バリデーションを配列で実行させるため配列に変換
		// >>>
		$files = $this->BcFileUploader[$Model->alias]->getUploadingFiles();
		foreach($files as $key => $file) {
			$this->settings[$Model->alias]['fields'][$key]['upload'] = (!empty($file['uploadable'])) ? true : false;
			if(!empty($file['uploadable'])) {
				$Model->data[$Model->alias][$key] = $file;
				// 公開承認のおける草稿の新規投稿対応
				$setting = $this->settings[$Model->alias]['fields'][$key];
				$Model->data[$Model->alias][$key] = $fileName = $this->BcFileUploader[$Model->alias]->saveFile($setting, $file);
				if (($setting['type'] == 'all' || $setting['type'] == 'image') && !empty($setting['imagecopy']) && in_array($file['ext'], $this->BcFileUploader[$Model->alias]->imgExts)) {
					$this->BcFileUploader[$Model->alias]->copyImages($setting, $file);
					if (!empty($setting['imageresize'])) {
						$filePath = $this->BcFileUploader[$Model->alias]->savePath . $fileName;
						$this->BcFileUploader[$Model->alias]->resizeImage($filePath, $filePath, $setting['imageresize']['width'], $setting['imageresize']['height'], $setting['imageresize']['thumb']);
					}
				}
			}
		}
		// <<<
	}

	/**
	 * before save
	 *
	 * ファイルをアップロードしている場合、データが配列になっているため、文字列データに変換する
	 * ただし、公開承認の下書きモードの場合、BlogPost側のデータが新しいデータで書き換わってしまっては問題となるため
	 * 保存させないようにするため処理をスルーさせる
	 * @param Model $Model
	 * @param array $options
	 * @return boolean
	 * @deprecated 後方互換 since v4.5.6, v5.0.0 で削除予定
	 * CuApprover-4.3.2以下に対応するための処理
	 */
	public function beforeSave(Model $Model, $options = [])
	{
		if (isset($Model->data['CuApproverApplication']['contentsMode'])
			&& isset($Model->data['CuApproverApplication']['is_published'])) {
			if($Model->alias === 'BlogPost' &&
				$Model->data['CuApproverApplication']['contentsMode'] === 'draft' &&
				$Model->data['CuApproverApplication']['is_published']) {
				return true;
			}
		}
		$files = $this->BcFileUploader[$Model->alias]->getUploadingFiles();
		foreach($files as $key => $file) {
			if(!empty($file['uploadable'])) {
				if(empty($Model->data['CuApproverApplication'])) {
					// Content の公開承認の下書き保存時、BcContentsBehavior::afterSave() で別途保存処理が発生するため、
					// その際、eyecatch の配列でくるが保存させないように unset する
					unset($Model->data[$Model->alias][$key]);
				} else {
					$Model->data[$Model->alias][$key] = $file['name'];
				}
			}
		}
		return true;
	}

	/**
	 * After save
	 *
	 * @param Model $Model
	 * @param array $options
	 * @return boolean
	 */
	public function afterSave(Model $Model, $created, $options = [])
	{
		// @deprecated 後方互換 since v4.5.6, v5.0.0 で削除予定
		// CuApprover-4.3.2以下に対応するための処理
		// >>>
		if(!empty($this->settings[$Model->alias]['fields'])) {
			$files = $this->BcFileUploader[$Model->alias]->getUploadingFiles();
			foreach($this->settings[$Model->alias]['fields'] as $key => $field) {
				$files[$key]['uploadable'] = (!empty($field['upload']))? true : false;
			}
			$this->BcFileUploader[$Model->alias]->setUploadingFiles($files);
		}
		// <<<

		$data = isset($Model->data[$Model->alias])? $Model->data[$Model->alias] : $Model->data;
        if ($Model->exists() && isset($this->oldEntity[$Model->alias])) {
            $this->BcFileUploader[$Model->alias]->deleteExistingFiles($this->oldEntity[$Model->alias]);
        }
        $entity = $this->BcFileUploader[$Model->alias]->saveFiles($data);

		// @deprecated 後方互換 since v4.5.6, v5.0.0 で削除予定
		// CuApprover-4.3.2以下に対応するための処理
		// >>>
		$this->uploaded[$Model->alias] = $this->BcFileUploader[$Model->alias]->uploaded;
		// <<<

        if ($Model->exists() && isset($this->oldEntity[$Model->alias])) {
            $entity = $this->BcFileUploader[$Model->alias]->deleteFiles($this->oldEntity[$Model->alias], $entity);
        }
        if ($this->BcFileUploader[$Model->alias]->isUploaded()) {
            $entity = $this->BcFileUploader[$Model->alias]->renameToBasenameFields($entity);
            $this->BcFileUploader[$Model->alias]->resetUploaded();

            // @deprecated 後方互換 since v4.5.6, v5.0.0 で削除予定
            // CuApprover-4.3.2以下に対応するための処理
            // >>>
            $this->uploaded[$Model->alias] = false;
            // <<<

        }
        $Model->data = $Model->save($entity, ['validate' => false, 'callbacks' => false]);

		// @deprecated 後方互換 since v4.5.6, v5.0.0 で削除
		// CuApprover-4.3.2以下に対応するための処理
		// 公開承認のおける草稿の新規投稿対応
		// afterValidate で無理やり生成したファイルを削除する
		// >>>
		if(!empty($files)) {
			foreach($files as $key => $file) {
				if(!empty($file['name'])) {
					$setting = $this->settings[$Model->alias]['fields'][$key];
					$this->BcFileUploader[$Model->alias]->deleteFile($setting, $file['name']);
				}
			}
		}
		// <<<
        return true;
	}

	/**
	 * Before delete
	 * 画像ファイルの削除を行う
	 * 削除に失敗してもデータの削除は行う
	 *
	 * @param Model $Model
	 * @param bool $cascade
	 * @return bool
	 */
	public function beforeDelete(Model $Model, $cascade = true)
	{
		$oldEntity = $this->getOldEntity($Model, $Model->id);
		$this->BcFileUploader[$Model->alias]->deleteFiles($oldEntity, [], true);
		return true;
	}

	/**
	 * 一時ファイルとして保存する
	 *
	 * @param Model $Model
	 * @param array $data
	 * @param string $tmpId
	 * @return mixed false|array
	 */
	public function saveTmpFiles(Model $Model, $data, $tmpId)
	{
		if(isset($data[$Model->alias])) {
			$entity = $data[$Model->alias];
		} else {
			$entity = $data;
			$data = [];
		}
		$data[$Model->alias] = $this->BcFileUploader[$Model->alias]->saveTmpFiles($entity, $tmpId);
		return $data;
	}

    /**
     * 設定情報を取得
     * @param $alias
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSettings(Model $Model)
    {
        return $this->BcFileUploader[$Model->alias]->settings;
    }

    /**
     * 設定情報を設定
     * @param $alias
     * @param $settings
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setSettings(Model $Model, $settings)
    {
        $this->BcFileUploader[$Model->alias]->settings = $settings;
    }

    /**
     * 保存先のパスを取得
     * @param $alias
     * @param false $isTheme
     * @param false $limited
     * @return string
     */
    public function getSaveDir(Model $Model, $isTheme = false, $limited = false)
    {
        return $this->BcFileUploader[$Model->alias]->getSaveDir($isTheme, $limited);
    }

	/**
	 * saveFiles
	 * @param Model $Model
	 * @param $entity
	 * @return mixed
	 * @deprecated 後方互換 since v4.5.6, v5.0.0 で削除予定
	 */
    public function saveFiles(Model $Model, $entity)
	{
		$entity[$Model->alias] = $this->BcFileUploader[$Model->alias]->saveFiles($entity[$Model->alias]);
		return $entity;
	}

	/**
	 * saveFiles
	 * @param Model $Model
	 * @param $entity
	 * @return mixed
	 * @deprecated 後方互換 since v4.5.6, v5.0.0 で削除予定
	 */
    public function deleteFiles(Model $Model, $newEntity)
    {
		$entity[$Model->alias] = $this->BcFileUploader[$Model->alias]->deleteFiles([], $newEntity);
		return $entity;
    }

	/**
	 * setupRequestData
	 * @param Model $Model
	 * @deprecated 後方互換 since v4.5.6, v5.0.0 で削除予定
	 * CuApprover-4.3.2以下に対応するための処理
	 */
	public function setupRequestData(Model $Model)
	{
		// CuApproverApplication.php 261行目あたり、$this->setupRequestData()　で、
		// BlogPost、Content モデルの削除の際の削除フラグが初期化されてしまうため return で実行しない
		// 初期化されなかったデータは deleteFileWhileChecking にて利用する
		// もともと上記の処理は、Content や Content のビヘイビア設定を
		// CuApproverApplication モデルに引き継がせるための処理だったが不要だったことがわかった。
		if($Model->alias === 'BlogPost' || $Model->alias === 'Content') return;
		$Model->data[$Model->alias] = $this->BcFileUploader[$Model->alias]->setupRequestData(isset($Model->data[$Model->alias])? $Model->data[$Model->alias] : $Model->data);
		// CuApprover プラグインにて、CuApproverApplicationBehavior.php 65行目あたり $model->CuApproverApplication->validates() で、
		// BcFileUploader->uploadingFiles が初期化されてしまうため退避
        $this->files[$Model->alias] = $this->BcFileUploader[$Model->alias]->getUploadingFiles();
	}

	/**
	 * delFile
	 * @param Model $Model
	 * @deprecated 後方互換 since v4.5.6, v5.0.0 で削除予定
	 * CuApprover-4.3.2以下に対応するための処理
	 */
	public function delFile(Model $Model, $fileName, $setting)
	{
		$this->BcFileUploader[$Model->alias]->deleteFile($setting, $fileName);
	}

	/**
	 * deleteFileWhileChecking
	 * @param Model $Model
	 * @deprecated 後方互換 since v4.5.6, v5.0.0 で削除予定
	 * CuApprover-4.3.2以下に対応するための処理
	 */
	public function deleteFileWhileChecking(Model $Model, $setting, $requestData, $oldValue)
	{
		if($Model->alias === 'CuApproverApplication') {
			// 公開承認の下書きのファイル削除の場合、削除フラグがリセットされたデータを利用しているため、
			// 元データの files を利用する
			if(!empty($requestData['CuApproverApplication']['blog_content_id'])) {
				$files = $this->BcFileUploader['BlogPost']->getUploadingFiles();
			} else {
				$files = $this->BcFileUploader['Content']->getUploadingFiles();
			}
		} else {
			$files = $this->BcFileUploader[$Model->alias]->getUploadingFiles();
		}
		if(!$files && !empty($this->files[$Model->alias])) {
			$files = $this->files[$Model->alias];
		}
		if(isset($files[$setting['name']])) {
			$file = $files[$setting['name']];
		} else {
			$file = [];
		}
		return [
			$Model->alias => $this->BcFileUploader[$Model->alias]->deleteFileWhileChecking($setting, $file, $requestData[$Model->alias], [$setting['name'] => $oldValue])
		];
	}

	/**
	 * renameToBasenameField
	 * @param Model $Model
	 * @deprecated 後方互換 since v4.5.6, v5.0.0 で削除予定
	 * CuApprover-4.3.2以下に対応するための処理
	 */
	public function renameToBasenameField(Model $Model, $setting)
	{
		$files = $this->BcFileUploader[$Model->alias]->getUploadingFiles();
		if(!$files && !empty($this->files[$Model->alias])) {
			$files = $this->files[$Model->alias];
		}
		if(isset($files[$setting['name']])) {
			$file = $files[$setting['name']];
			if(!empty($file['name'])) {
				return $this->BcFileUploader[$Model->alias]->renameToBasenameField($setting, $file, $Model->data[$Model->alias]);
			}
		}
		return $Model->data[$Model->alias][$setting['name']];
	}

	/**
	 * saveFileWhileChecking
	 * @param Model $Model
	 * @deprecated 後方互換 since v4.5.6, v5.0.0 で削除予定
	 * CuApprover-4.3.2以下に対応するための処理
	 */
	public function saveFileWhileChecking(Model $Model, $setting, $data, $options)
	{
		$file = $this->BcFileUploader[$Model->alias]->saveFileWhileChecking($setting, $this->BcFileUploader[$Model->alias]->getUploadingFiles()[$setting['name']], $data[$Model->alias], $options);
		if($file) {
			$files = $this->BcFileUploader[$Model->alias]->getUploadingFiles();
			$files[$setting['name']] = $file;
			$this->BcFileUploader[$Model->alias]->setUploadingFiles($files);
			$data[$Model->alias][$setting['name']] = $file['name'];
		}
		$this->uploaded[$Model->alias] = $this->BcFileUploader[$Model->alias]->uploaded;
		return $data;
	}

	/**
	 * getFieldBasename
	 * @param Model $Model
	 * @deprecated 後方互換 since v4.5.6, v5.0.0 で削除予定
	 * CuApprover-4.3.2以下に対応するための処理
	 */
	public function getFieldBasename(Model $Model, $setting, $ext)
	{
		$files = $this->BcFileUploader[$Model->alias]->getUploadingFiles();
		if(!$files && !empty($this->files[$Model->alias])) {
			$files = $this->files[$Model->alias];
		}
		if(isset($files[$setting['name']])) {
			$file = $files[$setting['name']];
			if(empty($file['ext'])) {
				$file = ['ext' => $ext];
			}
			return $this->BcFileUploader[$Model->alias]->getFieldBasename($setting, $file, $Model->data[$Model->alias]);
		}
		return false;
	}

	/**
	 * getFileName
	 * @param Model $Model
	 * @deprecated 後方互換 since v4.5.6, v5.0.0 で削除予定
	 * CuApprover-4.3.2以下に対応するための処理
	 */
	public function getFileName(Model $Model , $setting, $name)
	{
		return $this->BcFileUploader[$Model->alias]->getFileName($setting, $name);
	}

	/**
	 * getOldEntity
	 * @param Model $model
	 * @param int $id
	 * @return array|int|null
	 */
	public function getOldEntity(Model $model, $id)
	{
		if($model instanceof Content) {
			$softDelete = $model->softDelete(null);
			$model->softDelete(false);
		}
		$oldEntity = $model->find('first', [
			'conditions' => [$model->alias . '.id' => $id],
			'recursive' => -1
		]);
		if($model instanceof Content) {
			$model->softDelete($softDelete);
		}
		return ($oldEntity)? $oldEntity[$model->alias] : [];
	}

	/**
	 * renameToBasenameFields
	 * @param Model $Model
	 */
	public function renameToBasenameFields(Model $model, $copy = false)
	{
		$model->data[$model->alias] = $this->BcFileUploader[$model->alias]->renameToBasenameFields($model->data[$model->alias], $copy);
		return $model->data;
	}

}
