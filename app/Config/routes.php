<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

// CUSTOMIZE DELETE 2015/03/27 n1215
// CakePHPのPagesコントローラーの挙動を無効
// >>>
///**
// * Here, we are connecting '/' (base path) to controller called 'Pages',
// * its action called 'display', and we pass a param to select the view file
// * to use (in this case, /app/View/Pages/home.ctp)...
// */
//	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
///**
// * ...and connect the rest of 'Pages' controller's URLs.
// */
//	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
// <<<

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

// CUSTOMIZE ADD 2014/07/03 ryuring
// >>>
/**
 * Include Files
 *
 * 先に読み込んだ設定から適用されるので Baser の routes 設定を上書きしたい場合はこれより上部に記述します。
 */
require BASER_CONFIGS . 'routes.php';
// <<<

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
// @deprecated 5.0.0 since 4.0.3
// baserCMSコアの routes にて同様の処理をしている為、将来的に除外する
// 下記を読み込んでいる場合、AdminPrefix を変更したとしても、/admin/~ でアクセスできてしまう。
// コメントアウトした上での動作確認が必要
	require CAKE . 'Config' . DS . 'routes.php';
