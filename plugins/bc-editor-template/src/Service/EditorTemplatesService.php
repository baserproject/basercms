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

namespace BcEditorTemplate\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Error\BcException;
use BaserCore\Utility\BcUtil;
use BcEditorTemplate\Model\Table\EditorTemplatesTable;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;

/**
 * EditorTemplatesService
 *
 * @property EditorTemplatesTable $EditorTemplates
 */
class EditorTemplatesService implements EditorTemplatesServiceInterface
{

    /**
     * Constructor
     *
     * @checked
     * @noTodo
     */
    public function __construct()
    {
        $this->EditorTemplates = TableRegistry::getTableLocator()->get('BcEditorTemplate.EditorTemplates');
    }

    /**
     * エディタテンプレートの初期値を取得する
     *
     * @return EntityInterface
     * @checked
     * @noTodo
     */
    public function getNew()
    {
        return $this->EditorTemplates->newEntity([]);
    }

    /**
     * 単一のエディターテンプレートを取得する
     *
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     */
    public function get(int $id)
    {
        return $this->EditorTemplates->get($id);
    }

    /**
     * エディタテンプレートの一覧を取得する
     *
     * @return \Cake\ORM\Query
     * @checked
     * @noTodo
     */
    public function getIndex()
    {
        return $this->EditorTemplates->find();
    }

    /**
     * エディタテンプレートを追加する
     *
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     */
    public function create(array $postData)
    {
        if (BcUtil::isOverPostSize()) {
            throw new BcException(__d(
                'baser',
                '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                ini_get('post_max_size')
            ));
        }
        $entity = $this->EditorTemplates->patchEntity($this->EditorTemplates->newEmptyEntity(), $postData);
        return $this->EditorTemplates->saveOrFail($entity);
    }

    /**
     * エディターテンプレートを更新する
     *
     * @param EntityInterface $entity
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     */
    public function update(EntityInterface $entity, array $postData)
    {
        if (BcUtil::isOverPostSize()) {
            throw new BcException(__d(
                'baser',
                '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                ini_get('post_max_size')
            ));
        }
        $entity = $this->EditorTemplates->patchEntity($entity, $postData);
        return $this->EditorTemplates->saveOrFail($entity);
    }

    /**
     * エディターテンプレートを削除する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     */
    public function delete(int $id) {
        $entity = $this->get($id);
        return $this->EditorTemplates->delete($entity);
    }

    /**
     * エディターテンプレートのリストを取得する
     *
     * @return array
     * @checked
     * @noTodo
     */
    public function getList()
    {
        return $this->EditorTemplates->find('list')->toArray();
    }

}
