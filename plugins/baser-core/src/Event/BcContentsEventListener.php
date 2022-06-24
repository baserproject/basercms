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

use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Utility\Inflector;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\Note;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\View\BcAdminAppView;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Service\PermissionsServiceInterface;

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
     * Form Before Create
     *
     * @param Event $event
     * @checked
     * @noTodo
     * @unitTest
     */
    public function formBeforeCreate(Event $event)
    {
        if (!BcUtil::isAdminSystem()) {
            return;
        }
        $options = $event->getData('options');
        if(!is_array($options)) {
            $options = [];
        }
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
        if (!BcUtil::isAdminSystem()) {
            return;
        }
        $View = $event->getSubject();
        if ($event->getData('id') == 'PermissionAdminEditForm') {
            return;
        }
        if (!preg_match('/(AdminEditForm|AdminEditAliasForm)$/', $event->getData('id'))) {
            return;
        }
        return $event->getData('out') . "\n" . $View->element('content_fields');
    }

    /**
     * Form After Submit
     *
     * フォームの保存ボタンの前後に、一覧、プレビュー、削除ボタン、その他のエレメントを配置する
     * プレビューを配置する場合は、コンテンツの設定にて、preview を true にする
     *
     * @param Event $event
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function formAfterSubmit(Event $event)
    {
        $preOut = $event->getData('out');

        if (!BcUtil::isAdminSystem()) {
            return $preOut;
        }
        /**  @var BcAdminAppView $View*/
        $View = $event->getSubject();
        $data = $View->getRequest()->getData();
        if (!preg_match('/(AdminEditForm|AdminEditAliasForm)$/', $event->getData('id'))) {
            return $preOut;
        }
        $content = $data['Contents'] ?? array_column($data, 'content')[0]; // Contentエンティティ or 関連エンティティ
        $setting = Configure::read('BcContents.items.' . $content['plugin'] . '.' . $content['type']);
        $isAvailablePreview = (!empty($setting['preview']) && $content['type'] != 'ContentFolder');
        $path = BcUtil::getPrefix() . "/" . Inflector::dasherize($event->getSubject()->getPlugin()) . '/contents/delete';
        $service = $this->getService(PermissionsServiceInterface::class);
        $checked = false;
        foreach(BcUtil::loginUser()->user_groups as $index => $group) {
            if ($service->check($path, [$index => $group->id])) $checked = true;
        }
        $isAvailableDelete = empty($content['site_root']) && $checked;
        $event->setData('out', implode("\n", [
            $View->element('content_options'),
            $View->element('content_actions', [
                'isAvailablePreview' => $isAvailablePreview,
                'isAvailableDelete' => $isAvailableDelete,
                'currentAction' => $preOut,
                'isAlias' => ($content['alias_id'])
            ]),
            $View->element('content_related'),
            $View->element('content_info')
        ]));
        return $event->getData('out');
    }

}
