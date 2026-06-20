<?php

namespace BcBurgerEditor\Event;

use BaserCore\Event\BcViewEventListener;
use BaserCore\Utility\BcUtil;
use BcBurgerEditor\Lib\BurgerEditorUtil;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\EventInterface;
use Cake\View\View;
require_once Plugin::path('BcBurgerEditor') . 'libs' . DS . 'simple_html_dom.php';
use simple_html_dom;

/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.1.0
 * @license       https://basercms.net/license/index.html MIT License
 */
class BurgerEditorViewEventListener extends BcViewEventListener
{
	/**
	 * 登録イベント
	 *
	 * @var array
	 */
	public $events = [
		'beforeLayout',
		'afterLayout'
	];

	/**
	 * beforeLayout
	 *
	 * @param EventInterface $event
	 * @return boolean
	 */
	public function beforeLayout(EventInterface $event)
	{
		/** @var View $View */
		$View = $event->getSubject();
		if($View->getPlugin() === 'DebugKit') return true;
		if(!$View->helpers()->has('BurgerEditor')) return true;
		$request = $View->getRequest();
		// GoogleMaps APIの取得
		$googleMapsApiKey = BurgerEditorUtil::getGoogleMapApiKey();

		// 管理画面はJS・CSSの自動的用を除外
		if (BcUtil::isAdminSystem()) {
			// プレビューは適用
			if (($request->getParam('controller') === 'Pages' && $request->getParam('action') === 'display')
				|| ($request->getParam('plugin') === 'BcBlog' && $request->getParam('action') === 'archives')) {
			} else {
				return true;
			}
		}

		// 除外レイアウト
		$excdlueViewPath = [
			'email/text',    // メール
			'Blog/rss',        // ブログRSS
			'Feed',            // フィード
		];
		if (in_array($View->getTemplatePath(), $excdlueViewPath)) {
			return true;
		}

		$View->loadHelper('BcBurgerEditor.BurgerEditor');
		$shouldLoadStyle = $View->BurgerEditor->shouldLoadStyle();

		// ユーザ(サイト制作者)定義CSSの自動読込
		$userCssList = array();

		if ($shouldLoadStyle && Configure::read('Bge.loadCSS.bge_style_default')) {
			$userCssList[] = "BcBurgerEditor.bge_style_default";
		}

		$themeDirectory = BcUtil::getCurrentTheme();

		if (Configure::read('Bge.loadCSS.bge_style')) {
			// themeのcss優先
			if (file_exists(WWW_ROOT . 'theme' . DS . $themeDirectory . DS . 'css' . DS . 'bge_style.css')) {
				$path = WWW_ROOT . 'theme' . DS . $themeDirectory . DS . 'css' . DS . 'bge_style.css';
				$userCssList[] = 'bge_style.css' . BurgerEditorUtil::getSuffix($path);;
			// themeになく、webroot/cssにあれば読込
			} elseif (file_exists(WWW_ROOT . 'css' . DS . 'bge_style.css')) {
				$path = WWW_ROOT . 'css' . DS . 'bge_style.css';
				$userCssList[] = 'bge_style.css' . BurgerEditorUtil::getSuffix($path);
			// themeになく、webroot/cssにもない場合、プラグイン標準のファイルを読み込む
			} else {
				$path = WWW_ROOT . 'app' . DS . 'Plugin' . DS . 'BcBurgerEditor' . DS . 'webroot' . DS . 'css' . DS . 'bge_style.css';
				$userCssList[] = 'BcBurgerEditor.bge_style' . BurgerEditorUtil::getSuffix($path);
			}
		}

		// colorbox用スタイル
		if (Configure::read('Bge.loadCSS.colorbox')) {
			$userCssList[] = 'BcBurgerEditor.colorbox';
		}

		$View->BcBaser->css($userCssList, false);

		// JSの自動読込
		$jsList = [];

		// bge_functions.min.jsの読み込み
		$jsList[] = 'BcBurgerEditor.bge_modules/bge_functions.min.js';
		// jquery.colorbox.jsの自動読み込み
		$jsList[] = 'BcBurgerEditor.bge_modules/jquery.colorbox-min.js';

		if ($jsList) $View->BcBaser->js($jsList, false, ['defer' => 'defer']);

		// google map APIの読込
		if ($googleMapsApiKey) {
			$googleMapsAPIURL = '//maps.google.com/maps/api/js?key=' . $googleMapsApiKey;
			$View->BcBaser->js($googleMapsAPIURL, false, ['defer' => 'defer']);
		}


		// 表示が不要なBurgerEditorのhidden要素を削除
		$matches = null;
		$output = $View->fetch('content');
		preg_match('/<\!\-\- BaserPageTagBegin \-\->.*?<\!\-\- BaserPageTagEnd \-\->/is', $output, $matches);

		$bcPageTag = empty($matches[0])? '' : $matches[0];
		$output = preg_replace('/<\!\-\- BaserPageTagBegin \-\->.*?<\!\-\- BaserPageTagEnd \-\->/is', '', $output);

		$dom = new simple_html_dom();
		$dom->load($output, null, false);
		$bgbList = $dom->find('[data-bgb]');

		$wrapperClass = Configure::read('Bge.wrapperClass') ? Configure::read('Bge.wrapperClass') : 'bge-contents';

		// キャッシュ時間判別
		// ブログの場合はそもそもキャッシュを利用しないのでfalse
		// viewキャッシュを利用する場合はviewDurationの値が入る
		// 固定ページでviewDurationより短い更新時間が設定されていればより短い切り替え時間がないか走査する
		$nearlyCacheTime = property_exists($View, 'cacheAction') ? $View->cacheAction : false;
		$useCache = false;
		if (Configure::read('Bge.publishTimer') && $nearlyCacheTime !== false) {
			$nearlyCacheTime = $View->cacheAction;
			$useCache = true;
		}

		// BurgerEditorのコンテンツの場合
		if (count($bgbList)) {
			foreach($bgbList as $bgb) {
				// hidden 削除
				foreach($bgb->find('input[type=hidden]') as $hidden) {
					$hidden->outertext = '';
				}
				// embed typeの変換
				foreach($bgb->find('[data-bgt=embed]') as $embed) {
					$inject = '';
					foreach($embed->find('[data-bge=embed-code]') as $code) {
						$inject .= base64_decode($code->text());
					}
					$embed->outertext = $inject;
				}
				// 公開日時の変換
				if (Configure::read('Bge.publishTimer')) {
					// previewかつdate検証指定
					if ($View->getRequest()->getQuery('preview') && $View->getRequest()->getQuery('bgtimerdate')) {
						$bgPublishTimerDate = strtotime($View->getRequest()->getQuery('bgtimerdate'));
					} else {
						$bgPublishTimerDate = time();
					}
					foreach($bgb->attr as $attrName => $attrVal) {
						if ($attrName === 'data-bgb-publish-datetime') {
							if (strtotime($attrVal) > $bgPublishTimerDate) {
								$bgb->outertext = '';
							}
						}
						if ($attrName === 'data-bgb-unpublish-datetime') {
							if (strtotime($attrVal) <= $bgPublishTimerDate) {
								$bgb->outertext = '';
							}
						}
					}
				}
			}
		}

		if (Configure::read('Bge.autoWrapper')) {
			$View->assign('content', '<div class="' . $wrapperClass . '">' . $bcPageTag . $dom . '</div>');
		} else {
			$View->assign('content', $bcPageTag . $dom);
		}

		unset($dom);

		return true;

	}

	/**
	 * afterLayout
	 *
	 * @param EventInterface $event
	 * @return boolean
	 */
	public function afterLayout(EventInterface $event)
	{
		/** @var View $View */
		$View = $event->getSubject();

		// google map apiが２回呼ばれていたら、後で呼ばれたものを削除する
		$output = $View->fetch('content');
		if (preg_match_all('/<script.*?<\/script>/i', $output, $matches)) {
			$times = 0;
			$gmList = [];
			foreach($matches[0] as $match) {
				if (preg_match('/src=("|\').*?\/\/maps\.google\.com\/maps\/api\/js/i', $match, $gmMatchs)) {
					$times++;
					$gmList[] = $match;
				}
			}
			// 書き換え
			if ($times >= 2) {
				foreach($gmList as $i => $gmScript) {
					$output = preg_replace("/" . preg_quote($gmScript, "/") . "/", "__REPACE_MAP{$i}__", $output, 1);
				}
				foreach($gmList as $i => $gmScript) {
					if ($i === 0) {
						$output = str_replace("__REPACE_MAP{$i}__", $gmScript, $output);
					} else {
						$output = str_replace("__REPACE_MAP{$i}__", '<!-- delete google map api calling by BGE -->', $output);
					}
				}
				$View->output = $output;

			}
		}
		return true;
	}
}
