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

use Cake\View\Helper;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\PagesService;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Service\PagesServiceInterface;

/**
 * BcAdminPageHelper
 * @property PagesService $PagesService
 * @property BcAdminHelper $BcAdmin
 */
class BcAdminPageHelper extends Helper
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Helper
     * @var string[]
     */
    public $helpers = ['BaserCore.BcAdmin'];

    /**
     * initialize
     * @param array $config
     * @checked
     * @noTodo
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->PagesService = $this->getService(PagesServiceInterface::class);
        $this->BcAdmin->setPublishLink($this->PagesService->getPublishLink($this->_View->getRequest()));
    }

    /**
     * 固定ページテンプレートリストを取得する
     *
     * @param $contentId
     * @param $theme
     * @return array
     */
    public function getPageTemplateList($contentId, $theme)
    {
        return $this->PagesService->getPageTemplateList($contentId, $theme);
    }
}
