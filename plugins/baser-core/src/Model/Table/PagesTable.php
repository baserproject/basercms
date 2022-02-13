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
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\Note;
use Cake\Event\EventInterface;
use Cake\Validation\Validator;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\EntityInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Service\ContentServiceInterface;

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
        ->requirePresence('id', 'update');

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

        return $validator;
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
        // 検索用テーブルに登録
        if ($this->searchIndexSaving) {
            // TODO ucmitz: BcSearchIndexManagerBehaviorが追加されていないため一旦スキップ
            return;
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
     * PHP構文チェック
     *
     * @param string $check チェック対象文字列
     * @return bool
     * @checked
     * @unitTest
     * @note(value="BcApp.validSyntaxWithPageがsetting.phpに定義されていないためコメントアウト")
     */
    public function phpValidSyntax($check)
    {
        if (empty($check)) {
            return true;
        }
        // TODO ucmitz: note
        // if (!Configure::read('BcApp.validSyntaxWithPage')) {
        //     return true;
        // }
        if (!function_exists('exec')) {
            return true;
        }
        // CL版 php がインストールされてない場合はシンタックスチェックできないので true を返す
        exec('php --version 2>&1', $output, $exit);
        if ($exit !== 0) {
            return true;
        }

        if (BcUtil::isWindows()) {
            $tmpName = tempnam(TMP, "syntax");
            $tmp = new File($tmpName);
            $tmp->open("w");
            $tmp->write($check);
            $tmp->close();
            $command = sprintf("php -l %s 2>&1", escapeshellarg($tmpName));
            exec($command, $output, $exit);
            $tmp->delete();
        } else {
            $format = 'echo %s | php -l 2>&1';
            $command = sprintf($format, escapeshellarg($check));
            exec($command, $output, $exit);
        }

        if ($exit === 0) {
            return true;
        }
        $message = __d('baser', 'PHPの構文エラーです') . '： ' . PHP_EOL . implode(' ' . PHP_EOL, $output);
        return $message;
    }
}
