<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader.Config
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */


Router::connect('/files/uploads/*', ['plugin' => 'uploader', 'controller' => 'uploader_files', 'action' => 'view_limited_file']);
