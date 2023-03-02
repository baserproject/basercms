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

namespace BcMail\Model\Table;

use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Model\Entity\Content;
use BaserCore\Utility\BcContainerTrait;
use BcMail\Service\MailMessagesServiceInterface;
use Cake\Core\Plugin;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\Validation\Validator;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * メールコンテンツモデル
 *
 * @property MailFieldsTable $MailFields
 */
class MailContentsTable extends MailAppTable
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;
    use BcContainerTrait;

    /**
     * behaviors
     *
     * @var array
     */
    public $actsAs = ['BcSearchIndexManager', 'BcCache', 'BcContents'];

    /**
     * hasMany
     *
     * @var array
     */
    public $hasMany = ['MailField' =>
    [
        'className' => 'BcMail.MailField',
        'order' => 'sort',
        'foreignKey' => 'mail_content_id',
        'dependent' => true,
        'exclusive' => false,
        'finderQuery' => ''
    ]];

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('mail_contents');
        $this->setPrimaryKey('id');
        $this->hasMany('MailFields', [
            'className' => 'BcMail.MailFields',
            'order' => 'sort',
            'foreignKey' => 'mail_content_id',
            'dependent' => true
        ]);
        $this->addBehavior('Timestamp');
        $this->addBehavior('BaserCore.BcContents');
        if (Plugin::isLoaded('BcSearchIndex')) {
            $this->addBehavior('BcSearchIndex.BcSearchIndexManager');
        }
    }

    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        // TODO ucmitz 未実装
        return $validator;

        $this->validate = [
            'id' => [
                [
                    'rule' => 'numeric',
                    'on' => 'update',
                    'message' => __d('baser', 'IDに不正な値が利用されています。')
                ]
            ],
            'sender_name' => [
                [
                    'rule' => ['notBlank'],
                    'message' => __d('baser', '送信先名を入力してください。')
                ],
                [
                    'rule' => ['maxLength', 255],
                    'message' => __d('baser', '送信先名は255文字以内で入力してください。')
                ]
            ],
            'subject_user' => [
                [
                    'rule' => ['notBlank'],
                    'message' => __d('baser', '自動返信メール件名[ユーザー宛]を入力してください。')
                ],
                [
                    'rule' => ['maxLength', 255],
                    'message' => __d('baser', '自動返信メール件名[ユーザー宛]は255文字以内で入力してください。')
                ]
            ],
            'subject_admin' => [
                [
                    'rule' => ['notBlank'],
                    'message' => __d('baser', '自動送信メール件名[管理者宛]を入力してください。')
                ],
                [
                    'rule' => ['maxLength', 255],
                    'message' => __d('baser', '自動返信メール件名[管理者宛]は255文字以内で入力してください。')
                ]
            ],
            'form_template' => [
                [
                    'rule' => ['halfText'],
                    'message' => __d('baser', 'メールフォームテンプレート名は半角のみで入力してください。'),
                    'allowEmpty' => false
                ],
                [
                    'rule' => ['maxLength', 20],
                    'message' => __d('baser', 'フォームテンプレート名は20文字以内で入力してください。')
                ]
            ],
            'mail_template' => [
                [
                    'rule' => ['halfText'],
                    'message' => __d('baser', '送信メールテンプレートは半角のみで入力してください。'),
                    'allowEmpty' => false
                ],
                [
                    'rule' => ['maxLength', 20],
                    'message' => __d('baser', 'メールテンプレート名は20文字以内で入力してください。')
                ]
            ],
            'redirect_url' => [
                [
                    'rule' => ['maxLength', 255],
                    'message' => __d('baser', 'リダイレクトURLは255文字以内で入力してください。')
                ]
            ],
            'sender_1' => [
                [
                    'rule' => ['emails'],
                    'allowEmpty' => true,
                    'message' => __d('baser', '送信先メールアドレスの形式が不正です。')
                ]
            ],
            'sender_2' => [
                [
                    'rule' => ['emails'],
                    'allowEmpty' => true,
                    'message' => __d('baser', '送信先メールアドレスの形式が不正です。')
                ]
            ],
            'ssl_on' => [
                [
                    'rule' => 'checkSslUrl',
                    "message" => __d('baser', 'SSL通信を利用するには、システム設定で、事前にSSL通信用のWebサイトURLを指定してください。')
                ]
            ]
        ];
    }

    /**
     * SSL用のURLが設定されているかチェックする
     *
     * @param array $check チェック対象文字列
     * @return boolean
     */
    public function checkSslUrl($check)
    {
        if ($check[key($check)] && !Configure::read('BcEnv.sslUrl')) {
            return false;
        }

        return true;
    }

    /**
     * 英数チェック
     *
     * @param array $check チェック対象文字列
     * @return boolean
     */
    public static function alphaNumeric($check)
    {
        if (!preg_match("/^[a-z0-9]+$/", $check[key($check)])) {
            return false;
        }
        return true;
    }

    /**
     * afterSave
     *
     * @return void
     */
    public function afterSave($created, $options = [])
    {
        // TODO ucmitz 未実装
        return;

        // 検索用テーブルへの登録・削除
        if (!$this->data['Content']['exclude_search'] && $this->data['Content']['status']) {
            $this->saveSearchIndex($this->createSearchIndex($this->data));
        } else {
            $this->deleteSearchIndex($this->data['MailContent']['id']);
        }
    }

    /**
     * 検索用データを生成する
     *
     * @param array $data
     * @return array|false
     */
    public function createSearchIndex($data)
    {
        if (!isset($data['MailContent']) || !isset($data['Content'])) {
            return false;
        }
        $mailContent = $data['MailContent'];
        $content = $data['Content'];
        return [
            'SearchIndex' =>
            [
                'type'          => __d('baser', 'メール'),
                'model_id'      => (!empty($mailContent['id'])) ? $mailContent['id'] : $this->id,
                'content_id'    => $content['id'],
                'site_id'       => $content['site_id'],
                'title'         => $content['title'],
                'detail'        => $mailContent['description'],
                'url'           => $content['url'],
                'status'        => $content['status'],
                'publish_begin' => $content['publish_begin'],
                'publish_end'   => $content['publish_end']
            ]
        ];
    }

    /**
     * メールコンテンツデータをコピーする
     *
     * @param int $id ページID
     * @param int $newParentId 新しい親コンテンツID
     * @param string $newTitle 新しいタイトル
     * @param int $newAuthorId 新しいユーザーID
     * @param int $newSiteId 新しいサイトID
     * @return mixed mailContent|false
     */
    public function copy(
        int $id,
        int $newParentId,
        string $newTitle,
        int $newAuthorId,
        int $newSiteId = null
    ) {
        $data = $this->find()->where(['MailContents.id' => $id])->contain('Contents')->first();
        $oldData = clone $data;

        // EVENT MailContents.beforeCopy
        $event = $this->dispatchLayerEvent('beforeCopy', [
            'data' => $data,
            'id' => $id,
        ]);
        if ($event !== false) {
            $data = $event->getResult() === true || is_null($event->getResult()) ? $event->getData('data') : $event->getResult();
        }

        $url = $data->content->url;
        $siteId = $data->content->site_id;
        $name = $data->content->name;
        $eyeCatch = $data->content->eyecatch;
        unset($data->id);
        unset($data->created);
        unset($data->modified);
        $data->content = new Content([
            'name' => $name,
            'parent_id' => $newParentId,
            'title' => $newTitle,
            'author_id' => $newAuthorId,
            'site_id' => $newSiteId,
            'exclude_search' => false,
			'description' => $data->content->description,
			'eyecatch' => $data->content->eyecatch
        ]);

        $newEntity = $this->patchEntity($this->newEmptyEntity(), $data->toArray());
        if (!is_null($newSiteId) && $siteId != $newSiteId) {
            $data->content = new Content([
                'site_id' => $newSiteId,
                'parent_id' => $this->Contents->copyContentFolderPath($url, $newSiteId)
            ]);
        }
        $this->getConnection()->begin();

        try {
            $result = $this->save($newEntity);
            if(!$result) {
                $this->getConnection()->rollback();
                return false;
            }
            $newEntity = clone $result;

            // メールフィールドコピー
            $mailFields = $this->MailFields->find()
                ->where(['MailFields.mail_content_id' => $id])
                ->order(['MailFields.sort'])
                ->all();
            if($mailFields) {
                foreach($mailFields as $field) {
                    $field->mail_content_id = $newEntity->id;
                    if (!$this->MailFields->copy(null, $field, ['sortUpdateOff' => true])) {
                        $this->getConnection()->rollback();
                        return false;
                    }
                }
            }

            // メッセージテーブル生成
            $messagesService = $this->getService(MailMessagesServiceInterface::class);
            $messagesService->createTable($newEntity->id);
            $messagesService->construction($newEntity->id);

            // TODO ucmitz 未実装
            // >>>
//            if ($eyeCatch) {
//                $content = clone $data->content;
//                $content->eyecatch = $eyeCatch;
//                $content = $this->Contents->renameToBasenameFields(true);
//                $result = $this->Content->save($content);
//                if(!$result) {
//                    $this->getConnection()->rollback();
//                    return false;
//                }
//                $newEntity->content = $result;
//            }
            // <<<

            // EVENT BlogContents.afterCopy
            $this->dispatchLayerEvent('afterCopy', [
                'id' => $newEntity->id,
                'data' => $newEntity,
                'oldId' => $id,
                'oldData' => $oldData,
            ]);

            $this->getConnection()->commit();
            return $newEntity;
        } catch (PersistenceFailedException $e) {
            $this->getConnection()->rollback();
            return false;
        }
    }

    /**
     * 公開済の conditions を取得
     *
     * @return array 公開条件（conditions 形式）
     */
    public function getConditionAllowAccepting()
    {
        $conditions[] = ['or' => [
            [$this->alias . '.publish_begin <=' => date('Y-m-d H:i:s')],
            [$this->alias . '.publish_begin' => null],
            [$this->alias . '.publish_begin' => '0000-00-00 00:00:00']
        ]];
        $conditions[] = ['or' => [
            [$this->alias . '.publish_end >=' => date('Y-m-d H:i:s')],
            [$this->alias . '.publish_end' => null],
            [$this->alias . '.publish_end' => '0000-00-00 00:00:00']
        ]];
        return $conditions;
    }

    /**
     * 公開されたコンテンツを取得する
     *
     * @param Model $model
     * @param string $type
     * @param array $query
     * @return array|null
     */
    public function findAccepting($type = 'first', $query = [])
    {
        $getConditionAllowAccepting = $this->getConditionAllowAccepting();
        if (!empty($query['conditions'])) {
            $query['conditions'] = array_merge(
                $getConditionAllowAccepting,
                $query['conditions']
            );
        } else {
            $query['conditions'] = $getConditionAllowAccepting;
        }
        return $this->find($type, $query);
    }
}
