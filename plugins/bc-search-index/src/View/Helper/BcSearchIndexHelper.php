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

namespace BcSearchIndex\View\Helper;

use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Utility\BcContainerTrait;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use Cake\View\Helper;

/**
 * Class BcSearchIndexHelper
 */
class BcSearchIndexHelper extends Helper
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;
    use BcContainerTrait;

    /**
     * 公開状態確認
     *
     * @param array $data
     * @return bool
     * @checked
     * @noTodo
     * @unitTest （SearchIndexesTable に委ねる）
     */
    public function allowPublish($data)
    {
        $service = $this->getService(SearchIndexesServiceInterface::class);
        return $service->allowPublish($data);
    }

}
