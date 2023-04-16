<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] CSVダウンロード
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $encoding
 * @var array $messages
 * @var int $mailContentId
 * @var string $contentName
 */
$this->BcCsv->encoding = $encoding;
$this->BcCsv->addModelDatas('MailMessage', $messages);
$this->BcCsv->download($contentName);
