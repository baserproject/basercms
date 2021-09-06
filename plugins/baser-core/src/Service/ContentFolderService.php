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

use Cake\ORM\TableRegistry;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\EntityInterface;
use BaserCore\Model\Table\ContentFoldersTable;
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
        return $this->ContentFolders->get($id, ['contain' => ['Contents']]);
    }

    /**
     * コンテンツフォルダー登録
     * @param array $data
     * @return \Cake\Datasource\EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData)
    {
        $contentFolder = $this->ContentFolders->newEmptyEntity();
        $contentFolder = $this->ContentFolders->patchEntity($contentFolder, $postData);
        return ($result = $this->ContentFolders->save($contentFolder)) ? $result : $contentFolder;
    }

    /**
     * コンテンツフォルダーを削除する
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete($id)
    {
        $ContentFolder = $this->get($id);
        return $this->Contents->hardDelete($ContentFolder->content) && $this->ContentFolders->delete($ContentFolder);
    }
}
