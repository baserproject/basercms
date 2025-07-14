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

namespace BcCustomContent\Service;

use BaserCore\Error\BcException;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcFolder;
use BaserCore\Utility\BcUtil;
use BcCustomContent\Model\Table\CustomContentsTable;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomContentsService
 *
 * @property CustomContentsTable $CustomContents
 */
class CustomContentsService implements CustomContentsServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * CustomContents Table
     * @var CustomContentsTable|Table
     */
    public CustomContentsTable|Table $CustomContents;

    /**
     * Constructor
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->CustomContents = TableRegistry::getTableLocator()->get('BcCustomContent.CustomContents');
    }

    /**
     * カスタムコンテンツの一覧データ取得する
     * @param array $queryParams
     * @return Query
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams = []): Query
    {
        $queryParams = array_merge([
            'status' => 'publish',
            'contain' => ['Contents']
        ], $queryParams);

        if (is_null($queryParams['contain'])) {
            $fields = $this->CustomContents->getSchema()->columns();
            $query = $this->CustomContents->find()->contain(['Contents'])->select($fields);
        } else {
            $query = $this->CustomContents->find()->contain($queryParams['contain']);
        }

        if (!empty($queryParams['limit'])) {
            $query->limit($queryParams['limit']);
        }

        if ($queryParams['status'] === 'publish') {
            $query->where($this->CustomContents->Contents->getConditionAllowPublish());
        }

        if (!empty($queryParams['description'])) {
            $query->where(['description LIKE' => '%' . $queryParams['description'] . '%']);
        }

        return $query;
    }

    /**
     * カスタムコンテンツの単一データ取得する
     *
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(int $id, array $options = [])
    {
        $options = array_merge([
            'status' => ''
        ], $options);
        $conditions = [];
        if ($options['status'] === 'publish') {
            $conditions = $this->CustomContents->Contents->getConditionAllowPublish();
        }
        return $this->CustomContents->get($id,
            contain:  ['Contents' => ['Sites']],
            conditions: $conditions
        );
    }

    /**
     * カスタムコンテンツの初期値となるエンティティを取得する
     *
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(): EntityInterface
    {
        return $this->CustomContents->newEntity([
            'list_count' => 10,
            'list_order' => 'id',
            'list_direction' => 'DESC',
            'template' => 'default',
        ]);
    }

    /**
     * カスタムコンテンツを登録する
     *
     * @param array $postData
     * @param array $options
     * @return \Cake\Datasource\EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData, $options = []): ?EntityInterface
    {
        $entity = $this->CustomContents->patchEntity(
            $this->getNew(),
            $postData,
            $options
        );
        return $this->CustomContents->saveOrFail($entity);
    }

    /**
     * カスタムコンテンツを更新する
     *
     * @param EntityInterface $entity
     * @param array $postData
     * @param array $options
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $entity, array $postData, $options = []): ?EntityInterface
    {
        if (BcUtil::isOverPostSize()) {
            throw new BcException(__d('baser_core', '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。', ini_get('post_max_size')));
        }
        if ($postData['custom_table_id']) {
            $options['validate'] = 'withTable';
        }

        if (Configure::read('BcContents.autoUpdateContentCreatedDate')
            && isset($postData['content']['modified_date'])
            && $entity->content->modified_date == $postData['content']['modified_date']
        ) {
            $postData['content']['modified_date'] = date('Y-m-d H:i:s');
        }

        $entity = $this->CustomContents->patchEntity($entity, $postData, $options);
        return $this->CustomContents->saveOrFail($entity);
    }

    /**
     * @param $id
     * @return bool
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete($id): bool
    {
        $customContent = $this->get($id, ['contain' => []]);
        return $this->CustomContents->delete($customContent);
    }

    /**
     * カスタムコンテンツに関連するコントロールのソースを取得する
     *
     * @param string $field
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getControlSource(string $field, array $options = []): array
    {
        switch($field) {
            case 'custom_table_id':
                // コンテンツタイプのみ取得
                return $this->getService(CustomTablesServiceInterface::class)->getList(['type' => 1]);

            case 'list_order':
                if(!isset($options['custom_table_id'])) {
                    throw new BcException(__d('baser_core', 'list_order のコントロールソースを取得する場合は、custom_table_id の指定が必要です。'));
                }
                return $this->getListOrders($options['custom_table_id']);
            case 'template':
                if(!isset($options['site_id'])) {
                    throw new BcException(__d('baser_core', 'template のコントロールソースを取得する場合は、site_id の指定が必要です。'));
                }
                return $this->getTemplates($options['site_id']);
        }
        return [];
    }

    /**
     * 並び順フィールドのリストを取得
     *
     * @param int $tableId
     * @return string[]
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getListOrders(int $tableId): array
    {
        $list = ['id' => 'No', 'published' => __d('baser_core', '公開日付'), 'created' => __d('baser_core', '登録日'), 'modified' => __d('baser_core', '編集日')];
        if(!$tableId) return $list;
        $table = $this->CustomContents->CustomTables->get($tableId, contain: [
            'CustomLinks' => ['CustomFields']
        ]);
        if($table->custom_links) {
            foreach($table->custom_links as $customLink) {
                if($customLink->custom_field->status && $customLink->custom_field->type !== 'group') {
                    $list[$customLink->name] = $customLink->title;
                }
            }
        }
        return $list;
    }

    /**
     * テンプレートのリストを取得
     *
     * @param int $siteId
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTemplates(int $siteId): array
    {
        $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sites->get($siteId);

        $templatesPaths = array_merge(
            [Plugin::templatePath($site->getAppliedTheme()) . 'plugin' . DS . 'BcCustomContent' . DS],
            [Plugin::templatePath($site->getAppliedTheme())],
            App::path('templates'),
            [Plugin::templatePath(Configure::read('BcApp.coreFrontTheme')) . 'plugin' . DS . 'BcCustomContent' . DS],
            [Plugin::templatePath('BcCustomContent')]
        );

        $templates = [];
        foreach($templatesPaths as $templatePath) {
            $templatePath .= 'CustomContent' . DS;
            $folder = new BcFolder($templatePath);
            $files = $folder->getFolders();
            if ($files) {
                if ($templates) {
                    $templates = array_merge($templates, $files);
                } else {
                    $templates = $files;
                }
            }
        }
        return array_combine($templates, $templates);
    }

    /**
     * 関連づいたカスタムテーブルを除外する
     *
     * 全てのカスタムコンテンツを対象とする
     *
     * @param int $tableId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function unsetTable(int $tableId): void
    {
        $entities = $this->CustomContents->find()->where(['custom_table_id' => $tableId])->all();
        if(!$entities->count()) return;
        foreach($entities as $entity) {
            $entity->custom_table_id = null;
            $this->CustomContents->save($entity);
        }
    }

    /**
     * リストを取得する
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList(): array
    {
        return $this->CustomContents->find('list',
            keyField: 'id',
            valueField: 'content.title'
        )->contain(['Contents'])->toArray();
    }
}
