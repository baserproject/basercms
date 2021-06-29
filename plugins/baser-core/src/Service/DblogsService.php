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
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Error\BcException;

/**
 * Class DblogsService
 */
class DblogsService implements DblogsServiceInterface
{

    /**
     * Dblogs Table
     * @var \Cake\ORM\Table
     */
    private $Dblogs;

    /**
     * DblogsService constructor.
     */
    public function __construct()
    {
        $this->Dblogs = TableRegistry::getTableLocator()->get('BaserCore.Dblogs');
    }

    /**
     * DBログ登録
     * @param array $data
     * @return \Cake\Datasource\EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $data)
    {
        $dblog = $this->Dblogs->newEntity($data);
        $savedDblog = $this->Dblogs->save($dblog);
        if (!$savedDblog) {
            throw new BcException(__d('baser', 'DBログの保存に失敗しました。'));
        }
        return $savedDblog;
    }

}
