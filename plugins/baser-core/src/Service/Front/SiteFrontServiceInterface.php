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

namespace BaserCore\Service\Front;

use BaserCore\Model\Entity\Site;
use BaserCore\Utility\BcAbstractDetector;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * SiteFrontServiceInterface
 */
interface SiteFrontServiceInterface
{

    /**
     * 現在のURLからサイトを取得する
     *
     * @param bool $direct
     * @return Site|null
     */
    public function findCurrent($direct = true): Site;

    /**
     * 現在のサイトに関連するメインサイトを取得
     *
     * @return Site|null
     */
    public function findCurrentMain(): ?Site;

    /**
     * 現在のサイトとユーザーエージェントに関連するサイトを取得する
     *
     * @param bool $sameMainUrl
     * @param BcAbstractDetector $detector
     * @param bool $sameMainUrl
     * @return Site|null
     */
    public function findCurrentSub($sameMainUrl = false, BcAbstractDetector $agent = null, $lang = null): ?Site;

}
