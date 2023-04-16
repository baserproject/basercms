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

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Utility\Inflector;

/**
 * コントローラーが見つからないエラー
 *
 * @var string $controller
 */

$pluginDot = empty($plugin) ? null : $plugin . '.';
$namespace = Configure::read('App.namespace');
$prefixNs = $prefixPath = '';

$incompleteInflection = (strpos($controller, '_') !== false || strpos($controller, '-'));
$originalClass = $controller;

$controller = Inflector::camelize($controller);

if (!empty($prefix)) {
    $prefix = array_map('Cake\Utility\Inflector::camelize', explode('/', $prefix));
    $prefixNs = '\\' . implode('\\', $prefix);
    $prefixPath = implode(DIRECTORY_SEPARATOR, $prefix) . DIRECTORY_SEPARATOR;
}

if (!empty($plugin)) {
    $namespace = str_replace('/', '\\', $plugin);
}

$filePath = 'Controller' . DIRECTORY_SEPARATOR . $prefixPath . h($controller) . 'Controller.php';
if (empty($plugin)) {
    $path = APP_DIR . DIRECTORY_SEPARATOR . $filePath;
} else {
    $path = Plugin::classPath($plugin) . $filePath;
}

$this->layout = 'dev_error';

$this->assign('title', 'Missing Controller');
$this->assign('templateName', 'missing_controller.php');

?>


<?php $this->start('subheading');?>
<strong>Error</strong>
<?php if ($incompleteInflection): ?>
    Your routing resulted in <em><?= h($originalClass) ?></em> as a controller name.
<?php else: ?>
    <em><?= h($pluginDot . $controller) ?>Controller</em> could not be found.
<?php endif; ?>
<?php $this->end() ?>


<?php $this->start('file'); ?>
<?php if ($incompleteInflection): ?>
    <p>The controller name <em><?= h($originalClass) ?></em> has not been properly inflected, and
    could not be resolved to a controller that exists in your application.</p>

    <p>Ensure that your URL <strong><?= h($this->request->getUri()->getPath()) ?></strong> is
    using the same inflection style as your routes do. By default applications use <code>DashedRoute</code>
    and URLs should use <strong>-</strong> to separate multi-word controller names.</p>
<?php else: ?>
    <p>
        In the case you tried to access a plugin controller make sure you added it to your composer file or you use the autoload option for the plugin.
    </p>
    <p class="error">
        <strong>Suggestion</strong>
        Create the class <em><?= h($controller) ?>Controller</em> below in file: <?= h($path) ?>
    </p>

    <?php
    $code = <<<PHP
    <?php
    namespace {$namespace}\Controller{$prefixNs};

    use {$namespace}\Controller\AppController;

    class {$controller}Controller extends AppController
    {

    }
PHP;
    ?>
    <div class="code-dump"><?php highlight_string($code); ?></div>
<?php endif; ?>
<?php $this->end(); ?>
