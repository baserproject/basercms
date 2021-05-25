<?php
// TODO : コード確認要
use BaserCore\Utility\BcUtil;
use Cake\Event\Event;

return;
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Event
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class BcContentsEventListener
 *
 * baserCMS Contents Event Listener
 *
 * 階層コンテンツと連携したフォーム画面を表示する為のイベント
 * BcContentsComponent でコントロールされる
 *
 * @package Baser.Event
 */
class BcContentsEventListener extends CakeObject implements CakeEventListener
{

    /**
     * Implemented Events
     *
     * @return array
     */
    public function implementedEvents()
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
     */
    public function formBeforeCreate(Event $event)
    {
        if (!BcUtil::isAdminSystem()) {
            return;
        }
        $event->setData('options', ['type' => 'file']);
    }

    /**
     * Form After Create
     *
     * @param Event $event
     * @return string
     */
    public function formAfterCreate(Event $event)
    {
        if (!BcUtil::isAdminSystem()) {
            return;
        }
        $View = $event->getSubject();
        if ($event->getData('id') == 'FavoriteAdminEditForm' || $event->getData('id') == 'PermissionAdminEditForm') {
            return;
        }
        if (!preg_match('/(AdminEditForm|AdminEditAliasForm)$/', $event->getData('id'))) {
            return;
        }
        return $event->getData('out') . "\n" . $View->element('admin/content_fields');
    }

    /**
     * Form After Submit
     *
     * フォームの保存ボタンの前後に、一覧、プレビュー、削除ボタン、その他のエレメントを配置する
     * プレビューを配置する場合は、コンテンツの設定にて、preview を true にする
     *
     * @param Event $event
     * @return string
     */
    public function formAfterSubmit(Event $event)
    {
        if (!BcUtil::isAdminSystem()) {
            return $event->getData('out');
        }
        /* @var BcAppView $View */
        $View = $event->getSubject();
        $data = $View->request->getData();
        if (!preg_match('/(AdminEditForm|AdminEditAliasForm)$/', $event->getData('id'))) {
            return $event->getData('out');
        }
        $setting = Configure::read('BcContents.items.' . $data['Content']['plugin'] . '.' . $data['Content']['type']);

        $PermissionModel = ClassRegistry::init('Permission');
        $isAvailablePreview = (!empty($setting['preview']) && $data['Content']['type'] != 'ContentFolder');
        $isAvailableDelete = (empty($data['Content']['site_root']) && $PermissionModel->check('/' . Configure::read('Routing.prefixes.0') . '/contents/delete', $View->viewVars['user']['user_group_id']));

        $event->setData('out', implode("\n", [
            $View->element('admin/content_options'),
            $View->element('admin/content_actions', [
                'isAvailablePreview' => $isAvailablePreview,
                'isAvailableDelete' => $isAvailableDelete,
                'currentAction' => $event->getData('out'),
                'isAlias' => ($data['Content']['alias_id'])
            ]),
            $View->element('admin/content_related'),
            $View->element('admin/content_info')
        ]));
        return $event->getData('out');
    }

}
