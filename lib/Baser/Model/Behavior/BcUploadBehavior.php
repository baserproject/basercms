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
App::uses('BcUpload', 'Lib');

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
 * @property BcUpload $BcUpload
 */
class BcUploadBehavior extends ModelBehavior
{

	/**
	 * 保存ディレクトリ
	 *
	 * @var string[]
	 */
	public $savePath = [];

	/**
	 * 保存時にファイルの重複確認を行うディレクトリ
	 *
	 * @var array
	 */
	public $existsCheckDirs = [];

	/**
	 * 設定
	 *
	 * @var array
	 */
	public $settings = null;

	/**
	 * セットアップ
	 *
	 * @param Model $Model
	 * @param array $settings actsAsの設定
	 */
	public function setup(Model $Model, $settings = [])
	{
        $this->BcUpload = new BcUpload();
        $this->BcUpload->initialize($settings, $Model);
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
        $this->BcUpload->setupTmpData(isset($Model->data[$Model->alias])? $Model->data[$Model->alias] : $Model->data);
        $Model->data[$Model->alias] = $this->BcUpload->setupRequestData(isset($Model->data[$Model->alias])? $Model->data[$Model->alias] : $Model->data);
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
            $this->BcUpload->deleteExistingFiles(isset($Model->data[$Model->alias])? $Model->data[$Model->alias] : $Model->data);
        }
        $Model->data[$Model->alias] = $this->BcUpload->saveFiles(isset($Model->data[$Model->alias])? $Model->data[$Model->alias] : $Model->data);
        if ($Model->exists()) {
            $Model->data[$Model->alias] = $this->BcUpload->deleteFiles(isset($Model->data[$Model->alias])? $Model->data[$Model->alias] : $Model->data);
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
        if ($this->BcUpload->isUploaded()) {
            $Model->data[$Model->alias] = $this->BcUpload->renameToBasenameFields(isset($Model->data[$Model->alias])? $Model->data[$Model->alias] : $Model->data);
            $this->BcUpload->resetUploaded();
        }
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
		$this->BcUpload->deleteFiles(isset($Model->data[$Model->alias])? $Model->data[$Model->alias] : $Model->data, true);
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
		 return [$Model->alias => $this->BcUpload->saveTmpFiles(
			isset($data[$Model->alias])? $data[$Model->alias] : $data,
			$tmpId
		)];
	}

    /**
     * 設定情報を取得
     * @param $alias
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSettings(Model $Model, $alias)
    {
        return $this->BcUpload->settings[$alias];
    }

    /**
     * 設定情報を設定
     * @param $alias
     * @param $settings
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setSettings(Model $Model, $alias, $settings)
    {
        $this->BcUpload->settings[$alias] = $settings;
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
        return $this->BcUpload->getSaveDir($isTheme, $limited);
    }

}
