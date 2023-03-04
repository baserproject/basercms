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

namespace BcCustomContent\Model\Table;

use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Model\Table\AppTable;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Validation\Validator;

/**
 * CustomTablesTable
 *
 * @property CustomLinksTable $CustomLinks
 * @property CustomContentsTable $CustomContents
 */
class CustomTablesTable extends AppTable
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;

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
        $this->addBehavior('Timestamp');

        // 関連エントリーはテーブルごと削除するので setDependent は実行しない
        $this->hasMany('CustomEntries')
            ->setClassName('BcCustomContent.CustomEntries')
            ->setForeignKey('custom_table_id')
            ->setDependent(false);
        $this->hasOne('CustomContents')
            ->setClassName('BcCustomContent.CustomContents')
            ->setForeignKey('custom_table_id')
            ->setDependent(false);
        $this->setHasManyLinksByThreaded();
    }

    /**
     * ツリー構造形式の関連フィールドを hasMany で設定する
     */
    public function setHasManyLinksByThreaded()
    {
        // 関連フィールドは削除される
        $this->hasMany('CustomLinks')
            ->setClassName('BcCustomContent.CustomLinks')
            ->setForeignKey('custom_table_id')
            ->setSort(['CustomLinks.lft' => 'ASC'])
            ->setFinder('threaded')
            ->setDependent(true);
    }

    /**
     * ツリー構造形式ではない通常一覧の関連フィールドを hasMany で設定する
     */
    public function setHasManyLinksByAll()
    {
        $this->hasMany('CustomLinks')
            ->setClassName('BcCustomContent.CustomLinks')
            ->setForeignKey('custom_table_id')
            ->setSort(['CustomLinks.lft' => 'ASC'])
            ->setFinder('all')
            ->setDependent(true);
    }

    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 255, __d('baser_core', '255文字以内で入力してください。'))
            ->notEmptyString('name', __d('baser_core', '識別名を入力してください。'))
            ->regex('name', '/^[a-z0-9_]+$/', __d('baser_core', '識別名は半角英数字とアンダースコアのみで入力してください。'))
            ->add('name', [[
                'rule' => ['validateUnique'],
                'provider' => 'table',
                'message' => __d('baser_core', '既に登録のある識別名です。')
            ]]);
        $validator
            ->scalar('title')
            ->maxLength('title', 255, __d('baser_core', '255文字以内で入力してください。'))
            ->notEmptyString('title', __d('baser_core', 'タイトルを入力してください。'));

        return $validator;
    }

}
