<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcSpaSample\Controller;

use App\Controller\AppController as BaseController;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Event\EventInterface;

/**
 * Class AppController
 */
class AppController extends BaseController
{
    public function beforeRender(EventInterface $event): void
    {
        $this->viewBuilder()->setClassName('BcSpaSample.App');
        $this->viewBuilder()->setTheme('BcAdminThird');
    }
}
