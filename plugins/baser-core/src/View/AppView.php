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

namespace BaserCore\View;

use BaserCore\View\Helper\BcFormHelper;
use BaserCore\View\Helper\BcTimeHelper;
use BaserCore\View\Helper\BcToolbarHelper;
use BaserCore\View\Helper\BcUploadHelper;
use Cake\View\View;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\View\Helper\BcContentsHelper;
use BaserCore\View\Helper\BcBaserHelper;
use BaserCore\Event\BcEventDispatcherTrait;
use RuntimeException;
use Cake\View\Exception\MissingLayoutException;
use Cake\View\Exception\MissingTemplateException;

/**
 * Class AppView
 * @property BcBaserHelper $BcBaser
 * @property BcUploadHelper $BcUpload
 * @property BcToolbarHelper $BcToolbar
 * @property BcFormHelper $BcForm
 * @property BcTimeHelper $BcTime
 * @property BcContentsHelper $BcContents
 */
class AppView extends View
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * initialize
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->addHelper('BaserCore.BcTime');
        $this->addHelper('BaserCore.BcForm', ['templates' => 'BaserCore.bc_form']);
        $this->addHelper('BaserCore.BcAdmin');
        $this->addHelper('BaserCore.BcContents');
        $this->addHelper('BaserCore.BcPage');
        $this->addHelper('BaserCore.BcBaser');
        $this->loadHelper('BaserCore.BcArray');
        $this->addHelper('BaserCore.BcUpload');
        $this->addHelper('BaserCore.BcToolbar');
        $this->addHelper('Paginator');
        $this->assign('title', $this->get('title'));
    }

    /**
     * テンプレートを描画する
     * 固定ページで利用
     *
     * @param string $templateFile Filename of the template.
     * @return string Rendered output
     * @checked
     * @noTodo
     */
    public function evaluate(string $templateFile): string
    {
        $dataForView = [];
        foreach($this->getVars() as $key) {
            $dataForView[$key] = $this->get($key);
        }
        return parent::_evaluate($templateFile, $dataForView);
    }

    /**
     * 拡張子を取得する
     * @return string
     * @checked
     * @noTodo
     */
    public function getExt(): string
    {
        return $this->_ext;
    }

    /**
     * Returns filename of given action's template file as a string.
     * CamelCased action names will be under_scored by default.
     * This means that you can have LongActionNames that refer to
     * long_action_names.php templates. You can change the inflection rule by
     * overriding _inflectTemplateFileName.
     *
     * @param string|null $name Controller action to find template filename for
     * @return string Template filename
     * @throws \Cake\View\Exception\MissingTemplateException when a template file could not be found.
     * @throws \RuntimeException When template name not provided.
     */
    protected function _getTemplateFileName(?string $name = null): string
    {
        $templatePath = $subDir = '';

        if ($this->templatePath) {
            $templatePath = $this->templatePath . DIRECTORY_SEPARATOR;
        }
        if ($this->subDir !== '') {
            $subDir = $this->subDir . DIRECTORY_SEPARATOR;
            // Check if templatePath already terminates with subDir
            if ($templatePath != $subDir && substr($templatePath, -strlen($subDir)) === $subDir) {
                $subDir = '';
            }
        }

        if ($name === null) {
            $name = $this->template;
        }

        if (empty($name)) {
            throw new RuntimeException('Template name not provided');
        }

        // CUSTOMIZE ADD 2023/06/16 kaburk
        // イベントを追加
        // >>>
        // EVENT beforeGetTemplateFileName
        $event = $this->dispatchLayerEvent('beforeGetTemplateFileName', ['name' => $name], ['class' => '', 'plugin' => '']);
        if ($event !== false) {
            $name = ($event->getResult() === null || $event->getResult() === true)? $event->getData('name') : $event->getResult();
        }
        // EVENT PluginName.ControllerName.beforeGetTemplateFileName
        $event = $this->dispatchLayerEvent('beforeGetTemplateFileName', ['name' => $name]);
        if ($event !== false) {
            $name = ($event->getResult() === null || $event->getResult() === true)? $event->getData('name') : $event->getResult();
        }
        // <<<

        [$plugin, $name] = $this->pluginSplit($name);
        $name = str_replace('/', DIRECTORY_SEPARATOR, $name);

        if (strpos($name, DIRECTORY_SEPARATOR) === false && $name !== '' && $name[0] !== '.') {
            $name = $templatePath . $subDir . $this->_inflectTemplateFileName($name);
        } elseif (strpos($name, DIRECTORY_SEPARATOR) !== false) {
            if ($name[0] === DIRECTORY_SEPARATOR || $name[1] === ':') {
                $name = trim($name, DIRECTORY_SEPARATOR);
            } elseif (!$plugin || $this->templatePath !== $this->name) {
                $name = $templatePath . $subDir . $name;
            } else {
                $name = $subDir . $name;
            }
        }

        $name .= $this->_ext;
        $paths = $this->_paths($plugin);
        foreach ($paths as $path) {
            if (is_file($path . $name)) {
                return $this->_checkFilePath($path . $name, $path);
            }
        }

        throw new MissingTemplateException($name, $paths);
    }

    /**
     * Returns layout filename for this template as a string.
     *
     * @param string|null $name The name of the layout to find.
     * @return string Filename for layout file.
     * @throws \Cake\View\Exception\MissingLayoutException when a layout cannot be located
     * @throws \RuntimeException
     */
    protected function _getLayoutFileName(?string $name = null): string
    {
        if ($name === null) {
            if (empty($this->layout)) {
                throw new RuntimeException(
                    'View::$layout must be a non-empty string.' .
                    'To disable layout rendering use method View::disableAutoLayout() instead.'
                );
            }
            $name = $this->layout;
        }

        // CUSTOMIZE ADD 2023/06/16 kaburk
        // イベントを追加
        // >>>
        // EVENT beforeGetLayoutFileName
        $event = $this->dispatchLayerEvent('beforeGetLayoutFileName', ['name' => $name], ['class' => '', 'plugin' => '']);
        if ($event !== false) {
            $name = ($event->getResult() === null || $event->getResult() === true)? $event->getData('name') : $event->getResult();
        }
        // EVENT PluginName.ControllerName.beforeGetLayoutFileName
        $event = $this->dispatchLayerEvent('beforeGetLayoutFileName', ['name' => $name]);
        if ($event !== false) {
            $name = ($event->getResult() === null || $event->getResult() === true)? $event->getData('name') : $event->getResult();
        }
        // <<<

        [$plugin, $name] = $this->pluginSplit($name);
        $name .= $this->_ext;

        foreach ($this->getLayoutPaths($plugin) as $path) {
            if (is_file($path . $name)) {
                return $this->_checkFilePath($path . $name, $path);
            }
        }

        $paths = iterator_to_array($this->getLayoutPaths($plugin));
        throw new MissingLayoutException($name, $paths);
    }

    /**
     * Finds an element filename, returns false on failure.
     *
     * @param string $name The name of the element to find.
     * @param bool $pluginCheck - if false will ignore the request's plugin if parsed plugin is not loaded
     * @return string|false Either a string to the element filename or false when one can't be found.
     */
    protected function _getElementFileName(string $name, bool $pluginCheck = true)
    {
        // CUSTOMIZE ADD 2023/06/16 kaburk
        // イベントを追加
        // >>>
        // EVENT beforeGetElementFileName
        $event = $this->dispatchLayerEvent('beforeGetElementFileName', ['name' => $name], ['class' => '', 'plugin' => '']);
        if ($event !== false) {
            $name = ($event->getResult() === null || $event->getResult() === true)? $event->getData('name') : $event->getResult();
        }
        // EVENT PluginName.ControllerName.beforeGetElementFileName
        $event = $this->dispatchLayerEvent('beforeGetElementFileName', ['name' => $name]);
        if ($event !== false) {
            $name = ($event->getResult() === null || $event->getResult() === true)? $event->getData('name') : $event->getResult();
        }
        // <<<

        [$plugin, $name] = $this->pluginSplit($name, $pluginCheck);

        $name .= $this->_ext;
        foreach ($this->getElementPaths($plugin) as $path) {
            if (is_file($path . $name)) {
                return $path . $name;
            }
        }

        return false;
    }

}
