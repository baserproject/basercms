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

/**
 * 固定ページデフォルトテンプレート
 * @var BaserCore\View\BcAdminAppView $this
 * @var Page $page
 */
$this->BcAdmin->setTitle($page['content']['title']);
$this->BcBaser->setDescription($page['content']['description']);
$this->BcBaser->setPageEditLink($page['id']);

echo $page['contents'];

$this->BcBaser->updateInfo();
