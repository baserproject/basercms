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

namespace BcWidgetArea\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BcWidgetArea\Model\Table\WidgetAreasTable;
use Cake\ORM\TableRegistry;

/**
 * WidgetAreasService
 *
 * @property WidgetAreasTable $WidgetAreas
 */
class WidgetAreasService implements WidgetAreasServiceInterface
{

    public function __construct()
    {
        $this->WidgetAreas = TableRegistry::getTableLocator()->get('BcWidgetArea.WidgetAreas');
    }

    /**
     * 単一データ取得
     */
    public function get()
    {
    }

    /**
     * 一覧データ取得
     * @checked
     * @noTodo
     * @param array $queryParams
     * @return \Cake\ORM\Query
     */
    public function getIndex(array $queryParams = [])
    {
        $options = array_merge([
            'limit' => null
        ], $queryParams);
        $query = $this->WidgetAreas->find();
        if(!is_null($options['limit'])) $query->limit($options['limit']);
        return $query;
    }

    /**
     * 初期データ取得
     */
    public function getNew()
    {

    }

    /**
     * 作成
     */
    public function create()
    {
    }

    /**
     * 編集
     */
    public function update()
    {

    }

    /**
     * 削除
     */
    public function delete()
    {
    }
}
