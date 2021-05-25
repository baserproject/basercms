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
            $meta = [
                'file' => $fileName,
                'path' => str_replace(ROOT, '', $path),
                'class' => '',
                'method' => '',
                'checked' => false,
                'unitTest' => false,
                'noTodo' => false
            ];
            if (preg_match('/^[a-z]/', $fileName) || !preg_match('/\.php$/', $fileName)) {
                $file = new File($path);
                $code = $file->read();
                if (preg_match('/@checked/', $code)) {
                    $meta['checked'] = true;
                }
                if (preg_match('/@noTodo/', $code)) {
                    $meta['checked'] = true;
                }
                if (preg_match('/@noTodo/', $code)) {
                    $meta['unitTest'] = true;
                }
                $metas[] = $meta;
                continue;
            }
            try {
                $className = $this->pathToClass($path);
                $meta['class'] = $className;
                $class = new ReflectionClass($className);
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
                    'noTodo' => false
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
            foreach(['checked', 'unitTest', 'noTodo'] as $property) {
                foreach($methodAnnotations as $annotation) {
                    if ($property === $annotation->name) {
                        $annotations[$property] = true;
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
            $trait = new ReflectionClass($traits[key($traits)]->name);
            $traitMethods = $trait->getMethods();
            foreach($traitMethods as $traitMethod) {
                $traitMethodsArray[] = $traitMethod->name;
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
        return str_replace('baser-core', 'BaserCore', $file);
    }

}

