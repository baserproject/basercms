<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader.Helper
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */

/**
 * アップローダーヘルパー
 *
 * @package         Uploader.View.Helper
 */
class UploaderHelper extends AppHelper
{
	/**
	 * アップロードファイルの保存URL
	 *
	 * @var        string
	 * @access    public
	 */
	public $savedUrl = '';
	/**
	 * アップロードファイルの保存パス
	 *
	 * @var        string
	 * @access    public
	 */
	public $savePath = '';
	/**
	 * ヘルパー
	 *
	 * @var        array
	 * @access    public
	 */
	public $helpers = ['Html'];

	/**
	 * Before Render
	 *
	 * @return    void
	 * @access    public
	 */
	public function beforeRender($viewFile)
	{

		parent::beforeRender($viewFile);
		$this->savedUrl = '/files/uploads/';
		$this->savePath = WWW_ROOT . 'files' . DS . 'uploads' . DS;

	}

	/**
	 * リスト用のimgタグを出力する
	 *
	 * @param array $uploaderFile
	 * @param array $options
	 * @return    string    imgタグ
	 */
	public function file($uploaderFile, $options = [])
	{

		if (isset($uploaderFile['UploaderFile'])) {
			$uploaderFile = $uploaderFile['UploaderFile'];
		}

		$imgUrl = $this->getFileUrl($uploaderFile['name']);

		$pathInfo = pathinfo($uploaderFile['name']);
		$ext = $pathInfo['extension'];
		$_options = ['alt' => $uploaderFile['alt']];
		$options = Set::merge($_options, $options);

		if (in_array(strtolower($ext), ['gif', 'jpg', 'png'])) {
			if (isset($options['size'])) {
				$resizeName = $pathInfo['filename'] . '__' . $options['size'] . '.' . $ext;

				if (!empty($uploaderFile['publish_begin']) || !empty($uploaderFile['publish_end'])) {
					$savePath = $this->savePath . 'limited' . DS . $resizeName;
				} else {
					$savePath = $this->savePath . $resizeName;
				}
				if (file_exists($savePath)) {
					$imgUrl = $this->getFileUrl($resizeName);
					unset($options['size']);
				}
			}
			return $this->Html->image($imgUrl, $options);
		} else {
			$imgUrl = 'Uploader.icon_upload_file.png';
			return $this->Html->image($imgUrl, $options);
		}

	}

	/**
	 * ファイルが保存されているURLを取得する
	 *
	 * @param string $fileName
	 * @return    string
	 * @access    public
	 */
	public function getFileUrl($fileName)
	{

		if ($fileName) {
			return $this->savedUrl . $fileName;
		} else {
			return '';
		}

	}

	/**
	 * ダウンロードリンクを表示
	 *
	 * @param array $uploaderFile
	 * @param string $linkText
	 * @return    string
	 */
	public function download($uploaderFile, $linkText = '≫ ダウンロード')
	{
		if (isset($uploaderFile['UploaderFile'])) {
			$uploaderFile = $uploaderFile['UploaderFile'];
		}
		$fileUrl = $this->getFileUrl($uploaderFile['name']);
		// HtmlヘルパではスマートURLオフの場合に正常なURLが取得できないので、直接記述
		return '<a href="' . $fileUrl . '" target="_blank">' . $linkText . '</a>';
	}

	/**
	 * ファイルの公開制限期間が設定されているか判定する
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function isLimitSetting($data)
	{

		if (!empty($data['UploaderFile'])) {
			$data = $data['UploaderFile'];
		}
		if (!empty($data['publish_begin']) || !empty($data['publish_end'])) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * ファイルの公開状態を取得する
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function isPublish($data)
	{
		if (isset($data['UploaderFile'])) {
			$data = $data['UploaderFile'];
		}
		$isPublish = true;

		if ($data['publish_begin'] == '0000-00-00 00:00:00') {
			$data['publish_begin'] = null;
		}
		if ($data['publish_end'] == '0000-00-00 00:00:00') {
			$data['publish_end'] = null;
		}
		// 期限を設定している場合に条件に該当しない場合は強制的に非公開とする
		if (($data['publish_begin'] && $data['publish_begin'] >= date('Y-m-d H:i:s')) ||
			($data['publish_end'] && $data['publish_end'] <= date('Y-m-d H:i:s'))) {
			$isPublish = false;
		}

		return $isPublish;
	}

}
