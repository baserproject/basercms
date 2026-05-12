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

namespace BaserCore\Utility;

use BaserCore\Model\Entity\UserInterface;
use Cake\Cache\Cache;

/**
 * BcEditSession
 */
class BcEditSession
{

    /**
     * Cache config name
     */
    public const CACHE_CONFIG = 'default';

    /**
     * Cache key prefix
     */
    private const CACHE_KEY_PREFIX = 'bc_edit_session_';

    /**
     * Edit sessions are lightweight warnings, not hard locks.
     */
    private const EXPIRES = 300;

    /**
     * Mark a resource as being edited.
     *
     * @param string $type
     * @param int $id
     * @param UserInterface|null $user
     * @return array|null
     */
    public static function mark(string $type, int $id, ?UserInterface $user): ?array
    {
        if (!$user) {
            return null;
        }

        $key = self::createKey($type, $id);
        $current = Cache::read($key, self::CACHE_CONFIG);

        if (
            $current &&
            (int)$current['user_id'] !== (int)$user->id &&
            $current['started'] + self::EXPIRES >= time()
        ) {
            return $current;
        }

        Cache::write($key, [
            'user_id' => $user->id,
            'user_name' => $user->getDisplayName(),
            'started' => time()
        ], self::CACHE_CONFIG);

        return null;
    }

    /**
     * Clear a resource edit session.
     *
     * @param string $type
     * @param int $id
     * @return bool
     */
    public static function clear(string $type, int $id): bool
    {
        return Cache::delete(self::createKey($type, $id), self::CACHE_CONFIG);
    }

    /**
     * Create cache key.
     *
     * @param string $type
     * @param int $id
     * @return string
     */
    private static function createKey(string $type, int $id): string
    {
        return self::CACHE_KEY_PREFIX . preg_replace('/[^a-z0-9_]/i', '_', $type) . '_' . $id;
    }

}
