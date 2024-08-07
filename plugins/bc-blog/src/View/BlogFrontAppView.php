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

namespace BcBlog\View;

use BaserCore\View\BcFrontAppView;
use BcBlog\View\Helper\BlogHelper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BcBlog\View\Helper\RssHelper;

/**
 * Class BlogAppView
 * @property BlogHelper $Blog
 * @property RssHelper $Rss
 */
class BlogFrontAppView extends BcFrontAppView
{

    /**
     * initialize
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->addHelper('BcBlog.Blog');
        $this->addHelper('BcBlog.Rss');
    }

}
