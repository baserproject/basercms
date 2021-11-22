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

namespace BaserCore\Annotation;

/**
 * Class UnitTest
 * @package BaserCore\Annotation
 * @Annotation
 */
final class Note
{
    /**
     * Name
     * @var string
     */
    public $name = 'note';

    public $value = '';

}
