<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.0
 * @license       https://basercms.net/license/index.html MIT License
 */

use BaserCore\Service\PluginsServiceInterface;
use BaserCore\Utility\BcContainer;
use Cake\ORM\TableRegistry;
use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Utility\BcUpdateLog;
use Cake\Core\Configure;

try {
    // Seoプラグイン有効化
    $pluginsService = BcContainer::get()->get(PluginsServiceInterface::class);
    $pluginsService->install('BcSeo');
    Configure::load('BcSeo.setting');

    // SeoMetasテーブル存在確認
    $seoMetasTable = TableRegistry::getTableLocator()->get('BcSeo.SeoMetas');
    $seoMetasTableName = $seoMetasTable->getTable();
    $bcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);
    if (!$bcDatabaseService->columnExists($seoMetasTableName, 'id')) {
        throw new Exception();
    }

    // Sites description, keywords 移行
    $sitesTable = TableRegistry::getTableLocator()->get('BaserCore.Sites');
    $sitesTableName = $sitesTable->getTable();
    if ($bcDatabaseService->columnExists($sitesTableName, 'description') &&
        $bcDatabaseService->columnExists($sitesTableName, 'keyword')
    ) {
        $sites = $sitesTable->find()
            ->where([
                'OR' => [
                    'Sites.keyword !=' => '',
                    'Sites.description !=' => '',
                ]
            ])
            ->all();
        foreach ($sites as $site) {
            if ($seoMetasTable->exists(['table_alias' => 'Sites', 'entity_id' => $site->id])) {
                continue;
            }
            $entity = $seoMetasTable->newEntity([
                'table_alias' => 'Sites',
                'entity_id' => $site->id,
                'description' => $site->description,
                'keywords' => $site->keyword,
            ]);
            $seoMetasTable->saveOrFail($entity);
        }

        // Sites description, keywords カラム削除
        $bcDatabaseService->removeColumn($sitesTableName, 'description');
        $bcDatabaseService->removeColumn($sitesTableName, 'keyword');
    }

    // Contents description 移行
    $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
    $contentsTableName = $contentsTable->getTable();
    if ($bcDatabaseService->columnExists($contentsTableName, 'description')) {
        $contents = $contentsTable->find()
            ->where(['Contents.description !=' => ''])
            ->all();
        foreach ($contents as $content) {
            if ($seoMetasTable->exists(['table_alias' => 'Contents', 'entity_id' => $content->id])) {
                continue;
            }
            $entity = $seoMetasTable->newEntity([
                'table_alias' => 'Contents',
                'entity_id' => $content->id,
                'description' => $content->description,
            ]);
            $seoMetasTable->saveOrFail($entity);
        }

        // Contents description カラム削除
        $bcDatabaseService->removeColumn($contentsTableName, 'description');
    }
} catch (Exception $e) {
    BcUpdateLog::set(__d('baser_core', 'サイト・コンテンツの説明文・キーワードのBcSeoプラグインへの移行が失敗しました。'));
    return;
}
