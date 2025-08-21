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

namespace BcSeo\Model\Table;

use BaserCore\Model\Table\AppTable;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Routing\Router;
use Cake\Validation\Validator;

/**
 * Class SeoMetasTable
 */
class SeoMetasTable extends AppTable
{
    /**
     * initialize
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->addBehavior('Timestamp');

        $seoFields = Configure::read('BcSeo.fields');
        if (!$seoFields) {
            return;
        }

        $bcUploadFields = [];
        foreach ($seoFields as $fieldName => $seoField) {
            if ($seoField['type'] === 'file') {
                $bcUploadFields[$fieldName] = [
                    'type' => 'image',
                    'namefield' => 'id',
                    'nameformat' => '%08d',
                ];
            }
        }

        if ($bcUploadFields) {
            $this->addBehavior('BaserCore.BcUpload', [
                'subdirDateFormat' => 'Y/',
                'saveDir' => 'seo_metas',
                'fields' => $bcUploadFields,
            ]);
        }
    }

    /**
     * validationDefault
     */
    public function validationDefault(Validator $validator): Validator
    {
        $seoFields = Configure::read('BcSeo.fields');
        foreach ($seoFields as $fieldName => $seoField) {
            if ($seoField['type'] === 'file') {
                $validator
                    ->add($fieldName, [
                        'fileExt' => [
                            'rule' => ['fileExt', ['gif', 'jpg', 'jpeg', 'png']],
                            'provider' => 'bc',
                            'message' => __d('baser_core', '画像を選択してください。'),
                            'last' => true,
                        ],
                    ]);
            } elseif (in_array($seoField['type'], ['text', 'textarea'])) {
                $validator
                    ->allowEmptyString($fieldName)
                    ->scalar($fieldName)
                    ->maxLength($fieldName, 255, __d('baser_core', '{0}文字以内で入力してください。', 255));
            }
        }

        return $validator;
    }

    /**
     * afterSave
     */
    public function afterSave(EventInterface $event, EntityInterface $entity)
    {
        $request = Router::getRequest();
        if (!$request || $request->getParam('action') !== 'copy' || $entity->_bcSeoCopied) {
            return;
        }
        // ファイル複製
        $entity->set('_bcSeoCopied', true);
        $this->renameToBasenameFields($entity, true);
        $this->save($entity);
    }
}
