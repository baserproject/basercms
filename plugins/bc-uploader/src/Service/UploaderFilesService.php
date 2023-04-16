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

namespace BcUploader\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Error\BcException;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Datasource\EntityInterface;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;

/**
 * UploaderFilesService
 */
class UploaderFilesService implements UploaderFilesServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * constructor.
     *
     * @checked
     * @noTodo
     */
    public function __construct()
    {
        $this->UploaderFiles = TableRegistry::getTableLocator()->get('BcUploader.UploaderFiles');
        $this->uploaderConfigsService = $this->getService(UploaderConfigsServiceInterface::class);
    }

    /**
     * アップロードファイルの一覧を取得
     *
     * @param array $queryParams
     * @return \Cake\ORM\Query
     * @checked
     * @noTodo
     */
    public function getIndex(array $queryParams = [])
    {
        $params = array_merge([
            'num' => null
        ], $queryParams);

        $conditions = $this->createAdminIndexConditions($params);
        $query = $this->UploaderFiles->find()
            ->order(['created DESC'])
            ->where($conditions);

        if($params['num']) {
            $query->limit($params['num']);
        }
        return $query;
    }

    /**
     * 一覧の検索条件を生成する
     *
     * @param array $params
     * @return array
     * @checked
     * @noTodo
     */
    protected function createAdminIndexConditions(array $params)
    {
        $conditionsTmp = [];
        if(!empty($params['conditions'])) {
            $conditionsTmp = $params['conditions'];
            unset($params['conditions']);
        }
        $conditions = [];
        if (!empty($params['uploader_category_id'])) {
            $conditions = ['UploaderFiles.uploader_category_id' => $params['uploader_category_id']];
        }
        if (!empty($params['uploader_type'])) {
            switch($params['uploader_type']) {
                case 'img':
                    $conditions['or'][] = ['UploaderFiles.name LIKE' => '%.png'];
                    $conditions['or'][] = ['UploaderFiles.name LIKE' => '%.jpg'];
                    $conditions['or'][] = ['UploaderFiles.name LIKE' => '%.gif'];
                    break;
                case 'etc':
                    $conditions['and'][] = ['UploaderFiles.name NOT LIKE' => '%.png'];
                    $conditions['and'][] = ['UploaderFiles.name NOT LIKE' => '%.jpg'];
                    $conditions['and'][] = ['UploaderFiles.name NOT LIKE' => '%.gif'];
                    break;
                case 'all':
                case '':
            }
        }
        if (!empty($params['name'])) {
            $conditions['and']['or'][] = ['UploaderFiles.name LIKE' => '%' . $params['name'] . '%'];
            $conditions['and']['or'][] = ['UploaderFiles.alt LIKE' => '%' . $params['name'] . '%'];
        }
        // 管理ユーザ以外が利用時、ユーザ制限がOnになっていれば一覧に表示しない
        $uploaderConfig = $this->uploaderConfigsService->get();
        if ($uploaderConfig->use_permission && !BcUtil::isAdminUser()) {
            $user = BcUtil::loginUser();
            if ($user) $conditions['UploaderFiles.user_id'] = $user->id;
        }
        return array_merge_recursive($conditionsTmp, $conditions);
    }

    /**
     * コントロールソースを取得する
     *
     * @param string $field フィールド名
     * @param array $options
     * @return array|false コントロールソース
     * @checked
     * @noTodo
     */
    public function getControlSource($field = null, $options = [])
    {
        switch ($field) {
            case 'user_id':
                $usersTable = TableRegistry::getTableLocator()->get('BaserCore.Users');
                return $usersTable->getUserList($options);
            case 'uploader_category_id':
                $uploaderCategoriesTable = TableRegistry::getTableLocator()->get('BcUploader.UploaderCategories');
                return $uploaderCategoriesTable->find('list')->order(['UploaderCategories.id'])->toArray();
        }
        return false;
    }

    /**
     * アップロードファイルを取得する
     *
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     */
    public function get(int $id)
    {
        return $this->UploaderFiles->get($id);
    }

    /**
     * アップロードファイルを削除する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     */
    public function delete(int $id): bool
    {
        $entity = $this->UploaderFiles->get($id);
        if(!$this->isEditable($entity->toArray())) {
            throw new BcException(__d('baser_core', 'ファイルの変更権限がありません。' ));
        }
        return $this->UploaderFiles->delete($entity);
    }

    /**
     * アップロードファイルを登録する
     *
     * @param array $postData
     * @return \Cake\Datasource\EntityInterface
     * @checked
     * @noTodo
     */
    public function create(array $postData)
    {
        if (BcUtil::isOverPostSize()) {
            throw new BcException(__d('baser_core',
                '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                ini_get('post_max_size')
            ));
        }
        if (!empty($postData['publish_begin'])) $postData['publish_begin'] = new FrozenTime($postData['publish_begin']);
        if (!empty($postData['publish_end'])) $postData['publish_end'] = new FrozenTime($postData['publish_end']);

        if (!isset($postData['file'])){
            throw new BcException(__d('baser_core', 'ファイルが存在しません。'));
        }

        $postData['file']['name'] = str_replace(['/', '&', '?', '=', '#', ':', '%', '+'], '_', h($postData['file']['name']));
        $postData['name'] = $postData['file'];
        $postData['alt'] = $postData['name']['name'];
        $entity = $this->UploaderFiles->patchEntity($this->getNew(), $postData);
        return $this->UploaderFiles->saveOrFail($entity);
    }

    /**
     * アップロードファイルを更新する
     *
     * @param EntityInterface $entity
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     */
    public function update(EntityInterface $entity, array $postData)
    {
        if(!$this->isEditable($postData)) {
            throw new BcException(__d('baser_core', 'ファイルの変更権限がありません。' ));
        }
        if (!empty($postData['publish_begin'])) {
            $postData['publish_begin'] = new FrozenTime($postData['publish_begin']);
        }
        if (!empty($postData['publish_end'])) {
            $postData['publish_end'] = new FrozenTime($postData['publish_end']);
        }
        $entity = $this->UploaderFiles->patchEntity($entity, $postData);
        return $this->UploaderFiles->saveOrFail($entity);
    }

    /**
     * 編集可能な権限を持っているかどうか
     *
     * @param array $postData
     * @return bool
     * @checked
     * @noTodo
     */
    public function isEditable(array $postData)
    {
        if(!$this->uploaderConfigsService->get()->use_permission) return true;
        $user = BcUtil::loginUser();
        if (!BcUtil::isAdminUser($user) && $postData['user_id'] !== $user->id) {
            return false;
        }
        return true;
    }

    /**
     * 初期データ取得
     *
     * @return \Cake\Datasource\EntityInterface
     * @checked
     * @noTodo
     */
    public function getNew()
    {
        return $this->UploaderFiles->newEntity([
            'user_id' => BcUtil::loginUser()->id
        ]);
    }

}
