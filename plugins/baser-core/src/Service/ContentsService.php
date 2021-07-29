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
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Model\Table\SitesTable;

class ContentsService implements ContentsServiceInterface
{

    /**
     * Contents
     *
     * @var ContentsTable
     */
    public $Contents;

    /**
     * Sites
     *
     * @var SitesTable
     */
    public $Sites;

    public function __construct()
    {
        $this->Contents = TableRegistry::getTableLocator()->get("BaserCore.Contents");
        $this->Sites = TableRegistry::getTableLocator()->get("BaserCore.Sites");

    }
}

