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
 * @package BaserCore\Service
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
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(): EntityInterface
    {
        return $this->Pages->newEntity([]);
    }

    /**
     * リストデータ取得
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
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id, array $queryParams = []): EntityInterface
    {
        $queryParams = array_merge([
            'status' => ''
        ], $queryParams);
        $conditions = [];
        if($queryParams['status'] === 'published') {
            $conditions = $this->Pages->Contents->getConditionAllowPublish();
        }
        return $this->Pages->get($id, [
            'contain' => ['Contents' => ['Sites']],
            'conditions' => $conditions
        ]);
    }

    /**
     * 固定ページをゴミ箱から取得する
     * @param int $id
     * @return EntityInterface|array
     * @throws RecordNotFoundException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTrash($id)
    {
        $page = $this->Pages->findById($id)->contain('Contents', function (Query $q) {
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
     * @param array|null $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams=[]): Query
    {
        $query = $this->Pages->find('all')->contain('Contents');
        if (!empty($queryParams['limit'])) {
            $query->limit($queryParams['limit']);
        }

        $queryList = ['contents', 'draft'];

        foreach ($queryParams as $key => $value) {
            if (in_array($key, $queryList)) {
                $query->where(["$key LIKE" => '%' . $value . '%']);
            }
        }
        return $query;
    }

    /**
     * 固定ページ登録
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
        $page = $this->Pages->newEmptyEntity();
        $page = $this->Pages->patchEntity($page, $postData, $options);
        return $this->Pages->saveOrFail($page);
    }

    /**
     * ページ情報を更新する
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
            throw new BcException(__d(
                'baser',
                '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                ini_get('post_max_size')
            ));
        }
        $options = array_merge(['associated' => ['Contents' => ['validate' => 'default']]], $options);
        $page = $this->Pages->patchEntity($target, $pageData, $options);
        return $this->Pages->saveOrFail($page, ['atomic' => false]);
    }

    /**
     * 固定ページを削除する
     * @param int $id
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function delete(int $id): bool
    {
        $Page = $this->get($id);
        return $this->Pages->delete($Page);
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

        if ($contentId != 1) {
            /** @var ContentFoldersService $ContentFoldersService  */
            $ContentFoldersService = $this->getService(ContentFoldersServiceInterface::class);
            $parentTemplate = $ContentFoldersService->getParentTemplate($contentId, 'page');
            $searchKey = array_search($parentTemplate, $pageTemplates);
            if ($searchKey !== false) {
                unset($pageTemplates[$searchKey]);
            }
            $pageTemplates = ['' => sprintf(__d('baser', '親フォルダの設定に従う（%s）'), $parentTemplate)] + $pageTemplates;
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
            $postData['entity_id'],
            $postData['parent_id'],
            $postData['title'],
            BcUtil::loginUser()->id,
            $postData['site_id']
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
        return isset($controlSources[$field]) ? $controlSources[$field] : false;
    }

    /**
     * 編集リンクを取得する
     * @param ServerRequest $request
     * @return array|string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getEditLink(ServerRequest $request)
    {
        if(BcUtil::isAdminSystem()) return '';
        if($request->getParam('controller') !== 'Pages') return '';
        if($request->getParam('action') !== 'view') return '';
        return [
            'prefix' => 'Admin',
            'controller' => 'Pages',
            'action' => 'edit',
            $request->getAttribute('currentContent')->entity_id
        ];
    }

    /**
     * ページテンプレートを取得する
     * @param EntityInterface $page
     * @return mixed
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
