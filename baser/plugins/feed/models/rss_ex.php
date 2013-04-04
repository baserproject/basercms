<?php
// TODO 未検証
/* SVN FILE: $Id$ */
/**
 * RSS取得モデルであるRssクラスを継承したクラス
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.feed.models
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import("Model","Feed.Rss");
App::import('Vendor','Feed.simplepie'); // オブジェクトのシリアライズの問題があるのでここから動かさない事
/**
 * RSS取得モデルであるRssクラスを継承したクラス
 *
 * @package baser.plugins.feed.models
 */
class RssEx extends Rss {
/**
 * キャッシュフォルダー
 * @var string
 * @access	public
 */
	var $cacheFolder = 'views';
/**
 * データを削除する（ログ記録オプション付）
 *
 * @param 	string RSSのURL
 * @param 	int 取得する件数
 * @param 	string キャッシュ保持期間
 * @param string 抽出するカテゴリ
 * @return array RSSデータ
 * @access public
 */
	function findAll($feedUrl, $limit = 10, $cacheExpires = null,$category = null) {

		// simplepieでフィードを取得する
		$feed = $this->__getSimplePie($feedUrl,$cacheExpires);

		// 指定カテゴリで絞り込む
		$feed['Items'] = $this->filteringCategory($feed['Items'],$category);

		if (isset($feed['Items']) && $limit && count($feed['Items']>$limit)) {
			$feed['Items'] = @array_slice($feed['Items'], 0, $limit);
		}

		return $feed;

	}
/**
 * カテゴリで抽出する
 *
 * @param array $items
 * @param mixed $filterCategory
 * @return array $items
 * @access public
 */
	function filteringCategory($items,$filterCategory = null) {

		if(!$items || !$filterCategory) {
			return $items;
		}

		$_items = array();
		foreach($items as $item) {

			if(empty($item['category']['value'])) continue;

			/* 属しているカテゴリを取得 */
			$category = '';
			switch(gettype($item['category']['value'])) {
				case 'object':
					if(get_class($item['category']['value']) == 'SimplePie_Category') {
						$category = $item['category']['value']->term;
					}
					break;
				case 'string':
					$category = $item['category']['value'];
					break;
			}

			// 該当するカテゴリのみを取得
			if(is_array($filterCategory)) {
				if(in_array($category,$filterCategory)) {
					$_items[] = $item;
				}
			}else {
				if($category == $filterCategory) {
					$_items[] = $item;
				}
			}

		}

		return $_items;

	}
/**
 * SimplePieでRSSを取得する
 *
 * @param string RSSのURL
 * @param string キャッシュ保持期間
 * @return array RSSデータ
 * @access private
 */
	function __getSimplePie($url,$cacheExpires = null) {

		if(!$url) {
			return false;
		}
		if(Configure::read('Cache.check') == false || Configure::read('debug') > 0) {
			// キャッシュをクリア
			clearCache($this->__createCacheHash('', $url),'views','.rss');
		}

		// キャッシュを取得
		$cachePath = $this->cacheFolder.$this->__createCacheHash('.rss', $url);
		$rssData = cache($cachePath, null, $cacheExpires);

		if (empty($rssData)) {

			$feed = new SimplePie();
			$feed->feed_url = $url;
			$feed->enable_cache(false);

			// 一旦デバッグモードをオフに
			$debug = Configure::read('debug');
			Configure::write('debug',0);

			$ret = $feed->init();

			Configure::write('debug',$debug);

			if(!$ret) {
				return false;
			}

			$rssData = $this->__convertSimplePie($feed->get_items());

			// ログインしてなければキャッシュを作成
			if(!isset($_SESSION['Auth']['User'])) {
				cache($cachePath, serialize($rssData));
				chmod(CACHE.$cachePath,0666);
			}

			if ($rssData) {
				return $rssData;
			}
			else {
				return false;
			}

		}else {
			return unserialize($rssData);
		}

	}
/**
 * SimplePieで取得したデータを表示用に整形する
 * 2009/09/09	ryuring
 *				古いバージョンのSimplePieでは、WordPress2.8.4が出力するRSSを解析できない事が判明。
 * 				SimplePie1.2に載せ換えて対応した。
 * TODO			このままでは、itemがない場合、RSS自体の情報が取得できないので修正が必要
 *
 * @param string SimplePieで取得したデータ
 * @return array RSSデータ
 * @access private
 */
	function __convertSimplePie($datas) {

		if(!$datas) {
			return null;
		}

		$simplePie = $datas[0]->get_feed();
		$feed['Channel']['title']['value'] = $simplePie->get_title();
		$feed['Channel']['link']['value'] = $simplePie->get_link();
		$feed['Channel']['description']['value'] = $simplePie->get_description();
		$feed['Channel']['pubDate']['value'] = '';
		$feed['Channel']['language']['value'] = $simplePie->get_language();
		$feed['Channel']['generator']['value'] = 'baserCMS';
		$feed['Items'] = array();

		foreach($datas as $data) {

			$tmp = array();
			$tmp['title']['value'] = $data->get_title();
			$tmp['link']['value'] = $data->get_link();
			$tmp['pubDate']['value'] = date("r",strtotime($data->get_date('Y-m-d H:i:s')));
			$tmp['dc:creator']['value'] = $data->get_author();
			$cat = $data->get_category();
			if($cat) {
				$tmp['category']['value'] = $cat->get_term();
			} else {
				$tmp['category']['value'] = '';
			}
			$tmp['guid']['value'] = $data->get_id();
			$tmp['guid']['attributes']['isPermaLink'] = $data->get_permalink();
			$tmp['description']['value'] = $data->get_description();
			$tmp['wfw:commentRss']['value'] = $data->get_title();

			$tmp['encoded']['value'] = $data->get_content();
			if(preg_match("/(<img.*?src=\"(.*?)\".*?\/>)/s", $tmp['encoded']['value'], $matches)) {
				$tmp['img']['tag'] = $matches[1];
				$tmp['img']['url'] = $matches[2];
			}else {
				$tmp['img']['tag'] = '';
			}

			$feed['Items'][] = $tmp;

		}

		//$feed['Channel']['title']['value'] = $datas['info']['title'];
		//$feed['Channel']['link']['value'] = $datas['info']['link']['alternate'][0];
		/*if(!empty($datas['info']['description']))
			$feed['Channel']['description']['value'] = $datas['info']['description'];
		if(!empty($datas['last-modified'])){
			$feed['Channel']['pubDate']['value'] = $datas['last-modified'];
		}*/
		//$feed['Channel']['generator']['value'] = 'baserCMS';
		/*if(!empty($datas['info']['language']))
			$feed['Channel']['language']['value'] = $datas['info']['language'];*/

		/*if(!empty($datas['items'])){
            foreach($datas['items'] as $data){
                $tmp = array();
                $tmp['title']['value'] = $data->data['title'];
                $tmp['link']['value'] = $data->data['link']['alternate'][0];
                //$tmp['comments']['value'] = '';
                if(!empty($data->data['pubdate'])){
                    $tmp['pubDate']['value'] = date("r",$data->data['pubdate']);
                }elseif(!empty($data->data['dc:date'])){
                    $tmp['pubDate']['value'] = date("r",$data->data['dc:date']);
                }
                if(!empty($data->data['creator'][0]->name))
                    $tmp['dc:creator']['value'] = $data->data['creator'][0]->name;
                if(!empty($data->data['category'][0]))
                    $tmp['category']['value'] = $data->data['category'][0];
                $tmp['guid']['value'] = @$data->data['guid']['data'];
                $tmp['guid']['attributes']['isPermaLink'] = @$data->data['guid']['permalink'];
                if(!empty($data->data['description']))
                    $tmp['description']['value'] = $data->data['description'];
                $tmp['wfw:commentRss']['value'] = $data->data['title'];

				if(!empty($data->data['encoded'])){
					$tmp['encoded']['value'] = $data->data['encoded'];
					if(preg_match("/(<img.*?src=\"(.*?)\".*?\/>)/s", $tmp['encoded']['value'], $matches)){
						$tmp['img']['tag'] = $matches[1];
						$tmp['img']['url'] = $matches[2];
						
					}else{
						$tmp['img']['tag'] = '';
					}
				}
				
				$feed['Items'][] = $tmp;
				
            }
        }else{
            $feed['Items'] = array();
        }*/
		return $feed;

	}
	
}
