<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

 namespace BcUploader\View\Helper;

use BcUploader\Model\Entity\UploaderFile;
use Cake\Event\Event;
use Cake\View\Helper;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * アップローダーヘルパー
 */
class UploaderHelper extends Helper
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
     * @return void
     */
    public function beforeRender(Event $event, $viewFile)
    {
        $this->savedUrl = '/files/uploads/';
        $this->savePath = WWW_ROOT . 'files' . DS . 'uploads' . DS;
    }

    /**
     * リスト用のimgタグを出力する
     *
     * @param UploaderFile $uploaderFile
     * @param array $options
     * @return string imgタグ
     * @checked
     * @noTodo
     */
    public function file(UploaderFile $uploaderFile, array $options = [])
    {
        $imgUrl = $this->getFileUrl($uploaderFile->name);
        $pathInfo = pathinfo($uploaderFile->name);
        $ext = $pathInfo['extension'];
        $_options = ['alt' => $uploaderFile->alt];
        $options = array_merge($_options, $options);
        if (in_array(strtolower($ext), ['gif', 'jpg', 'png'])) {
            if (isset($options['size'])) {
                $resizeName = $pathInfo['filename'] . '__' . $options['size'] . '.' . $ext;
                if (!empty($uploaderFile->publish_begin) || !empty($uploaderFile->publish_end)) {
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
            $imgUrl = 'BcUploader.icon_upload_file.png';
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
     * @param UploaderFile $uploaderFile
     * @param string $linkText
     * @return string
     * @checked
     * @noTodo
     */
    public function download(UploaderFile $uploaderFile, $linkText = '≫ ダウンロード')
    {
        $fileUrl = $this->getFileUrl($uploaderFile->name);
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
            ($data['publish_end'] && $data['publish_end'] <= date('Y-m-d H:i:s'))
        ) {
            $isPublish = false;
        }

        return $isPublish;
    }

}
