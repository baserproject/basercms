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

namespace BcCcFile\Event;

use ArrayObject;
use BaserCore\Error\BcException;
use BaserCore\Event\BcControllerEventListener;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Model\Entity\CustomLink;
use BcCustomContent\Model\Table\CustomEntriesTable;
use BcCustomContent\Model\Table\CustomLinksTable;
use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcCcFileControllerEventListener
 */
class BcCcFileControllerEventListener extends BcControllerEventListener
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Event
     *
     * @var string[]
     */
    public $events = [
        'BcCustomContent.CustomEntries.startup',
        'BcCustomContent.CustomContent.beforeRender'
    ];

    /**
     * BcCustomContent CustomEntries Startup
     *
     * @param EventInterface $event
     * @param ArrayObject $content
     * @param ArrayObject $options
     */
    public function bcCustomContentCustomEntriesStartup(EventInterface $event)
    {
        $request = $event->getSubject()->getRequest();
        if(!$this->isAction('Add', false) && !$this->isAction('Edit', false)) return;
        $tableId = $request->getParam('pass.0');
        $this->setupUploader($tableId);
    }

    /**
     * BcCustomContent CustomEntries Before Render
     *
     * @param EventInterface $event
     */
    public function bcCustomContentCustomContentBeforeRender(EventInterface $event)
    {
        /** @var Controller $controller */
        $controller = $event->getSubject();
        if($this->isAction('Index', false)) {
            $table = $controller->viewBuilder()->getVar('customTable');
            if(!$table) throw new BcException(__d('baser', 'ビュー変数 $customTable がセットされていません。'));
            $this->setupUploader($table->id);
        } elseif($this->isAction('View', false)) {
            $entry = $controller->viewBuilder()->getVar('customEntry');
            if(!$entry) throw new BcException(__d('baser', 'ビュー変数 $customEntry がセットされていません。'));
            $this->setupUploader($entry->custom_table_id);
        }
    }

    /**
     * アップローダーの準備を行う
     *
     * @param int $tableId
     */
    public function setupUploader(int $tableId)
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
