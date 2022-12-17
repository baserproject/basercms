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

namespace BaserCore\Controller;

use Cake\Core\Configure;
use Cake\Filesystem\File;
use BaserCore\Utility\BcUtil;
use BaserCore\Vendor\Imageresizer;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * アップロードコントローラー
 */
class UploadsController extends AppController
{

    /**
     * セッションに保存した一時ファイルを出力する
     * @param string $name
     * @return void
     */
    public function tmp()
    {
        echo $this->output(func_get_args(), func_num_args());
        exit;
        // return $this->response->withStringBody($this->output(func_get_args(), func_num_args()));
    }

    /**
     * セッションに保存した一時ファイルを返す
     *
     * @param  array $args
     * @param  int $funcNum
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function output($args, $funcNum)
    {
        $session = $this->request->getSession();
        $size = '';
        if ($funcNum > 1) {
            $size = $args[0];
            $name = $args[1];
        } else {
            $name = $args[0];
        }
        $sessioName = str_replace(['.', '/'], ['_', '_'], $name);
        $sessionData = $session->read('Upload.' . $sessioName);

        Configure::write('debug', 0);
        $type = $sessionData['type'];
        $ext = BcUtil::decodeContent($type, $name);
        if (!$ext) {
            $this->BcMessage->setError(__d('baser', '拡張子が間違っています。'));
            $this->notFound();
        }

        $fileInfo = [];
        if (isset($sessionData['imagecopy'][$size])) {
            $fileInfo = $sessionData['imagecopy'][$size];
        } elseif (!empty($sessionData['imageresize'])) {
            $fileInfo = $sessionData['imageresize'];
        } else {
            $size = '';
        }

        if (!$size) {
            $data = base64_decode($session->read('Upload.' . $sessioName . '.data'));
        } else {
            if (is_dir(TMP . 'uploads')) {
                mkdir(TMP . 'uploads');
                chmod(TMP . 'uploads', 0777);
            }

            $path = TMP . 'uploads' . DS . $name;
            $file = new File($path, true);
            $file->write(base64_decode($session->read('Upload.' . $sessioName . '.data'), 'wb'));
            $file->close();

            $thumb = false;

            if (!empty($fileInfo['thumb'])) {
                $thumb = $fileInfo['thumb'];
            }
            $imageresizer = new Imageresizer(APP . 'tmp');
            $imageresizer->resize($path, $path, $fileInfo['width'], $fileInfo['height'], $thumb);
            $data = file_get_contents($path);
            unlink($path);
        }

        if ($ext !== 'gif' && $ext !== 'jpg' && $ext !== 'png') {
            Header("Content-disposition: attachment; filename=" . $name);
        }
        Header("Content-type: " . $type . "; name=" . $name);
        return $data;
    }

}
