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
use Cake\ORM\Table;
use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;
use BaserCore\Utility\BcUtil;
use Cake\Event\EventInterface;
use Cake\Validation\Validator;
use Cake\Datasource\EntityInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\Note;

/**
 * Class PagesTable
 */
class PagesTable extends Table
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;
    use BcContainerTrait;

    /**
     * 更新前のページファイルのパス
     *
     * @var string
     */
    public $oldPath = '';

    /**
     * ファイル保存可否
     * true の場合、ページデータ保存の際、ページテンプレートファイルにも内容を保存する
     * テンプレート読み込み時などはfalseにして保存しないようにする
     *
     * @var boolean
     */
    public $fileSave = true;

    /**
     * 検索テーブルへの保存可否
     *
     * @var boolean
     */
    public $searchIndexSaving = true;

    /**
     * 公開WebページURLリスト
     * キャッシュ用
     *
     * @var mixed
     */
    protected $_publishes = -1;

    /**
     * WebページURLリスト
     * キャッシュ用
     *
     * @var mixed
     */
    protected $_pages = -1;

    /**
     * 最終登録ID
     * モバイルページへのコピー処理でスーパークラスの最終登録IDが上書きされ、
     * コントローラーからは正常なIDが取得できないのでモバイルページへのコピー以外の場合だけ保存する
     *
     * @var int
     */
    private $__pageInsertID = null;

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
        parent::initialize($config);
        $this->addBehavior('BaserCore.BcContents');
        // $this->addBehavior('BaserCore.BcSearchIndexManager');
    }

    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
        ->integer('id')
        ->numeric('id', __d('baser', 'IDに不正な値が利用されています。'), 'update')
        ->requirePresence('id', true);

        $validator
        ->scalar('contents')
        ->allowEmptyString('contents', null)
        ->maxLengthBytes('contents', 64000, __d('baser', '本稿欄に保存できるデータ量を超えています。'))
        ->add('contents', 'custom', [
            'rule' => [$this, 'phpValidSyntax'],
            'message' => __d('baser', '本稿欄でPHPの構文エラーが発生しました。')
        ])
        ->add('contents', [
            'containsScript' => [
                'rule' => ['containsScript'],
                'provider' => 'bc',
                'message' => __d('baser', '本稿欄でスクリプトの入力は許可されていません。')
            ]
        ]);

        $validator
        ->scalar('draft')
        ->allowEmptyString('draft', null)
        ->maxLengthBytes('draft', 64000, __d('baser', '本稿欄に保存できるデータ量を超えています。'))
        ->add('draft', 'custom', [
            'rule' => [$this, 'phpValidSyntax'],
            'message' => __d('baser', '本稿欄でPHPの構文エラーが発生しました。')
        ])
        ->add('draft', [
            'containsScript' => [
                'rule' => ['containsScript'],
                'provider' => 'bc',
                'message' => __d('baser', '本稿欄でスクリプトの入力は許可されていません。')
            ]
        ]);

        $validator
        ->scalar('code')
        ->allowEmptyString('code', null)
        ->add('code', 'custom', [
            'rule' => [$this, 'phpValidSyntax'],
            'message' => __d('baser', '本稿欄でPHPの構文エラーが発生しました。')
        ])
        ->add('code', [
            'containsScript' => [
                'rule' => ['containsScript'],
                'provider' => 'bc',
                'message' => __d('baser', '本稿欄でスクリプトの入力は許可されていません。')
            ]
        ]);

        return $validator;
    }

    /**
     * Before Save
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return bool
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if (!$this->fileSave) {
            return true;
        }

        // 保存前のページファイルのパスを取得
        $ContentService = $this->getService(ContentServiceInterface::class);
        if ($ContentService->exists($entity->content->id) && !empty($entity->content)) {
            $this->oldPath = $this->getPageFilePath(
                $this->find('first', [
                        'conditions' => ['Page.id' => $entity->id],
                        'recursive' => 0]
                )
            );
        } else {
            $this->oldPath = '';
        }

        // 新しいページファイルのパスが開けるかチェックする
        $result = true;
        if (!empty($entity->content)) {
            if (!$this->checkOpenPageFile($entity)) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * afterSave
     *
     * @param  EventInterface $event
     * @param  EntityInterface $entity
     * @param  ArrayObject $options
     * @return void
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {

        // parent::afterSave($created, $options);

        if (empty($entity->id)) {
            $data = $this->read(null, $this->id);
        } else {
            $data = $this->read(null, $entity->id);
        }

        if ($this->fileSave) {
            $this->createPageTemplate($data);
        }

        // 検索用テーブルに登録
        if ($this->searchIndexSaving) {
            if (empty($entity->content->exclude_search)) {
                $this->saveSearchIndex($this->createSearchIndex($entity));
            } else {
                $this->deleteSearchIndex($entity->id);
            }
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
        if (!isset($data['Page']['id']) || !isset($data['Content']['id'])) {
            return false;
        }
        $page = $data['Page'];
        $content = $data['Content'];
        if (!isset($content['publish_begin'])) {
            $content['publish_begin'] = '';
        }
        if (!isset($content['publish_end'])) {
            $content['publish_end'] = '';
        }

        if (!$content['title']) {
            $content['title'] = Inflector::camelize($content['name']);
        }

        // $this->idに値が入ってない場合もあるので
        if (!empty($page['id'])) {
            $modelId = $page['id'];
        } else {
            $modelId = $this->id;
        }

        $host = '';
        $url = $content['url'];
        $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sites->findById($content['site_id'])->first();
        if ($site->useSubDomain) {
            $host = $site->alias;
            if ($site->domainType == 1) {
                $host .= '.' . BcUtil::getMainDomain();
            }
            $url = preg_replace('/^\/' . preg_quote($site->alias, '/') . '/', '', $url);
        }
        $parameters = explode('/', preg_replace("/^\//", '', $url));
        $detail = $this->requestAction(['admin' => false, 'plugin' => false, 'controller' => 'pages', 'action' => 'display'], ['?' => [
            'force' => 'true',
            'host' => $host
        ], 'pass' => $parameters, 'return']);

        $detail = preg_replace('/<!-- BaserPageTagBegin -->.*?<!-- BaserPageTagEnd -->/is', '', $detail);
        $description = '';
        if (!empty($content['description'])) {
            $description = $content['description'];
        }
        return ['SearchIndex' => [
            'model_id' => $modelId,
            'type' => __d('baser', 'ページ'),
            'content_id' => $content['id'],
            'site_id' => $content['site_id'],
            'title' => $content['title'],
            'detail' => $description . ' ' . $detail,
            'url' => $url,
            'status' => $content['status'],
            'publish_begin' => $content['publish_begin'],
            'publish_end' => $content['publish_end']
        ]];
    }

    /**
     * コントロールソースを取得する
     *
     * @param string $field フィールド名
     * @param array $options
     * @return mixed $controlSource コントロールソース
     */
    public function getControlSource($field, $options = [])
    {
        switch($field) {
            case 'user_id':
            case 'author_id':
                $controlSources[$field] = $this->Content->User->getUserList($options);
                break;
        }

        if (isset($controlSources[$field])) {
            return $controlSources[$field];
        } else {
            return false;
        }
    }

    /**
     * ページデータをコピーする
     *
     * 固定ページテンプレートの生成処理を実行する必要がある為、
     * Content::copy() は利用しない
     *
     * @param int $id ページID
     * @param int $newParentId 新しい親コンテンツID
     * @param string $newTitle 新しいタイトル
     * @param int $newAuthorId 新しいユーザーID
     * @param int $newSiteId 新しいサイトID
     * @return mixed page Or false
     */
    public function copy($id, $newParentId, $newTitle, $newAuthorId, $newSiteId = null)
    {
        // TODO 暫定措置
        // >>>
        return;
        // <<<

        $data = $this->find('first', ['conditions' => ['Page.id' => $id], 'recursive' => 0]);
        $oldData = $data;

        // EVENT Page.beforeCopy
        $event = $this->dispatchLayerEvent('beforeCopy', [
            'data' => $data,
            'id' => $id,
        ]);
        if ($event !== false) {
            $data = $event->getResult() === true? $event->getData('data') : $event->getResult();
        }

        $url = $data['Content']['url'];
        $siteId = $data['Content']['site_id'];
        $name = $data['Content']['name'];
        $eyeCatch = $data['Content']['eyecatch'];
        $description = $data['Content']['description'];
        unset($data['Page']['id']);
        unset($data['Page']['created']);
        unset($data['Page']['modified']);
        unset($data['Content']);
        $data['Content'] = [
            'name' => $name,
            'parent_id' => $newParentId,
            'title' => $newTitle,
            'author_id' => $newAuthorId,
            'site_id' => $newSiteId,
            'description' => $description
        ];
        if (!is_null($newSiteId) && $siteId != $newSiteId) {
            $data['Content']['site_id'] = $newSiteId;
            $data['Content']['parent_id'] = $this->Content->copyContentFolderPath($url, $newSiteId);
        }
        $this->getDataSource()->begin();
        $this->create(['Content' => $data['Content'], 'Page' => $data['Page']]);
        if ($data = $this->save()) {
            if ($eyeCatch) {
                $data['Content']['eyecatch'] = $eyeCatch;
                $this->Content->set(['Content' => $data['Content']]);
                $result = $this->Content->renameToBasenameFields(true);
                $result = $this->Content->save($result, ['validate' => false, 'callbacks' => false]);
                $data['Content'] = $result['Content'];
            }

            $data['Page']['id'] = $this->getLastInsertID();

            // EVENT Page.afterCopy
            $event = $this->dispatchLayerEvent('afterCopy', [
                'data' => $data,
                'id' => $data['Page']['id'],
                'oldId' => $id,
                'oldData' => $oldData,
            ]);

            $this->getDataSource()->commit();
            return $data;
        }
        $this->getDataSource()->rollback();
        return false;
    }

    /**
     * PHP構文チェック
     *
     * @param array $check チェック対象文字列
     * @return bool
     */
    public function phpValidSyntax($check)
    {
        if (empty($check[key($check)])) {
            return true;
        }
        if (!Configure::read('BcApp.validSyntaxWithPage')) {
            return true;
        }
        if (!function_exists('exec')) {
            return true;
        }
        // CL版 php がインストールされてない場合はシンタックスチェックできないので true を返す
        exec('php --version 2>&1', $output, $exit);
        if ($exit !== 0) {
            return true;
        }

        if (isWindows()) {
            $tmpName = tempnam(TMP, "syntax");
            $tmp = new File($tmpName);
            $tmp->open("w");
            $tmp->write($check[key($check)]);
            $tmp->close();
            $command = sprintf("php -l %s 2>&1", escapeshellarg($tmpName));
            exec($command, $output, $exit);
            $tmp->delete();
        } else {
            $format = 'echo %s | php -l 2>&1';
            $command = sprintf($format, escapeshellarg($check[key($check)]));
            exec($command, $output, $exit);
        }

        if ($exit === 0) {
            return true;
        }
        $message = __d('baser', 'PHPの構文エラーです') . '： ' . PHP_EOL . implode(' ' . PHP_EOL, $output);
        return $message;
    }
}
