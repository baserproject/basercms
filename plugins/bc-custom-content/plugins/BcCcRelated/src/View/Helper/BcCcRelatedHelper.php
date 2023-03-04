<?php
/**
 * CuCustomField : baserCMS Custom Field Text Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfText.View.Helper
 * @license          MIT LICENSE
 */

namespace BcCcRelated\View\Helper;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\View\Helper\BcAdminFormHelper;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Model\Entity\CustomLink;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use Cake\View\Helper;

/**
 * Class BcCcRelatedHelper
 *
 * @property BcAdminFormHelper $BcAdminForm
 */
class BcCcRelatedHelper extends Helper
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Helper
     * @var string[]
     */
    public $helpers = [
        'BaserCore.BcAdminForm' => ['templates' => 'BaserCore.bc_form']
    ];

    /**
     * テーブルリストを取得する
     * @return array
     */
    public function getTableList()
    {
        /** @var CustomTablesServiceInterface $tablesService */
        $tablesService = $this->getService(CustomTablesServiceInterface::class);
        return $tablesService->getList(['type' => 2]);
    }

    /**
     * control
     *
     * @param string $fieldName
     * @param CustomField $field
     * @param array $options
     * @return string
     */
    public function control(CustomLink $link, array $options = []): string
    {
        $field = $link->custom_field;
        if(empty($field->meta['BcCcRelated']['custom_table_id'])) return '';
        $filterName = $field->meta['BcCcRelated']['filter_name'];
        $filterValue = $field->meta['BcCcRelated']['filter_value'];
        $conditions = [];
        if($filterName && $filterValue) {
            $conditions['conditions'] = ['CustomEntries.' . $filterName => $filterValue];
        }

        /** @var CustomEntriesServiceInterface $entriesService */
        $entriesService = $this->getService(CustomEntriesServiceInterface::class);
        $currentTableId = $entriesService->CustomEntries->tableId;
        $entriesService->setup($field->meta['BcCcRelated']['custom_table_id']);

        $options = array_merge([
            'type' => 'select',
            'options' => $entriesService->getList($conditions),
            'empty' => __d('baser_core', '選択してください'),
        ], $options);

        // プレビューの場合はテーブルIDが存在しない
        if($currentTableId) {
            $entriesService->setup($currentTableId);
        }
        return $this->BcAdminForm->control($link->name, $options);
    }

    /**
     * プレビュー
     *
     * @param CustomLink $link
     * @return string
     */
    public function preview(CustomLink $link)
    {
        $options = [
            ':value' => 'entity.default_value'
        ];
        return $this->control($link, $options) . '<br>※ 関連データはリアルタイムでのプレビューは未対応です。保存してから確認してください。';
    }

    /**
     * Search Control
     * @param string $fieldName
     * @param CustomField $field
     * @param array $options
     * @return string
     */
    public function searchControl(CustomLink $link, array $options = []): string
    {
        return $this->control($link, $options);
    }

    /**
     * Get
     *
     * @param mixed $fieldValue
     * @param CustomLink $link
     * @param array $options
     * @return mixed
     */
    public function get($fieldValue, CustomLink $link, array $options = [])
    {
        if(!$fieldValue) return '';
        /** @var CustomEntriesServiceInterface $entriesService */
        $entriesService = $this->getService(CustomEntriesServiceInterface::class);
        $entriesService->setup($link->custom_field->meta['BcCcRelated']['custom_table_id']);
        $entry = $entriesService->get($fieldValue);
        return $entry->{$entry->custom_table->display_field};
    }

}
