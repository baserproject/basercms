<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.1.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcBurgerEditor\Service;

use BaserCore\Utility\BcFolder;
use BaserCore\Utility\BcUtil;
use BcBurgerEditor\Lib\BurgerEditorUtil;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Routing\Router;

class BurgerEditorService
{

    protected $bgEditorBase;
    /**
     * 利用したタイプ
     *
     * @var array
     */
    protected $useAddonList = [];

    /**
     * 画像の保存先パス
     *
     * @var string
     */
    protected $imageFileBaseDir = '';

    /**
     * 画像の公開URL
     *
     * @var string
     */
    protected $imageFileBaseURL = '';

    /**
     * 画像以外のファイルの保存先パス
     *
     * @var string
     */
    protected $otherFileBaseDir = '';

    /**
     * 画像以外のファイルの公開URL
     *
     * @var string
     */
    protected $otherFileBaseURL = '';

    /**
     * 画像ファイルの最大ID
     *
     * @var int
     */
    protected $imageFileMaxId = 0;

    /**
     * 画像以外のファイルの最大ID
     *
     * @var int
     */
    protected $otherFileMaxId = 0;

    public function __construct(array $config = [])
    {
        $this->bgEditorBase = Plugin::path('BcBurgerEditor');
    }

    /**
     * 保存先のパスとURLを解決する
     *
     * 保存先フォルダが無い場合はプラグインの初期化処理で作成し、
     * Bge.fileShare が無効の場合はログインユーザ別のフォルダを用意する
     *
     * @return void
     */
    public function setupSavePath()
    {
        $this->imageFileBaseDir = realpath(WWW_ROOT) . DS . 'files' . DS . 'bgeditor' . DS . 'img' . DS;
        $this->otherFileBaseDir = realpath(WWW_ROOT) . DS . 'files' . DS . 'bgeditor' . DS . 'other' . DS;

        $baseUrl = Router::url('/');
        $this->imageFileBaseURL = $baseUrl . 'files/bgeditor/img/';
        $this->otherFileBaseURL = $baseUrl . 'files/bgeditor/other/';

        // フォルダがない場合はinit処理を実行する
        if (!file_exists($this->imageFileBaseDir) || !file_exists($this->otherFileBaseDir)) {
            /** @var \BcBurgerEditor\BcBurgerEditorPlugin $plugin */
            $plugin = Plugin::getCollection()->get('BcBurgerEditor');
            $plugin->init();
        }

        // 設定値により、ユーザ別にファイル場所を設置
        if (!Configure::read("Bge.fileShare")) {
            $user = BcUtil::loginUser();
            $userId = $user['id'];
            $this->imageFileBaseDir .= $userId . DS;
            $this->otherFileBaseDir .= $userId . DS;
            $this->imageFileBaseURL .= $userId . '/';
            $this->otherFileBaseURL .= $userId . '/';
            if (!file_exists($this->imageFileBaseDir)) {
                mkdir($this->imageFileBaseDir);
                chmod($this->imageFileBaseDir, 0777);
            }
            if (!file_exists($this->otherFileBaseDir)) {
                mkdir($this->otherFileBaseDir);
                chmod($this->otherFileBaseDir, 0777);
            }
        }
    }

    /**
     * 画像の保存先パスを取得する
     *
     * @return string
     */
    public function getImageFileBaseDir()
    {
        return $this->imageFileBaseDir;
    }

    /**
     * 画像の公開URLを取得する
     *
     * @return string
     */
    public function getImageFileBaseURL()
    {
        return $this->imageFileBaseURL;
    }

    /**
     * 画像以外のファイルの保存先パスを取得する
     *
     * @return string
     */
    public function getOtherFileBaseDir()
    {
        return $this->otherFileBaseDir;
    }

    /**
     * 画像以外のファイルの公開URLを取得する
     *
     * @return string
     */
    public function getOtherFileBaseURL()
    {
        return $this->otherFileBaseURL;
    }

    /**
     * 画像ファイルの一覧を取得する
     *
     * あわせて最大IDを更新する
     *
     * @return array
     */
    public function getImageList()
    {
        $dir = new BcFolder($this->imageFileBaseDir);
        $tmpList = [];
        $files = $dir->find();
        foreach($files as $file) {
            if ($file == ".DS_Store") continue;
            if (preg_match('/(__midium|__small|__org)\.[a-z0-9]+$/i', $file)) {
                continue;
            }

            $path = $dir->pwd();
            if (substr($path, -1) != DS) {
                $path = $path . DS;
            }
            $fileKey = filemtime($path . $file);
            if (preg_match('/^(\d+)__/', $file, $matches) && isset($matches[1])) {
                $fileKey = intval($matches[1]) * 100000 + 2000000000;
            }
            while(1) {
                if (!isset($tmpList[$fileKey])) break;
                $fileKey++;
            }
            $tmpList[$fileKey] = $path . $file;

            // ファイルID取得
            $fileId = self::getFileId($file);
            if ($this->imageFileMaxId < $fileId) $this->imageFileMaxId = $fileId;
        }
        krsort($tmpList);
        return array_values($tmpList);
    }

    /**
     * 画像以外のファイルの一覧を取得する
     *
     * あわせて最大IDを更新する
     *
     * @return array
     */
    public function getFileList()
    {
        $dir = new BcFolder($this->otherFileBaseDir);
        $tmpList = [];
        $files = $dir->find();
        foreach($files as $file) {
            $path = $dir->pwd();
            if (substr($path, -1) != DS) {
                $path = $path . DS;
            }
            $fileKey = filemtime($path . $file);
            while(1) {
                if (!isset($tmpList[$fileKey])) break;
                $fileKey++;
            }
            $tmpList[$fileKey] = $path . $file;

            // ファイルID取得
            $fileId = self::getFileId($file);
            if ($this->otherFileMaxId < $fileId) $this->otherFileMaxId = $fileId;
        }
        krsort($tmpList);
        return array_values($tmpList);
    }

    /**
     * 画像ファイルの次のIDを採番する
     *
     * @return int
     */
    public function nextImageFileId()
    {
        return ++$this->imageFileMaxId;
    }

    /**
     * 画像以外のファイルの次のIDを採番する
     *
     * @return int
     */
    public function nextOtherFileId()
    {
        return ++$this->otherFileMaxId;
    }

    /**
     * ファイル名からIDを取得する
     *
     * @param string $fileName ファイル名
     * @return mixed (int|null)
     */
    protected static function getFileId($fileName)
    {
        preg_match("/^(\d+)__/", $fileName, $matches);
        if (isset($matches[1])) return $matches[1];
        return null;
    }

    /**
     * レスポンス用のファイルリストとページネーションを作成する
     *
     * @param array $fileList
     * @param int $targetPage
     * @param int $selectedFileId
     * @param int $pageNum
     * @return array 該当ページ分のファイルリスト(data)とページネーション情報(pagination)を含む
     */
    public static function getFileListWithPagination($fileList, $targetPage, $selectedFileId, $pageNum)
    {
        $startIndex = 0;
        $currentPage = 1;

        if(is_null($targetPage) && $selectedFileId){
            // 選択済み画像ページを表示（初期表示時限定）
            foreach($fileList as $key => $file){
                if($file['fileId'] === $selectedFileId){
                    $currentPage = intdiv((int)$key, $pageNum) + 1;
                    $startIndex = ($currentPage - 1) * $pageNum;
                }
            }
        }elseif($targetPage >= 1){
            // 指定ページを表示
            $startIndex = ($targetPage - 1) * $pageNum;
            if(count($fileList) <= $startIndex){
                // ページ数分の要素がない場合は先頭ページを表示
                $startIndex = 0;
                $currentPage = 1;
            }else{
                $currentPage = $targetPage;
            }
        }

        $pageList = array_slice($fileList, $startIndex, $pageNum);

        // 最大ページ数
        $imagePaginationMaxPage = intdiv(count($fileList)-1, $pageNum) + 1;

        return [
            'data' => $pageList,
            'pagination' => [
                'pageMaxNumber' => $imagePaginationMaxPage,
                'currentPageNumber' => $currentPage,
                'selectedFileId' => $selectedFileId,
            ],
        ];
    }

    /**
     * プラグインの基準パスを取得する
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->bgEditorBase;
    }

    /**
     * ブロックの基準パスを取得する
     *
     * @return string
     */
    public function getBlockPath()
    {
        return BurgerEditorUtil::getAddonPath()[0] . 'block' . DS;
    }

    /**
     * タイプの基準パスを取得する
     *
     * @return string
     */
    public function getTypePath()
    {
        return BurgerEditorUtil::getAddonPath()[0] . 'type' . DS;
    }

    /**
     * タイプの表示用テンプレートを出力する
     *
     * Addon を提供するプラグイン側のタイプも対象とするため、
     * パスの解決は BurgerEditorUtil に委譲する
     *
     * @param string $name タイプ名
     * @return void
     */
    public function element($name)
    {
        $this->addAddonList($name);
        $typePath = BurgerEditorUtil::getTypePath($name);
        if (!$typePath || !file_exists($typePath . 'value.php')) {
            return;
        }
        echo '<div class="value' . $name . '">';
        include $typePath . 'value.php';
        echo '</div>';
    }

    /**
     * 利用したタイプを記録する
     *
     * @param string $name タイプ名
     * @return void
     */
    protected function addAddonList($name)
    {
        if (!in_array($name, $this->useAddonList)) $this->useAddonList[] = $name;
    }

    /**
     * 利用したタイプの一覧を取得する
     *
     * @return array
     */
    public function getAddonList()
    {
        return $this->useAddonList;
    }

    /**
     * 画像をExif情報を元に正しい確度に回転する
     *
     * @param $file
     * @return bool
     */
    public function rotateImage($file)
    {
        if (!function_exists('exif_read_data')) {
            return false;
        }
        $exif = @exif_read_data($file); // exifが読めるか読み込むまでわからないため@ハンドリング
        if (empty($exif) || empty($exif['Orientation'])) {
            return true;
        }
        switch($exif['Orientation']) {
            case 3:
                $angle = 180;
                break;
            case 6:
                $angle = 270;
                break;
            case 8:
                $angle = 90;
                break;
            default:
                return true;
        }
        $imgInfo = getimagesize($file);
        $imageType = $imgInfo[2];
        // 元となる画像のオブジェクトを生成
        switch($imageType) {
            case IMAGETYPE_GIF:
                $srcImage = imagecreatefromgif($file);
                break;
            case IMAGETYPE_JPEG:
                $srcImage = imagecreatefromjpeg($file);
                break;
            case IMAGETYPE_PNG:
                $srcImage = imagecreatefrompng($file);
                break;
            default:
                return false;
        }
        $rotate = imagerotate($srcImage, $angle, 0);
        switch($imageType) {
            case IMAGETYPE_GIF:
                imagegif($rotate, $file);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($rotate, $file, 100);
                break;
            case IMAGETYPE_PNG:
                imagepng($rotate, $file);
                break;
            default:
                return false;
        }
        imagedestroy($srcImage);
        imagedestroy($rotate);
        return true;
    }
}
