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

namespace BcContentLink\Model\Table;

use BaserCore\Model\Table\AppTable;
use Cake\Validation\Validator;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class ContentLinksTable
 *
 * リンク モデル
 */
class ContentLinksTable extends AppTable
{

    /**
     * initialize
     *
     * コンテンツテーブルと連携するための、BcContentsBehavior を追加する
     *
     * @param array $config
     * @checked
     * @noTodo
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('BaserCore.BcContents');
    }

    /**
     * Validation Default
     *
     * バリデーションの設定を行う。
     *
     * - url
     *  - 入力必須
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
        ->numeric('id', __d('baser', 'IDに不正な値が利用されています。'), 'update')
        ->requirePresence('id', 'update');

        $validator
        ->scalar('url')
        ->notEmptyString('url', __d('baser', 'リンク先URLを入力してください。'), 'update');

        return $validator;
    }

}
