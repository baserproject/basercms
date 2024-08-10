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
 */
#[\AllowDynamicProperties]
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
    public array $helpers = [
        'BaserCore.BcHtml'
    ];

    /**
     * afterLayout
     *
     * @return void
     * @checked
     */
    public function afterLayout($layoutFile)
    {
        $view = $this->getView();
        $request = $view->getRequest();
        $sites = \Cake\ORM\TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sites->findByUrl($request->getPath());

        if (
            $request->getParam('_ext') !== 'rss' &&
            $site->device === 'smartphone' &&
            $view->getLayoutPath() !== 'Emails' . DS . 'text'
        ) {
            if (empty($request->getAttribute('currentSite'))) {
                return;
            }
            // 内部リンクの自動変換
            if ($site->auto_link) {
                $siteUrl = Configure::read('BcEnv.siteUrl');
                $currentAlias = $request->getAttribute('currentSite')->alias;
                $base = '/' . $request->getAttribute('base');
                $regBaseUrls = [
                    preg_quote($base, '/'),
                    preg_quote(preg_replace('/\/$/', '', $siteUrl) . $base, '/'),
                ];
                $regBaseUrl = implode('|', $regBaseUrls);

                // 一旦プレフィックスを除外
                $reg = '/<a([^<]*?)href="((' . $regBaseUrl . ')(' . $currentAlias . '\/([^\"]*?)))\"/';
                $view->assign('content', preg_replace_callback($reg, [$this, '_removePrefix'], $view->fetch('content')));

                // プレフィックス追加
                $reg = '/<a([^<]*?)href=\"(' . $regBaseUrl . ')([^\"]*?)\"/';
                $view->assign('content', preg_replace_callback($reg, [$this, '_addPrefix'], $view->fetch('content')));
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
