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

namespace BaserCore\Service;

use BaserCore\Error\BcException;
use Cake\Http\ServerRequest;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use BaserCore\Utility\BcUtil;
use BaserCore\Model\Entity\Page;
use Cake\Datasource\EntityInterface;
use BaserCore\Model\Table\PagesTable;
use BaserCore\Utility\BcContainerTrait;
use Cake\Datasource\Exception\RecordNotFoundException;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\Note;
use BaserCore\Annotation\NoTodo;

/**
 * Class PagesService
 * @property PagesTable $Pages
 */
class PagesService implements PagesServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Pages Table
     * @var PagesTable
     */
    public $Pages;

    /**
     * Pageservice constructor.
     *
     * @checked
     * @unitTest
     * @noTodo
     */
    public function __construct()
    {
        $this->Pages = TableRegistry::getTableLocator()->get('BaserCore.Pages');
        $this->Contents = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $this->Users = TableRegistry::getTableLocator()->get('BaserCore.Users');
    }

    /**
     * 初期データ取得
     *
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(int $parentId = null, string $name = ''): EntityInterface
    {
        $parent  = null;
		if($parentId) {
            $parent = $this->Contents->find()->where([
                'Contents.type' => 'ContentFolder',
                'Contents.id' => $parentId,
            ])->first();
		}
		if (!$parent) {
			return $this->Pages->newEntity([]);
		} else {
            if($name) {
                $title = $name;
                $name = BcUtil::urlencode(mb_substr($name, 0, 230, 'UTF-8'));
            } else {
                $title = '';
            }
		    return $this->Pages->newEntity([
				'content' => [
                    'name' => $name,
                    'title' => $title,
                    'type' => 'Page',
                    'plugin' => 'BaserCore',
                    'alias_id' => null,
                    'site_root' => false,
                    'site_id' => $parent->site_id,
                    'parent_id' => $parent->id,
                    'self_status' => false
                ]
			], ['associated' => ['Contents' => ['validate' => false]]]);
		}
    }

    /**
     * リストデータ取得
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList(): array
    {
        return $this->Pages->find('list', [
            'keyField' => 'id',
            'valueField' => 'content.title'
        ])->contain(['Contents'])->toArray();
    }

    /**
     * 固定ページを取得する
     *
     * @param int $id
     * @param array $options
     *  - `status`: ステータス。 publish を指定すると公開状態のもののみ取得（初期値：全て）
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id, array $options = []): EntityInterface
    {
        $options = array_merge([
            'status' => '',
            'contain' => ['Contents' => ['Sites']],
            'draft' => false
        ], $options);
        $conditions = [];
        if ($options['status'] === 'publish') {
            $conditions = $this->Pages->Contents->getConditionAllowPublish();
        }
        $entity = $this->Pages->get($id, [
            'contain' => $options['contain'],
            'conditions' => $conditions,
        ]);
        if($options['draft'] === false) {
            unset($entity->draft);
        }
        return $entity;
    }

    /**
     * 固定ページをゴミ箱から取得する
     *
     * @param int $id
     * @return EntityInterface|array
     * @throws RecordNotFoundException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTrash($id)
    {
        $page = $this->Pages->findById($id)->contain('Contents', function(Query $q) {
            return $q->applyOptions(['withDeleted'])->contain(['Sites'])->where(['Contents.deleted_date IS NOT NULL']);
        })->firstOrFail();
        if (isset($page->content)) {
            return $page;
        } else {
            throw new RecordNotFoundException('Record not found in table "contents"');
        }
    }

    /**
     * ユーザー管理の一覧用のデータを取得
     *
     * @param array|null $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams = []): Query
    {
        $options = array_merge([
            'status' => '',
            'contain' => ['Contents'],
            'draft' => null
        ], $queryParams);

        $fields = $this->Pages->getSchema()->columns();
        if (is_null($options['contain']) || $options['draft'] === false) {
            if($options['draft'] === false) {
                unset($fields[array_search('draft', $fields)]);
            }
            $query = $this->Pages->find()->contain('Contents')->select($fields);
        } else {
            $query = $this->Pages->find()->contain($options['contain']);
        }

        if (!empty($options['limit'])) {
            $query->limit($options['limit']);
        }

        return $this->createIndexConditions($query, $options);
    }

    /**
     * 固定ページ一覧用の検索条件を生成する
     *
     * @param Query $query
     * @param array $options
     * @return Query
     * @checked
     * @noTodo
     */
    protected function createIndexConditions(Query $query, $options = [])
    {
        $options = array_merge([
            'status' => null
        ], $options);

        $conditions = [];
        if ($options['status'] === 'publish') {
            $conditions = $this->Pages->Contents->getConditionAllowPublish();
        }
        $queryList = ['contents', 'draft'];
        foreach ($options as $key => $value) {
            if (in_array($key, $queryList)) {
                $conditions["$key LIKE"] = '%' . $value . '%';
                $query->where(["$key LIKE" => '%' . $value . '%']);
            }
        }
        return $query->where($conditions);
    }

    /**
     * 固定ページ登録
     *
     * @param array $data
     * @param array $options
     * @return \Cake\Datasource\EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData, $options = []): ?EntityInterface
    {
        if (BcUtil::isOverPostSize()) {
            throw new BcException(__d('baser_core',
                '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                ini_get('post_max_size')
            ));
        }
        $page = $this->Pages->newEmptyEntity();
        $page = $this->Pages->patchEntity($page, $postData, $options);
        return $this->Pages->saveOrFail($page);
    }

    /**
     * ページ情報を更新する
     *
     * @param EntityInterface $target
     * @param array $pageData
     * @param array $options
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $pageData, $options = []): ?EntityInterface
    {
        if (BcUtil::isOverPostSize()) {
            throw new BcException(__d('baser_core',
                '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                ini_get('post_max_size')
            ));
        }
        $options = array_merge(['associated' => ['Contents' => ['validate' => 'default']]], $options);
        $page = $this->Pages->patchEntity($target, $pageData, $options);
        return $this->Pages->saveOrFail($page, ['atomic' => false]);
    }

    /**
     * 物理削除
     *
     * @param int $id
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     * @unitTest
     */
    public function delete(int $id): bool
    {
        $page = $this->get($id, ['contain' => []]);
        return $this->Pages->delete($page);
    }

    /**
     * 固定ページテンプレートリストを取得する
     *
     * @param int $contentId
     * @param array|string $plugins
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getPageTemplateList($contentId, $plugins)
    {
        $pageTemplates = BcUtil::getTemplateList('Pages', $plugins);

        if ($contentId && $contentId != 1) {
            /** @var ContentFoldersService $ContentFoldersService */
            $ContentFoldersService = $this->getService(ContentFoldersServiceInterface::class);
            $parentTemplate = $ContentFoldersService->getParentTemplate($contentId, 'page');
            $searchKey = array_search($parentTemplate, $pageTemplates);
            if ($searchKey !== false) {
                unset($pageTemplates[$searchKey]);
            }
            $pageTemplates = ['' => sprintf(__d('baser_core', '親フォルダの設定に従う（%s）'), $parentTemplate)] + $pageTemplates;
        }
        return $pageTemplates;
    }

    /**
     * ページデータをコピーする
     *
     * @param array $postData
     * @return Page $result
     * @checked
     * @unitTest
     * @noTodo
     */
    public function copy($postData)
    {
        return $this->Pages->copy(
            $postData['entity_id'] ?? null,
            $postData['parent_id'] ?? null,
            $postData['title'] ?? null,
            BcUtil::loginUser()->id,
            $postData['site_id'] ?? null
        );
    }

    /**
     * コントロールソースを取得する
     *
     * @param string $field フィールド名
     * @param array $conditions
     * @return array|false $controlSource コントロールソース
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getControlSource($field, $conditions = [])
    {
        if (in_array($field, ['user_id', 'author_id'])) {
            $controlSources[$field] = $this->Users->getUserList($conditions);
        }
        return isset($controlSources[$field])? $controlSources[$field] : false;
    }

    /**
     * 編集リンクを取得する
     *
     * @param ServerRequest $request
     * @return array|string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getEditLink(ServerRequest $request)
    {
        if (BcUtil::isAdminSystem()) return '';
        if ($request->getParam('controller') !== 'Pages') return '';
        if ($request->getParam('action') !== 'view') return '';
        return [
            'prefix' => 'Admin',
            'controller' => 'Pages',
            'action' => 'edit',
            $request->getAttribute('currentContent')->entity_id
        ];
    }

    /**
     * ページテンプレートを取得する
     *
     * @param EntityInterface $page
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getPageTemplate(EntityInterface $page)
    {
        if ($page->page_template) return $page->page_template;
        $contentFolderService = $this->getService(ContentFoldersServiceInterface::class);
        return $contentFolderService->getParentTemplate(
            $page->content->id,
            'page'
        );
    }

}
