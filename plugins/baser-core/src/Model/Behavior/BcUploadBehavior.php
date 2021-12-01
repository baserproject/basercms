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
namespace BaserCore\Model\Behavior;

use ArrayObject;
use BaserCore\Vendor\Imageresizer;
use Cake\Http\Session;
use Cake\ORM\Behavior;
use Cake\Utility\Hash;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\Note;
use Cake\Event\EventInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\EntityInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Service\SiteConfigServiceInterface;

/**
 * Class BcUploadBehavior
 *
 * ファイルアップロードビヘイビア
 *
 * 《設定例》
 * public $actsAs = array(
 *  'BcUpload' => array(
 *     'saveDir'  => "editor",
 *     'fields'  => array(
 *       'image'  => array(
 *         'type'      => 'image',
 *         'namefield'    => 'id',
 *         'nameadd'    => false,
 *            'subdirDateFormat'    => 'Y/m'    // Or false
 *         'imageresize'  => array('prefix' => 'template', 'width' => '100', 'height' => '100'),
 *                'imagecopy'        => array(
 *                    'thumb'            => array('suffix' => 'template', 'width' => '150', 'height' => '150'),
 *                    'thumb_mobile'    => array('suffix' => 'template', 'width' => '100', 'height' => '100')
 *                )
 *       ),
 *       'pdf' => array(
 *         'type'      => 'pdf',
 *         'namefield'    => 'id',
 *         'nameformat'  => '%d',
 *         'nameadd'    => false
 *       )
 *     )
 *   )
 * );
 *
 * @package Baser.Model.Behavior
 */
class BcUploadBehavior extends Behavior
{

    /**
     * BcContainerTrait
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
     * @var \SessionComponent
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
     *
     * @var array
     */
    public $uploaded = [];
    /**
     * initialize
     * @param  array $config
     * @return void
     * @checked
     * @unitTest
     */
    public function initialize(array $config): void
    {
        $this->table = $this->table();
        $this->alias = $this->alias;
        $settings = Hash::merge([
            'saveDir' => '',
            'existsCheckDirs' => [],
            'fields' => [],
        ], $config);
        foreach ($settings['fields'] as $key => $field) {
            if (empty($field['name'])) {
                $settings['fields'][$key]['name'] = $key;
            }
            if (!empty($field['imageresize'])) {
                if (empty($field['imageresize']['thumb'])) {
                    $settings['fields'][$key]['imageresize']['thumb'] = false;
                }
            } else {
                $settings['fields'][$key]['imageresize'] = false;
            }
            if (!isset($field['getUniqueFileName'])) {
                $settings['fields'][$key]['getUniqueFileName'] = true;
            }
        }
        $this->setConfig('settings.' . $this->alias, $settings);
        $this->savePath[$this->alias] = $this->getSaveDir($this->alias);
        // NOTE: 同じテーブルのディレクトリがないかをチェックする箇所
        $this->existsCheckDirs[] = $this->getExistsCheckDirs($this->alias);
        // NOTE: is_dirの判定がどこで使われてるのかわからない
        if (!is_dir($this->savePath[$this->alias])) {
            mkdir($this->savePath[$this->alias], 0777, true);
        }
        $this->Session = new Session();
    }

    /**
     * BeforeMarshal
     *
     * アップロード用のリクエストデータを変換する
     * @param EventInterface $event
     * @param ArrayObject $data
     * @param ArrayObject $options
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options)
    {
        $this->setupRequestData($data);

        $this->setConfig('settings.' . $this->alias . ".uploadedFile", $data['eyecatch']);
        // eyecatchをobjectからstringに変更
        if ($data['eyecatch']) {
            $data['eyecatch'] = $data['eyecatch']->getClientFileName();
        }
    }


    /**
     * Before Save
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return bool
     * @model 全体
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        // if (!$entity->getErrors()) {
        //     $uploadedFile = $this->getConfig('settings.' . $this->alias . ".uploadedFile");
        //     $uploadedFile->moveTo($this->savePath[$this->alias] . $uploadedFile->getClientFileName());
        //     // NOTE: 2回通ってきてしまうため、offにする
        //     $event = $this->table->getEventManager()->matchingListeners('beforeSave');
        //     if ($event) $this->table->getEventManager()->off('Model.beforeSave');
        //     return true;
        // }

        if ($entity->id) {
            $this->deleteExistingFiles();
        }

        $Model->data = $this->deleteFiles($entity, $Model->data);

        $result = $this->saveFiles($Model, $Model->data);
        if ($result) {
            $Model->data = $result;
            return true;
        } else {
            return false;
        }
    }

    /**
     * リクエストされたデータを処理しやすいようにセットアップする
     *
     * @param $content
     * @checked
     * @unitTest
     */
    public function setupRequestData($content)
    {
        $settings = $this->getConfig('settings.' . $this->alias);
        if (!empty($settings['fields'])) {
            foreach($settings['fields'] as $key => $field) {
                $upload = false;
                $uploadedFile = ((array) $content)[$field['name']];
                if (!empty($uploadedFile) && is_object($uploadedFile) && $uploadedFile->getSize() != 0) {
                    if ($uploadedFile->getClientFileName()) {
                        $upload = true;
                    }
                } else {
                    if (isset($content[$field['name'] . '_tmp'])) {
                        // TODO: セッション未実装のため一時的にコメントアウト
                        // セッションに一時ファイルが保存されている場合は復元する
                        // if ($this->moveFileSessionToTmp($Model, $field['name'])) {
                        //     $data = $Model->data[$Model->name];
                        //     $upload = true;
                        // }
                    } elseif (isset($content[$field['name'] . '_'])) {
                        // 新しいデータが送信されず、既存データを引き継ぐ場合は、元のフィールド名に戻す
                        // TODO:  エラーに関してのデータはafterMarshal以後にて実装する
                        // if (isset($data[$field['name']]['error']) && $data[$field['name']]['error'] == UPLOAD_ERR_NO_FILE) {
                        //     $Model->data[$Model->name][$field['name']] = $Model->data[$Model->name][$field['name'] . '_'];
                        //     unset($Model->data[$Model->name][$field['name'] . '_']);
                        // }
                    }
                }
                if ($upload) {
                    // 拡張子を取得
                    $settings['fields'][$key]['ext'] = $field['ext'] = BcUtil::decodeContent($uploadedFile->getClientMediaType(), $uploadedFile->getClientFileName());
                    $this->setConfig('settings.' . $this->alias, $settings);
                    // タイプ別除外
                    $targets = [];
                    if ($field['type'] == 'image') {
                        $targets = $this->imgExts;
                    } elseif (is_array($field['type'])) {
                        $targets = $field['type'];
                    } elseif ($field['type'] != 'all') {
                        $targets = [$field['type']];
                    }
                    if ($targets && !in_array($field['ext'], $targets)) {
                        $upload = false;
                    }
                }
                $settings['fields'][$key]['upload'] = $upload;
                $this->setConfig('settings.' . $this->alias, $settings);
            }
        }
    }

    /**
     * After save
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @model 全体
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        // TODO: 一時措置
        return;
        if ($this->uploaded[$Model->name]) {
            $Model->data = $this->renameToBasenameFields($Model);
            $Model->data = $Model->save($Model->data, ['callbacks' => false, 'validate' => false]);
            $this->uploaded[$Model->name] = false;
        }
        $settings = $this->getConfig('settings.' . $this->alias);
        foreach($settings['fields'] as $key => $value) {
            $settings['fields'][$key]['upload'] = false;
        }
        return true;
    }

    /**
     * 一時ファイルとして保存する
     *
     * @param Model $Model
     * @param array $data
     * @param string $tmpId
     * @return mixed false|array
     * @model 全体
     */
    public function saveTmpFiles(Model $Model, $data, $tmpId)
    {
        $this->Session->delete('Upload');
        $Model->data = $data;
        $this->tmpId = $tmpId;
        $this->setupRequestData($Model);
        $Model->data = $this->deleteFiles($Model, $Model->data);
        $result = $this->saveFiles($Model, $Model->data);
        if ($result) {
            $Model->data = $result;
            return $Model->data;
        } else {
            return false;
        }
    }

    /**
     * 削除対象かチェックしながらファイル群を削除する
     *
     * @param EntityInterface $entity
     * @param array $requestData
     * @return array
     * @model $field['name']
     */
    public function deleteFiles($entity, $requestData)
    {
        $settings = $this->getConfig('settings.' . $this->alias);
        foreach($settings['fields'] as $key => $field) {
            $oldValue = '';
            if ($entity && !empty($entity[$field['name']])) {
                $oldValue = $entity[$field['name']];
            } elseif (!empty($entity[$field['name']]) && !is_array($entity[$field['name']])) {
                $oldValue = $entity[$field['name']];
            }
            $requestData = $this->deleteFileWhileChecking($entity, $field, $requestData, $oldValue);
        }
        return $requestData;
    }

    /**
     * 削除対象かチェックしながらファイルを削除する
     *
     * @param EntityInterface $entity
     * @param array $fieldSetting
     * @param array $requestData
     * @param string $oldValue
     * @return array $requestData
     * @model 全体
     */
    public function deleteFileWhileChecking(EntityInterface $entity, $fieldSetting, $requestData, $oldValue = null)
    {
        $fieldName = $fieldSetting['name'];
        if (!empty($requestData[$this->alias][$fieldName . '_delete'])) {
            if (!$this->tmpId) {
                $this->delFile($entity, $oldValue, $fieldSetting);
                $requestData[$this->alias][$fieldName] = '';
            } else {
                $requestData[$this->alias][$fieldName] = $oldValue;
            }
        }
        return $requestData;
    }

    /**
     * ファイル群を保存する
     *
     * @param array $requestData
     * @return mixed false|array
     * @model 全体
     */
    public function saveFiles(Model $Model, $requestData)
    {
        $this->uploaded[$this->alias] = false;
        $settings = $this->getConfig('settings.' . $this->alias);
        foreach($settings['fields'] as $key => $field) {
            $result = $this->saveFileWhileChecking($Model, $field, $requestData);
            if ($result) {
                $requestData = $result;
            } else {
                // 失敗したら処理を中断してfalseを返す
                return false;
            }
        }
        return $requestData;
    }

    /**
     * 保存対象かチェックしながらファイルを保存する
     * @param array $fieldSetting
     * @param array $requestData
     * @param array $options
     *    - deleteTmpFiles : 一時ファイルを削除するかどうか
     * @return mixed bool|$requestData
     * @model 全体
     */
    public function saveFileWhileChecking(Model $Model, $fieldSetting, $requestData, $options = [])
    {
        $options = array_merge([
            'deleteTmpFiles' => true
        ], $options);

        if (empty($requestData[$this->alias][$fieldSetting['name']])
            || !is_array($requestData[$this->alias][$fieldSetting['name']])
        ) {
            return $requestData;
        }

        if (!$this->tmpId && empty($fieldSetting['upload'])) {
            if (!empty($requestData[$this->alias][$fieldSetting['name']]) && is_array($requestData[$this->alias][$fieldSetting['name']])) {
                unset($requestData[$this->alias][$fieldSetting['name']]);
            }
            return $requestData;
        }
        // ファイル名が重複していた場合は変更する
        if ($fieldSetting['getUniqueFileName'] && !$this->tmpId) {
            $requestData[$this->alias][$fieldSetting['name']]['name'] = $this->getUniqueFileName($Model, $fieldSetting['name'], $requestData[$this->alias][$fieldSetting['name']]['name'], $fieldSetting);
        }
        // 画像を保存
        $tmpName = (!empty($requestData[$this->alias][$fieldSetting['name']]['tmp_name']))? $requestData[$this->alias][$fieldSetting['name']]['tmp_name'] : false;
        if (!$tmpName) {
            return $requestData;
        }
        $fileName = $this->saveFile($Model, $fieldSetting);
        if ($fileName) {
            if (!$this->copyImages($Model, $fieldSetting, $fileName)) {
                return false;
            }
            // ファイルをリサイズ
            if (!$this->tmpId) {
                if (!empty($fieldSetting['imageresize'])) {
                    $filePath = $this->savePath[$Model->alias] . $fileName;
                    $this->resizeImage($filePath, $filePath, $fieldSetting['imageresize']['width'], $fieldSetting['imageresize']['height'], $fieldSetting['imageresize']['thumb']);
                }
                $requestData[$this->alias][$fieldSetting['name']] = $fileName;
            } else {
                $requestData[$this->alias][$fieldSetting['name']]['session_key'] = $fileName;
            }
            // 一時ファイルを削除
            if ($options['deleteTmpFiles']) {
                @unlink($tmpName);
            }
            $this->uploaded[$this->alias] = true;
        } else {
            if ($this->tmpId) {
                return $requestData;
            } else {
                return false;
            }
        }
        return $requestData;
    }

    /**
     * セッションに保存されたファイルデータをファイルとして保存する
     *
     * @param Model $Model
     * @param string $fieldName
     * @return boolean
     * @model $fieldName . '_tmp'
     */
    public function moveFileSessionToTmp(Model $Model, $fieldName)
    {
        $fileName = $Model->data[$Model->alias][$fieldName . '_tmp'];
        $sessionKey = str_replace(['.', '/'], ['_', '_'], $fileName);
        $tmpName = $this->savePath[$Model->alias] . $sessionKey;
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

        // 元の名前を取得
        /*$pos = strpos($sessionKey, '_');
        $fileName = substr($sessionKey, $pos + 1, strlen($sessionKey));*/

        // アップロードされたデータとしてデータを復元する
        $uploadInfo['error'] = 0;
        $uploadInfo['name'] = $fileName;
        $uploadInfo['tmp_name'] = $tmpName;
        $uploadInfo['size'] = $fileSize;
        $uploadInfo['type'] = $fileType;
        $Model->data[$Model->alias][$fieldName] = $uploadInfo;
        unset($Model->data[$Model->alias][$fieldName . '_tmp']);
        return true;
    }

    /**
     * ファイルを保存する
     *
     * @param Model $Model
     * @param array $field 画像保存対象フィールドの設定
     * @return mixed false|ファイル名
     * @model $field['name']
     */
    public function saveFile(Model $Model, $field)
    {
        // データを取得
        $file = $Model->data[$Model->name][$field['name']];

        if (empty($file['tmp_name'])) {
            return false;
        }
        if (!empty($file['error']) && $file['error'] != 0) {
            return false;
        }

        $fileName = $this->getSaveFileName($Model, $field, $file['name']);
        $filePath = $this->savePath[$Model->alias] . $fileName;
        $this->rotateImage($file['tmp_name']);

        if (!$this->tmpId) {
            if (copy($file['tmp_name'], $filePath)) {
                chmod($filePath, 0666);
                $ret = $fileName;
            } else {
                $ret = false;
            }
        } else {
            $_fileName = str_replace(['.', '/'], ['_', '_'], $fileName);
            $this->Session->write('Upload.' . $_fileName, $field);
            $this->Session->write('Upload.' . $_fileName . '.type', $file['type']);
            $this->Session->write('Upload.' . $_fileName . '.data', file_get_contents($file['tmp_name']));
            return $fileName;
        }

        return $ret;
    }

    /**
     * 保存用ファイル名を取得する
     *
     * @param $field
     * @param $name
     * @return mixed|string
     * @model $field['namefield']
     */
    public function getSaveFileName(Model $Model, $field, $name)
    {
        // プレフィックス、サフィックスを取得
        $prefix = '';
        $suffix = '';
        if (!empty($field['prefix'])) {
            $prefix = $field['prefix'];
        }
        if (!empty($field['suffix'])) {
            $suffix = $field['suffix'];
        }
        // 保存ファイル名を生成
        if (!$this->tmpId) {
            $basename = preg_replace("/\." . $field['ext'] . "$/is", '', $name);
            $fileName = $prefix . $basename . $suffix . '.' . $field['ext'];
            $existsFile = false;
            foreach($this->existsCheckDirs[$Model->alias] as $existsCheckDir) {
                if (file_exists($existsCheckDir . $fileName)) {
                    $existsFile = true;
                    break;
                }
            }
            if ($existsFile) {
                if (preg_match('/(.+_)([0-9]+)$/', $basename, $matches)) {
                    $basename = $matches[1] . ((int)$matches[2] + 1);
                } else {
                    $basename = $basename . '_1';
                }
                $fileName = $this->getSaveFileName($Model, $field, $basename . '.' . $field['ext']);
            }
        } else {
            if (!empty($field['namefield'])) {
                $Model->data[$Model->alias][$field['namefield']] = $this->tmpId;
                $fileName = $this->getFieldBasename($Model, $field, $field['ext']);
            } else {
                $fileName = $this->tmpId . '_' . $field['name'] . '.' . $field['ext'];
            }
        }
        return $fileName;
    }

    /**
     * 画像をExif情報を元に正しい確度に回転する
     *
     * @param $file
     * @return bool
     */
    public function rotateImage($file)
    {
        if (!function_exists('exif_read_data')) {
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
     * @param EntityInterface $entity
     * @param array $field 画像保存対象フィールドの設定
     * @return boolean
     * @model $field['name'] tmp_name
     */
    public function copyImage(EntityInterface $entity, $field)
    {
        // データを取得
        $file = $entity->{$field['name']};

        // プレフィックス、サフィックスを取得
        $prefix = '';
        $suffix = '';
        if (!empty($field['prefix'])) {
            $prefix = $field['prefix'];
        }
        if (!empty($field['suffix'])) {
            $suffix = $field['suffix'];
        }

        // 保存ファイル名を生成
        $basename = preg_replace("/\." . $field['ext'] . "$/is", '', $file['name']);
        $fileName = $prefix . $basename . $suffix . '.' . $field['ext'];

        $filePath = $this->savePath[$this->alias] . $fileName;

        if (!empty($field['thumb'])) {
            $thumb = $field['thumb'];
        } else {
            $thumb = false;
        }

        return $this->resizeImage($entity[$field['name']]['tmp_name'], $filePath, $field['width'], $field['height'], $thumb);
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
     * Before delete
     * 画像ファイルの削除を行う
     * 削除に失敗してもデータの削除は行う
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @model 全体
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        // TODO: 一時措置
        return;
        // $Model->data = $Model->find('first', [
        //     'conditions' => [
        //         $Model->alias . '.' . $Model->primaryKey => $Model->id
        //     ]
        // ]);
        // $this->delFiles($Model);
        // return true;
    }

    /**
     * 画像ファイル群を削除する
     *
     * @param EntityInterface $entity
     * @param string $fieldName フィールド名
     * @model $entity[$field['name']]
     */
    public function delFiles(EntityInterface $entity, $fieldName = null)
    {
        $settings = $this->getConfig('settings.' . $this->alias);
        foreach($settings['fields'] as $key => $field) {
            if (empty($field['name'])) {
                $field['name'] = $key;
            }
            if (!$fieldName || ($fieldName && $fieldName == $field['name'])) {
                if (!empty($entity[$field['name']])) {
                    $file = $entity[$field['name']];

                    // DBに保存されているファイル名から拡張子を取得する
                    preg_match('/\.([^.]+)\z/', $file, $match);
                    if (!empty($match[1])) {
                        $field['ext'] = $match[1];
                    }

                    $this->delFile($entity, $file, $field);
                }
            }
        }
    }

    /**
     * ファイルを削除する
     *
     * @param string $file
     * @param array $field 保存対象フィールドの設定
     * - ext 対象のファイル拡張子
     * - prefix 対象のファイルの接頭辞
     * - suffix 対象のファイルの接尾辞
     * @param boolean $delImagecopy
     * @return boolean
     */
    public function delFile($file, $field, $delImagecopy = true)
    {
        if (!$file) {
            return true;
        }

        if (empty($field['ext'])) {
            $pathinfo = pathinfo($file);
            $field['ext'] = $pathinfo['extension'];
        }

        // プレフィックス、サフィックスを取得
        $prefix = '';
        $suffix = '';
        if (!empty($field['prefix'])) {
            $prefix = $field['prefix'];
        }
        if (!empty($field['suffix'])) {
            $suffix = $field['suffix'];
        }

        // 保存ファイル名を生成
        $basename = preg_replace("/\." . $field['ext'] . "$/is", '', $file);
        $fileName = $prefix . $basename . $suffix . '.' . $field['ext'];
        $filePath = $this->savePath[$this->alias] . $fileName;
        if (!empty($field['imagecopy']) && $delImagecopy) {
            foreach($field['imagecopy'] as $copy) {
                $copy['name'] = $field['name'];
                $copy['ext'] = $field['ext'];
                $this->delFile($file, $copy, false);
            }
        }

        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return true;
    }

    /**
     * 全フィールドのファイル名をフィールド値ベースのファイル名に変更する
     *
     * @param Model $Model
     * @param bool $copy
     * @return array
     * @model 全体
     */
    public function renameToBasenameFields(Model $Model, $copy = false)
    {
        $data = $Model->data;
        $settings = $this->getConfig('settings.' . $this->alias);
        foreach($settings['fields'] as $key => $setting) {
            if (empty($setting['name'])) {
                $setting['name'] = $key;
            }
            $value = $this->renameToBasenameField($Model, $setting, $copy);
            if ($value !== false) {
                $data[$Model->alias][$setting['name']] = $value;
            }
        }
        return $data;
    }

    /**
     * ファイル名をフィールド値ベースのファイル名に変更する
     *
     * @param \Model $Model
     * @param array $setting
     * @param bool $copy
     * @return bool|mixed
     * @model 全体
     */
    public function renameToBasenameField(Model $Model, $setting, $copy = false)
    {
        if (empty($setting['namefield']) || empty($Model->data[$Model->alias][$setting['name']])) {
            return false;
        }
        $oldName = $Model->data[$Model->alias][$setting['name']];
        if (is_array($oldName)) {
            return false;
        }
        $saveDir = $this->savePath[$Model->alias];
        $saveDirInTheme = $this->getSaveDir($Model, true);
        $oldSaveDir = '';
        if (file_exists($saveDir . $oldName)) {
            $oldSaveDir = $saveDir;
        } elseif (file_exists($saveDirInTheme . $oldName)) {
            $oldSaveDir = $saveDirInTheme;
        }
        if (!file_exists($oldSaveDir . $oldName)) {
            return '';
        }
        $pathinfo = pathinfo($oldName);
        $newName = $this->getFieldBasename($Model, $setting, $pathinfo['extension']);
        if (!$newName) {
            return false;
        }
        if ($oldName == $newName) {
            return false;
        }
        if (!empty($setting['imageresize'])) {
            $newName = $this->getFileName($Model, $setting['imageresize'], $newName);
        } else {
            $newName = $this->getFileName($Model, null, $newName);
        }

        if (!$copy) {
            rename($oldSaveDir . $oldName, $saveDir . $newName);
        } else {
            copy($oldSaveDir . $oldName, $saveDir . $newName);
        }
        if (!empty($setting['imagecopy'])) {
            foreach($setting['imagecopy'] as $copysetting) {
                $oldCopyname = $this->getFileName($Model, $copysetting, $oldName);
                if (file_exists($oldSaveDir . $oldCopyname)) {
                    $newCopyname = $this->getFileName($Model, $copysetting, $newName);
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
     * @param Model $Model
     * @param array $setting
     * - namefield 対象となるファイルのベースの名前が格納されたフィールド名
     * - nameformat ファイル名のフォーマット
     * - name ファイル名の後に追加する名前
     * - nameadd nameを追加しないか
     * @param string $ext ファイルの拡張子
     * @return mixed false / string
     * @model $Model->id
     */
    public function getFieldBasename(Model $Model, $setting, $ext)
    {
        if (empty($setting['namefield'])) {
            return false;
        }
        $data = $Model->data[$Model->alias];
        if (!isset($data[$setting['namefield']])) {
            if ($setting['namefield'] == 'id' && $Model->id) {
                $basename = $Model->id;
            } else {
                return false;
            }
        } else {
            $basename = $data[$setting['namefield']];
        }

        if (!empty($setting['nameformat'])) {
            $basename = sprintf($setting['nameformat'], $basename);
        }

        if (!isset($setting['nameadd']) || $setting['nameadd'] !== false) {
            $basename .= '_' . $setting['name'];
        }

        $subdir = '';
        $settings = $this->getConfig('settings.' . $this->alias);
        if (!empty($settings['subdirDateFormat'])) {
            $subdir .= date($settings['subdirDateFormat']);
            if (!preg_match('/\/$/', $subdir)) {
                $subdir .= '/';
            }
            $subdir = str_replace('/', DS, $subdir);
            $path = $this->savePath[$Model->alias] . $subdir;
            if (!is_dir($path)) {
                $Folder = new Folder();
                $Folder->create($path);
                $Folder->chmod($path, 0777);
            }
        }

        return $subdir . $basename . '.' . $ext;
    }

    /**
     * ベースファイル名からプレフィックス付のファイル名を取得する
     *
     * @param array $setting
     * @param string $filename
     * @return string
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
     * @param string $fieldName 一意の名前を取得する元となるフィールド名
     * @param string $fileName 対象のファイル名
     * @return string
     * @model $Model->find
     */
    public function getUniqueFileName(Model $Model, $fieldName, $fileName, $setting = null)
    {
        $pathinfo = pathinfo($fileName);
        $basename = preg_replace("/\." . $pathinfo['extension'] . "$/is", '', $fileName);

        $ext = $setting['ext'];

        // 先頭が同じ名前のリストを取得し、後方プレフィックス付きのフィールド名を取得する
        $conditions[$this->alias . '.' . $fieldName . ' LIKE'] = $basename . '%' . $ext;
        $datas = $Model->find('all', ['conditions' => $conditions, 'fields' => [$fieldName], 'order' => "{$this->alias}.{$fieldName}"]);
        $datas = Hash::extract($datas, "{n}.{$this->alias}.{$fieldName}");
        $numbers = [];

        if ($datas) {
            foreach($datas as $data) {
                $_basename = preg_replace("/\." . $ext . "$/is", '', $data);
                $lastPrefix = preg_replace('/^' . preg_quote($basename, '/') . '/', '', $_basename);
                if (!$lastPrefix) {
                    $numbers[1] = 1;
                } elseif (preg_match("/^__([0-9]+)$/s", $lastPrefix, $matches)) {
                    $numbers[$matches[1]] = true;
                }
            }
            if ($numbers) {
                $prefixNo = 1;
                while(true) {
                    if (!isset($numbers[$prefixNo])) {
                        break;
                    }
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
     * 保存先のフォルダを取得する
     *
     * @param string $alias
     * @param bool $isTheme
     * @return string $saveDir
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSaveDir(string $alias, $isTheme = false, $limited = false)
    {
        if (!$isTheme) {
            $basePath = WWW_ROOT . 'files' . DS;
        } else {
            $siteConfig = $this->getService(SiteConfigServiceInterface::class);
            $theme = $siteConfig->getValue('theme');
            if ($theme) {
                $basePath = WWW_ROOT . 'theme' . DS . $theme . DS . 'files' . DS;
            } else {
                $basePath = BcUtil::getViewPath() . 'files' . DS;
            }
        }
        if ($limited) {
            $basePath = $basePath . $limited . DS;
        }
        if ($this->getConfig("settings.${alias}.saveDir")) {
            $saveDir = $basePath . $this->getConfig("settings.${alias}.saveDir") . DS;
        } else {
            $saveDir = $basePath;
        }
        return $saveDir;
    }

    /**
     * 保存時にファイルの重複確認を行うディレクトリのリストを取得する
     *
     * @param string $modelName
     * @return array $existsCheckDirs
     */
    private function getExistsCheckDirs($modelName)
    {
        $existsCheckDirs = [];
        // TODO: $this->savePathがまだないためコメントアウト
        // $existsCheckDirs[] = $this->savePath[$modelName];

        $basePath = WWW_ROOT . 'files' . DS;
        $existsCheckDirs = $this->getConfig("${modelName}.existsCheckDirs");
        if ($existsCheckDirs) {
            foreach($existsCheckDirs as $existsCheckDir) {
                $existsCheckDirs[] = $basePath . $existsCheckDir . DS;
            }
        }

        return $existsCheckDirs;
    }

    /**
     * 既に存在するデータのファイルを削除する
     * @note $entityにのみ作用?
     * @model 全体
     */
    public function deleteExistingFiles()
    {
        // $dataTmp = $Model->data;
        $settings = $this->getConfig('settings.' . $this->alias);
        $uploadFields = array_keys($settings['fields']);
        $targetFields = [];
        // TODO: postの場合がなんちゃら
        // $dataTmpを今で表すとどうなるか確認する
        foreach($uploadFields as $field) {
            if (!empty($dataTmp[$Model->alias][$field]['tmp_name'])) {
                $targetFields[] = $field;
            }
        }
        if (!$targetFields) {
            return;
        }
        // $Model->set($Model->find('first', [
        //     'conditions' => [$Model->alias . '.' . $Model->primaryKey => $Model->data[$Model->alias][$Model->primaryKey]],
        //     'recursive' => -1
        // ]));
        foreach($targetFields as $field) {
            $this->delFiles($Model, $field);
        }
        $Model->set($dataTmp);
    }

    /**
     * 画像をコピーする
     *
     * @param Model $Model
     * @param string $fileName
     * @param array $field
     * @return bool
     * @model 全体
     */
    public function copyImages(Model $Model, $field, $fileName)
    {
        if (!$this->tmpId && ($field['type'] == 'all' || $field['type'] == 'image') && !empty($field['imagecopy']) && in_array($field['ext'], $this->imgExts)) {
            foreach($field['imagecopy'] as $copy) {
                // コピー画像が元画像より大きい場合はスキップして作成しない
                $size = $this->getImageSize($this->savePath[$Model->alias] . $fileName);
                if ($size && $size['width'] < $copy['width'] && $size['height'] < $copy['height']) {
                    if (isset($copy['smallskip']) && $copy['smallskip'] === false) {
                        $copy['width'] = $size['width'];
                        $copy['height'] = $size['height'];
                    } else {
                        continue;
                    }
                }

                // ファイル名の重複を回避する為の処理、元画像ファイルと同様に、コピー画像ファイルにも対応する
                if (isset($Model->data[$Model->alias]['name']['name']) && $fileName !== $Model->data[$Model->alias]['name']['name']) {
                    $Model->data[$Model->alias]['name']['name'] = $fileName;
                }
                $copy['name'] = $field['name'];
                $copy['ext'] = $field['ext'];
                $ret = $this->copyImage($Model, $copy);
                if (!$ret) {
                    // 失敗したら処理を中断してfalseを返す
                    return false;
                }
            }
        }
        return true;
    }

}
