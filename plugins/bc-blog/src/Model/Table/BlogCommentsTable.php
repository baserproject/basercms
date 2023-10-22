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

namespace BcBlog\Model\Table;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Validation\Validator;

/**
 * ブログコメントモデル
 *
 * @property BlogPostsTable $BlogPosts
 * @property BlogContentsTable $BlogContents
 */
class BlogCommentsTable extends BlogAppTable
{

    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->notEmptyString('name', __d('baser_core', 'お名前を入力してください。'))
            ->maxLength('name', 50, __d('baser_core', 'お名前は50文字以内で入力してください。'));
        $validator
            ->scalar('email')
            ->allowEmptyString('email')
            ->email('email', false, __d('baser_core', 'Eメールの形式が不正です。'))
            ->maxLength('email', 255, __d('baser_core', 'Eメールは255文字以内で入力してください。'));
        $validator
            ->scalar('url')
            ->allowEmptyString('url')
            ->url('url', __d('baser_core', 'URLの形式が不正です。'))
            ->maxLength('url', 255, __d('baser_core', 'URLは255文字以内で入力してください。'));
        $validator
            ->scalar('message')
            ->notEmptyString('message', __d('baser_core', 'コメントを入力してください。'));
        return $validator;
    }

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('blog_comments');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('BlogPosts', [
            'className' => 'BcBlog.BlogPosts',
            'foreignKey' => 'blog_post_id',
        ]);
        $this->belongsTo('BlogContents', [
            'className' => 'BcBlog.BlogContents',
            'foreignKey' => 'blog_content_id',
        ]);
    }

}
