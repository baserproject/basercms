<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Config
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * @var BcAppView $this
 * @var string $editLink
 */

echo '<div class="bca-widget-edit-link">' . $this->BcBaser->getLink(__d('baser', '編集する'), $editLink) . '</div>';
