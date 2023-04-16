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

namespace BcEditorTemplate\Model\Table;

use BaserCore\Model\Table\AppTable;
use BaserCore\Utility\BcUtil;
use Cake\Validation\Validator;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class EditorTemplate
 *
 * エディタテンプレート　モデル
 */
class EditorTemplatesTable extends AppTable
{

    /**
     * Initialize
     *
     * @param array $config
     * @checked
     * @noTodo
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('editor_templates');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('BaserCore.BcUpload', [
            'saveDir' => "editor",
            'fields' => [
                'image' => [
                    'type' => 'image',
                    'namefield' => 'id',
                    'nameadd' => false,
                    'imageresize' => ['prefix' => 'template', 'width' => '100', 'height' => '100']
                ]
            ]
        ]);
    }

    /**
     * バリデーション設定
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');
        $validator
            ->scalar('name')
            ->maxLength('name', 50, __d('baser_core', 'テンプレート名は50文字以内で入力してください。'))
            ->notEmptyString('name', __d('baser_core', 'テンプレート名を入力してください。'));
        $validator
            ->scalar('description')
            ->maxLength('description', 255, __d('baser_core', '説明文は255文字以内で入力してください。'));
        $validator
            ->allowEmptyString('image')
            ->add('image', [
                'fileCheck' => [
                    'rule' => ['fileCheck', BcUtil::convertSize(ini_get('upload_max_filesize'))],
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'ファイルのアップロード制限を超えています。')
                ]
            ])
            ->add('image', [
                'fileCheck' => [
                    'rule' => ['fileExt', ['gif', 'jpg', 'jpeg', 'jpe', 'jfif', 'png']],
                    'provider' => 'bc',
                    'message' => __d('baser_core', '許可されていないファイルです。')
                ]
            ]);
        return $validator;
    }

}
