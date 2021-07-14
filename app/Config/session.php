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
 * セッション設定
 */

require BASER_CONFIGS .'session.php';

/**
 * セッションの有効期限（分）
 * デフォルト：2日間
 */
//Configure::write('Session.timeout', 60 * 24 * 2);

/**
 * セッションをデータベースに保存する場合の設定
 * デフォルト：php
 *
 * 予め セッションのテーブル {prefix}_cake_sessions を作成しておいてください。
 * SQLは app/Config/Schema/sessions.sql になります。
 */
//Configure::write('Session.defaults', 'database');
