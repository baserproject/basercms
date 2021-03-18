<?php
namespace BaserCore\Controller\Admin;

use Cake\Filesystem\Folder;
use Exception;
use ReflectionClass;

class ReflectionController extends BcAdminAppController {

    public function index() {
        $basePath = ROOT . DS . 'plugins' . DS;
        $methods = $this->reflection($basePath);
        foreach($methods as $method) {
            echo $method . "<br>";
        }
        exit();
    }

    private function reflection($path) {
        $folder = new Folder($path);
        $files = $folder->read(true, true, true);
        $classMethods = [];

        foreach($files[0] as $file) {
            if(preg_match('/(\/node_modules\/|\/vendors\/|Migrations|Seeds)/', $file)) {
                continue;
            }
            $classMethods = array_merge($classMethods, $this->reflection($file . DS));
        }

        foreach($files[1] as $file) {
            $fileName = basename($file);
            if(preg_match('/(\.png|\.gif|\.jpg|\.md|\.lock|\.scss|\.css|\.json|\.psd|\.csv|\.min\.js|\.mo|.\po|\.pot|\.eot|\.svg|\.ttf|\.woff|\.woff2|\.map|\.html|\.bundle\.js)$/', $fileName)) {
                continue;
            }
            $file = str_replace(ROOT . DS . 'plugins', '', $file);
            $file = str_replace('/src', '', $file);
            $file = str_replace('/tests', '/Test', $file);
            $file = str_replace('/', '\\', $file);
            $file = str_replace('.php', '', $file);
            $file = str_replace('bc-admin-third', 'BcAdminThird', $file);
            $file = str_replace('bc-blog', 'BcBlog', $file);
            $file = str_replace('bc-mail', 'BcMail', $file);
            $file = str_replace('bc-uploader', 'BcUploader', $file);
            $className = str_replace('baser-core', 'BaserCore', $file);
            if(preg_match('/^[a-z]/', $fileName)) {
                $classMethods[] = $className;
                continue;
            }
            try {
                $class = new ReflectionClass($className);
            } catch(Exception $e) {
                $classMethods[] = $className;;
                continue;
            }
            $methods = $class->getMethods();
            foreach($methods as $method) {
                if('\\' . $method->class === $className) {
                    $classMethods[] = $className . "," . $method->name;
                }
            }
        }
        return $classMethods;
    }
}
