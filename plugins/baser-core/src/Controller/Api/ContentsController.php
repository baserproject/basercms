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

namespace BaserCore\Controller\Api;

use BaserCore\Service\ContentServiceInterface;
use Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class ContentsController
 * @package BaserCore\Controller\Api
 */
class ContentsController extends BcApiController
{

    /**
     * コンテンツ情報取得
     * @param ContentServiceInterface $Contents
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(ContentServiceInterface $contents, $id)
    {
        $this->set([
            'contents' => $contents->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['contents']);
    }

    /**
     * コンテンツ情報一覧取得
     *
     * @param  ContentServiceInterface $contents
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(ContentServiceInterface $contents, $type="index")
    {
        switch ($type) {
            case "index":
                $data = $this->paginate($contents->getIndex($this->request->getQueryParams()));
                break;
            case "trash":
                $data = $this->paginate($contents->getTrashIndex($this->request->getQueryParams()));
                break;
            case "tree":
                $data = $this->paginate($contents->getTreeIndex($this->request->getQueryParams()));
                break;
            case "table":
                $data = $this->paginate($contents->getTableIndex($this->request->getQueryParams()));
                break;
        }
        $this->set([
            'contents' => $data
        ]);
        $this->viewBuilder()->setOption('serialize', ['contents']);
    }
}
