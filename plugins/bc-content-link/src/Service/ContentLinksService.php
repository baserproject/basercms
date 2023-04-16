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

namespace BcContentLink\Service;

use BcContentLink\Model\Table\ContentLinksTable;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * ContentLinksService
 * @property ContentLinksTable $ContentLinks
 */
class ContentLinksService implements ContentLinksServiceInterface
{

    /**
     * Constructor
     *
     * ContentLinksTable を初期化し、メンバーにセットする
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->ContentLinks = TableRegistry::getTableLocator()->get('BcContentLink.ContentLinks');
    }

    /**
     * 単一のエンティティを取得する
     *
     * Site を内包した、Content を内包する
     *
     * @param int $id
     *  - status: ステータス。publish を指定すると公開状態のもののみ取得（初期値：全て）
     * @return EntityInterface
     * @checked
     * @noTodo
     */
    public function get($id, $options = [])
    {
        $options = array_merge([
            'status' => '',
            'contain' => ['Contents' => ['Sites']]
        ], $options);
        $conditions = [];
        if($options['status'] === 'publish') {
            $conditions = $this->ContentLinks->Contents->getConditionAllowPublish();
        }
        return $this->ContentLinks->get($id, [
            'contain' => $options['contain'],
            'conditions' => $conditions
        ]);
    }

    /**
     * コンテンツリンクを新しく登録する
     *
     * @param array $postData
     * @return \Cake\Datasource\EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData)
    {
        $entity = $this->ContentLinks->newEntity(['url' => '']);
        $entity = $this->ContentLinks->patchEntity($entity, $postData);
        return $this->ContentLinks->saveOrFail($entity);
    }

    /**
     * コンテンツリンクを更新する
     *
     * @param EntityInterface $target
     * @param array $postData
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $postData): ?EntityInterface
    {
        $entity = $this->ContentLinks->patchEntity($target, $postData);
        return $this->ContentLinks->saveOrFail($entity);
    }

    /**
     * リンクをを削除する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete($id): bool
    {
        $entity = $this->get($id, ['contain' => []]);
        return $this->ContentLinks->delete($entity);
    }
}
