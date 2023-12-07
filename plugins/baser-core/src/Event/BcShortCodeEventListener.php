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

namespace BaserCore\Event;

use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\View\View;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcShortCodeEventListener
 *
 * baserCMS Short Code Event Listener
 */
class BcShortCodeEventListener implements EventListenerInterface
{

    /**
     * Implemented Events
     *
     * @return array
     * @checked
     * @noTodo
     */
    public function implementedEvents(): array
    {
        return [
            'View.afterRender' => ['callable' => 'afterRender']
        ];
    }

    /**
     * After Render
     *
     * @param Event $event
     * @return void
     * @checked
     * @noTodo
     */
    public function afterRender(Event $event)
    {
        if (BcUtil::isAdminSystem()) return;
        $view = $event->getSubject();
        $this->_execShortCode($view);
    }

    /**
     * ショートコードを実行する
     *
     * @param View $View
     * @return void
     * @checked
     * @noTodo
     */
    protected function _execShortCode(View $view)
    {
        $shortCodes = Configure::read('BcShortCode');
        if (!$shortCodes) return;

        $output = $view->fetch('content');
        if (!is_array($shortCodes)) $shortCodes = [$shortCodes];

        foreach($shortCodes as $plugin => $values) {
            foreach($values as $shortCode) {
                $func = explode('.', $shortCode);
                if (empty($func[0]) || empty($func[1])) continue;

                $regex = '/(\[' . preg_quote($shortCode, '/') . '(|\s(.*?))\])/';

                if (preg_match_all($regex, $output, $matches)) {

                    foreach($matches[1] as $k => $match) {
                        $target = $match;
                        $args = [];
                        if (!empty($matches[3][$k])) {
                            $args = explode(',', $matches[3][$k]);
                            foreach($args as $key => $value) {
                                if (strpos($value, '|') !== false) {
                                    $args[$key] = BcUtil::pairToAssoc($value);
                                }
                            }
                        }

                        if (isset($view->{$func[0]})) {
                            $Helper = $view->{$func[0]};
                        } else {
                            $className = $plugin . "\\" . "View\\Helper\\" . $func[0] . 'Helper';
                            $Helper = new $className($view);
                        }
                        if(method_exists($Helper, $func[1])) {
                            $result = call_user_func_array([$Helper, $func[1]], $args);
                            $output = str_replace($target, $result, $output);
                        }
                    }
                }
            }
        }
        $view->assign('content', $output);
    }

}
