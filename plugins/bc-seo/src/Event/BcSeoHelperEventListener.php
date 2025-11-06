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

namespace BcSeo\Event;

use BaserCore\Event\BcHelperEventListener;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\View\Form\EntityContext;
use Cake\Utility\Hash;

/**
 * Class BcSeoHelperEventListener
 */
class BcSeoHelperEventListener extends BcHelperEventListener
{
    public $events = [
        'Form.beforeCreate',
        'BcFormTable.after',
    ];

    /**
     * formBeforeCreate
     */
    public function formBeforeCreate(EventInterface $event)
    {
        if (!BcUtil::isAdminSystem()) {
            return;
        }

        // ファイルアップロードのために multipart/form-data 指定を追加
        // ブログカテゴリ・サイトのフォームで必要
        $seoForms = Configure::read('BcSeo.seoForms');
        $eventIds = Hash::flatten($seoForms, '{s}.eventIds');
        if (!in_array($event->getData('id'), $eventIds)) {
            return;
        }

        $options = $event->getData('options');
        $options['enctype'] = 'multipart/form-data';
        $event->setData('options', $options);
    }

    /**
     * bcFormTableAfter
     */
    public function bcFormTableAfter(EventInterface $event) {
        if (!BcUtil::isAdminSystem()) {
            return;
        }

        // 編集欄追加
        $eventId = $event->getData('id');
        $View = $event->getSubject();

        $seoForms = Configure::read('BcSeo.seoForms');

        $request = $View->getRequest();
        $controller = $request->getParam('controller');
        $action = $request->getParam('action');

        // エイリアス
        if ($controller === 'Contents' && $action === 'edit_alias') {
            $content = $View->get('content');
            $type = Inflector::variable($content->type);
            if (!isset($seoForms[$type])) {
                return;
            }
        // 他
        } else {
            foreach ($seoForms as $configType => $seoForm) {
                foreach ($seoForm['eventIds'] as $configEventId) {
                    if ($eventId === $configEventId) {
                        $type = $configType;
                        break;
                    }
                }
            }
        }

        if (empty($type)) {
            return;
        }

        $context = $event->getSubject()->BcAdminForm->context();
        if (!$context instanceof EntityContext) {
            return;
        }

        // テーブル情報取得
        $repository = $context->entity()->getSource();
        $table = TableRegistry::getTableLocator()->get($repository);
        if ($table->hasBehavior('BcContents')) {
            $table = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        }
        $tableAlias = $table->getAlias();
        $tableId = ($tableAlias === 'CustomEntries') ? $table->tableId : 0;

        // 表示する項目取得
        $seoFields = Configure::read('BcSeo.fields');
        $seoFields = array_filter($seoFields, function ($field) use ($type) {
            return (empty($field['ignoreTypes']) || !in_array($type, $field['ignoreTypes']));
        });

        $View->BcBaser->element('BcSeo.seo_form', [
            'tableAlias' => $tableAlias,
            'tableId' => $tableId,
            'seoFields' => $seoFields,
        ]);
    }
}
