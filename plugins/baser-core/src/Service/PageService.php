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

namespace BaserCore\Service;

use Cake\ORM\Query;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\NoTodo;
use BaserCore\Model\Entity\Page;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Core\Exception\Exception;
use Cake\Datasource\EntityInterface;
use BaserCore\Model\Table\PagesTable;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Service\ContentFolderService;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Service\ContentFolderServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;

/**
 * Class PageService
 * @package BaserCore\Service
 * @property PagesTable $Pages
 */
class PageService implements PageServiceInterface
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
     */
    public function __construct()
    {
        $this->Pages = TableRegistry::getTableLocator()->get('BaserCore.Pages');
        $this->Contents = TableRegistry::getTableLocator()->get('BaserCore.Contents');
    }

    /**
     * 固定ページを取得する
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): EntityInterface
    {
        return $this->Pages->get($id, ['contain' => ['Contents' => ['Sites']]]);
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
    public function create(array $postData, $options=[])
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
     */
    public function update(EntityInterface $target, array $pageData, $options = [])
    {
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
    public function delete($id)
    {
        $Page = $this->get($id);
        return $this->Pages->delete($Page);
    }

    /**
	 * 本文にbaserが管理するタグを追加する
	 *
	 * @param string $id ID
	 * @param string $contents 本文
	 * @param string $title タイトル
	 * @param string $description 説明文
	 * @return string 本文の先頭にbaserCMSが管理するタグを付加したデータ
	 */
	public function addBaserPageTag($id, $contents, $title, $description)
	{
		$tag = [];
		$tag[] = '<!-- BaserPageTagBegin -->';
		$title = str_replace("'", "\'", str_replace("\\", "\\\\'", $title));
		$description = str_replace("'", "\'", str_replace("\\", "\\\\'", $description));
		$tag[] = '<?php $this->BcBaser->setTitle(\'' . $title . '\') ?>';
		$tag[] = '<?php $this->BcBaser->setDescription(\'' . $description . '\') ?>';

		if ($id) {
			$tag[] = '<?php $this->BcBaser->setPageEditLink(' . $id . ') ?>';
		}
		$tag[] = '<!-- BaserPageTagEnd -->';
		return implode("\n", $tag) . "\n\n" . $contents;
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
            /** @var ContentFolderService $ContentFolderService  */
            $ContentFolderService = $this->getService(ContentFolderServiceInterface::class);
            $parentTemplate = $ContentFolderService->getParentTemplate($contentId, 'page');
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
     * 固定ページテンプレートの生成処理を実行する必要がある為、
     * Content::copy() は利用しない
     *
     * @param array $postData
     * @return Page $result
     * @checked
     * @unitTest
     */
    public function copy($postData)
    {
        $page = $this->get($postData['entityId']);
        $oldSiteId = $page->content->site_id;
        unset($postData['entityId'], $postData['contentId'], $page->id, $page->content->id, $page->created, $page->modified);
        foreach ($postData as $key => $value) {
            $page->content->{Inflector::underscore($key)} = $value;
        }
        // EVENT Page.beforeCopy
        // $event = $this->dispatchLayerEvent('beforeCopy', [
        //     'data' => $data,
        //     'id' => $id,
        // ]);
        // if ($event !== false) {
        //     $data = $event->getResult() === true? $event->getData('data') : $event->getResult();
        // }
        if (!is_null($postData['siteId']) && $postData['siteId'] !== $oldSiteId) {
            $page->content->parent_id = $this->Contents->copyContentFolderPath($page->content->url, $page->content->site_id);
        }
        $newPage = $this->Pages->patchEntity($this->Pages->newEmptyEntity(), $page->toArray());
        $result = $this->Pages->saveOrFail($newPage);
        if ($result->content->eyecatch) {
            $content = $this->Contents->renameToBasenameFields($result->content, true);
            $result->content = $content;
        }
        return $result;
        // EVENT Page.afterCopy
        // $event = $this->dispatchLayerEvent('afterCopy', [
        //     'data' => $data,
        //     'id' => $data['Page']['id'],
        //     'oldId' => $id,
        //     'oldData' => $oldData,
        // ]);
    }
}
