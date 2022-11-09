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

/**
 * ブログコメントモデル
 *
 * @package Blog.Model
 */
class BlogCommentsTable extends BlogAppTable
{

    /**
     * BlogComment constructor.
     *
     * @param bool $id
     * @param null $table
     * @param null $ds
     * TODO ucmitz 一旦、コメントアウト
     */
//    public function __construct($id = false, $table = null, $ds = null)
//    {
//        parent::__construct($id, $table, $ds);
//        $this->validate = [
//            'name' => [
//                ['rule' => ['notBlank'], 'message' => __d('baser', 'お名前を入力してください。')],
//                ['rule' => ['maxLength', 50], 'message' => __d('baser', 'お名前は50文字以内で入力してください。')]
//            ],
//            'email' => [
//                'email' => ['rule' => ['email'], 'message' => __d('baser', 'Eメールの形式が不正です。'), 'allowEmpty' => true],
//                'maxLength' => ['rule' => ['maxLength', 255], 'message' => __d('baser', 'Eメールは255文字以内で入力してください。')]
//            ],
//            'url' => [
//                'url' => ['rule' => ['url'], 'message' => __d('baser', 'URLの形式が不正です。'), 'allowEmpty' => true],
//                'maxLength' => ['rule' => ['maxLength', 255], 'message' => __d('baser', 'URLは255文字以内で入力してください。')]
//            ],
//            'message' => [
//                ['rule' => ['notBlank'], 'message' => __d('baser', 'コメントを入力してください。')]
//            ]
//        ];
//    }


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
    }
    /**
     * 初期値を取得する
     *
     * @return array 初期値データ
     */
    public function getDefaultValue()
    {
        $data[$this->name]['name'] = 'NO NAME';
        return $data;
    }

    /**
     * コメントを追加する
     * @param array $data
     * @param string $contentId
     * @param string $postId
     * @param string $commentApprove
     * @return boolean
     */
    public function add($data, $contentId, $postId, $commentApprove)
    {
        if (isset($data['BlogComment'])) {
            $data = $data['BlogComment'];
        }

        // Modelのバリデートに引っかからないための対処
        $data['url'] = str_replace('&#45;', '-', $data['url']);
        $data['email'] = str_replace('&#45;', '-', $data['email']);

        $data['blog_post_id'] = $postId;
        $data['blog_content_id'] = $contentId;

        if ($commentApprove) {
            $data['status'] = false;
        } else {
            $data['status'] = true;
        }

        $data['no'] = $this->getMax('no', ['blog_content_id' => $contentId]) + 1;
        $this->create($data);

        return $this->save();
    }
}
