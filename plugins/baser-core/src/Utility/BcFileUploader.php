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

namespace BaserCore\Utility;

use ArrayObject;
use Cake\Http\Session;
use Cake\ORM\Entity;
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
 * Class BcFileUploader
 */
class BcFileUploader
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
    public $savePath = '';

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
    public $uploaded = false;

    /**
     * Table
     * @var Table
     */
    public $table = null;

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
        $this->settings = $this->getSettings($config);
        $this->savePath = $this->getSaveDir();
        if (!is_dir($this->savePath)) {
            $Folder = new Folder();
            $Folder->create($this->savePath);
            $Folder->chmod($this->savePath, 0777, true);
        }
        $this->existsCheckDirs = $this->getExistsCheckDirs();
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
    public function getSettings(array $config): array
    {
        $setting = Hash::merge([
            'saveDir' => '',
            'existsCheckDirs' => [],
            'fields' => []
        ], $config);
        foreach($setting['fields'] as $key => $field) {
            if (empty($field['name'])) {
                $setting['fields'][$key]['name'] = $key;
            }
            if (!empty($field['imageresize'])) {
                if (empty($field['imageresize']['thumb'])) {
                    $setting['fields'][$key]['imageresize']['thumb'] = false;
                }
            } else {
                $setting['fields'][$key]['imageresize'] = false;
            }
            if (!isset($field['getUniqueFileName'])) {
                $setting['fields'][$key]['getUniqueFileName'] = true;
            }
        }
        return $setting;
    }

    /**
     * 保存時にファイルの重複確認を行うディレクトリのリストを取得する
     *
     * @param string alias
     * @checked
     * @noTodo
     * @unitTest
     */
    private function getExistsCheckDirs(): array
    {
        $existsCheckDirs = [];
        $existsCheckDirs[] = $this->savePath;
        $basePath = WWW_ROOT . 'files' . DS;
        if ($this->settings['existsCheckDirs']) {
            foreach($this->settings['existsCheckDirs'] as $existsCheckDir) {
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
        foreach($this->settings['fields'] as $setting) {
            $name = $setting['name'];
            if (!empty($data[$name]) && is_array($data[$name])) {
                $file = $data[$name];
                $file['uploadable'] = $this->isUploadable($setting['type'], $file['type'], $file);
                $file['ext'] = BcUtil::decodeContent($file['type'], @$file['name']);
                if ($file['uploadable']) {
                    // arrayをstringとして変換
                    $data[$name] = $file['name'];
                } elseif (isset($file['error']) && (int) $file['error'] === UPLOAD_ERR_NO_FILE) {
                    if (isset($data[$name . '_'])) {
                        // 新しいデータが送信されず、既存データを引き継ぐ場合は、元のフィールド名に戻す
                        $data[$name] = $data[$name . '_'];
                    } else {
                        $data[$name] = '';
                    }
                }
                if(isset($data[$setting['name'] . '_'])) unset($data[$setting['name'] . '_']);
            }
            if(isset($data[$name . '_delete'])) {
            	$file['delete'] = $data[$name . '_delete'];
            	unset($data[$name . '_delete']);
            } else {
            	$file['delete'] = null;
            }
            $files[$name] = $file;
        }
        $this->setUploadingFiles($files);
        return $data;
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
        foreach($this->settings['fields'] as $setting) {
            $name = $setting['name'];
            if (isset($data[$name . '_tmp']) && $this->moveFileSessionToTmp($data, $name)) {
                $data[$setting['name']] = $this->getUploadingFiles()[$setting['name']];
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
        if (!empty($file) && is_array($file) && (int) @$file['error'] === 0 && $file['name'] && $file['tmp_name']) {
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
     * @param EntityInterface $entity
     * @checked
     * @noTodo
     * @unitTest
     */
    public function saveFiles($entity)
    {
        $files = $this->getUploadingFiles();
        $this->uploaded = false;
        foreach($this->settings['fields'] as $setting) {
            $file = $files[$setting['name']] ?? [];
            $result = $this->saveFileWhileChecking($setting, $file, $entity);
            if($result) {
                $files[$setting['name']] = $result;
                if(!empty($files[$setting['name']]['name'])) {
                    $entity->{$setting['name']} = $files[$setting['name']]['name'];
                }
            }
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
    public function saveFileWhileChecking($setting, $file, $entity, $options = [])
    {
        if (empty($file['uploadable'])) return false;
        $options = array_merge([
            'deleteTmpFiles' => true
        ], $options);
        if ($setting['getUniqueFileName']) {
            $file['name'] = $this->getUniqueFileName($setting, $file, $entity);
        }
        $fileName = $this->saveFile($setting, $file);
        if ($fileName) {
            $file['name'] = $fileName;
			if (($setting['type'] == 'all' || $setting['type'] == 'image') && !empty($setting['imagecopy']) && in_array($file['ext'], $this->imgExts)) {
				$this->copyImages($setting, $file);
			}
			if (!empty($setting['imageresize'])) {
				$filePath = $this->savePath . $fileName;
				$this->resizeImage($filePath, $filePath, $setting['imageresize']['width'], $setting['imageresize']['height'], $setting['imageresize']['thumb']);
			}
            if ($options['deleteTmpFiles']) {
                @unlink($file['tmp_name']);
            }
            $this->uploaded = true;
        } else {
            $file['name'] = '';
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
    	if(empty($file['tmp_name'])) return false;
        $fileName = $this->getSaveFileName($setting, $file);
        $filePath = $this->savePath . $fileName;
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
     * @param bool $force
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteFiles($oldEntity, $newEntity, $force = false)
    {
        $files = $this->getUploadingFiles();
        foreach($this->settings['fields'] as $setting) {
            $file = $files[$setting['name']] ?? [];
            $newEntity = $this->deleteFileWhileChecking($setting, $file, $newEntity, $oldEntity, $force);
        }
        return $newEntity;
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
        if ((!empty($file['delete']) || $force) && !empty($oldEntity->{$setting['name']})) {
            $this->deleteFile($setting, $oldEntity->{$setting['name']});
            $newEntity->{$setting['name']} = '';
        }
        return $newEntity;
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
        if (!$file || is_array($file)) return true;
        $prefix = (!empty($setting['prefix']))? $setting['prefix'] : '';
        $suffix = (!empty($setting['suffix']))? $setting['suffix'] : '';
        $pathinfo = pathinfo($file);
        $ext = $pathinfo['extension'];
        $filePath = $this->savePath . $prefix . preg_replace("/\." . $ext . "$/is", '', $file) . $suffix . '.' . $ext;
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
        $tmpName = $this->savePath . $sessionKey;
        $fileData = base64_decode($this->Session->read('Upload.' . $sessionKey . '.data'));
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
        $uploadInfo['ext'] = BcUtil::decodeContent($fileType, $fileName);
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
    	if(empty($file['name'])) return '';
        $name = $file['name'];
        $ext = $file['ext'];
        $prefix = (!empty($setting['prefix']))? $setting['prefix'] : '';
        $suffix = (!empty($setting['suffix']))? $setting['suffix'] : '';
        $pathinfo = pathinfo($name);
        $basename = $pathinfo['filename'];
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
        $filePath = $this->savePath . $prefix . $basename . $suffix . '.' . $file['ext'];
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
        foreach($this->settings['fields'] as $setting) {
            $value = $this->renameToBasenameField($setting, $files[$setting['name']], $entity, $copy);
            if ($value !== false) {
                $entity->{$setting['name']} = $value;
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
        if (empty($setting['namefield']) || empty($file) || !empty($file['delete'])) {
            return false;
        }
        $oldName = @$file['name'];
        if (!$oldName || is_array($oldName)) {
            return false;
        }
        if(!empty($file['ext'])) {
        	$pathInfo = pathinfo($oldName);
        	$oldName = $pathInfo['filename'] . '.' . $file['ext'];
        }
        $saveDir = $this->savePath;
        $saveDirInTheme = $this->getSaveDir(true);
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
        if (is_null($entity->{$setting['namefield']})) return false;
        $basename = $entity->{$setting['namefield']};
        if (!empty($setting['nameformat'])) {
            $basename = sprintf($setting['nameformat'], $basename);
        }
        if (!isset($setting['nameadd']) || $setting['nameadd'] !== false) {
            $basename .= '_' . $setting['name'];
        }
        $subdir = '';
        if (!empty($this->settings['subdirDateFormat'])) {
            $subdir .= date($this->settings['subdirDateFormat']);
            if (!preg_match('/\/$/', $subdir)) {
                $subdir .= '/';
            }
            $subdir = str_replace('/', DS, $subdir);
            $path = $this->savePath . $subdir;
            if (!is_dir($path)) {
                $Folder = new Folder();
                $Folder->create($path);
                $Folder->chmod($path, 0777);
            }
        }
        if(empty($file['ext'])) {
        	$pathInfo = pathinfo($entity->{$setting['name']});
        	$file['ext'] = $pathInfo['extension'];
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
    public function getBasename(array $setting, string $filename): string
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
     * @param array $file 対象のファイル
     * @param EntityInterface $entity
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUniqueFileName(array $setting, array $file, EntityInterface $entity): string
    {
    	if(!empty($this->settings['getUniqueFileName']) && method_exists($this->table, $this->settings['getUniqueFileName'])) {
    		return $this->table->{$this->settings['getUniqueFileName']}($setting, $file, $entity);
    	}
		if(!isset($file['name'])) return '';
        $ext = $file['ext'];
        $pathInfo = pathinfo($file['name']);
        $basename = $pathInfo['filename'];
        // 先頭が同じ名前のリストを取得し、後方プレフィックス付きのフィールド名を取得する
        $where = [$setting['name'] . ' LIKE' => $basename . '%' . $ext];
        if($entity->id) {
            $where = array_merge($where, ['id <>' => $entity->id]);
        }
        $records = $this->table->find()->where($where)->select($setting['name'])->all()->toArray();
        $numbers = [];
        if ($records) {
            foreach($records as $data) {
                if (!empty($data[$setting['name']])) {
                    $_basename = preg_replace("/\." . $ext . "$/is", '', $data[$setting['name']]);
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
     * @param bool $isTheme
     * @param bool $limited
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSaveDir(bool $isTheme = false, bool $limited = false): string
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
        if ($this->settings['saveDir']) {
            $saveDir = $basePath . $this->settings['saveDir'] . DS;
        } else {
            $saveDir = $basePath;
        }
        return $saveDir;
    }

    /**
     * ファイルが重複しているかをチェックする
     *
     * @param string $fileName
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isFileExists(string $fileName): bool
    {
        $duplicates = $this->existsCheckDirs;
        if ($duplicates) {
            // existsCheckDirが存在する場合
            foreach($duplicates as $dir) {
                if (file_exists($dir . DS . $fileName)) return true;
            }
        } else {
            // saveDirのみの場合
            if (file_exists($this->savePath . $fileName)) return true;
        }
        return false;
    }

    /**
     * アップロード中のフィールドにおいて既に存在する画像を全て削除する
     *
     * @param EntityInterface $oldEntity
     * @param bool $force
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteExistingFiles($oldEntity, $force = false): void
    {
        if (!$oldEntity) return;
        $files = $this->getUploadingFiles();
        if (!$files) return;
        foreach($files as $name => $file) {
            if(!empty($file['uploadable']) || $force) {
                $this->deleteExistingFile($name, $file, $oldEntity, $force);
            }
        }
    }

    /**
     * アップロード中のフィールドにおいて既に存在する画像を削除する
     *
     * @param string $name
     * @param array $file
     * @param EntityInterface $entity
     * @param bool $force
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteExistingFile(string $name, array $file, EntityInterface $entity, bool $force = false)
    {
        if ((!empty($file['tmp_name']) || $force) && $entity->{$name}) {
            $this->deleteFile($this->settings['fields'][$name], $entity->{$name});
        }
    }

    /**
     * 画像をコピーする
     * @param array $setting
     * @param array $file
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copyImages(array $setting, array $file): void
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
    public function setUploadingFiles(array $files): void
    {
        $this->uploadingFiles = $files;
    }

    /**
     * 実際にアップロードされた情報を取得する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUploadingFiles(): array
    {
        return $this->uploadingFiles ?? [];
    }

    /**
     * 一時ファイルとして保存する
     *
     * @param array $data
     * @param string $tmpId
     * @return false|EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function saveTmpFiles($data, $tmpId)
    {
        $this->Session->delete('Upload');
        $this->tmpId = $tmpId;
        $data = $this->setupRequestData($data);
        $files = $this->getUploadingFiles();
        $entity = new Entity($data);
        foreach($this->settings['fields'] as $setting) {
            $fileName = $this->saveTmpFile($setting, $files[$setting['name']], $entity);
            if($fileName) {
                $entity[$setting['name']] = $files[$setting['name']] = $fileName;
                $entity[$setting['name'] . '_tmp'] = $entity[$setting['name']];
            } elseif($fileName === false) {
                $entity[$setting['name']] = $files[$setting['name']] = '';
			}
        }
		// 削除するチェックボックスにチェックが入っている場合の処理
		foreach ($files as $field => $value) {
			if(!empty($value['delete'])) {
				unset($entity[$field]);
			}
		}
        $this->setUploadingFiles($files);
        return $entity;
    }

    /**
     * ファイルを保存する
     *
     * @param array $setting 画像保存対象フィールドの設定
     * @param array $file
     * @param EntityInterface $entity
     * @return string|false ファイル名
     * @checked
     * @noTodo
     * @unitTest
     */
    public function saveTmpFile($setting, $file, $entity)
    {
        if (empty($file['tmp_name'])) return '';
    	if(!empty($file['error'])) {
    		if($file['error'] === UPLOAD_ERR_NO_FILE) {
    			return '';
    		} else {
    			return false;
    		}
		}
        $fileName = $this->getSaveTmpFileName($setting, $file, $entity);
        $this->rotateImage($file['tmp_name']);
        $name = str_replace(['.', '/'], ['_', '_'], $fileName);
        $this->Session->write('Upload.' . $name, $setting);
        $this->Session->write('Upload.' . $name . '.type', $file['type']);
        $this->Session->write('Upload.' . $name . '.data', base64_encode(file_get_contents($file['tmp_name'])));
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
            if(empty($entity[$setting['namefield']])) {
                $entity[$setting['namefield']] = $this->tmpId;
            }
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
        return $this->uploaded ?? false;
    }

    /**
     * アップロード状態をリセット
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetUploaded()
    {
        $this->uploaded = false;
    }

}
