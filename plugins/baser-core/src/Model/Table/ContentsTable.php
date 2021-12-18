<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Model\Table;

use ArrayObject;
use Cake\Core\Plugin;
use Cake\Utility\Hash;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use BaserCore\Model\AppTable;
use BaserCore\Utility\BcUtil;
use Cake\Event\EventInterface;
use Cake\Validation\Validator;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\Note;
use BaserCore\Model\Entity\Content;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ConnectionManager;
use SoftDelete\Model\Table\SoftDeleteTrait;

/**
 * Class ContentsTable
 * @property SitesTable $Sites
 */
class ContentsTable extends AppTable
{
    use SoftDeleteTrait;

    protected $softDeleteField = 'deleted_date';

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     * @checked
     * @unitTest
     */
    public function initialize(array $config): void
    {
        FrozenTime::setToStringFormat('yyyy/MM/dd HH:mm:ss');
        parent::initialize($config);
        $this->addBehavior('Tree', ['level' => 'level']);
        $this->addBehavior('BaserCore.BcUpload', [
            'saveDir' => "contents",
            'fields' => [
                'eyecatch' => [
                    'type' => 'image',
                    'namefield' => 'id',
                    'nameadd' => true,
                    'nameformat' => '%08d',
                    'subdirDateFormat' => 'Y/m',
                    //'imageresize' => array('width' => '800', 'height' => '800'),
                    'imagecopy' => [
                        'thumb' => ['suffix' => '_thumb', 'width' => '300', 'height' => '300'],
                        'medium' => ['suffix' => '_midium', 'width' => '800', 'height' => '800']
                    ]
                ]
            ]]);
        $this->belongsTo('Sites', [
            'className' => 'BaserCore.Sites',
            'foreignKey' => 'site_id',
        ]);
        $this->belongsTo('Users', [
            'className' => 'BaserCore.Users',
            'foreignKey' => 'author_id',
        ]);
        $this->addBehavior('Timestamp');
    }

    /**
     * 関連データを更新する
     *
     * @var bool
     */
    public $updatingRelated = true;

    /**
     * システムデータを更新する
     *
     * @var bool
     */
    protected $updatingSystemData = true;

    /**
     * 保存前の親ID
     *
     * IDの変更比較に利用
     *
     * @var null
     */
    public $beforeSaveParentId = null;

    /**
     * Implemented Events
     *
     * beforeDelete の優先順位を SoftDeleteBehaviorより高くする為に調整
     *
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            // 'Model.beforeFind' => ['callable' => 'beforeFind', 'passParams' => true],
            // 'Model.afterFind' => ['callable' => 'afterFind', 'passParams' => true],
            'Model.beforeMarshal' => 'beforeMarshal',
            'Model.afterValidate' => ['callable' => 'afterValidate'],
            'Model.beforeSave' => ['callable' => 'beforeSave', 'passParams' => true],
            'Model.afterMarshal' => 'afterMarshal',
            'Model.afterSave' => ['callable' => 'afterSave', 'passParams' => true],
            'Model.afterDelete' => 'afterDelete',
        ];
    }
    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
        ->integer('id')
        ->allowEmptyString('id', null, 'create')
        ->numeric('id', __d('baser', 'IDに不正な値が利用されています。'), 'update');

        $validator
        ->scalar('name')
        ->notEmptyString('name', __d('baser', 'URLを入力してください。'))
        ->maxLength('name', 230, __d('baser', '名前は230文字以内で入力してください。'))
        ->add('name', [
            'bcUtileUrlencodeBlank' => [
                'rule' => ['bcUtileUrlencodeBlank'],
                'provider' => 'bc',
                'message' => __d('baser', 'URLはスペース、全角スペース及び、指定の記号(\\\'|`^"(){}[];/?:@&=+$,%<>#!)だけの名前は付けられません。')
            ]
        ])
        ->notEmptyString('name', __d('baser', 'スラッグを入力してください。'))
        ->add('name', [
            'duplicateRelatedSiteContent' => [
                'rule' => [$this, 'duplicateRelatedSiteContent'],
                'message' => __d('baser', '連携しているサブサイトでスラッグが重複するコンテンツが存在します。重複するコンテンツのスラッグ名を先に変更してください。'),
            ]
        ]);
        $validator
        ->scalar('title')
        ->notEmptyString('title', __d('baser', 'タイトルを入力してください。'))
        ->maxLength('title', 230, __d('baser', 'タイトルは230文字以内で入力してください。'))
        ->regex('title', '/\A(?!.*(\t)).*\z/', __d('baser', 'タイトルはタブを含む名前は付けられません。'))
        ->add('title', [
            'bcUtileUrlencodeBlank' => [
                'rule' => ['bcUtileUrlencodeBlank'],
                'provider' => 'bc',
                'message' => __d('baser', 'タイトルはスペース、全角スペース及び、指定の記号(\\\'|`^"(){}[];/?:@&=+$,%<>#!)だけの名前は付けられません。')
            ]
        ]);
        $validator
        ->allowEmptyString('eyecatch')
        ->add('eyecatch', [
            'fileCheck' => [
                'rule' => ['fileCheck', BcUtil::convertSize(ini_get('upload_max_filesize'))],
                'provider' => 'bc',
                'message' => __d('baser', 'ファイルのアップロードに失敗しました。')
            ]
        ]);
        $validator
        ->allowEmptyDateTime('self_publish_begin')
        ->add('self_publish_begin', [
            'checkDate' => [
                'rule' => ['checkDate'],
                'provider' => 'bc',
                'message' => __d('baser', '公開開始日に不正な文字列が入っています。')
            ]
        ]);

        $validator
        ->allowEmptyDateTime('self_publish_end')
        ->add('self_publish_end', [
            'checkDate' => [
                'rule' => ['checkDate'],
                'provider' => 'bc',
                'message' => __d('baser', '公開終了日に不正な文字列が入っています。')
            ]
        ])
        ->add('self_publish_end', [
            'checkDateAfterThan' => [
                'rule' => ['checkDateAfterThan'],
                'provider' => 'bc',
                'message' => __d('baser', '公開終了日は、公開開始日より新しい日付で入力してください。')
            ]
        ]);
        $validator
        ->allowEmptyDateTime('created_date');
        // TODO: %Y-%m-%d形式か%Y/%m/%d形式か判断して、書き換える
        // ->add('created_date', [
        //     'checkDate' => [
        //         'rule' => ['checkDate'],
        //         'provider' => 'bc',
        //         'message' => __d('baser', '作成日に不正な文字列が入っています。')
        //     ]
        // ]);
        $validator
        ->allowEmptyDateTime('modified_date')
        ->add('modified_date', [
            'checkDate' => [
                'rule' => ['checkDate'],
                'provider' => 'bc',
                'message' => __d('baser', '更新日に不正な文字列が入っています。')
            ]
        ]);
        return $validator;
    }

    /**
     * サイト設定にて、エイリアスを利用してメインサイトと自動連携するオプションを利用時に、
     * 関連するサブサイトで、関連コンテンツを作成する際、同階層に重複名称のコンテンツがないか確認する
     *
     *    - 新規の際は、存在するだけでエラー
     *    - 編集の際は、main_site_content_id が自身のIDでない、alias_id が自身のIDでない場合エラー
     *
     * @param $check
     * @return bool
     */
    public function duplicateRelatedSiteContent($check)
    {
        // TODO: 代替措置
        // if (!$this->Sites->isMain($this->data['Content']['site_id'])) {
        //     return true;
        // }
        // $parents = $this->getPath($this->data['Content']['parent_id'], ['name'], -1);
        // $parents = Hash::extract($parents, "{n}.Content.name");
        // unset($parents[0]);
        // if ($this->data['Content']['site_id']) {
        //     unset($parents[1]);
        // }
        // $baseUrl = '/';
        // if ($parents) {
        //     $baseUrl = '/' . implode('/', $parents) . '/';
        // }
        // $sites = $this->Sites->find('all', ['conditions' => ['Site.main_site_id' => $this->data['Content']['site_id'], 'relate_main_site' => true]]);
        // // URLを取得
        // $urlAry = [];
        // foreach($sites as $site) {
        //     $prefix = $site['Site']['name'];
        //     if ($site['Site']['alias']) {
        //         $prefix = $site['Site']['alias'];
        //     }
        //     $urlAry[] = '/' . $prefix . $baseUrl . $check;
        // }
        // $conditions = ['Content.url' => $urlAry];
        // if (!empty($this->data['Content']['id'])) {
        //     $conditions = array_merge($conditions, [
        //         ['or' => ['Content.alias_id <>' => $this->data['Content']['id'], 'Content.alias_id' => null]],
        //         ['or' => ['Content.main_site_content_id <>' => $this->data['Content']['id'], 'Content.main_site_content_id' => null]]
        //     ]);
        // }
        // if ($this->find('count', ['conditions' => $conditions])) {
        //     return false;
        // }
        return true;
    }

    /**
     * Before Marshal
     *
     * @param EventInterface $event
     * @param ArrayObject $data
     * @param ArrayObject $options
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options)
    {
        if (!empty($data['content'])) {
            // コンテンツ一覧にて、コンテンツを登録した直後のリネーム処理までは新規追加とみなして処理を行う為、$create で判定させる
            $create = empty($data['content']['id']);
            // タイトルは強制的に255文字でカット
            if (!empty($data['content']['title'])) {
                $data['content']['title'] = mb_substr($data['content']['title'], 0, 254, 'UTF-8');
            }
            if ($create) {
                // IEのURL制限が2083文字のため、全て全角文字を想定し231文字でカット
                if (!isset($data['content']['name'])) {
                    $data['content']['name'] = BcUtil::urlencode(mb_substr($data['content']['title'], 0, 230, 'UTF-8'));
                }
                if (!isset($data['content']['self_status'])) {
                    $data['content']['self_status'] = false;
                }
                if (!isset($data['content']['self_publish_begin'])) {
                    $data['content']['self_publish_begin'] = null;
                }
                if (!isset($data['content']['self_publish_end'])) {
                    $data['content']['self_publish_end'] = null;
                }
                if (!isset($data['content']['created_date'])) {
                    $data['content']['created_date'] = FrozenTime::now();
                }
                if (!isset($data['content']['site_root'])) {
                    $data['content']['site_root'] = 0;
                }
                if (!isset($data['content']['exclude_search'])) {
                    $data['content']['exclude_search'] = 0;
                }
                if (!isset($data['content']['author_id'])) {
                    $user = BcUtil::loginUser('Admin');
                    $data['content']['author_id'] = $user['id'];
                }
            } else {
                if (empty($data['content']['modified_date'])) {
                    $data['content']['modified_date'] = FrozenTime::now();
                }
                if (isset($data['content']['name'])) {
                    $data['content']['name'] = BcUtil::urlencode(mb_substr($data['content']['name'], 0, 230, 'UTF-8'));
                }
            }
            // name の 重複チェック＆リネーム
            if (!empty($data['content']['name'])) {
                $contentId = null;
                if (!empty($data['content']['id'])) {
                    $contentId = $data['content']['id'];
                }
                $data['content']['name'] = $this->getUniqueName($data['content']['name'], $data['content']['parent_id'], $contentId);
            }
        }
    }

    /**
     * afterMarshal
     * FrozenTime形式のデータをバリデーション前にstringとして保存
     * @param  EventInterface $event
     * @param  EntityInterface $entity
     * @param  ArrayObject $options
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterMarshal(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        $columns = ConnectionManager::get('default')->getSchemaCollection()->describe($this->getTable())->columns();
        foreach ($columns as $field) {
            if ($entity->get($field) instanceof FrozenTime) $entity->set($field, $entity->get($field)->__toString());
        }
    }

    /**
     * After Validate
     */
    public function afterValidate()
    {
        parent::afterValidate();
        // 新規追加の際、name は、title より自動設定される為、バリデーションエラーが発生してもエラーメッセージを表示しない
        if (empty($this->data['Content']['id']) && !empty($this->validationErrors['name'])) {
            unset($this->validationErrors['name']);
        }
    }

    /**
     * ゴミ箱のコンテンツを取得する
     * @param int $id
     * @return EntityInterface|array
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTrash($id)
    {
        return $this->findById($id)->applyOptions(['withDeleted'])->contain(['Sites'])->where(['Contents.deleted_date IS NOT NULL'])->firstOrFail();
    }

    /**
     * コンテンツ情報を物理削除する
     * @param Content $content
     * @param bool $enableTree(デフォルト:false) TreeBehaviorの有無
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function hardDel($content, $enableTree = false): bool
    {
        if (!empty($content->deleted_date)) {
            if ($enableTree && !$this->hasBehavior('Tree')) {
                $this->addBehavior('Tree');
            }
            if (!$enableTree && $this->hasBehavior('Tree')) {
                $this->removeBehavior('Tree');
            }
            return $this->hardDelete($content);
        }
        return false;
    }

    /**
     * 一意の name 値を取得する
     *
     * @param string $name name フィールドの値
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUniqueName($name, $parentId, $contentId = null)
    {

        // 先頭が同じ名前のリストを取得し、後方プレフィックス付きのフィールド名を取得する
        $query = $this->find()->where(['name LIKE' => $name . '%', 'parent_id' => $parentId]);
        if ($contentId) {
            $query = $query->andWhere(['id <>' => $contentId]);
        }
        $datas = $query->select('name')->order('name')->all()->toArray();
        $datas = Hash::extract($datas, '{n}.name');
        $numbers = [];

        if ($datas) {
            foreach($datas as $data) {
                if ($name === $data) {
                    $numbers[1] = 1;
                } elseif (preg_match("/^" . preg_quote($name, '/') . "_([0-9]+)$/s", $data, $matches)) {
                    $numbers[$matches[1]] = true;
                }
            }
            if ($numbers) {
                $prefixNo = 1;
                while(true) {
                    if (!isset($numbers[$prefixNo])) {
                        break;
                    }
                    $prefixNo++;
                }
                if ($prefixNo == 1) {
                    return $name;
                } else {
                    return $name . '_' . ($prefixNo);
                }
            } else {
                return $name;
            }
        } else {
            return $name;
        }

    }

    /**
     * Before Save
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if (!empty($entity->id)) {
            $this->beforeSaveParentId = $entity->parent_id;
        }
        return parent::beforeSave($event, $entity, $options);
    }

    /**
     * afterSave
     *
     * @param  EventInterface $event
     * @param  EntityInterface $entity
     * @param  ArrayObject $options
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($this->updatingSystemData) {
            $this->updateSystemData($entity);
        }
        if ($this->updatingRelated) {
            // ゴミ箱から戻す場合、 type の定義がないが問題なし
            if (!empty($entity->type) && $entity->type == 'ContentFolder') {
                $this->updateChildren($entity->id);
            }
            $this->updateRelateSubSiteContent($entity);
            if (!empty($entity->parent_id) && $this->beforeSaveParentId != $entity->parent_id) {
                $SiteConfig = TableRegistry::getTableLocator()->get('BaserCore.SiteConfigs');
                $SiteConfig->updateContentsSortLastModified();
                $this->beforeSaveParentId = null;
            }
        }
    }

    /**
     * 関連するコンテンツ本体のデータキャッシュを削除する
     * @param Content $content
     */
    public function deleteAssocCache($content)
    {
        if (empty($content->plugin) || empty($content->type)) {
            $content = $this->find()->applyOptions(['withDeleted'])->select(['plugin', 'type'])->where(['id' => $content->id])->first();
        }
        $assoc = $content->plugin . '.' . Inflector::pluralize($content->type);
        if ($content->plugin != 'BaserCore') {
            if (!Plugin::isLoaded($content->plugin)) {
                return;
            }
        }
        $AssocTable = TableRegistry::getTableLocator()->get($assoc);
        // if ($AssocTable && !empty($AssocTable->actsAs) && in_array('BcCache', $AssocTable->actsAs)) {
        //     if ($AssocTable->Behaviors->hasMethod('delCache')) {
        //         $AssocTable->delCache();
        //     }
        // }
    }

    /**
     * After Delete
     *
     * 関連コンテンツのキャッシュを削除する
     * @param  EventInterface $event
     * @param  EntityInterface $entity
     * @param  ArrayObject $options
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($entity) {
            $this->deleteRelateSubSiteContent($entity);
            $this->deleteAlias($entity);
        }
        $this->deleteAssocCache($entity);
    }

    /**
     * 自データのエイリアスを削除する
     *
     * 全サイトにおけるエイリアスを全て削除
     *
     * @param Content $content
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function deleteAlias($content): void
    {
        if (empty($content->alias_id)) {
            $contents = $this->find()->select('id')->where(['Contents.alias_id' => $content->id])->applyOptions(['callbacks' => false]);
            if (!$contents->isEmpty()) {
                // afterDelete・afterSaveのループを防ぐ
                foreach (['afterSave', 'afterDelete'] as $eventName) {
                    $event = $this->getEventManager()->matchingListeners($eventName);
                    if ($event) $this->getEventManager()->off('Model.' . $eventName);
                }
                foreach($contents as $content) {
                    $this->removeFromTree($content);
                    $this->hardDelete($content, ['callbacks' => false]);
                }
            }
        }
    }

    /**
     * メインサイトの場合、連携設定がされている子サイトのエイリアス削除する
     *
     * @param Content $content
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function deleteRelateSubSiteContent($content)
    {
        // 自身がエイリアスか確認し、エイリアスの場合は終了
        if (!$content->alias_id) {
            // メインサイトか確認し、メインサイトでない場合は終了
            if (is_null($content->site_id) || !$this->Sites->isMain($content->site_id)) {
                return;
            }
            // 連携設定となっている小サイトを取得
            $sites = $this->Sites->find()->where(['main_site_id' => $content->site_id, 'relate_main_site' => true]);
            if ($sites->isEmpty()) {
                return;
            }
            // 同階層に同名のコンテンツがあるか確認
            foreach($sites as $site) {
                $content = $this->find()->where(['site_id' => $site->id, 'main_site_content_id' => $content->id])->first();
                if ($content) {
                    // afterDelete・afterSaveのループを防ぐ
                    foreach (['afterSave', 'afterDelete'] as $eventName) {
                        $event = $this->getEventManager()->matchingListeners($eventName);
                        if ($event) $this->getEventManager()->off('Model.' . $eventName);
                    }
                    // 存在する場合は、自身のエイリアスかどうか確認し削除する
                    if ($content->alias_id == $content->id) {
                        $this->removeFromTree($content);
                        $this->hardDelete($content);
                    } elseif ($content->type == 'ContentFolder') {
                        $this->updateChildren($content->id);
                    }
                }
            }
        }
    }

    /**
     * メインサイトの場合、連携設定がされている子サイトのエイリアスを追加・更新する
     *
     * @param Content $data
     * @return bool
     * @checked
     * @note(value="TODO内容を荒川さんに確認")
     */
    protected function updateRelateSubSiteContent($data)
    {
        // 自身がエイリアスか確認し、エイリアスの場合は終了
        if (!empty($data->alias_id) || !isset($data->site_id)) {
            return true;
        }

        $isContentFolder = (bool) (!empty($data->type) && $data->type == 'ContentFolder');
        // メインサイトか確認し、メインサイトでない場合は終了
        if (!$this->Sites->isMain($data->site_id)) {
            return true;
        }
        // 連携設定となっている小サイトを取得
        $sites = $this->Sites->find()->where(['main_site_id' => $data->site_id, 'relate_main_site' => true]);
        if ($sites->isEmpty()) {
            return true;
        }
        // TODO ucmitz: コンテンツないのに通ってしまうため内部コンテンツがあるかを確認し、なければ処理を終了するよう一時措置
        if ($this->find()->where(['site_id' => $sites->first()->id])->isEmpty()) {
            return true;
        }

        $_data = $this->find()->where(['id' => $data->id])->first();
        if ($_data) {
            $data = $this->patchEntity($_data, $data->toArray(), ['validate' => false]);
        }

        // URLが空の場合はゴミ箱へ移動する処理のため、連携更新を行わない
        if (!$data->url) {
            return true;
        }

        // TODO ucmitz: 未確認
        // $CreateModel = $this;
        // if ($isContentFolder) {
        //     $CreateModel = TableRegistry::getTableLocator()->get('BaserCore.ContentFolder');
        // }

        $pureUrl = $this->pureUrl($data->url, $data->site_id);
        // 同階層に同名のコンテンツがあるか確認
        $result = true;
        foreach($sites as $site) {
            if (!$site->status) {
                continue;
            }
            $url = $pureUrl;
            $prefix = $this->Sites->getPrefix($site->id);
            if ($prefix) {
                $url = '/' . $prefix . $url;
            }
            $content = $this->find()->where([
                'site_id' => $site->id,
                'or' => [
                    'main_site_content_id' => $data->id,
                    'url' => $url
                ],
            ])->first();
            if ($content) {
                // 存在する場合は、自身のエイリアスかどうか確認し、エイリアスの場合は、公開状態とタイトル、説明文、アイキャッチ、更新日を更新
                // フォルダの場合も更新する
                if ($content->alias_id == $data->id || ($content->type == 'ContentFolder' && $isContentFolder)) {
                    $content->name = urldecode($data->name);
                    $content->title = $data->title;
                    $content->description = $data->description;
                    $content->self_status = $data->self_status;
                    $content->self_publish_begin = $data->self_publish_begin;
                    $content->self_publish_end = $data->self_publish_end;
                    $content->created_date = $data->created_date;
                    $content->modified_date = $data->modified_date;
                    $content->exclude_search = $data->exclude_search;
                    if (!empty($data->eyecatch)) {
                        $content->eyecatch = $data->eyecatch;
                    }
                    $url = $data->url;
                    if ($content->type == 'ContentFolder') {
                        $url = preg_replace('/\/[^\/]+\/$/', '/', $url);
                    }
                    $content->parent_id = $this->copyContentFolderPath($url, $site->id);
                } else {
                    $content->name = urldecode($data->name);
                }
                if (!$this->save($content)) {
                    $result = false;
                }
            } else {
                // 存在しない場合はエイリアスを作成
                // フォルダの場合は実体として作成
                $content = clone($data);
                unset($content->id);
                unset($content->name);
                unset($content->url);
                unset($content->lft);
                unset($content->rght);
                unset($content->created_date);
                unset($content->modified_date);
                unset($content->created);
                unset($content->modified);
                unset($content->layout_template);
                $content->name = $data->name;
                $content->main_site_content_id = $data->id;
                $content->site_id = $site->id;
                $url = $data->url;
                if ($content->type == 'ContentFolder') {
                    $url = preg_replace('/\/[^\/]+\/$/', '/', $url);
                    unset($content->entity_id);
                } else {
                    $content->alias_id = $data->id;
                }
                $content->parent_id = $this->copyContentFolderPath($url, $site->id);
                $content = $this->newEntity($content->toArray(), ['validate' => false]);
                if (!$this->save($content)) {
                    $result = false;
                }
            }
        }
        return $result;
    }

    /**
     * 現在のフォルダのURLを元に別サイトにフォルダを生成する
     * 最下層のIDを返却する
     *
     * @param $currentUrl
     * @param $targetSiteId
     * @return bool|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copyContentFolderPath($currentUrl, $targetSiteId)
    {

        $current = $this->find()->where(['url' => $currentUrl]);
        if ($current->isEmpty()) {
            return false;
        } else {
            $currentId = $current->first()->id;
        }
        $prefix = $this->Sites->getPrefix($targetSiteId);
        $path = $this->find('path', ['for' => $currentId])->toArray();
        if (!$path) {
            return false;
        }
        $url = '/';
        if ($prefix) {
            $url .= $prefix . '/';
        }
        unset($path[0]);
        $parentId = $this->Sites->getRootContentId($targetSiteId);
        /* @var ContentFolder $ContentFolder */
        $ContentFolder = TableRegistry::getTableLocator()->get('ContentFolder');
        foreach($path as $currentContentFolder) {
            if ($currentContentFolder->type != 'ContentFolder') {
                break;
            }
            if ($currentContentFolder->site_root) {
                continue;
            }
            $url .= $currentContentFolder->name;
            if ($this->findByUrl($url)) {
                return false;
            }
            $url .= '/';
            $targetContentFolder = $this->findByUrl($url);
            if ($targetContentFolder) {
                $parentId = $targetContentFolder->id;
            } else {
                $data = [
                    'content' => [
                        'name' => $currentContentFolder->name,
                        'title' => $currentContentFolder->title,
                        'parent_id' => $parentId,
                        'plugin' => 'BaserCore',
                        'type' => 'ContentFolder',
                        'site_id' => $targetSiteId,
                        'self_status' => true
                    ]
                ];
                $ContentFolder->create($data);
                if ($ContentFolder->save()) {
                    $parentId = $ContentFolder->Content->id;
                } else {
                    return false;
                }
            }
        }
        return $parentId;
    }

    /**
     * サブサイトのプレフィックスがついていない純粋なURLを取得
     *
     * @param string $url
     * @param int $siteId
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function pureUrl($url, $siteId)
    {
        if (empty($url)) {
            $url = '/';
        }
        $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sites->findById($siteId)->first();
        return $site ? $site->getPureUrl($url) : $url;
    }

    /**
     * Content data を作成して保存する
     *
     * @param array $content
     * @param string $plugin
     * @param string $type
     * @param int $entityId
     * @return EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createContent($content, $plugin, $type, $entityId = null, $validate = true)
    {
        if (!isset($content)) return false;
        $content['plugin'] = $plugin;
        $content['type'] = $type;
        $content['entity_id'] = $entityId;
        if (!isset($content['deleted_date'])) {
            $content['deleted_date'] = '';
        }
        if (!isset($content['site_root'])) {
            $content['site_root'] = 0;
        }
        if (!isset($content['exclude_search'])) {
            $content['exclude_search'] = 0;
        }
        if (!isset($content['created_date'])) {
            $content['created_date'] = FrozenTime::now();
        }
        $content = $this->newEntity($content);
        return $this->save($content);
    }

    /**
     * コンテンツデータよりURLを生成する
     *
     * @param int $id コンテンツID
     * @return mixed URL | false
     * @checked
     * @unitTest
     * @note(value="TODO内容を荒川さんに確認")
     */
    public function createUrl($id)
    {
        switch ((int)$id) {
            case null:
                return false;
            case 1:
                $url = '/';
                break;
            default:
                // =========================================================================================================
                // サイト全体のURLを変更する場合、TreeBehavior::getPath() を利用するとかなりの時間がかかる為、DataSource::query() を利用する
                // 2018/02/04 ryuring
                // プリペアドステートメントを利用する為、fetchAll() を利用しようとしたが、SQLite のドライバが対応してない様子。
                // CakePHP３系に対応する際、SQLite を標準のドライバに変更してから、プリペアドステートメントに書き換えていく。
                // それまでは、SQLインジェクション対策として、値をチェックしてから利用する。
                // =========================================================================================================
                $connection = ConnectionManager::get('default');
                $content = $connection
                    ->newQuery()
                    ->select(['lft', 'rght'])
                    ->from('contents')
                    ->where(['id' => $id, 'deleted_date IS' => null])
                    ->execute()
                    ->fetchAll('assoc');
                if ($content) {
                    // TODO ucmitz: $content[0][0]がなぜ必要か未確認
                    $content = isset($content[0]) ? $content[0] : $content[0][0];
                } else {
                    return false;
                }
                $parents = $connection
                    ->newQuery()
                    ->select(['name', 'plugin', 'type'])
                    ->from('contents')
                    ->where(['lft <=' => $content['lft'], 'rght >=' => $content['rght'],'deleted_date IS' => null])
                    ->order(['lft' => 'ASC'])
                    ->execute()
                    ->fetchAll('assoc');
                unset($parents[0]);
                if (!$parents) {
                    return false;
                } else {
                    $names = [];
                    unset($content);
                    foreach($parents as $parent) {
                        if (isset($parent)) {
                            $parent = $parent;
                        } else {
                            $parent = $parent[0];
                        }
                        $names[] = $parent['name'];
                        $content = $parent;
                    }
                    $plugin = $content['plugin'];
                    $type = $content['type'];
                    $url = '/' . implode('/', $names);
                    $setting = Configure::read('BcContents.items.' . $plugin . '.' . $type);
                    if ($type == 'ContentFolder' || empty($setting['omitViewAction'])) {
                        $url .= '/';
                    }
                }
                break;
        }
        return $url;
    }

    /**
     * システムデータを更新する
     *
     * URL / 公開状態 / メインサイトの関連コンテンツID
     *
     * @param Content $content
     * @return Content
     * @checked
     * @unitTest
     * @noTodo
     */
    protected function updateSystemData($content)
    {
        if (empty($content->name)) {
            if ($content->id != 1) {
                return false;
            }
        }
        if ($content->site_id) {
            $site = $this->Sites->find()->where(['id' => $content->site_id])->first();
        }
        // URLを更新
        $content->url = $this->createUrl($content->id);
        // 親フォルダの公開状態に合わせて公開状態を更新（自身も含める）
        if (isset($content->self_status)) {
            $content->status = $content->self_status;
        }
        // null の場合、isset で判定できないので array_key_exists を利用
        if (isset($content->self_publish_begin)) {
            $content->publish_begin = $content->self_publish_begin;
        }
        if (isset($content->self_publish_end)) {
            $content->publish_end = $content->self_publish_end;
        }
        if (!empty($content->parent_id)) {
            $parent = $this->find()->select(['name', 'status', 'publish_begin', 'publish_end'])->where(['id' => $content->parent_id])->first();
            if (!$parent->status || $parent->publish_begin || $parent->publish_begin) {
                $content->status = $parent->status;
                $content->publish_begin = $parent->publish_begin;
                $content->publish_end = $parent->publish_end;
            }
        }
        // 主サイトの関連コンテンツIDを更新
        if (!empty($site)) {
            // 主サイトの同一階層のコンテンツを特定
            $prefix = $site->name;
            if ($site->alias) {
                $prefix = $site->alias;
            }
            $url = preg_replace('/^\/' . preg_quote($prefix, '/') . '\//', '/', $content->url);
            $mainSitePrefix = $this->Sites->getPrefix($site->main_site_id);
            if ($mainSitePrefix) {
                $url = '/' . $mainSitePrefix . $url;
            }
            // main_site_content_id を更新
            if ($site->main_site_id) {
                $mainSiteContent = $this->find()->select(['id'])->where(['site_id' => $site->main_site_id, 'url' => $url])->first();
                $content->main_site_content_id = $mainSiteContent->id;
            } else {
                $content->main_site_content_id = null;
            }
        }
        $event = $this->getEventManager()->matchingListeners('afterSave');
        if ($event) $this->getEventManager()->off('Model.afterSave');
        return $this->save($content, ['validate' => false]);
    }

    /**
     * ID を指定して公開状態かどうか判定する
     * @param $id
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function isPublishById($id)
    {
        return !$this->findById($id)->where([$this->getConditionAllowPublish()])->isEmpty();
    }

    /**
     * 子ノードのURLを全て更新する
     *
     * @param $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function updateChildren($id)
    {
        $children = $this->find('children', ['for' => $id])->order('lft');
        $result = true;
        if (!$children->isEmpty()) {
            foreach($children as $child) {
                if (!$this->updateSystemData($child)) {
                    $result = false;
                }
            }
        }
        return $result;
    }

    /**
     * タイプよりコンテンツを取得する
     *
     * @param string $type 例）Blog.BlogContent
     * @param int $entityId
     * @return array
     * @checked
     * @unitTest
     * @noTodo
     */
    public function findByType($type, $entityId = null)
    {
        [$plugin, $type] = pluginSplit($type);
        if (!$plugin) {
            $plugin = 'BaserCore';
        }
        $conditions = [
            'Contents.plugin' => $plugin,
            'Contents.type' => $type,
            'Contents.alias_id IS NULL',
        ];
        if ($entityId) {
            $conditions['Contents.entity_id'] = $entityId;
        }
        return $this->find('all')->where([$conditions])->order(['Contents.id'])->first();
    }

    /**
     * タイプよりコンテンツを削除する
     *
     * @param string $type 例）Blog.BlogContent
     * @param int $entityId
     * @return bool
     */
    public function deleteByType($type, $entityId = null)
    {
        [$plugin, $type] = pluginSplit($type);
        if (!$plugin) {
            $plugin = 'BaserCore';
        }
        $conditions = [
            'plugin' => $plugin,
            'type' => $type,
            'alias_id' => null
        ];
        if ($entityId) {
            $conditions['Content.entity_id'] = $entityId;
        }
        $softDelete = $this->softDelete(null);
        $this->softDelete(false);
        $id = $this->field('id', $conditions);
        $result = $this->removeFromTree($id, true);
        $this->softDelete($softDelete);
        return $result;
    }

    /**
     * 公開済の conditions を取得
     *
     * @return array 公開条件（conditions 形式）
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getConditionAllowPublish()
    {
        return [
            'Contents.status' => true,
            ['or' => [
                ['Contents.publish_begin <=' => date('Y-m-d H:i:s')],
                ['Contents.publish_begin IS' => null],
                ['Contents.publish_begin' => '0000-00-00 00:00:00']
            ]],
            ['or' => [
                ['Contents.publish_end >=' => date('Y-m-d H:i:s')],
                ['Contents.publish_end IS' => null],
                ['Contents.publish_end' => '0000-00-00 00:00:00']
            ]]
        ];
    }

    /**
     * データが公開済みかどうかチェックする
     *
     * @param boolean $status 公開ステータス
     * @param string $publishBegin 公開開始日時
     * @param string $publishEnd 公開終了日時
     * @return    bool
     * @checked
     * @unitTest
     * @note(value="TODO内容を荒川さんに確認")
     */
    public function isPublish($status, $publishBegin, $publishEnd)
    {
        // TODO ucmitz: frozenTime形式に移行するべき
        if (!$status) {
            return false;
        }
        if ($publishBegin && $publishBegin != '0000-00-00 00:00:00') {
            if ($publishBegin > date('Y-m-d H:i:s')) {
                return false;
            }
        }
        if ($publishEnd && $publishEnd != '0000-00-00 00:00:00') {
            if ($publishEnd < date('Y-m-d H:i:s')) {
                return false;
            }
        }
        return true;
    }

    /**
     * タイトル、URL、公開状態が更新されているか確認する
     *
     * @param int $id コンテンツID
     * @param array $newData 新しいコンテンツデータ
     * @checked
     * @unitTest
     * @note(value="TODO内容を荒川さんに確認")
     */
    public function isChangedStatus($id, $newData)
    {
        try {
        $before = $this->get($id);
        } catch(\Cake\Datasource\Exception\RecordNotFoundException $e) {
            return true;
        }
        // TODO ucmitz: PagesController使用時に再確認する
        $beforeStatus = $this->isPublish($before->self_status,  $before->self_publish_begin, $before->self_publish_end);
        $afterStatus = $this->isPublish($newData['self_status'], $newData['self_publish_begin'], $newData['self_publish_end']);
        if ($beforeStatus != $afterStatus || $before->title  != $newData['title'] || $before->url != $newData['url']) {
            return true;
        }
        return false;
    }

    /**
     * 親のテンプレートを取得する
     *
     * @param $id
     */
    public function getParentTemplate($id)
    {
        $contents = $this->getPath($id, null, -1);
        $contents = array_reverse($contents);
        unset($contents[0]);
        $parentTemplates = Hash::extract($contents, '{n}.Content.layout_template');
        $parentTemplate = '';
        foreach($parentTemplates as $parentTemplate) {
            if ($parentTemplate) {
                break;
            }
        }
        if (!$parentTemplate) {
            $parentTemplate = 'default';
        }
        return $parentTemplate;
    }

    /**
     * 関連サイトの関連コンテンツを取得する
     *
     * @param int $id
     * @return array|false
     */
    public function getRelatedSiteContents($id, $options = [])
    {
        $options = array_merge([
            'excludeIds' => []
        ], $options);
        $conditions = [
            ['OR' => [
                ['Content.id' => $id],
                ['Content.main_site_content_id' => $id]
            ]],
            ['OR' => [
                ['Site.status' => true],
                ['Site.status' => null]    // ルートメインサイト
            ]]
        ];
        if ($options['excludeIds']) {
            if (count($options['excludeIds']) == 1) {
                $options['excludeIds'] = $options['excludeIds'][0];
            }
            $conditions['Content.site_id <>'] = $options['excludeIds'];
        }
        $conditions = array_merge($conditions, $this->getConditionAllowPublish());
        $contents = $this->find('all', [
            'conditions' => $conditions,
            'order' => ['Content.id'],
            'recursive' => 0
        ]);
        $mainSite = $this->Sites->getRootMain();
        foreach($contents as $key => $content) {
            if ($content['Content']['site_id'] == 0) {
                $contents[$key]['Site'] = $mainSite;
            }
        }
        return $contents;
    }

    /**
     * キャッシュ時間を取得する
     *
     * @param mixed $id | $data
     * @return mixed int or false
     */
    public function getCacheTime($data)
    {
        if (!is_array($data)) {
            $data = $this->find('first', ['conditions' => ['Content.id' => $data], 'recursive' => 0]);
        }
        if (isset($data['Content'])) {
            $data = $data['Content'];
        }
        if (!$data) {
            return false;
        }
        // #10680 Modify 2016/01/22 gondoh
        // 3.0.10 で追加されたViewキャッシュ分離の設定値を、後方互換のため存在しない場合は旧情報で取り込む
        $duration = Configure::read('BcCache.viewDuration');
        if (empty($duration)) {
            $duration = Configure::read('BcCache.duration');
        }
        // 固定ページなどの公開期限がviewDulationより短い場合
        if ($data['status'] && $data['publish_end'] && $data['publish_end'] != '0000-00-00 00:00:00') {
            if (strtotime($duration) - time() > (strtotime($data['publish_end']) - time())) {
                $duration = strtotime($data['publish_end']) - time();
            }
        }
        return $duration;
    }

    /**
     * 全てのURLをデータの状況に合わせ更新する
     *
     * @return bool
     */
    public function updateAllUrl()
    {
        $contents = $this->find('all', [
            'recursive' => -1,
            'order' => ['Content.lft']
        ]);
        $result = true;
        $updatingRelated = $this->updatingRelated;
        $updatingSystemData = $this->updatingSystemData;
        $this->updatingRelated = false;
        $this->updatingSystemData = false;
        foreach($contents as $content) {
            $content['Content']['url'] = $this->createUrl($content['Content']['id'], $content['Content']['plugin'], $content['Content']['type']);
            if (!$this->save($content)) {
                $result = false;
            }
        }
        $this->updatingRelated = $updatingRelated;
        $this->updatingSystemData = $updatingSystemData;
        return $result;
    }

    /**
     * 指定したコンテンツ配下のコンテンツのURLを一括更新する
     * @param $id
     */
    public function updateChildrenUrl($id)
    {
        set_time_limit(0);
        $children = $this->children($id, false, ['url', 'id'], 'Content.lft', null, null, -1);
        $db = $this->getDataSource();
        if ($children) {
            foreach($children as $key => $child) {
                // サイト全体を更新する為、サイト規模によってはかなり時間がかかる為、SQLを利用
                $sql = 'UPDATE ' . $this->tablePrefix . 'contents SET url = ' . $db->value($this->createUrl($child['Content']['id']), 'integer') . ' WHERE id = ' . $db->value($child['Content']['id'], 'integer');
                if (!$db->execute($sql)) {
                    $this->getDataSource()->rollback();
                    return false;
                }
            }
        }
        $this->getDataSource()->commit();
        return true;
    }

    /**
     * コンテンツ管理のツリー構造をリセットする
     */
    public function resetTree()
    {
        $this->Behaviors->unload('Tree');
        $this->updatingRelated = false;
        $siteRoots = $this->find('all', ['conditions' => ['Content.site_root' => true], 'order' => 'lft', 'recursive' => -1]);
        $count = 0;
        $mainSite = [];
        foreach($siteRoots as $siteRoot) {
            $count++;
            $siteRoot['Content']['lft'] = $count;
            $siteRoot['Content']['level'] = ($siteRoot['Content']['id'] == 1)? 0 : 1;
            $siteRoot['Content']['parent_id'] = ($siteRoot['Content']['id'] == 1)? null : 1;
            $contents = $this->find('all', ['conditions' => ['Content.site_id' => $siteRoot['Content']['site_id'], 'Content.site_root' => false], 'order' => 'lft', 'recursive' => -1]);
            if ($contents) {
                foreach($contents as $content) {
                    $count++;
                    $content['Content']['lft'] = $count;
                    $count++;
                    $content['Content']['rght'] = $count;
                    $content['Content']['level'] = $siteRoot['Content']['level'] + 1;
                    $content['Content']['parent_id'] = $siteRoot['Content']['id'];
                    $this->save($content, false);
                }
            }
            if ($siteRoot['Content']['id'] == 1) {
                $mainSite = $siteRoot;
            } else {
                $count++;
                $siteRoot['Content']['rght'] = $count;
                $this->save($siteRoot, false);
            }
        }
        $count++;
        $mainSite['Content']['rght'] = $count;
        $this->save($mainSite, false);
        // ゴミ箱
        $this->Behaviors->unload('SoftDelete');
        $contents = $this->find('all', ['conditions' => ['Content.deleted_date IS NOT NULL'], 'order' => 'lft', 'recursive' => -1]);
        if ($contents) {
            foreach($contents as $content) {
                $count++;
                $content['Content']['lft'] = $count;
                $count++;
                $content['Content']['rght'] = $count;
                $content['Content']['level'] = 0;
                $content['Content']['parent_id'] = null;
                $content['Content']['site_id'] = null;
                $this->save($content, false);
            }
        }
        // 関連データ更新機能をオンにした状態で再度更新
        $this->Behaviors->load('Tree');
        $this->updatingRelated = true;
        $contents = $this->find('all', ['order' => 'lft', 'recursive' => -1]);
        if ($contents) {
            foreach($contents as $content) {
                // バリデーションをオンにする事で同名コンテンツを強制的にリネームする
                // beforeValidate でリネーム処理を入れている為
                // （第二引数を false に設定しない）
                $this->save($content);
            }
        }
        return true;
    }

    /**
     * URLに関連するコンテンツ情報を取得する
     * サイト情報を含む
     *
     * @param string $url 検索対象のURL
     * @param bool $publish 公開状態かどうか
     * @param bool $extend 拡張URLに対応するかどうか /news/ というコンテンツが存在する場合、/news/archives/1 で検索した際にヒットさせる
     * @param bool $sameUrl 対象をメインサイトと同一URLで表示するサイト設定内のコンテンツするかどうか
     * @param bool $useSubDomain 対象をサブドメインを利用しているサイト設定内のコンテンツをするかどうか
     * @return mixed false|array Content データ
     * @checked
     * @noTodo
     * @unitTest
     */
    public function findByUrl($url, $publish = true, $extend = false, $sameUrl = false, $useSubDomain = false)
    {
        $url = preg_replace('/^\//', '', $url);
        $query = $this->find()->order(['url' => 'DESC'])->contain('Sites');
        if ($extend) {
            $params = explode('/', $url);
            $condUrls = [];
            $condUrls[] = '/' . implode('/', $params);
            $count = count($params);
            for($i = $count; $i > 1; $i--) {
                unset($params[$i - 1]);
                $path = implode('/', $params);
                $condUrls[] = '/' . $path . '/';
                $condUrls[] = '/' . $path;
            }
            // 固定ページはURL拡張はしない
            $query->where([
                'Contents.type <>' => 'Page',
                'Contents.url IN' => $condUrls
            ]);
        } else {
            $query->where([
                'Contents.url IN' => $this->getUrlPattern($url)
            ]);
        }
        $query->innerJoinWith('Sites', function($q) use($sameUrl, $useSubDomain) {
            return $q->where([
                ['Sites.status' => true],
                ['Sites.same_main_url' => $sameUrl],
                ['Sites.use_subdomain' => $useSubDomain]
            ]);
        });
        if ($publish) {
            $query->where($this->getConditionAllowPublish());
        }
        $content = $query->first();
        if (!$content) {
            return false;
        }
        if ($extend && $content->type == 'ContentFolder') {
            return false;
        }
        return $content;
    }

    /**
     * 同じ階層における並び順を取得
     *
     * id が空の場合は、一番最後とみなす
     *
     * @param string $id
     * @param int $parentId
     * @return bool|int|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getOrderSameParent($id, $parentId)
    {
        $contents = $this->find()->select(['id', 'parent_id', 'title'])->where(['parent_id' => $parentId])->order('lft');
        $order = null;
        if (!$contents->isEmpty()) {
            if ($id) {
                foreach($contents as $key => $data) {
                    if ($id == $data->id) {
                        $order = $key + 1;
                        break;
                    }
                }
            } else {
                return $contents->all()->count();
            }
        } else {
            return false;
        }
        return $order;
    }

    /**
     * オフセットを元にコンテンツを移動する
     *
     * @param $id
     * @param $offset
     * @return EntityInterface|bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function moveOffset($id, $offset)
    {
        $offset = (int)$offset;
        $content = $this->get($id);
        if ($offset > 0) {
            $result = $this->moveDown($content, abs($offset));
        } elseif ($offset < 0) {
            $result = $this->moveUp($content, abs($offset));
        } else {
            $result = true;
        }
        return $result ? $content : false;
    }

    /**
     * UpdatingSystemData無効化
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function disableUpdatingSystemData()
    {
        $this->updatingSystemData = false;
    }

    /**
     * UpdatingSystemData有効化
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function enableUpdatingSystemData()
    {
        $this->updatingSystemData = true;
    }
}
