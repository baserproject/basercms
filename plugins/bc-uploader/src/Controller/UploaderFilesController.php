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

namespace BcUploader\Controller;

use BaserCore\Controller\BcFrontAppController;
use BaserCore\Utility\BcUtil;
use BcUploader\Service\UploaderFilesServiceInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * ファイルアップローダーコントローラー
 */
class UploaderFilesController extends BcFrontAppController
{

    /**
     * 公開期間のチェックを行う
     *
     */
    public function view_limited_file(UploaderFilesServiceInterface $service, string $filename)
    {
        $display = false;
        if (BcUtil::loginUser()) {
            $display = true;
        } else {
            $conditions = [
                'UploaderFiles.name' => $service->UploaderFiles->getSourceFileName($filename),
                ['or' => [
                    ['UploaderFiles.publish_begin <=' => date('Y-m-d H:i:s')],
                    ['UploaderFiles.publish_begin IS' => NULL],
                ]],
                ['or' => [
                    ['UploaderFiles.publish_end >=' => date('Y-m-d H:i:s')],
                    ['UploaderFiles.publish_end IS' => NULL],
                ]]
            ];
            $data = $service->getIndex(['conditions' => $conditions]);
            if ($data->count()) {
                $display = true;
            }
        }

        if ($display) {
            $info = pathinfo($filename);
            $ext = $info['extension'];
            $contentsMaping = [
                "gif" => "image/gif",
                "jpg" => "image/jpeg",
                "jpeg" => "image/jpeg",
                "png" => "image/png",
                "swf" => "application/x-shockwave-flash",
                "pdf" => "application/pdf",
                "sig" => "application/pgp-signature",
                "spl" => "application/futuresplash",
                "doc" => "application/msword",
                "ai" => "application/postscript",
                "torrent" => "application/x-bittorrent",
                "dvi" => "application/x-dvi",
                "gz" => "application/x-gzip",
                "pac" => "application/x-ns-proxy-autoconfig",
                "tar.gz" => "application/x-tgz",
                "tar" => "application/x-tar",
                "zip" => "application/zip",
                "mp3" => "audio/mpeg",
                "m3u" => "audio/x-mpegurl",
                "wma" => "audio/x-ms-wma",
                "wax" => "audio/x-ms-wax",
                "wav" => "audio/x-wav",
                "xbm" => "image/x-xbitmap",
                "xpm" => "image/x-xpixmap",
                "xwd" => "image/x-xwindowdump",
                "css" => "text/css",
                "html" => "text/html",
                "js" => "text/javascript",
                "txt" => "text/plain",
                "xml" => "text/xml",
                "mpeg" => "video/mpeg",
                "mov" => "video/quicktime",
                "avi" => "video/x-msvideo",
                "asf" => "video/x-ms-asf",
                "wmv" => "video/x-ms-wmv"
            ];
            header("Content-type: " . $contentsMaping[$ext]);
            readfile(WWW_ROOT . 'files' . DS . 'uploads' . DS . 'limited' . DS . $filename);
            exit();
        } else {
            $this->notFound();
        }
    }

}
