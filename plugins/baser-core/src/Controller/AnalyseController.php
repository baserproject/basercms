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

namespace BaserCore\Controller;

use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Doctrine\Common\Annotations\AnnotationReader;
use Exception;
use ReflectionClass;
use ReflectionMethod;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Controller\AppController;

/**
 * Class AnalyseController
 * @package BaserCore\Controller
 */
class AnalyseController extends AppController
{

    /**
     * Exclude ext
     * @var array
     */
    private const EXCLUDE_EXT = [
        '.png', '.gif', '.jpg', '.md', '.lock', '.scss', '.css', '.json', '.psd', '.csv',
        '.min.js', '.mo', '.po', '.pot', '.eot', '.svg', '.ttf', '.woff', '.woff2', '.map', '.html', '.bundle.js', '.txt'
    ];

    private const CONVERT_CLASS_NAME = [
        '\BaserCore\Routing\RouteCollection' => '\Cake\Routing\RouteCollection'
    ];

    /**
     * 解析したファイル情報一覧
     *
     * .json 付でアクセスすることで JSON を出力
     * 例）http://localhost/baser/baser-core/analyse/index.json
     * 例）http://localhost/baser/baser-core/analyse/index/baser-core.json
     * API) http://reflection.basercms.net/baser/baser-core/analyse/index/baser-core.json
     *
     * @param string|null $pluginName
     * @checked
     * @unitTest
     * @noTodo
     */
    public function index($pluginName = null)
    {
        $basePath = ROOT . DS . 'plugins' . DS;
        if ($pluginName) {
            $list = $this->getList($basePath . $pluginName);
        } else {
            $list = $this->getList($basePath);
        }
        $this->set(compact('list'));
        $this->viewBuilder()
            ->setOption('serialize', ['list'])
            ->setOption('jsonOptions', JSON_FORCE_OBJECT);
    }

    /**
     * 解析したファイル情報一覧を取得
     *
     * 再帰処理
     *
     * @param string $path
     * @return array
     * @checked
     * @unitTest
     * @noTodo
     */
    private function getList($path)
    {
        $folder = new Folder($path);
        $files = $folder->read(true, true, true);
        $metas = [];

        foreach($files[0] as $file) {
            if (preg_match('/(\/node_modules\/|\/vendors\/|Migrations|Seeds)/', $file)) {
                continue;
            }
            $metas = array_merge($metas, $this->getList($file . DS));
        }
        foreach($files[1] as $path) {
            $fileName = basename($path);
            if (preg_match('/(' . str_replace(',', '|', preg_quote(implode(',', self::EXCLUDE_EXT))) . ')$/', $fileName)) {
                continue;
            }
            $pathArray = explode(DS, str_replace(ROOT . DS . 'plugins' . DS, '', $path));
            $meta = [
                'file' => $fileName,
                'path' => str_replace(ROOT, '', $path),
                'type' => $pathArray[1],
                'class' => '',
                'method' => '',
                'checked' => false,
                'unitTest' => false,
                'noTodo' => false,
                'doc' => false,
                'note' => ''
            ];
            if (preg_match('/^[a-z]/', $fileName) || !preg_match('/\.php$/', $fileName)) {
                $file = new File($path);
                $code = $file->read();
                if (preg_match('/@checked/', $code)) {
                    $meta['checked'] = true;
                }
                if (preg_match('/@noTodo/', $code)) {
                    $meta['noTodo'] = true;
                }
                if (preg_match('/@unitTest/', $code)) {
                    $meta['unitTest'] = true;
                }
                if (preg_match('/@doc/', $code)) {
                    $meta['doc'] = true;
                }
                if (preg_match('/@note\(value="(.+?)"\)/', $code, $matches)) {
                    $meta['note'] = $matches[1];
                }
                $metas[] = $meta;
                continue;
            }
            try {
                $className = $this->pathToClass($path);
                if(!empty(self::CONVERT_CLASS_NAME[$className])) {
                    $className = self::CONVERT_CLASS_NAME[$className];
                }
                $meta['class'] = $className;
                $class = new ReflectionClass($className);
                if($class->isInterface()) continue;
                $traitMethodsArray = $this->getTraitMethod($class);
            } catch (Exception $e) {
                $metas[] = $meta;
                continue;
            }
            $methods = $class->getMethods();
            foreach($methods as $method) {
                $meta = array_merge($meta, [
                    'checked' => false,
                    'unitTest' => false,
                    'noTodo' => false,
                    'doc' => false,
                    'note' => ''
                ]);
                if ('\\' . $method->class === $className && !in_array($method->name, $traitMethodsArray)) {
                    $meta['method'] = $method->name;
                    $meta = array_merge($meta, $this->getAnnotations($className, $method->name));
                    $metas[] = $meta;
                }
            }
        }
        return $metas;
    }

    /**
     * アノテーションを取得
     *
     * @param string $className
     * @param string $methodName
     * @return array
     * @throws \ReflectionException
     * @checked
     * @unitTest
     * @noTodo
     */
    private function getAnnotations($className, $methodName)
    {
        $reader = new AnnotationReader();
        $methodAnnotations = $reader->getMethodAnnotations(new ReflectionMethod($className, $methodName));
        $annotations = [];
        if ($methodAnnotations) {
            foreach(['checked', 'unitTest', 'noTodo', 'doc', 'note'] as $property) {
                foreach($methodAnnotations as $annotation) {
                    if ($property === $annotation->name) {
                        if(isset($annotation->value)) {
                            $annotations[$property] = $annotation->value;
                        } else {
                            $annotations[$property] = true;
                        }
                    }
                }
            }
        }
        return $annotations;
    }

    /**
     * トレイトのメソッド一覧を配列で取得
     *
     * @param ReflectionClass $reflection
     * @return array
     * @throws \ReflectionException
     * @checked
     * @unitTest
     * @noTodo
     */
    private function getTraitMethod(ReflectionClass $reflection)
    {
        $traits = $reflection->getTraits();
        $traitMethodsArray = [];
        if ($traits) {
            foreach($traits as $value) {
                $trait = new ReflectionClass($value->name);
                $traitMethods = $trait->getMethods();
                foreach($traitMethods as $traitMethod) {
                    $traitMethodsArray[] = $traitMethod->name;
                }
            }
        }
        return $traitMethodsArray;
    }

    /**
     * パス情報から namespace 付きのクラス名を取得
     *
     * @param string $path
     * @return string|string[]
     * @checked
     * @unitTest
     * @noTodo
     */
    private function pathToClass($path)
    {
        $file = str_replace(ROOT . DS . 'plugins', '', $path);
        $file = str_replace('/src', '', $file);
        $file = str_replace('/tests', '/Test', $file);
        $file = str_replace('/', '\\', $file);
        $file = str_replace('.php', '', $file);
        $file = str_replace('bc-admin-third', 'BcAdminThird', $file);
        $file = str_replace('bc-blog', 'BcBlog', $file);
        $file = str_replace('bc-mail', 'BcMail', $file);
        $file = str_replace('bc-uploader', 'BcUploader', $file);
        $file = str_replace('bc-editor-template', 'BcEditorTemplate', $file);
        $file = str_replace('bc-favorite', 'BcFavorite', $file);
        $file = str_replace('bc-front', 'BcFront', $file);
        $file = str_replace('bc-installer', 'BcInstaller', $file);
        $file = str_replace('bc-search-index', 'BcSearchIndex', $file);
        $file = str_replace('bc-theme-config', 'BcThemeConfig', $file);
        $file = str_replace('bc-theme-file', 'BcThemeFile', $file);
        $file = str_replace('bc-widget-area', 'BcWidgetArea', $file);
        return str_replace('baser-core', 'BaserCore', $file);
    }

}

