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

namespace BaserCore\Model\Table;

use ArrayObject;
use BaserCore\Utility\BcUtil;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\Event\EventInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\EntityInterface;
use BaserCore\Event\BcEventDispatcherTrait;

/**
 * Class AppTable
 */
class AppTable extends Table
{
    use BcEventDispatcherTrait;

    /**
     * 一時イベント
     * イベントを一時にオフにする場合に対象のコールバック処理を一時的に格納する
     * @var array
     */
    public $tmpEvents = [];

    /**
     * 公開状態のフィールド
     * AppTable::getConditionAllowPublish() で利用
     * @var string
     */
    public $publishStatusField = 'status';

    /**
     * 公開開始日のフィールド
     * AppTable::getConditionAllowPublish() で利用
     * @var string
     */
    public $publishBeginField = 'publish_begin';

    /**
     * 公開終了日のフィールド
     * AppTable::getConditionAllowPublish() で利用
     * @var string
     */
    public $publishEndField = 'publish_end';

    /**
     * テーブルをセット
     *
     * プレフィックスを追加する
     *
     * @param string $table
     * @return AppTable
     * @checked
     * @noTodo
     */
    public function setTable(string $table)
    {
        $table = $this->addPrefix($table);
        return parent::setTable($table);
    }

    /**
     * テーブルを取得
     *
     * プレフィックスを追加する
     *
     * @return string
     * @checked
     * @noTodo
     */
    public function getTable(): string
    {
        $table = parent::getTable();
        $this->_table = $this->addPrefix($table);
        return $this->_table;
    }

    /**
     * Belongs To Many
     *
     * joinTable にプレフィックスを追加
     *
     * @param string $associated
     * @param array $options
     * @return BelongsToMany
     * @checked
     * @noTodo
     */
    public function belongsToMany(string $associated, array $options = []): BelongsToMany
    {
        if(isset($options['joinTable'])) {
            $options['joinTable'] = $this->addPrefix($options['joinTable']);
        }
        return parent::belongsToMany($associated, $options);
    }

    /**
     * テーブル名にプレフィックスを追加する
     *
     * $this->getConnection()->config() を利用するとユニットテストで問題が発生するため、BcUtil::getCurrentDbConfig()を利用する
     *
     * $this->getConnection()->config()を利用すると、
     * そのテーブルに connection が設定されてしまう。
     *
     * ユニットテストの dataProvider で、テーブルを初期化する場合、
     * タイミング的に、接続についてテスト用のエイリアスが設定されていないので、
     * テスト用の接続ではなく、 default がセットされてしまう。
     *
     * @param $table
     * @return string
     * @checked
     * @noTodo
     */
    public function addPrefix($table)
    {
        $prefix = BcUtil::getCurrentDbConfig()['prefix'];
        if(!preg_match('/^' . $prefix . '/', $table)) {
            return $prefix . $table;
        }
        return $table;
    }

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        FrozenTime::setToStringFormat('yyyy-MM-dd HH:mm:ss');
    }

    /**
     * 配列の文字コードを変換する
     *
     * @param array $data 変換前のデータ
     * @param string $outenc 変換後の文字コード
     * @param string $inenc 変換元の文字コード
     * @return array 変換後のデータ
     * @TODO GLOBAL グローバルな関数として再配置する必要あり
     */
    public function convertEncodingByArray($data, $outenc, $inenc)
    {
        foreach($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->convertEncodingByArray($value, $outenc, $inenc);
            } else {
                if (mb_detect_encoding($value) <> $outenc) {
                    $data[$key] = mb_convert_encoding($value, $outenc, $inenc);
                }
            }
        }
        return $data;
    }

    /**
     * コントロールソースを取得する
     *
     * 継承先でオーバーライドする事
     *
     * @param $field
     * @return array
     */
    public function getControlSource($field)
    {
        return [];
    }

    /**
     * 機種依存文字の変換処理
     *
     * 内部文字コードがUTF-8である必要がある。
     * 多次元配列には対応していない。
     *
     * @param string    変換対象文字列
     * @return    string    変換後文字列
     * @TODO AppExModeに移行すべきかも
     */
    public function replaceText($str)
    {
        $ret = $str;
        $arr = [
            "\xE2\x85\xA0" => "I",
            "\xE2\x85\xA1" => "II",
            "\xE2\x85\xA2" => "III",
            "\xE2\x85\xA3" => "IV",
            "\xE2\x85\xA4" => "V",
            "\xE2\x85\xA5" => "VI",
            "\xE2\x85\xA6" => "VII",
            "\xE2\x85\xA7" => "VIII",
            "\xE2\x85\xA8" => "IX",
            "\xE2\x85\xA9" => "X",
            "\xE2\x85\xB0" => "i",
            "\xE2\x85\xB1" => "ii",
            "\xE2\x85\xB2" => "iii",
            "\xE2\x85\xB3" => "iv",
            "\xE2\x85\xB4" => "v",
            "\xE2\x85\xB5" => "vi",
            "\xE2\x85\xB6" => "vii",
            "\xE2\x85\xB7" => "viii",
            "\xE2\x85\xB8" => "ix",
            "\xE2\x85\xB9" => "x",
            "\xE2\x91\xA0" => "(1)",
            "\xE2\x91\xA1" => "(2)",
            "\xE2\x91\xA2" => "(3)",
            "\xE2\x91\xA3" => "(4)",
            "\xE2\x91\xA4" => "(5)",
            "\xE2\x91\xA5" => "(6)",
            "\xE2\x91\xA6" => "(7)",
            "\xE2\x91\xA7" => "(8)",
            "\xE2\x91\xA8" => "(9)",
            "\xE2\x91\xA9" => "(10)",
            "\xE2\x91\xAA" => "(11)",
            "\xE2\x91\xAB" => "(12)",
            "\xE2\x91\xAC" => "(13)",
            "\xE2\x91\xAD" => "(14)",
            "\xE2\x91\xAE" => "(15)",
            "\xE2\x91\xAF" => "(16)",
            "\xE2\x91\xB0" => "(17)",
            "\xE2\x91\xB1" => "(18)",
            "\xE2\x91\xB2" => "(19)",
            "\xE2\x91\xB3" => "(20)",
            "\xE3\x8A\xA4" => "(上)",
            "\xE3\x8A\xA5" => "(中)",
            "\xE3\x8A\xA6" => "(下)",
            "\xE3\x8A\xA7" => "(左)",
            "\xE3\x8A\xA8" => "(右)",
            "\xE3\x8D\x89" => "ミリ",
            "\xE3\x8D\x8D" => "メートル",
            "\xE3\x8C\x94" => "キロ",
            "\xE3\x8C\x98" => "グラム",
            "\xE3\x8C\xA7" => "トン",
            "\xE3\x8C\xA6" => "ドル",
            "\xE3\x8D\x91" => "リットル",
            "\xE3\x8C\xAB" => "パーセント",
            "\xE3\x8C\xA2" => "センチ",
            "\xE3\x8E\x9D" => "cm",
            "\xE3\x8E\x8F" => "kg",
            "\xE3\x8E\xA1" => "m2",
            "\xE3\x8F\x8D" => "K.K.",
            "\xE2\x84\xA1" => "TEL",
            "\xE2\x84\x96" => "No.",
            "\xE3\x8B\xBF" => "令和",
            "\xE3\x8D\xBB" => "平成",
            "\xE3\x8D\xBC" => "昭和",
            "\xE3\x8D\xBD" => "大正",
            "\xE3\x8D\xBE" => "明治",
            "\xE3\x88\xB1" => "(株)",
            "\xE3\x88\xB2" => "(有)",
            "\xE3\x88\xB9" => "(代)",
        ];

        return str_replace(array_keys($arr), array_values($arr), $str);
    }

    /**
     * 範囲を指定しての長さチェック
     *
     * @param mixed $check 対象となる値
     * @param int $min 値の最短値
     * @param int $max 値の最長値
     * @param boolean
     */
    public function between($check, $min, $max)
    {
        $check = (is_array($check))? current($check) : $check;
        $length = mb_strlen($check, Configure::read('App.encoding'));
        return ($length >= $min && $length <= $max);
    }

    /**
     * 指定フィールドのMAX値を取得する
     *
     * 現在数値フィールドのみ対応
     *
     * @param string $field
     * @param array $conditions
     * @return int
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getMax(string $field, array $conditions = []): int
    {
        $max = $this->find()->where($conditions)->all()->max($field);
        return $max->{$field} ?? 0;
    }

    /**
     * 英数チェック
     *
     * @param string $value チェック対象文字列
     * @return boolean
     */
    public static function alphaNumeric($value)
    {
        if (!$value) {
            return true;
        }
        if (preg_match("/^[a-zA-Z0-9]+$/", $value)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 一つ位置を上げる
     * @param string $id
     * @param array $conditions
     * @return boolean
     */
    public function sortup($id, $conditions)
    {
        return $this->changeSort($id, -1, $conditions);
    }

    /**
     * 一つ位置を下げる
     *
     * @param string $id
     * @param array $conditions
     * @return boolean
     */
    public function sortdown($id, $conditions)
    {
        return $this->changeSort($id, 1, $conditions);
    }

    /**
     * 並び順を変更する
     *
     * @param string $id
     * @param int $offset
     * @param array $options
     *   - conditions: データ取得条件
     *   - sortFieldName: ソートフィールドのカラム名 (初期値: sort)
     * @return boolean
     */
    public function changeSort($id, $offset, $options = [])
    {
        $options += [
            'conditions' => [],
            'sortFieldName' => 'sort',
        ];
        $offset = intval($offset);
        if ($offset === 0) {
            return true;
        }

        $conditions = $options['conditions'];

        $current = $this->get($id);

        // currentを含め変更するデータを取得
        if ($offset > 0) { // DOWN
            $order = [$options['sortFieldName']];
            $conditions[$options['sortFieldName'] . " >="] = $current->{$options['sortFieldName']};
        } else { // UP
            $order = [$options['sortFieldName'] . " DESC"];
            $conditions[$options['sortFieldName'] . " <="] = $current->{$options['sortFieldName']};
        }

        $result = $this->find()
            ->where($conditions)
            ->select(["id", $options['sortFieldName']])
            ->order($order)
            ->limit(abs($offset) + 1)
            ->all();

        $count = $result->count();
        if (!$count) {
            return false;
        }
        $entities = $result->toList();
        //データをローテーション
        $currentNewValue = $entities[$count - 1]->{$options['sortFieldName']};
        for($i = $count - 1; $i > 0; $i--) {
            $entities[$i]->{$options['sortFieldName']} = $entities[$i - 1]->{$options['sortFieldName']};
        }
        $entities[0]->{$options['sortFieldName']} = $currentNewValue;

        if (!$this->saveMany($entities)) {
            return false;
        }

        return true;
    }

    /**
     * Deconstructs a complex data type (array or object) into a single field value.
     *
     * @param string $field The name of the field to be deconstructed
     * @param mixed $data An array or object to be deconstructed into a field
     * @return mixed The resulting data that should be assigned to a field
     */
    public function deconstruct($field, $data)
    {
        if (!is_array($data)) {
            return $data;
        }

        $type = $this->getColumnType($field);

        // >>> CUSTOMIZE MODIFY 2013/11/10 ryuring 和暦対応
        /* if (!in_array($type, array('datetime', 'timestamp', 'date', 'time'))) { */
        // ---
        if (!in_array($type, ['string', 'text', 'datetime', 'timestamp', 'date', 'time'])) {
            // <<<
            return $data;
        }

        $useNewDate = (isset($data['year']) || isset($data['month']) ||
            isset($data['day']) || isset($data['hour']) || isset($data['minute']));

        // >>> CUSTOMIZE MODIFY 2013/11/10 ryuring 和暦対応
        /* $dateFields = array('Y' => 'year', 'm' => 'month', 'd' => 'day', 'H' => 'hour', 'i' => 'min', 's' => 'sec'); */
        // ---
        $dateFields = ['W' => 'wareki', 'Y' => 'year', 'm' => 'month', 'd' => 'day', 'H' => 'hour', 'i' => 'min', 's' => 'sec'];
        // <<<
        $timeFields = ['H' => 'hour', 'i' => 'min', 's' => 'sec'];
        $date = [];

        if (isset($data['meridian']) && empty($data['meridian'])) {
            return null;
        }

        if (isset($data['hour']) &&
            isset($data['meridian']) &&
            !empty($data['hour']) &&
            $data['hour'] != 12 &&
            $data['meridian'] === 'pm'
        ) {
            $data['hour'] = $data['hour'] + 12;
        }
        if (isset($data['hour']) && isset($data['meridian']) && $data['hour'] == 12 && $data['meridian'] === 'am') {
            $data['hour'] = '00';
        }
        if ($type === 'time') {
            foreach($timeFields as $key => $val) {
                if (!isset($data[$val]) || $data[$val] === '0' || $data[$val] === '00') {
                    $data[$val] = '00';
                } elseif ($data[$val] !== '') {
                    $data[$val] = sprintf('%02d', $data[$val]);
                }
                if (!empty($data[$val])) {
                    $date[$key] = $data[$val];
                } else {
                    return null;
                }
            }
        }

        // >>> CUSTOMIZE MODIFY 2013/11/10 ryuring 和暦対応
        /* if ($type === 'datetime' || $type === 'timestamp' || $type === 'date') { */
        // ---
        if ($type == 'text' || $type == 'string' || $type === 'datetime' || $type === 'timestamp' || $type === 'date') {
            // <<<
            foreach($dateFields as $key => $val) {
                if ($val === 'hour' || $val === 'min' || $val === 'sec') {
                    if (!isset($data[$val]) || $data[$val] === '0' || $data[$val] === '00') {
                        $data[$val] = '00';
                    } else {
                        $data[$val] = sprintf('%02d', $data[$val]);
                    }
                }

                // >>> CUSTOMIZE ADD 2013/11/10 ryuring 和暦対応
                if ($val == 'wareki' && !empty($data['wareki'])) {
                    $warekis = ['m' => 1867, 't' => 1911, 's' => 1925, 'h' => 1988, 'r' => 2018];
                    if (!empty($data['year'])) {
                        [$wareki, $year] = explode('-', $data['year']);
                        $data['year'] = $year + $warekis[$wareki];
                    }
                }
                // <<<
                // >>> CUSTOMIZE ADD 2013/11/10 ryuring 和暦対応
                /* if (!isset($data[$val]) || isset($data[$val]) && (empty($data[$val]) || $data[$val][0] === '-')) {
                  return null; */
                // ---
                if ($val != 'wareki' && !isset($data[$val]) || isset($data[$val]) && (empty($data[$val]) || (isset($data[$val][0]) && $data[$val][0] === '-'))) {
                    if ($type == 'text' || $type == 'string') {
                        return $data;
                    } else {
                        return null;
                    }
                }
                if (isset($data[$val]) && !empty($data[$val])) {
                    $date[$key] = $data[$val];
                }
            }
        }

        if ($useNewDate && !empty($date)) {
            // >>> CUSTOMIZE MODIFY 2013/11/10 ryuring 和暦対応
            /* $format = $this->getDataSource()->columns[$type]['format']; */
            // ---
            if ($type == 'text' || $type == 'string') {
                $format = 'Y-m-d H:i:s';
            } else {
                $format = $this->getDataSource()->columns[$type]['format'];
            }
            // <<<

            foreach(['m', 'd', 'H', 'i', 's'] as $index) {
                if (isset($date[$index])) {
                    $date[$index] = sprintf('%02d', $date[$index]);
                }
            }
            return str_replace(array_keys($date), array_values($date), $format);
        }
        return $data;
    }

    /**
     * 指定したモデル以外のアソシエーションを除外する
     *
     * @param array $auguments アソシエーションを除外しないモデル。
     * 　「.（ドット）」で区切る事により、対象モデルにアソシエーションしているモデルがさらに定義しているアソシエーションを対象とする事ができる
     * 　（例）UserGroup.Permission
     * @param boolean $reset バインド時に１回の find でリセットするかどうか
     * @return void
     */
    public function reduceAssociations($arguments, $reset = true)
    {
        $models = [];

        foreach($arguments as $index => $argument) {
            if (is_array($argument)) {
                if (count($argument) > 0) {
                    $arguments = am($arguments, $argument);
                }
                unset($arguments[$index]);
            }
        }

        foreach($arguments as $index => $argument) {
            if (!is_string($argument)) {
                unset($arguments[$index]);
            }
        }

        if (count($arguments) == 0) {
            $models[$this->name] = [];
        } else {
            foreach($arguments as $argument) {
                if (strpos($argument, '.') !== false) {
                    $model = substr($argument, 0, strpos($argument, '.'));
                    $child = substr($argument, strpos($argument, '.') + 1);

                    if ($child == $model) {
                        $models[$model] = [];
                    } else {
                        $models[$model][] = $child;
                    }
                } else {
                    $models[$this->name][] = $argument;
                }
            }
        }

        $relationTypes = ['belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany'];

        foreach($models as $bindingName => $children) {
            $model = null;

            foreach($relationTypes as $relationType) {
                $currentRelation = (isset($this->$relationType)? $this->$relationType : null);
                if (isset($currentRelation) && isset($currentRelation[$bindingName]) &&
                    is_array($currentRelation[$bindingName]) && isset($currentRelation[$bindingName]['className'])) {
                    $model = $currentRelation[$bindingName]['className'];
                    break;
                }
            }

            if (!isset($model)) {
                $model = $bindingName;
            }

            if (isset($model) && $model != $this->name && isset($this->$model)) {
                if (!isset($this->__backInnerAssociation)) {
                    $this->__backInnerAssociation = [];
                }
                $this->__backInnerAssociation[] = $model;
                $this->$model->reduceAssociations($children, $reset);
            }
        }

        if (isset($models[$this->name])) {
            foreach($models as $model => $children) {
                if ($model != $this->name) {
                    $models[$this->name][] = $model;
                }
            }

            $models = array_unique($models[$this->name]);
            $unbind = [];

            foreach($relationTypes as $relation) {
                if (isset($this->$relation)) {
                    foreach($this->$relation as $bindingName => $bindingData) {
                        if (!in_array($bindingName, $models)) {
                            $unbind[$relation][] = $bindingName;
                        }
                    }
                }
            }
            if (count($unbind) > 0) {
                $this->unbindModel($unbind, $reset);
            }
        }
    }

    /**
     * Used to report user friendly errors.
     * If there is a file app/error.php or app/app_error.php this file will be loaded
     * error.php is the AppError class it should extend ErrorHandler class.
     *
     * @param string $method Method to be called in the error class (AppError or ErrorHandler classes)
     * @param array $messages Message that is to be displayed by the error class
     */
    public function cakeError($method, $messages = [])
    {
        //======================================================================
        // router.php がロードされる前のタイミング（bootstrap.php）でエラーが発生した場合、
        // AppControllerなどがロードされていない為、Object::cakeError() を実行する事ができない。
        // router.php がロードされる前のタイミングでは、通常のエラー表示を行う
        //======================================================================
        if (!Configure::read('BcRequest.routerLoaded')) {
            trigger_error($method, E_USER_ERROR);
        } else {
            parent::cakeError($method, $messages);
        }
    }

    /**
     * コンテンツのURLにマッチする候補を取得する
     *
     * @param string $url
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUrlPattern($url)
    {
        $parameter = preg_replace('|^/|', '', $url);
        $paths = [];
        $paths[] = '/' . $parameter;
        if (preg_match('|/$|', $paths[0])) {
            $paths[] = $paths[0] . 'index';
        } elseif (preg_match('|^(.*?/)index$|', $paths[0], $matches)) {
            $paths[] = $matches[1];
        } elseif (preg_match('|^(.+?)\.html$|', $paths[0], $matches)) {
            $paths[] = $matches[1];
            if (preg_match('|^(.*?/)index$|', $matches[1], $matches)) {
                $paths[] = $matches[1];
            }
        }
        return $paths;
    }

    /**
     * 公開状態となっているデータを取得するための conditions 値を取得
     *
     * 公開状態（初期値：status）、公開開始日（初期値：publish_begin）、公開終了日（初期値：publish_end）
     * の組み合わせてによって配列を生成する。
     *
     * 公開状態が true であったとしても、公開期間が設定されている場合はそちらを優先する。
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getConditionAllowPublish()
    {
        $conditions[$this->getAlias() . '.' . $this->publishStatusField] = true;
        $conditions[] = ['or' => [[$this->getAlias() . '.' . $this->publishBeginField . ' <=' => date('Y-m-d H:i:s')],
            [$this->getAlias() . '.' . $this->publishBeginField . ' IS' => null]]];
        $conditions[] = ['or' => [[$this->getAlias() . '.' . $this->publishEndField . ' >=' => date('Y-m-d H:i:s')],
            [$this->getAlias() . '.' . $this->publishEndField . ' IS' => null]]];
        return $conditions;
    }

    /**
     * イベントを一時的にオフにする
     * @param string $eventKey
     */
    public function offEvent($eventKey)
    {
        $eventManager = $this->getEventManager();
        $this->tmpEvents[$eventKey] = $eventManager->listeners($eventKey);
        $eventManager->off($eventKey);
    }

    /**
     * 一時的にオフにしたイベントをオンにする
     * BcModelEventDispatcherは対象外とする
     * @param string $eventKey
     * @checked
     * @noTodo
     * @unitTest
     */
    public function onEvent($eventKey)
    {
        if (!isset($this->tmpEvents[$eventKey])) return;
        $eventManager = $this->getEventManager();
        foreach($this->tmpEvents[$eventKey] as $listener) {
            if (get_class($listener['callable'][0]) !== 'BaserCore\Event\BcModelEventDispatcher') {
                $eventManager->on($eventKey, [], $listener['callable']);
            }
        }
        unset($this->tmpEvents[$eventKey]);
    }

}
