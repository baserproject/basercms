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
 * フラッシュメッセージ
 *
 * BcBaserHelper::flash() より呼び出される
 * @var \BaserCore\View\BcFrontAppView $this
 * @var array $sessionMessageList
 * @var string $key
 */
?>


<div id="MessageBox" class="message-box">
  <?php
  foreach($sessionMessageList as $messageKey => $sessionMessage) {
    if ($key === $messageKey && $this->getRequest()->getSession()->check('Flash.' . $messageKey)) {
      echo $this->Flash->render($messageKey, ['escape' => false]);
    }
  }
  ?>
</div>
