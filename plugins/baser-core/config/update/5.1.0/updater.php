<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.19
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * 5.1.0 アップデーター
 */

use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Utility\BcContainer;
use BaserCore\Utility\BcUpdateLog;

$updateDir = __DIR__;

if (is_writable(ROOT . DS . 'src' . DS . 'View' . DS . 'AjaxView.php')) {
    copy($updateDir . DS . 'src' . DS . 'View' . DS . 'AjaxView.php', ROOT . DS . 'src' . DS . 'View' . DS . 'AjaxView.php');
} else {
    BcUpdateLog::set(__d('baser_core', ROOT . DS . 'src' . DS . 'View' . DS . 'AjaxView.php に書き込み権限がありません。' . $updateDir . DS . 'src' . DS . 'View' . DS . 'AjaxView.php をコピーして手動で上書きしてください。'));
}

/** @var \BaserCore\Service\SiteConfigsService $siteConfigsService */
$siteConfigsService = BcContainer::get()->get(SiteConfigsServiceInterface::class);
$siteConfigsService->setValue('allow_simple_password', true);
