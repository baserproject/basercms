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

/**
 * コンテンツリンク
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var \BcContentLink\Model\Entity\ContentLink $contentLink
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<?php if ($contentLink->url): ?>
<?php $this->BcBaser->scriptStart(['block' => true]); ?>
<?php // リンク先 URL は JavaScript の文字列リテラルとして出力するため json_encode() でリテラル化する（格納型 XSS 対策） ?>
window.location.href = <?php echo json_encode((string)$contentLink->url, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES) ?>;
<?php $this->BcBaser->scriptEnd(); ?>
<?php endif ?>
