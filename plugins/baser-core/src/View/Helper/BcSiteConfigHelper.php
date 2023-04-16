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

namespace BaserCore\View\Helper;

use BaserCore\Annotation\Checked;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\SiteConfigsService;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\View\Helper;

/**
 * BcSiteConfigHelper
 * @property SiteConfigsService $SiteConfigsService
 */
class BcSiteConfigHelper extends Helper
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * initialize
     * @param array $config
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        if(!BcUtil::isInstalled()) return;
        $this->SiteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
    }

    /**
     * サイト設定を取得
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getValue($fieldName)
    {
        return $this->SiteConfigsService->getValue($fieldName);
    }

}
