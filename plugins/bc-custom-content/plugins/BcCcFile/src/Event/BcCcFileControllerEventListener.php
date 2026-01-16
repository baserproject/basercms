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
use BcCcFile\Utility\BcCcFileUtil;
use Cake\Controller\Controller;
use Cake\Event\EventInterface;
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
        $tableId = $request->getParam('pass.0');
        if (!$tableId) $tableId = $request->getQuery('custom_table_id');
        BcCcFileUtil::setupUploader((int) $tableId);
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
        if(in_array($this->getAction(false), ['Index', 'Archives', 'Year'])) {
            $table = $controller->viewBuilder()->getVar('customTable');
            if(!$table) throw new BcException(__d('baser_core', 'ビュー変数 $customTable がセットされていません。'));
            BcCcFileUtil::setupUploader($table->id);
        } elseif($this->isAction('View', false)) {
            $entry = $controller->viewBuilder()->getVar('customEntry');
            if(!$entry) throw new BcException(__d('baser_core', 'ビュー変数 $customEntry がセットされていません。'));
            BcCcFileUtil::setupUploader($entry->custom_table_id);
        }
    }

}
