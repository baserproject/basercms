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

namespace BaserCore\Event;

use BaserCore\Service\Admin\ContentsAdminServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BaserCore\View\BcAdminAppView;
use Cake\Event\Event;
use BaserCore\Annotation\Note;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcContentsEventListener
 *
 * baserCMS Contents Event Listeners
 *
 * 階層コンテンツと連携したフォーム画面を表示する為のイベント
 * BcContentsComponent でコントロールされる
 *
 * @package Baser.Event
 */
class BcContentsEventListener extends BcEventListener
{

    /**
     * BcContainerTrait
     */
    use BcContainerTrait;

    /**
     * Implemented Events
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function implementedEvents(): array
    {
        return [
            'Helper.Form.beforeCreate' => ['callable' => 'formBeforeCreate'],
            'Helper.Form.afterCreate' => ['callable' => 'formAfterCreate'],
            'Helper.Form.afterSubmit' => ['callable' => 'formAfterSubmit']
        ];
    }

    /**
     * エンティティの変数名
     * @var string
     */
    protected $entityVarName;

    /**
     * Constructor
     * @checked
     * @noTodo
     */
    public function __construct($entityVarName)
    {
        parent::__construct();
        $this->entityVarName = $entityVarName;
    }

    /**
     * Form Before Create
     *
     * @param Event $event
     * @checked
     * @noTodo
     * @unitTest
     */
    public function formBeforeCreate(Event $event)
    {
        if (!BcUtil::isAdminSystem()) return;
        if ($event->getData('id') === 'PermissionAjaxAddForm') return;
        $options = $event->getData('options');
        if(!is_array($options)) $options = [];
        $options += ['type' => 'file'];
        $event->setData('options', $options);
    }

    /**
     * Form After Create
     *
     * @param Event $event
     * @return string|void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function formAfterCreate(Event $event)
    {
        if (!BcUtil::isAdminSystem()) return;
        if ($event->getData('id') === 'PermissionAjaxAddForm') return;
        if (!preg_match('/(AdminEditForm|AdminEditAliasForm)$/', $event->getData('id'))) return;
        $View = $event->getSubject();
        return $event->getData('out') . "\n" . $View->element('content_fields');
    }

    /**
     * Form After Submit
     *
     * フォームの保存ボタンの前後に、一覧、プレビュー、削除ボタン、その他のエレメントを配置する
     * プレビューを配置する場合は、コンテンツの設定にて、preview を true にする
     *
     * @param Event $event
     * @return void|string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function formAfterSubmit(Event $event)
    {
        if (!BcUtil::isAdminSystem()) return;
        if (!preg_match('/(AdminEditForm|AdminEditAliasForm)$/', $event->getData('id'))) return;
        /**  @var BcAdminAppView $view*/
        $view = $event->getSubject();
        $entity = $view->get($this->entityVarName);
        $adminService = $this->getService(ContentsAdminServiceInterface::class);
        $event->setData('out', implode("\n", [
            $view->element('content_options'),
            $view->element('content_actions', $adminService->getViewVarsForContentActions($entity->content, $event->getData('out'))),
            $view->element('content_related'),
            $view->element('content_info')
        ]));
    }

}
