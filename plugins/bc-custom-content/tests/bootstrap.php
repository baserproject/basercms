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

use BaserCore\Utility\BcUtil;
use Migrations\TestSuite\Migrator;

$findRoot = function($root) {
    do {
        $lastRoot = $root;
        $root = dirname($root);
        if (is_dir($root . '/vendor/cakephp/cakephp')) {
            return $root;
        }
    } while($root !== $lastRoot);
    throw new Exception("Cannot find the root of the application, unable to run tests");
};
$root = $findRoot(__FILE__);
unset($findRoot);
chdir($root);

require_once $root . '/vendor/autoload.php';
require $root . '/config/bootstrap.php';

BcUtil::includePluginClass('BcCustomContent');
(new Migrator())->runMany([
    ['plugin' => 'BcCcAutoZip'],
    ['plugin' => 'BcCcCheckbox'],
    ['plugin' => 'BcCcDate'],
    ['plugin' => 'BcCcDateTime'],
    ['plugin' => 'BcCcEmail'],
    ['plugin' => 'BcCcFile'],
    ['plugin' => 'BcCcHidden'],
    ['plugin' => 'BcCcMultiple'],
    ['plugin' => 'BcCcPassword'],
    ['plugin' => 'BcCcPref'],
    ['plugin' => 'BcCcRadio'],
    ['plugin' => 'BcCcRelated'],
    ['plugin' => 'BcCcSelect'],
    ['plugin' => 'BcCcTel'],
    ['plugin' => 'BcCcText'],
    ['plugin' => 'BcCcTextarea'],
    ['plugin' => 'BcCcWysiwyg'],
]);
