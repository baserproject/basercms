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
use BaserCore\Model\Entity\Content;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Model\Entity\CustomTable;
use BcCustomContent\Service\CustomEntriesService;
use BcCustomContent\Service\CustomTablesService;
use BcCustomContent\Service\CustomTablesServiceInterface;
use Cake\Datasource\EntityInterface;
use Cake\ORM\ResultSet;

/**
 * CustomEntriesAdminService
 */
class CustomEntriesAdminService extends CustomEntriesService implements CustomEntriesAdminServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * カスタムエントリー一覧用の View 変数を取得する
     *
     * @param EntityInterface $table
     * @param ResultSet|array $entities
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForIndex(EntityInterface $table, $entities): array
    {
        return [
            'tableId' => $table->id,
            'customTable' => $table,
            'entities' => $entities,
            'publishLink' => $this->getPublishLinkForIndex($table)
        ];
    }

    /**
     * カスタムテーブルを通常一覧の関連フィールドと一緒に取得する
     *
     * ※ ツリー構造ではない一覧を指す
     *
     * @param int $tableId
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTableWithLinksByAll(int $tableId) {
        /** @var CustomTablesService $customTables */
        $customTables = $this->getService(CustomTablesServiceInterface::class);
        // finder を threaded から all に変更
        $customTables->CustomTables->setHasManyLinksByAll();
        return $customTables->getWithLinks($tableId);
    }

    /**
     * カスタムエントリーの新規追加用の View 変数を取得する
     *
     * @param int $tableId
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForAdd(int $tableId, EntityInterface $entity): array
    {
        /** @var CustomTablesService $customTables */
        $customTables = $this->getService(CustomTablesServiceInterface::class);
        if ($customTables->hasCustomContent($tableId)) {
            $customTable = $customTables->getWithContentAndLinks($tableId);
            $availablePreview = true;
        } else {
            $customTable = $customTables->getWithLinks($tableId);
            $availablePreview = false;
        }
        $entryUrl = null;
        if($customTable->isContentTable()) {
            $entryUrl = $this->getUrl($customTable->custom_content->content, $entity);
        }
        return [
            'entity' => $entity,
            'tableId' => $tableId,
            'customTable' => $customTable,
            'availablePreview' => $availablePreview,
            'entryUrl' => $entryUrl,
        ];
    }

    /**
     * カスタムエントリーの編集画面用の View 変数を取得する
     *
     * @param int $tableId
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForEdit(int $tableId, EntityInterface $entity): array
    {
        /** @var CustomTablesService $customTables */
        $customTables = $this->getService(CustomTablesServiceInterface::class);
        /** @var CustomTable $customTable */
        if ($customTables->hasCustomContent($tableId)) {
            $customTable = $customTables->getWithContentAndLinks($tableId);
        } else {
            $customTable = $customTables->getWithLinks($tableId);
        }
        $publishLink = $entryUrl = null;
        $availablePreview = false;
        if($customTable->isContentTable()) {
            $publishLink = $this->getPublishLinkForEdit($customTable->custom_content->content, $entity);
            $availablePreview = true;
            $entryUrl = $this->getUrl($customTable->custom_content->content, $entity);
        }
        return [
            'entity' => $entity,
            'tableId' => $tableId,
            'customTable' => $customTable,
            'publishLink' => $publishLink,
            'availablePreview' => $availablePreview,
            'entryUrl' => $entryUrl
        ];
    }

    /**
     * カスタムエントリー一覧画面用の公開ページのリンクを取得する
     *
     * @param EntityInterface $table
     * @return string|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getPublishLinkForIndex(EntityInterface $table)
    {
        $publishLink = null;
        /** @var CustomTable $table */
        if($table->isContentTable()) {
            $content = $table->custom_content->content;
            /** @var ContentsServiceInterface $contentsService */
            $contentsService = $this->getService(ContentsServiceInterface::class);
            $publishLink = $contentsService->isAllowPublish($content)? $contentsService->getUrl($content->url, false, $content->site->use_subdomain) : null;
        }
        return $publishLink;
    }

    /**
     * カスタムエントリー編集画面用の公開ページのリンクを取得する
     *
     * @param Content $content
     * @param EntityInterface $entry
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getPublishLinkForEdit(Content $content, EntityInterface $entity)
    {
        if (!$this->isAllowPublish($entity)) return '';
        return $this->getUrl($content, $entity);
    }

}
