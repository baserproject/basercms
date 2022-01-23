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

namespace BaserCore\Utility;

use ArrayObject;
use Cake\Http\Session;
use Cake\ORM\Table;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use BaserCore\Vendor\Imageresizer;
use Cake\Datasource\EntityInterface;
use BaserCore\Annotation\Note;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcUploadBehavior
 */
class BcUpload
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * 保存ディレクトリ
     *
     * @var string[]
     */
    public $savePath = [];

    /**
     * 保存時にファイルの重複確認を行うディレクトリ
     *
     * @var array
     */
    public $existsCheckDirs = [];

    /**
     * 設定
     *
     * @var array
     */
    public $settings = null;

    /**
     * 一時ID
     *
     * @var string
     */
    public $tmpId = null;

    /**
     * Session
     *
     * @var Session
     */
    public $Session = null;

    /**
     * 画像拡張子
     *
     * @var array
     */
    public $imgExts = ['gif', 'jpg', 'jpeg', 'jpe', 'jfif', 'png'];

    /**
     * アップロードしたかどうか
     *
     * afterSave のリネーム判定に利用
     * モデルごとに設定する
     * @var array
     */
    public $uploaded = [];

    /**
     * Table
     * @var Table
     */
    public $table = null;

    /**
     * Alias
     * @var null
     */
    public $alias = null;

    /**
     * uploadingFiles
     *
     * @var array
     */
    private $uploadingFiles = [];

    /**
     * Initialize
     * @param array $config
     * @param Table $table
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config, $table): void
    {
        $this->table = $table;
        $this->alias = $this->table->getAlias();
        $this->settings = $this->getSettings($config);
        $this->savePath[$this->alias] = $this->getSaveDir($this->alias);
        if (!is_dir($this->savePath[$this->alias])) {
            $Folder = new Folder();
            $Folder->create($this->savePath[$this->alias]);
            $Folder->chmod($this->savePath[$this->alias], 0777, true);
        }
        $this->existsCheckDirs[$this->alias] = $this->getExistsCheckDirs($this->alias);
        $this->Session = new Session();
    }

    /**
     * configの初期設定を取得する
     *
     * @param array $config
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSettings($config): array
    {
        $settings[$this->alias] = Hash::merge([
            'saveDir' => '',
            'existsCheckDirs' => [],
            'fields' => []
        ], $config);
        foreach($settings[$this->alias]['fields'] as $key => $setting) {
            if (empty($setting['name'])) {
                $settings[$this->alias]['fields'][$key]['name'] = $key;
            }
            if (!empty($setting['imageresize'])) {
                if (empty($setting['imageresize']['thumb'])) {
                    $settings[$this->alias]['fields'][$key]['imageresize']['thumb'] = false;
                }
            } else {
                $settings[$this->alias]['fields'][$key]['imageresize'] = false;
            }
            if (!isset($setting['getUniqueFileName'])) {
                $settings[$this->alias]['fields'][$key]['getUniqueFileName'] = true;
            }
        }
        return $settings;
    }

    /**
     * 保存時にファイルの重複確認を行うディレクトリのリストを取得する
     *
     * @param string alias
     * @checked
     * @noTodo
     * @unitTest
     */
    private function getExistsCheckDirs($alias): array
    {
        $existsCheckDirs = [];
        $existsCheckDirs[] = $this->savePath[$alias];
        $basePath = WWW_ROOT . 'files' . DS;
        if ($this->settings[$alias]['existsCheckDirs']) {
            foreach($this->settings[$alias]['existsCheckDirs'] as $existsCheckDir) {
                $existsCheckDirs[] = $basePath . $existsCheckDir . DS;
            }
        }
        return $existsCheckDirs;
    }

    /**
     * リクエストされたデータを処理しやすいようにセットアップする
     * $data は参照渡し
     * @param array|ArrayObject $data
     * @checked
     * @unitTest
     * @noTodo
     */
    public function setupRequestData($data)
    {
        $files = [];
        foreach($this->settings[$this->alias]['fields'] as $setting) {
            $name = $setting['name'];
            if (!empty($data[$name]) && is_array($data[$name])) {
                $file = $data[$name];
                $file['uploadable'] = $this->isUploadable($setting['type'], $file['type'], $file);
            } else {
                continue;
            }
            if ($file['uploadable']) {
                // arrayをstringとして変換
                $data[$name] = $file['name'];
            } elseif (isset($data[$name . '_']) && isset($file['error']) && $file['error'] === UPLOAD_ERR_NO_FILE) {
                // 新しいデータが送信されず、既存データを引き継ぐ場合は、元のフィールド名に戻す
                $data[$name] = $data[$name . '_'];
                unset($data[$setting['name'] . '_']);
            } else {
                $data[$name] = '';
            }
            $file['ext'] = BcUtil::decodeContent($file['type'], @$file['name']);
            $file['delete'] = $data[$name . '_delete'] ?? null;
            $files[$name] = $file;
        }
        $this->setUploadingFiles($files);
    }

    /**
     * リクエストされたデータを処理しやすいようにセットアップする
     * $data は参照渡し
     * @param ArrayObject|array $data
     * @checked
     * @unitTest
     * @noTodo
     */
    public function setupTmpData($data)
    {
        foreach($this->settings[$this->alias]['fields'] as $setting) {
            $name = $setting['name'];
            if (isset($data[$name . '_tmp']) && $this->moveFileSessionToTmp($data, $name)) {
                // セッションに一時ファイルが保存されている場合は復元する
                unset($data[$setting['name'] . '_tmp']);
            }
        }
    }

    /**
     * アップロード可能か判定
     *
     * @param array|string $fileType
     * @param string $contentType
     * @param string|array $fileName
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isUploadable($fileType, $contentType, $file)
    {
        if (!empty($file) && is_array($file) && @$file['error'] === 0 && $file['name'] && $file['tmp_name']) {
            // タイプ別除外
            $targets = [];
            if ($fileType === 'image') {
                $targets = $this->imgExts;
            } elseif (is_array($fileType)) {
                $targets = $fileType;
            } elseif ($fileType !== 'all') {
                $targets = [$fileType];
            }
            if ($targets && !in_array(BcUtil::decodeContent($contentType, $file['name']), $targets)) {
                $uploadable = false;
            } else {
                $uploadable = true;
            }
        } else {
            $uploadable = false;
        }
        return $uploadable;
    }

    /**
     * ファイル群を保存する
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function saveFiles()
    {
        $files = $this->getUploadingFiles();
        $this->uploaded[$this->alias] = false;
        foreach($this->settings[$this->alias]['fields'] as $setting) {
            $file = $files[$setting['name']] ?? [];
            $files[$setting['name']] = $this->saveFileWhileChecking($setting, $file);
        }
        $this->setUploadingFiles($files);
    }

    /**
     * 保存対象かチェックしながらファイルを保存する
     * @param array $setting
     * @param array $file
     * @param array $options
     *    - deleteTmpFiles : 一時ファイルを削除するかどうか
     * @return array|false $file
     * @checked
     * @noTodo
     * @unitTest
     */
    public function saveFileWhileChecking($setting, $file, $options = [])
    {
        if (empty($file['uploadable'])) return $file;
        $options = array_merge([
            'deleteTmpFiles' => true
        ], $options);
        if ($setting['getUniqueFileName']) {
            $file['name'] = $this->getUniqueFileName($setting, $file);
        }
        $fileName = $this->saveFile($setting, $file);
        if ($fileName) {
            $file['name'] = $fileName;
            $this->copyImages($setting, $file);
            if (!empty($setting['imageresize'])) {
                $filePath = $this->savePath[$this->alias] . $fileName;
                $this->resizeImage($filePath, $filePath, $setting['imageresize']['width'], $setting['imageresize']['height'], $setting['imageresize']['thumb']);
            }
            if ($options['deleteTmpFiles']) {
                @unlink($file['tmp_name']);
            }
            $this->uploaded[$this->alias] = true;
        }
        return $file;
    }

    /**
     * ファイルを保存する
     *
     * @param array $setting 画像保存対象フィールドの設定
     * @param array $file
     * @return string|false ファイル名
     * @checked
     * @noTodo
     * @unitTest
     */
    public function saveFile($setting, $file)
    {
        $fileName = $this->getSaveFileName($setting, $file);
        $filePath = $this->savePath[$this->alias] . $fileName;
        $this->rotateImage($file['tmp_name']);
        if (copy($file['tmp_name'], $filePath)) {
            chmod($filePath, 0666);
            return $fileName;
        } else {
            return false;
        }
    }

    /**
     * 削除対象かチェックしながらファイル群を削除する
     * @param EntityInterface $newEntity
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteFiles($entity, $force = false)
    {
        if (!$entity->id) return;
        $files = $this->getUploadingFiles();
        $query = $this->table->find()->where(['id' => $entity->id]);
        if ($entity instanceof \BaserCore\Model\Entity\Content) {
            $oldEntity = $query->applyOptions(['withDeleted'])->first();
        } else {
            $oldEntity = $query->first();
        }
        foreach($this->settings[$this->alias]['fields'] as $setting) {
            $file = $files[$setting['name']] ?? [];
            $this->deleteFileWhileChecking($setting, $file, $entity, $oldEntity, $force);
        }
    }

    /**
     * 削除対象かチェックしながらファイルを削除する
     *
     * @param string $name
     * @param array $file
     * @param array $setting
     * @param EntityInterface $newEntity
     * @param EntityInterface $oldEntity
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteFileWhileChecking($setting, $file, $newEntity, $oldEntity, $force = false)
    {
        if (!empty($file['delete']) || $force) {
            $this->deleteFile($setting, $oldEntity->{$setting['name']});
            $newEntity->{$setting['name']} = '';
        }
    }

    /**
     * ファイルを削除する
     *
     * @param array $setting 保存対象フィールドの設定
     *  - prefix : 対象のファイルの接頭辞
     *  - suffix : 対象のファイルの接尾辞
     * @param string $file
     * @param boolean $delImagecopy
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteFile($setting, $file, $delImagecopy = true)
    {
        if (!$file) return true;
        $prefix = (!empty($setting['prefix']))? $setting['prefix'] : '';
        $suffix = (!empty($setting['suffix']))? $setting['suffix'] : '';
        $pathinfo = pathinfo($file);
        $ext = $pathinfo['extension'];
        $filePath = $this->savePath[$this->alias] . $prefix . preg_replace("/\." . $ext . "$/is", '', $file) . $suffix . '.' . $ext;
        if (!empty($setting['imagecopy']) && $delImagecopy) {
            foreach($setting['imagecopy'] as $copy) {
                $this->deleteFile($copy, $file, false);
            }
        }
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return true;
    }

    /**
     * セッションに保存されたファイルデータをファイルとして保存する
     *
     * @param ArrayObject $data
     * @param string $fieldName
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function moveFileSessionToTmp($data, $fieldName)
    {
        $fileName = $data[$fieldName . '_tmp'];
        $sessionKey = str_replace(['.', '/'], ['_', '_'], $fileName);
        $tmpName = $this->savePath[$this->alias] . $sessionKey;
        $fileData = $this->Session->read('Upload.' . $sessionKey . '.data');
        $fileType = $this->Session->read('Upload.' . $sessionKey . '.type');
        $this->Session->delete('Upload.' . $sessionKey);

        // サイズを取得
        if (ini_get('mbstring.func_overload') & 2 && function_exists('mb_strlen')) {
            $fileSize = mb_strlen($fileData, 'ASCII');
        } else {
            $fileSize = strlen($fileData);
        }

        if ($fileSize == 0) {
            return false;
        }

        // ファイルを一時ファイルとして保存
        $file = new File($tmpName, true, 0666);
        $file->write($fileData);
        $file->close();

        // アップロードされたデータとしてデータを復元する
        $uploadInfo['error'] = 0;
        $uploadInfo['name'] = $fileName;
        $uploadInfo['tmp_name'] = $tmpName;
        $uploadInfo['size'] = $fileSize;
        $uploadInfo['type'] = $fileType;
        $uploadInfo['uploadable'] = true;
        $uploadedFile[$fieldName] = $uploadInfo;
        $this->setUploadingFiles($uploadedFile);
        return true;
    }

    /**
     * 保存用ファイル名を取得する
     *
     * @param array $setting
     * @param array $file
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSaveFileName($setting, $file)
    {
        $name = $file['name'];
        $ext = $file['ext'];
        $prefix = (!empty($setting['prefix']))? $setting['prefix'] : '';
        $suffix = (!empty($setting['suffix']))? $setting['suffix'] : '';
        $basename = preg_replace("/\." . $ext . "$/is", '', $name);
        $fileName = $prefix . $basename . $suffix . '.' . $ext;
        if ($this->isFileExists($fileName)) {
            if (preg_match('/(.+_)([0-9]+)$/', $basename, $matches)) {
                $basename = $matches[1] . ((int)$matches[2] + 1);
            } else {
                $basename = $basename . '_1';
            }
            $file['name'] = $basename . '.' . $ext;
            $fileName = $this->getSaveFileName($setting, $file);
        }
        return $fileName;
    }

    /**
     * 画像をExif情報を元に正しい確度に回転する
     *
     * @param $file
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function rotateImage($file)
    {
        if (!extension_loaded("exif")) {
            return false;
        }
        $exif = @exif_read_data($file);
        if (empty($exif) || empty($exif['Orientation'])) {
            return true;
        }
        switch($exif['Orientation']) {
            case 3:
                $angle = 180;
                break;
            case 6:
                $angle = 270;
                break;
            case 8:
                $angle = 90;
                break;
            default:
                return true;
        }
        $imgInfo = getimagesize($file);
        $imageType = $imgInfo[2];
        // 元となる画像のオブジェクトを生成
        switch($imageType) {
            case IMAGETYPE_GIF:
                $srcImage = imagecreatefromgif($file);
                break;
            case IMAGETYPE_JPEG:
                $srcImage = imagecreatefromjpeg($file);
                break;
            case IMAGETYPE_PNG:
                $srcImage = imagecreatefrompng($file);
                break;
            default:
                return false;
        }
        $rotate = imagerotate($srcImage, $angle, 0);
        switch($imageType) {
            case IMAGETYPE_GIF:
                imagegif($rotate, $file);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($rotate, $file, 100);
                break;
            case IMAGETYPE_PNG:
                imagepng($rotate, $file);
                break;
            default:
                return false;
        }
        imagedestroy($srcImage);
        imagedestroy($rotate);
        return true;
    }

    /**
     * 画像をコピーする
     *
     * @param string $alias
     * @param array $setting 画像保存対象フィールドの設定
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copyImage($setting, $file)
    {
        $prefix = (!empty($setting['prefix']))? $setting['prefix'] : '';
        $suffix = (!empty($setting['suffix']))? $setting['suffix'] : '';
        $basename = preg_replace("/\." . $file['ext'] . "$/is", '', $file['name']);
        $filePath = $this->savePath[$this->alias] . $prefix . $basename . $suffix . '.' . $file['ext'];
        if (!empty($setting['thumb'])) {
            $thumb = $setting['thumb'];
        } else {
            $thumb = false;
        }
        return $this->resizeImage($file['tmp_name'], $filePath, $setting['width'], $setting['height'], $thumb);
    }

    /**
     * 画像ファイルをコピーする
     * リサイズ可能
     *
     * @param string $source コピー元のパス
     * @param string $distination コピー先のパス
     * @param int $width 横幅
     * @param int $height 高さ
     * @param boolean $thumb サムネイルとしてコピーするか
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resizeImage($source, $distination, $width = 0, $height = 0, $thumb = false)
    {
        if ($width > 0 || $height > 0) {
            $imageresizer = new Imageresizer();
            $ret = $imageresizer->resize($source, $distination, $width, $height, $thumb);
        } else {
            $ret = copy($source, $distination);
        }
        if ($ret) {
            chmod($distination, 0666);
        }
        return $ret;
    }

    /**
     * 画像のサイズを取得
     *
     * 指定したパスにある画像のサイズを配列(高さ、横幅)で返す
     *
     * @param string $path 画像のパス
     * @return mixed array / false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getImageSize($path)
    {
        $imginfo = getimagesize($path);
        if ($imginfo) {
            return ['width' => $imginfo[0], 'height' => $imginfo[1]];
        }
        return false;
    }

    /**
     * 全フィールドのファイル名をフィールド値ベースのファイル名に変更する
     *
     * @param EntityInterface $entity
     * @param bool $copy
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function renameToBasenameFields($entity, $copy = false)
    {
        $files = $this->getUploadingFiles();
        foreach($this->settings[$this->alias]['fields'] as $setting) {
            $value = $this->renameToBasenameField($setting, $files[$setting['name']], $entity, $copy);
            if ($value !== false) {
                $entity->eyecatch = $value;
                // 保存時にbeforeSaveとafterSaveのループを防ぐ
                $this->table->getEventManager()->off('Model.beforeSave');
                $this->table->getEventManager()->off('Model.afterSave');
                $this->table->save($entity, ['validate' => false]);
                $files[$setting['name']]['name'] = $value;
                $this->setUploadingFiles($files);
            }
        }
    }

    /**
     * ファイル名をフィールド値ベースのファイル名に変更する
     *
     * @param array $setting
     * @param array $file
     * @param EntityInterface $entity
     * @param bool $copy
     * @return bool|mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function renameToBasenameField($setting, $file, $entity, $copy = false)
    {
        if (empty($setting['namefield']) || empty($file)) {
            return false;
        }
        $oldName = $file['name'];
        if (is_array($oldName)) {
            return false;
        }
        $saveDir = $this->savePath[$this->alias];
        $saveDirInTheme = $this->getSaveDir($this->alias, true);
        $oldSaveDir = '';
        if (file_exists($saveDir . $oldName)) {
            $oldSaveDir = $saveDir;
        } elseif (file_exists($saveDirInTheme . $oldName)) {
            $oldSaveDir = $saveDirInTheme;
        }
        if (!file_exists($oldSaveDir . $oldName)) {
            return '';
        }
        $newName = $this->getFieldBasename($setting, $file, $entity);
        if (!$newName) {
            return false;
        }
        if ($oldName == $newName) {
            return false;
        }
        if (!empty($setting['imageresize'])) {
            $newName = $this->getFileName($setting['imageresize'], $newName);
        } else {
            $newName = $this->getFileName(null, $newName);
        }

        if (!$copy) {
            rename($oldSaveDir . $oldName, $saveDir . $newName);
        } else {
            copy($oldSaveDir . $oldName, $saveDir . $newName);
        }
        if (!empty($setting['imagecopy'])) {
            foreach($setting['imagecopy'] as $copysetting) {
                $oldCopyname = $this->getFileName($copysetting, $oldName);
                if (file_exists($oldSaveDir . $oldCopyname)) {
                    $newCopyname = $this->getFileName($copysetting, $newName);
                    if (!$copy) {
                        rename($oldSaveDir . $oldCopyname, $saveDir . $newCopyname);
                    } else {
                        copy($oldSaveDir . $oldCopyname, $saveDir . $newCopyname);
                    }
                }
            }
        }
        return str_replace(DS, '/', $newName);
    }

    /**
     * フィールドベースのファイル名を取得する
     *
     * @param array $setting
     * - namefield 対象となるファイルのベースの名前が格納されたフィールド名
     * - nameformat ファイル名のフォーマット
     * - name ファイル名の後に追加する名前
     * - nameadd nameを追加しないか
     * @param array $file ファイルの拡張子
     * @param EntityInterface $entity
     * @return mixed false / string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getFieldBasename($setting, $file, $entity)
    {
        if (empty($setting['namefield'])) return false;
        if (is_null($entity[$setting['namefield']])) return false;
        $basename = $entity[$setting['namefield']];
        if (!empty($setting['nameformat'])) {
            $basename = sprintf($setting['nameformat'], $basename);
        }
        if (!isset($setting['nameadd']) || $setting['nameadd'] !== false) {
            $basename .= '_' . $setting['name'];
        }
        $subdir = '';
        if (!empty($this->settings[$this->alias]['subdirDateFormat'])) {
            $subdir .= date($this->settings[$this->alias]['subdirDateFormat']);
            if (!preg_match('/\/$/', $subdir)) {
                $subdir .= '/';
            }
            $subdir = str_replace('/', DS, $subdir);
            $path = $this->savePath[$this->alias] . $subdir;
            if (!is_dir($path)) {
                $Folder = new Folder();
                $Folder->create($path);
                $Folder->chmod($path, 0777);
            }
        }
        return $subdir . $basename . '.' . $file['ext'];
    }

    /**
     * ベースファイル名からプレフィックス付のファイル名を取得する
     *
     * @param array $setting
     * @param string $filename
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getFileName($setting, $filename)
    {
        if (empty($setting)) {
            return $filename;
        }

        $pathinfo = pathinfo($filename);
        $ext = $pathinfo['extension'];
        // プレフィックス、サフィックスを取得
        $prefix = '';
        $suffix = '';
        if (!empty($setting['prefix'])) {
            $prefix = $setting['prefix'];
        }
        if (!empty($setting['suffix'])) {
            $suffix = $setting['suffix'];
        }

        $basename = preg_replace("/\." . $ext . "$/is", '', $filename);
        return $prefix . $basename . $suffix . '.' . $ext;
    }

    /**
     * ファイル名からベースファイル名を取得する
     * @param array $setting
     * @param string $filename
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getBasename($setting, $filename)
    {
        $pattern = "/^" . $setting['prefix'] . "(.*?)" . $setting['suffix'] . "\.[a-zA-Z0-9]*$/is";
        if (preg_match($pattern, $filename, $maches)) {
            return $maches[1];
        } else {
            return '';
        }
    }

    /**
     * 一意のファイル名を取得する
     *
     * @param array $setting 一意の名前を取得する元となるフィールド名
     * @param array $fileName 対象のファイル名
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUniqueFileName($setting, $file)
    {
        $ext = $file['ext'];
        $basename = preg_replace("/\." . $ext . "$/is", '', $file['name']);
        // 先頭が同じ名前のリストを取得し、後方プレフィックス付きのフィールド名を取得する
        $conditions[$setting['name'] . ' LIKE'] = $basename . '%' . $ext;
        $records = $this->table->find()->where([$conditions])->select($setting['name'])->all()->toArray();
        $numbers = [];
        if ($records) {
            foreach($records as $data) {
                if (!empty($data->{$setting['name']})) {
                    $_basename = preg_replace("/\." . $ext . "$/is", '', $data->{$setting['name']});
                    $lastPrefix = preg_replace('/^' . preg_quote($basename, '/') . '/', '', $_basename);
                    if (!$lastPrefix) {
                        $numbers[1] = 1;
                    } elseif (preg_match("/^__([0-9]+)$/s", $lastPrefix, $matches)) {
                        $numbers[$matches[1]] = true;
                    }
                }
            }
            if ($numbers) {
                $prefixNo = 1;
                while(true) {
                    if (!isset($numbers[$prefixNo])) break;
                    $prefixNo++;
                }
                if ($prefixNo == 1) {
                    return $basename . '.' . $ext;
                } else {
                    return $basename . '__' . ($prefixNo) . '.' . $ext;
                }
            } else {
                return $basename . '.' . $ext;
            }
        } else {
            return $basename . '.' . $ext;
        }
    }

    /**
     * 保存先のフォルダを設定し、取得する
     * @param null|string $alias (default : null)
     * @param string $saveDir
     * @param bool $isTheme
     * @param bool $limited
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSaveDir($alias, $isTheme = false, $limited = false): string
    {
        if (!$isTheme) {
            $basePath = WWW_ROOT . 'files' . DS;
        } else {
            $request = Router::getRequest();
            $site = $request->getAttribute('currentSite');
            if ($site->theme) {
                $basePath = ROOT . DS . 'plugins' . DS . $site->theme . DS . 'webroot' . DS . 'files' . DS;
            } else {
                $basePath = BcUtil::getViewPath() . 'files' . DS;
            }
        }
        if ($limited) {
            $basePath = $basePath . $limited . DS;
        }
        if ($this->settings[$alias]['saveDir']) {
            $saveDir = $basePath . $this->settings[$this->alias]['saveDir'] . DS;
        } else {
            $saveDir = $basePath;
        }
        return $saveDir;
    }

    /**
     * ファイルが重複しているかをチェックする
     *
     * @param string $fileName
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isFileExists($fileName): bool
    {
        $duplicates = $this->existsCheckDirs[$this->alias];
        if ($duplicates) {
            // existsCheckDirが存在する場合
            foreach($duplicates as $dir) {
                if (file_exists($dir . DS . $fileName)) return true;
            }
        } else {
            // saveDirのみの場合
            if (file_exists($this->savePath[$this->alias] . $fileName)) return true;
        }
        return false;
    }

    /**
     * アップロード中のフィールドにおいて既に存在する画像を全て削除する
     *
     * @param EntityInterface $oldEntity
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteExistingFiles($entity): void
    {
        $oldEntity = $this->table->find()->where(['id' => $entity->id])->first();
        if (!$oldEntity) return;
        $files = $this->getUploadingFiles();
        if (!$files) return;
        foreach($files as $name => $file) {
            $this->deleteExistingFile($name, $file, $oldEntity);
        }
    }

    /**
     * アップロード中のフィールドにおいて既に存在する画像を削除する
     *
     * @param string $name
     * @param array $file
     * @param EntityInterface $entity
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteExistingFile($name, $file, $entity)
    {
        if (!empty($file['tmp_name']) && $entity->{$name}) {
            $this->deleteFile($this->settings[$this->alias]['fields'][$name], $entity->{$name});
        }
    }

    /**
     * 画像をコピーする
     * @param array $setting
     * @param array $file
     * @checked
     * @unitTest
     */
    public function copyImages($setting, $file): void
    {
        if (($setting['type'] == 'all' || $setting['type'] == 'image') && !empty($setting['imagecopy']) && in_array($file['ext'], $this->imgExts)) {
            foreach($setting['imagecopy'] as $copy) {
                $copy['name'] = $setting['name'];
                $this->copyImage($copy, $file);
            }
        }
    }

    /**
     * 実際にアップロードされた情報を保持する
     *
     * @param array $files
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setUploadingFiles($files): void
    {
        $this->uploadingFiles[$this->alias] = $files;
    }

    /**
     * 実際にアップロードされた情報を取得する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUploadingFiles(): array
    {
        return $this->uploadingFiles[$this->alias] ?? [];
    }

    /**
     * 一時ファイルとして保存する
     *
     * @param array $data
     * @param string $tmpId
     * @return mixed false|array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function saveTmpFiles($data, $tmpId)
    {
        $this->Session->delete('Upload');
        $this->tmpId = $tmpId;
        $this->setupRequestData($data);
        $entity = $this->table->patchEntity($this->table->newEmptyEntity(), $data);
        $files = $this->getUploadingFiles();
        foreach($this->settings[$this->alias]['fields'] as $setting) {
            $files[$setting['name']] = $this->saveTmpFile($setting, $files[$setting['name']], $entity);
        }
        $this->setUploadingFiles($files);
        return $entity;
    }

    /**
     * ファイルを保存する
     *
     * @param EntityInterface $entity
     * @param array $file
     * @param array $setting 画像保存対象フィールドの設定
     * @return string|false ファイル名
     * @checked
     * @noTodo
     * @unitTest
     */
    public function saveTmpFile($setting, $file, $entity)
    {
        if (empty($file['tmp_name']) || (!empty($file['error']) && $file['error'] !== 0)) {
            return false;
        }
        $fileName = $this->getSaveTmpFileName($setting, $file, $entity);
        $this->rotateImage($file['tmp_name']);
        $name = str_replace(['.', '/'], ['_', '_'], $fileName);
        $this->Session->write('Upload.' . $name, $setting);
        $this->Session->write('Upload.' . $name . '.type', $file['type']);
        $this->Session->write('Upload.' . $name . '.data', file_get_contents($file['tmp_name']));
        $entity->{$setting['name'] . '_tmp'} = $fileName;
        return $fileName;
    }

    /**
     * 保存用ファイル名を取得する
     *
     * @param EntityInterface $entity
     * @param $setting
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSaveTmpFileName($setting, $file, $entity)
    {
        if (!empty($setting['namefield'])) {
            $entity->{$setting['namefield']} = $this->tmpId;
            $fileName = $this->getFieldBasename($setting, $file, $entity);
        } else {
            $fileName = $this->tmpId . '_' . $setting['name'] . '.' . $file['ext'];
        }
        return $fileName;
    }

    /**
     * アップロードされているかどうか
     * @return false|mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isUploaded()
    {
        return $this->uploaded[$this->alias] ?? false;
    }

    /**
     * アップロード状態をリセット
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetUploaded()
    {
        $this->uploaded[$this->alias] = false;
    }

}
