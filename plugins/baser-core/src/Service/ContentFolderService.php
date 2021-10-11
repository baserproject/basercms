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
use Cake\Utility\Hash;
use Cake\ORM\TableRegistry;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\EntityInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Model\Table\ContentFoldersTable;
use BaserCore\Service\ContentServiceInterface;
use BaserCore\Service\ContentFolderServiceInterface;
/**
 * Class ContentFolderService
 * @package BaserCore\Service
 * @property ContentFoldersTable $ContentFolders
 * @property ContentsTable $Contents
 */
class ContentFolderService implements ContentFolderServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * ContentFolders Table
     * @var ContentFoldersTable
     */
    public $ContentFolders;

    /**
     * ContentFolderService constructor.
     */
    public function __construct()
    {
        $this->ContentFolders = TableRegistry::getTableLocator()->get('BaserCore.ContentFolders');
        $this->Contents = TableRegistry::getTableLocator()->get('BaserCore.Contents');
    }

    /**
     * コンテンツフォルダーを取得する
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): EntityInterface
    {
        return $this->ContentFolders->get($id, ['contain' => ['Contents' => ['Sites']]]);
    }

    /**
     * コンテンツフォルダーをゴミ箱から取得する
     * @param int $id
     * @return EntityInterface|array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTrash($id)
    {
        return $this->ContentFolders->findById($id)->contain('Contents', function (Query $q) {
            return $q->applyOptions(['withDeleted'])->contain(['Sites'])->where(['Contents.deleted_date IS NOT NULL']);
        })->firstOrFail();
    }

    /**
     * コンテンツフォルダー一覧用のデータを取得
     * @param array $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams=[]): Query
    {
        $options = [];
        if (!empty($queryParams['num'])) {
            $options = ['limit' => $queryParams['num']];
        }
        $query = $this->ContentFolders->find('all', $options)->contain('Contents');
        if (!empty($queryParams['folder_template'])) {
            $query->where(['folder_template LIKE' => '%' . $queryParams['folder_template'] . '%']);
        }
        if (!empty($queryParams['page_template'])) {
            $query->where(['page_template LIKE' => '%' . $queryParams['page_template'] . '%']);
        }
        return $query;
    }

    /**
     * コンテンツフォルダー登録
     * @param array $data
     * @param array $options
     * @return \Cake\Datasource\EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData, $options=[])
    {
        $contentFolder = $this->ContentFolders->newEmptyEntity();
        $contentFolder = $this->ContentFolders->patchEntity($contentFolder, $postData, $options);
        return ($result = $this->ContentFolders->save($contentFolder)) ? $result : $contentFolder;
    }

    /**
     * コンテンツフォルダーを削除する
     * @param int $id
     * @return bool
     * @checked
     * @unitTest
     */
    public function delete($id)
    {
        $ContentFolder = $this->get($id);
        return $this->ContentFolders->delete($ContentFolder);
    }

    /**
     * コンテンツフォルダー情報を更新する
     * @param EntityInterface $target
     * @param array $contentFolderData
     * @param array $options
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $contentFolderData, $options = [])
    {
        $options = array_merge(['associated' => ['Contents' => ['validate' => 'default']]], $options);
        $contentFolder = $this->ContentFolders->patchEntity($target, $contentFolderData, $options);
        return ($result = $this->ContentFolders->save($contentFolder))? $result : $contentFolder;
    }

    /**
     * フォルダのテンプレートリストを取得する
     *
     * @param $contentId
     * @param $theme
     * @return array
     */
    public function getFolderTemplateList($contentId, $theme)
    {
        if (!is_array($theme)) {
            $theme = [$theme];
        }
        $folderTemplates = [];
        foreach($theme as $value) {
            $folderTemplates = array_merge($folderTemplates, BcUtil::getTemplateList('ContentFolders', '', $value));
        }
        if ($contentId != 1) {
            $parentTemplate = $this->getParentTemplate($contentId, 'folder');
            $searchKey = array_search($parentTemplate, $folderTemplates);
            if ($searchKey !== false) {
                unset($folderTemplates[$searchKey]);
            }
            $folderTemplates = ['' => sprintf(__d('baser', '親フォルダの設定に従う（%s）'), $parentTemplate)] + $folderTemplates;
        }
        return $folderTemplates;
    }

    /**
     * 親のテンプレートを取得する
     *
     * @param int $id
     * @param string $type folder|page
     */
    public function getParentTemplate($id, $type)
    {
        // TODO ucmitz 暫定措置
        // >>>
        return 'default';
        // <<<

        $this->Content->bindModel(
            ['belongsTo' => [
                'ContentFolder' => [
                    'className' => 'ContentFolder',
                    'foreignKey' => 'entity_id'
                ]
            ]
            ],
            false
        );
        $contents = $this->Content->getPath($id, null, 0);
        $this->Content->unbindModel(
            ['belongsTo' => [
                'ContentFolder'
            ]
            ]
        );
        $contents = array_reverse($contents);
        unset($contents[0]);
        $parentTemplates = Hash::extract($contents, '{n}.ContentFolder.' . $type . '_template');
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
}
