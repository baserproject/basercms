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

namespace BcCustomContent\Service\Admin;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Model\Entity\CustomLink;
use BcCustomContent\Service\CustomFieldsService;
use BcCustomContent\Service\CustomFieldsServiceInterface;
use BcCustomContent\Service\CustomLinksService;
use BcCustomContent\Service\CustomLinksServiceInterface;
use BcCustomContent\Service\CustomTablesService;
use Cake\Datasource\EntityInterface;
use Cake\Utility\Hash;

/**
 * CustomTablesAdminService
 */
class CustomTablesAdminService extends CustomTablesService implements CustomTablesAdminServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * コンテンツテーブルの編集画面用の View 変数を取得する
     *
     * @param EntityInterface $entity
     * @return array
     */
    public function getViewVarsForEdit(EntityInterface $entity)
    {
        /** @var CustomFieldsService $fieldsService */
        $fieldsService = $this->getService(CustomFieldsServiceInterface::class);
        $fields = $fieldsService->getIndex(['status' => true]);

        /** @var CustomLinksService $linksService */
        $linksService = $this->getService(CustomLinksServiceInterface::class);
        $flatLinks = $linksService->getIndex($entity->id, [
            'finder' => 'all',
            'status' => 'all',
            'contain' => ['CustomFields']
        ])->all()->toArray();
        $flatLinks = array_combine(Hash::extract($flatLinks, '{n}.id'), array_values($flatLinks));
        /** @var CustomLink $entity */
        return [
            'fields' => $fields,
            'customLinks' => $entity->custom_links,
            'flatLinks' => $flatLinks,
            'entity' => $entity
        ];
    }

}
