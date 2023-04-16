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
use Cake\Core\Configure;
use Cake\View\Helper;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Note;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * スマホヘルパー
 *
 */
class BcSmartphoneHelper extends Helper
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * ヘルパ
     *
     * @var array
     */
    public $helpers = ['BcHtml'];

    /**
     * afterLayout
     *
     * @return void
     * @checked
     */
    public function afterLayout($layoutFile)
    {
        // TODO ucmitz 未検証
        // ブログのRSSを実装してから確認する
        // >>>
//        if ($this->request->getParam('ext') === 'rss') {
//            $rss = true;
//        } else {
//            $rss = false;
//        }
        // ---
        $rss = false;
        // <<<
        $sites = \Cake\ORM\TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sites->findByUrl($this->_View->getRequest()->getPath());
        if (!$rss && $site->device == 'smartphone' && $this->_View->getLayoutPath() != 'Emails' . DS . 'text') {
            if (empty($this->_View->getRequest()->getAttribute('currentSite'))) {
                return;
            }
            // 内部リンクの自動変換
            if ($site->auto_link) {
                $siteUrl = Configure::read('BcEnv.siteUrl');
                $sslUrl = Configure::read('BcEnv.sslUrl');
                $currentAlias = $this->_View->getRequest()->getAttribute('currentSite')->alias;
                $base = '/' . $this->_View->getRequest()->getAttribute('base');
                $regBaseUrls = [
                    preg_quote($base, '/'),
                    preg_quote(preg_replace('/\/$/', '', $siteUrl) . $base, '/'),
                ];
                if ($sslUrl) {
                    $regBaseUrls[] = preg_quote(preg_replace('/\/$/', '', $sslUrl) . $base, '/');
                }
                $regBaseUrl = implode('|', $regBaseUrls);

                // 一旦プレフィックスを除外
                $reg = '/<a([^<]*?)href="((' . $regBaseUrl . ')(' . $currentAlias . '\/([^\"]*?)))\"/';
                $this->_View->assign('content', preg_replace_callback($reg, [$this, '_removePrefix'], $this->_View->fetch('content')));

                // プレフィックス追加
                $reg = '/<a([^<]*?)href=\"(' . $regBaseUrl . ')([^\"]*?)\"/';
                $this->_View->assign('content', preg_replace_callback($reg, [$this, '_addPrefix'], $this->_View->fetch('content')));
            }
        }
    }

    /**
     * リンクからモバイル用のプレフィックスを除外する
     * preg_replace_callback のコールバック関数
     *
     * @param array $matches
     * @return string
     * @checked
     * @noTodo
     */
    protected function _removePrefix($matches)
    {
        $etc = $matches[1];
        $baseUrl = $matches[3];
        if (strpos($matches[2], 'smartphone=off') !== false) {
            $url = $matches[2];
        } else {
            $url = $matches[5];
        }
        return '<a' . $etc . 'href="' . $baseUrl . $url . '"';
    }

    /**
     * リンクにモバイル用のプレフィックスを追加する
     * preg_replace_callback のコールバック関数
     *
     * @param array $matches
     * @return string
     * @checked
     * @noTodo
     */
    protected function _addPrefix($matches)
    {
        $currentAlias = $this->_View->getRequest()->getAttribute('currentSite')->alias;
        $baseUrl = $matches[2];
        $etc = $matches[1];
        $url = $matches[3];
        if (strpos($url, 'smartphone=off') !== false) {
            return '<a' . $etc . 'href="' . $baseUrl . $url . '"';
        } else {
            // 指定した絶対URLを記載しているリンクは変換しない
            $excludeList = Configure::read('BcApp.excludeAbsoluteUrlAddPrefix');
            if ($excludeList) {
                foreach($excludeList as $exclude) {
                    if (strpos($baseUrl, $exclude) !== false) {
                        return '<a' . $etc . 'href="' . $baseUrl . $url . '"';
                    }
                }
            }
            // 指定したディレクトリURLを記載しているリンクは変換しない
            $excludeList = Configure::read('BcApp.excludeListAddPrefix');
            if ($excludeList) {
                foreach($excludeList as $exclude) {
                    if (strpos($url, $exclude) !== false) {
                        return '<a' . $etc . 'href="' . $baseUrl . $url . '"';
                    }
                }
            }

            return '<a' . $etc . 'href="' . $baseUrl . $currentAlias . '/' . $url . '"';
        }
    }

}
