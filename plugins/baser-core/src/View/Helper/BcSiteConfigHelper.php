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

namespace BaserCore\View\Helper;

use BaserCore\Annotation\Checked;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\SiteConfigService;
use BaserCore\Service\SiteConfigServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\View\Helper;

/**
 * BcSiteConfigHelper
 * @property SiteConfigService $SiteConfigService
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
        $this->SiteConfigService = $this->getService(SiteConfigServiceInterface::class);
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
        return $this->SiteConfigService->getValue($fieldName);
    }

}
