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

namespace BaserCore\View\Helper;

use BaserCore\Event\BcEventDispatcherTrait;
use Cake\View\Helper;

/**
 * XMLヘルパー拡張
 *
 */
class BcXmlHelper extends Helper
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * XML document version
     *
     * @var string
     */
    private $version = '1.0';

    /**
     * XML document encoding
     *
     * @var string
     */
    private $encoding = 'UTF-8';

    /**
     * XML宣言を生成
     * IE6以外の場合のみ生成する
     *
     * @param array $attrib
     * @return string XML宣言
     */
    public function header($attrib = [])
    {
        $ua = @$_SERVER['HTTP_USER_AGENT'];
        if (!(preg_match("/Windows/", $ua) && preg_match("/MSIE/", $ua)) || !(preg_match("/MSIE 6/", $ua))) {
            if (Configure::read('App.encoding') !== null) {
                $this->encoding = Configure::read('App.encoding');
            }

            if (is_array($attrib)) {
                $attrib = array_merge(['encoding' => $this->encoding], $attrib);
            }
            if (is_string($attrib) && strpos($attrib, 'xml') !== 0) {
                $attrib = 'xml ' . $attrib;
            }

            $header = 'xml';
            if (is_string($attrib)) {
                $header = $attrib;
            } else {

                $attrib = array_merge(['version' => $this->version, 'encoding' => $this->encoding], $attrib);
                foreach($attrib as $key => $val) {
                    $header .= ' ' . $key . '="' . $val . '"';
                }
            }
            $out = '<' . '?' . $header . ' ?' . '>';

            return $out;
        }
    }

}
