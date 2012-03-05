<?php
/* SVN FILE: $Id$ */
/**
 * 1.6.14 バージョン アップデートスクリプト
 *
 * ----------------------------------------
 * 　アップデートの仕様について
 * ----------------------------------------
 * アップデートスクリプトや、スキーマファイルの仕様については
 * 次のファイルに記載されいているコメントを参考にしてください。
 *
 * /baser/controllers/updaters_controller.php
 *
 * スキーマ変更後、モデルを利用してデータの更新を行う場合は、
 * ClassRegistry を利用せず、モデルクラスを直接イニシャライズしないと、
 * スキーマのキャッシュが古いままとなるので注意が必要です。
 *
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.config.update
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * スキーマの読み込み
 *
 * contents / pages テーブルの更新
 */
	if(!$this->loadSchema('1.6.14')){
		$this->setMessage('contents / pages テーブルの構造変更に失敗しました。', true);
	} else {
		$this->setMessage('contens / pages テーブルの構造変更に成功しました。');
	}
/**
 * site_configs データ更新
 */
	App::import('Model', 'SiteConfig');
	$SiteConfig = new SiteConfig();
	$siteConfig = $SiteConfig->findExpanded();
	$siteConfig['content_types'] = '';
	if($SiteConfig->saveKeyValue($siteConfig)) {
		$this->setMessage('site_configs テーブルのデータ更新に成功しました。');
	} else {
		$this->setMessage('site_configs テーブルのデータ更新に失敗しました。', true);
	}
/**
 * contents データ更新
 */
	$result = true;
	App::import('Model', 'Content');
	$Content = new Content();
	$contents = $Content->find('all', array('cache' => false));
	if($contents) {
		foreach($contents as $content) {
			$content['Content']['priority'] = '0.5';
			switch ($content['Content']['model']) {
				case 'Page':
					$type = 'ページ';
					break;
				case 'BlogPost':
					$type = 'ブログ';
					break;
				default:
					$type = '';
			}
			$content['Content']['type'] = $type;
			$Content->set($content);
			if(!$Content->save()) {
				$result = false;
			}
		}
		if($result) {
			$this->setMessage('contents テーブルのデータ更新に成功しました。');
		} else {
			$this->setMessage('contents テーブルのデータ更新に失敗しました。', true);
		}
	}
/**
 * page_categories データ更新
 */
	App::import('Model', 'PageCategory');
	$PageCategory = new PageCategory();
	$data = array('PageCategory' => array(
		'name'		=> 'smartphone',
		'title'		=> 'スマートフォン',
		'created'	=> date('Y-m-d H:i:s')
	));
	$PageCategory->create($data);
	if($PageCategory->save()) {
		$this->setMessage('page_categories テーブルのデータ更新に成功しました。');
	} else {
		$this->setMessage('page_categories テーブルのデータ更新に失敗しました。', true);
	}