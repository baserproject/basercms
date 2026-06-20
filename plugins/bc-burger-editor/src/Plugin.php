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

namespace BcBurgerEditor;

use BaserCore\BcPlugin;
use BaserCore\Model\Table\PagesTable;
use BaserCore\Utility\BcUtil;
use Cake\Database\Driver\Mysql;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

/**
 * Class Plugin
 */
class Plugin extends BcPlugin
{

	/**
	 * インストール
	 *
	 * @param $options
	 * @return bool
	 */
	public function install($options = []): bool
	{
		$result = parent::install($options);
		$this->init();
		return $result;
	}

	/**
	 * 初期化処理
	 *
	 * @return void
	 */
	public function init()
	{
		$filesPath = WWW_ROOT . 'files';
		$savePath = $filesPath . DS . 'bgeditor';
		if (is_writable($filesPath) && !is_dir($savePath)) {
			mkdir($savePath);
			chmod($savePath, 0777);
		}
		if (!is_writable($savePath)) {
			chmod($savePath, 0777);
		}

		$saveImgPath = $savePath . DS . 'img';
		if (!is_dir($saveImgPath)) {
			mkdir($saveImgPath);
			chmod($saveImgPath, 0777);
		}
		if (!is_writable($saveImgPath)) {
			chmod($saveImgPath, 0777);
		}

		$saveOtherPath = $savePath . DS . 'other';
		if (!is_dir($saveOtherPath)) {
			mkdir($saveOtherPath);
			chmod($saveOtherPath, 0777);
		}
		if (!is_writable($saveOtherPath)) {
			chmod($saveOtherPath, 0777);
		}

		// サンプル画像コピー
		$path = $this->getPath();
		$pluginSampleImagePath = $path . 'webroot' . DS . 'img' . DS . 'bg-sample.png';
		copy($pluginSampleImagePath, $savePath . DS . 'bg-sample.png');
		// noimage画像コピー
		$pluginSampleImagePath = $path . 'webroot' . DS . 'img' . DS . 'bg-noimage.gif';
		copy($pluginSampleImagePath, $savePath . DS . 'bg-noimage.gif');
		// サンプルPDFコピー
		$pluginSampleFilePath = $path . 'webroot' . DS . 'img' . DS . 'bg-sample.pdf';
		copy($pluginSampleFilePath, $savePath . DS . 'bg-sample.pdf');

		/**
		 * datasourceがMySQLの場合schemaの利用によって固定ページ、ブログページの本文がtext型となり
		 * 最大文字数2万〜3万文字程度がbgeのプロパティ増加に制限となる可能性があるためmidiamtext型に変更する
		 */
		$db = ConnectionManager::get('default');
		$dbConf = $db->config();

		if ($dbConf['driver'] === Mysql::class) {
			/** @var PagesTable $model */
			$model = TableRegistry::getTableLocator()->get('BaserCore.Pages');
			$tableName = $model->getTable();
			$targetColumns = ['contents', 'draft'];
			$targetType = 'text';
			$modifyType = 'longtext';

			foreach($targetColumns as $targetColumn) {
				$column = $model->getSchema()->getColumn($targetColumn);
				if ($column && $column['type'] === $targetType) {
					$db->execute("ALTER TABLE {$tableName} MODIFY {$targetColumn} {$modifyType}");
				}
			}
			unset($model);

			if(\Cake\Core\Plugin::isLoaded('BcBlog')) {
				$model = TableRegistry::getTableLocator()->get('BcBlog.BlogPosts');
				$tableName = $model->getTable();
				$targetColumns = ['detail', 'detail_draft'];
				$targetType = 'text';
				$modifyType = 'longtext';

				foreach($targetColumns as $targetColumn) {
					$column = $model->getSchema()->getColumn($targetColumn);
					if ($column && $column['type'] === $targetType) {
						$db->execute("ALTER TABLE {$tableName} MODIFY {$targetColumn} {$modifyType}");
					}
				}
				unset($model);
			}
			BcUtil::clearAllCache();
		}
		unset($dbConf);

		// エディタをBurgerEditorに設定
		$siteConfig = TableRegistry::getTableLocator()->get('BaserCore.SiteConfigs');
		$siteConfig->saveKeyValue(['editor' => 'BcBurgerEditor.BurgerEditor']);
		unset($siteConfig);
	}

}
