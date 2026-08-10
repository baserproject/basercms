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

use BcBurgerEditor\Lib\BurgerEditorUtil;
use Cake\Core\Plugin;

class BurgerEditorService
{

    protected $bgEditorBase;
    protected static $useAddonList = [];


    public function __construct(array $config = [])
    {
        $this->bgEditorBase = Plugin::path('BcBurgerEditor');
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
        self::addAddonList($name);
        $typePath = BurgerEditorUtil::getTypePath($name);
        if (!$typePath || !file_exists($typePath . 'value.php')) {
            return;
        }
        echo '<div class="value' . $name . '">';
        include $typePath . 'value.php';
        echo '</div>';
    }

    protected static function addAddonList($name)
    {
        if (!in_array($name, self::$useAddonList)) self::$useAddonList[] = $name;
    }


    public static function getAddonList()
    {
        return self::$useAddonList;
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
