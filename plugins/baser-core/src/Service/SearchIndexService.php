<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service;

use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\NoTodo;
use BaserCore\Model\Entity\SearchIndex;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Core\Exception\Exception;
use Cake\Datasource\EntityInterface;
use BaserCore\Model\Table\SearchIndexsTable;
use BaserCore\Utility\BcContainerTrait;

/**
 * Class SearchIndexService
 * @package BaserCore\Service
 * @property SearchIndexsTable $SearchIndexs
 */
class SearchIndexService implements SearchIndexServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * SearchIndexs Table
     * @var SearchIndexsTable
     */
    public $SearchIndexs;

    /**
     * SearchIndexservice constructor.
     */
    public function __construct()
    {
        $this->SearchIndexs = TableRegistry::getTableLocator()->get('BaserCore.SearchIndexs');
    }

    /**
     * 索引を取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface
    {
        return $this->SearchIndexs->get($id);
    }
}
