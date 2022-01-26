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
	 * セットアップ
	 *
	 * @param Model $Model
	 * @param array $settings actsAsの設定
	 */
	public function setup(Model $Model, $settings = [])
	{
		$this->BcFileUploader[$Model->alias] = new BcFileUploader();
        $this->BcFileUploader[$Model->alias]->initialize($settings, $Model);
	}

	/**
	 * Before Validate
	 *
	 * @param Model $Model
	 * @param array $options
	 * @return mixed
	 */
	public function beforeValidate(Model $Model, $options = [])
	{
        $Model->data[$Model->alias] = $this->BcFileUploader[$Model->alias]->setupRequestData(isset($Model->data[$Model->alias])? $Model->data[$Model->alias] : $Model->data);
        $Model->data[$Model->alias] = $this->BcFileUploader[$Model->alias]->setupTmpData(isset($Model->data[$Model->alias])? $Model->data[$Model->alias] : $Model->data);
        return true;
	}

	/**
	 * Before save
	 *
	 * @param Model $Model
	 * @param array $options
	 * @return boolean
	 */
	public function beforeSave(Model $Model, $options = [])
	{
        if ($Model->exists()) {
            $this->BcFileUploader[$Model->alias]->deleteExistingFiles(isset($Model->data[$Model->alias])? $Model->data[$Model->alias] : $Model->data);
        }
        $Model->data[$Model->alias] = $this->BcFileUploader[$Model->alias]->saveFiles(isset($Model->data[$Model->alias])? $Model->data[$Model->alias] : $Model->data);
        if ($Model->exists()) {
            $Model->data[$Model->alias] = $this->BcFileUploader[$Model->alias]->deleteFiles(isset($Model->data[$Model->alias])? $Model->data[$Model->alias] : $Model->data);
        }
        return true;
	}

	/**
	 * After save
	 *
	 * @param Model $Model
	 * @param bool $created
	 * @param array $options
	 */
	public function afterSave(Model $Model, $created, $options = [])
	{
        if ($this->BcFileUploader[$Model->alias]->isUploaded()) {
            $Model->data[$Model->alias] = $this->BcFileUploader[$Model->alias]->renameToBasenameFields(isset($Model->data[$Model->alias])? $Model->data[$Model->alias] : $Model->data);
            $this->BcFileUploader[$Model->alias]->resetUploaded();
        }
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
		$this->BcFileUploader[$Model->alias]->deleteFiles(isset($Model->data[$Model->alias])? $Model->data[$Model->alias] : $Model->data, true);
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

}
