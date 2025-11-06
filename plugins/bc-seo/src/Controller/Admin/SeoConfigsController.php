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

namespace BcSeo\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * SeoConfigsController
 */
class SeoConfigsController extends BcAdminAppController
{
    use BcContainerTrait;

    /**
     * DBテーブルに存在しない項目のカラムを追加
     */
    public function update_db()
    {
        if ($this->getRequest()->is('post')) {
            $bcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);
            $seoMetasTable = TableRegistry::getTableLocator()->get('BcSeo.SeoMetas');
            $tableName = $seoMetasTable->getTable();

            $seoFields = Configure::read('BcSeo.fields');
            $addColumns = [];
            foreach ($seoFields as $fieldName => $seoField) {
                if (!$bcDatabaseService->columnExists($tableName, $fieldName)) {
                    $bcDatabaseService->addColumn($tableName, $fieldName, 'string', [
                        'comment' => $seoField['title'] ?? '',
                    ]);
                    $addColumns[] = $fieldName;
                }
            }
            $this->set(compact('addColumns'));
        }
    }
}
