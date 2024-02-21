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
namespace BcCcFile\Utility;

use BcCustomContent\Model\Entity\CustomLink;
use BcCustomContent\Model\Table\CustomEntriesTable;
use BcCustomContent\Model\Table\CustomLinksTable;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\NoTodo;

/**
 * Class BcCcFileUtil
 */
class BcCcFileUtil
{

    /**
     * アップローダーの準備を行う
     *
     * @param int $tableId
     * @checked
     * @noTodo
     */
    public static function setupUploader(int $tableId)
    {
        /** @var CustomLinksTable $linksTable */
        $linksTable = TableRegistry::getTableLocator()->get('BcCustomContent.CustomLinks');
        $links = $linksTable->find()
            ->contain(['CustomFields'])
            ->where([
                'CustomLinks.custom_table_id' => $tableId,
                'CustomFields.status' => true
            ])->all()->toArray();
        if(!$links) return;

        $fields = [];
        foreach($links as $link) {
            /** @var CustomLink $link */
            if($link->custom_field->type === 'BcCcFile') {
                $fields[$link->name] = [
                    'type' => 'all',
                    'namefield' => 'id',
                    'nameformat' => '%08d',
                    'imageresize' => ['width' => 1000, 'height' => 1000],
                    'imagecopy' => [
                        'thumb' => ['suffix' => '_thumb', 'width' => 300, 'height' => 300]
                    ]
                ];
            }
        }

        if(!$fields) return;

		$config = [
			'saveDir' => 'bc_custom_content' . DS . $tableId . DS . 'custom_entries',
			'subdirDateFormat' => 'Y/m/',
			'fields' => $fields,
			'getUniqueFileName' => 'getUniqueFileName'
		];

        /** @var CustomEntriesTable $entriesTable */
        $entriesTable = TableRegistry::getTableLocator()->get('BcCustomContent.CustomEntries');
        $entriesTable->addBehavior('BaserCore.BcUpload', $config);
    }

}
