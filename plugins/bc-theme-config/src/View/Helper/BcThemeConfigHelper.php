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

namespace BcThemeConfig\View\Helper;

use BaserCore\Utility\BcUtil;
use Cake\Core\Plugin;
use Cake\ORM\TableRegistry;
use Cake\View\Helper;

/**
 * BcThemeConfigHelper
 */
class BcThemeConfigHelper extends Helper
{

    /**
     * Helper
     *
     * @var string[]
     */
    public $helpers = ['BcBaser'];

    /**
     * メインイメージを出力する
     *
     * メインイメージは管理画面のテーマ設定にて指定
     *
     * @param array $options オプション
     *    - `all`: 全ての画像を出力する。
     *    - `num`: 指定した番号の画像を出力する。all を true とした場合は、出力する枚数となる。
     *    - `id` : all を true とした場合、UL タグの id 属性を指定できる。
     *    - `class` : all を true とした場合、UL タグの class 属性を指定できる。
     *    ※ その他の、パラメーターは、 BcBaserHelper->getThemeImage() を参照
     * @return void
     */
    public function mainImage($options = [])
    {
        $options = array_merge([
            'num' => 1,
            'all' => false,
            'id' => 'MainImage',
            'class' => false
        ], $options);
        if ($options['all']) {
            $id = $options['id'];
            $class = $options['class'];
            $num = $options['num'];
            unset($options['all']);
            unset($options['id']);
            unset($options['class']);
            $tag = '';
            for($i = 1; $i <= $num; $i++) {
                $options['num'] = $i;
                $themeImage = $this->getThemeImage('main_image', $options);
                if ($themeImage) {
                    $tag .= '<li>' . $themeImage . '</li>' . "\n";
                }
            }
            $ulAttr = '';
            if ($id !== false) {
                $ulAttr .= ' id="' . $id . '"';
            }
            if ($class !== false) {
                $ulAttr .= ' class="' . $class . '"';
            }
            echo '<ul' . $ulAttr . '>' . "\n" . $tag . "\n" . '</ul>';
        } else {
            echo $this->getThemeImage('main_image', $options);
        }
    }

    /**
     * ロゴを出力する
     *
     * @param array $options オプション（初期値 : array()）
     *    ※ パラメーターは、 BcBaserHelper->getThemeImage() を参照
     * @return void
     */
    public function logo($options = [])
    {
        echo $this->getThemeImage('logo', $options);
    }

    /**
     * テーマ画像を取得する
     *
     * @param string $name テーマ画像名（ log or main_image ）
     * @param array $options オプション（初期値 :array()）
     *    - `num` : main_imageの場合の番号指定（初期値 : ''）
     *    - `thumb`: サムネイルを取得する（初期値 : false）
     *    - `class`: 画像に設定する class 属性（初期値 : ''）
     *    - `popup`: ポップアップリンクを指定（初期値 : false）
     *    - `alt`    : 画像に設定する alt 属性。リンクの title 属性にも設定される。（初期値 : テーマ設定で設定された値）
     *    - `link`    : リンク先URL。popup を true とした場合、オリジナルの画像へのリンクとなる。（初期値 : テーマ設定で設定された値）
     *    - `maxWidth : 最大横幅（初期値 : ''）
     *    - `maxHeight: 最大高さ（初期値 : ''）
     *    - `width : 最大横幅（初期値 : ''）
     *    - `height: 最大高さ（初期値 : ''）
     *    - `noimage:
     *    - `output:
     * @return string $tag テーマ画像のHTMLタグ
     */
    public function getThemeImage($name, $options = [])
    {
        $themeConfigsTable = TableRegistry::getTableLocator()->get('BcThemeConfig.ThemeConfigs');
        $data = $themeConfigsTable->getKeyValue();

        $url = $imgPath = $uploadUrl = $uploadThumbUrl = $originUrl = '';
        $thumbSuffix = '_thumb';
        $dir = WWW_ROOT . 'files' . DS . 'theme_configs' . DS;
        $themeDir = Plugin::path(BcUtil::getCurrentTheme());
        $imgDir = $themeDir . 'webroot' . DS . 'img' . DS;
        $num = '';
        if (!empty($options['num'])) {
            $num = '_' . $options['num'];
        }
        $options = array_merge([
            'thumb' => false,
            'class' => '',
            'popup' => false,
            'alt' => $data[$name . '_alt' . $num],
            'link' => $data[$name . '_link' . $num],
            'maxWidth' => '',
            'maxHeight' => '',
            'width' => '',
            'height' => '',
            'noimage' => '', // 画像がなかった場合に表示する画像
            'output' => '', // 出力タイプ tag ,url を指定、未指定(or false)の場合は、tagで出力(互換性のため)
        ], $options);
        $name = $name . $num;

        if ($data[$name]) {
            $pathinfo = pathinfo($data[$name]);
            $uploadPath = $dir . $data[$name];
            $uploadThumbPath = $dir . $pathinfo['filename'] . $thumbSuffix . '.' . $pathinfo['extension'];
            $uploadUrl = '/files/theme_configs/' . $data[$name];
            $uploadThumbUrl = '/files/theme_configs/' . $pathinfo['filename'] . $thumbSuffix . '.' . $pathinfo['extension'];
        }

        if ($data[$name]) {
            if (!$options['thumb']) {
                if (file_exists($uploadPath)) {
                    $imgPath = $uploadPath;
                    $url = $uploadUrl;
                }
            } else {
                if (file_exists($uploadThumbPath)) {
                    $imgPath = $uploadThumbPath;
                    $url = $uploadThumbUrl;
                }
            }
            $originUrl = $uploadUrl;
        }

        if (!$url) {
            $exts = ['png', 'jpg', 'gif'];
            foreach($exts as $ext) {
                if (file_exists($imgDir . $name . '.' . $ext)) {
                    $url = BcUtil::getCurrentTheme() . '.' . $name . '.' . $ext;
                    $imgPath = $imgDir . $name . '.' . $ext;
                    $originUrl = $url;
                }
            }
        }

        // noimage が設定されていれば、画像がなくても処理を続ける
        if (!$url) {
            if ($options['noimage']) {
                $url = $options['noimage'];
            } else {
                return '';
            }
        }
        // outputがURLなら、URLを返す
        if ($options['output'] == 'url') {
            return $url;
        }

        $imgOptions = [];
        if ($options['class']) {
            $imgOptions['class'] = $options['class'];
        }
        if ($options['alt']) {
            $imgOptions['alt'] = $options['alt'];
        }
        if ($options['maxWidth'] || $options['maxHeight']) {
            $imginfo = getimagesize($imgPath);
            $widthRate = $heightRate = 0;
            if ($options['maxWidth']) {
                $widthRate = $imginfo[0] / $options['maxWidth'];
            }
            if ($options['maxHeight']) {
                $heightRate = $imginfo[1] / $options['maxHeight'];
            }
            if ($widthRate > $heightRate) {
                if ($options['maxWidth'] && $imginfo[0] > $options['maxWidth']) {
                    $imgOptions['width'] = $options['maxWidth'];
                }
            } else {
                if ($options['maxHeight'] && ($imginfo[1] > $options['maxHeight'])) {
                    $imgOptions['height'] = $options['maxHeight'];
                }
            }
        }
        if ($options['width']) {
            $imgOptions['width'] = $options['width'];
        }
        if ($options['height']) {
            $imgOptions['height'] = $options['height'];
        }

        $tag = $this->BcBaser->getImg($url, $imgOptions);
        if ($options['link'] || $options['popup']) {
            $linkOptions = [];
            if ($options['popup']) {
                $linkOptions['rel'] = 'colorbox';
                $link = $originUrl;
            } elseif ($options['link']) {
                $link = $options['link'];
                if (!empty($this->_View->getRequest()->getAttribute('currentSite')->alias)) {
                    if (empty($this->_View->getRequest()->getAttribute('currentSite')->same_main_url)) {
                        $link = '/' . $this->_View->getRequest()->getAttribute('currentSite')->alias . $link;
                    }
                }
            }
            if ($options['alt']) {
                $linkOptions['title'] = $options['alt'];
            }
            $linkOptions['escapeTitle'] = false;
            $tag = $this->BcBaser->getLink($tag, $link, $linkOptions);
        }
        return $tag;
    }

}
