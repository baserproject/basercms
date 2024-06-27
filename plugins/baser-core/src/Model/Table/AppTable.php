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

use BaserCore\Utility\BcUtil;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\I18n\FrozenTime;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Event\BcEventDispatcherTrait;

/**
 * Class AppTable
 */
class AppTable extends Table
{

    /**
     * Trait
     */
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
        return parent::setTable($this->addPrefix($table));
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
        $this->setTable(parent::getTable());
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
        if (isset($options['joinTable'])) {
            $options['joinTable'] = $this->addPrefix($options['joinTable']);
        }
        return parent::belongsToMany($associated, $options);
    }

    /**
     * findの前後にイベントを追加する
     *
     * @param string $type the type of query to perform
     * @param array<string, mixed> $options An array that will be passed to Query::applyOptions()
     * @return \Cake\ORM\Query The query builder
     * @checked
     * @noTodo
     */
    public function find(string $type = 'all', array $options = []): Query
    {
        // EVENT beforeFind
        $event = $this->dispatchLayerEvent('beforeFind', compact('type', 'options'));
        if ($event !== false) {
            $options = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('options') : $event->getResult();
        }

        $result = parent::find($type, $options);

        // EVENT afterFind
        $event = $this->dispatchLayerEvent('afterFind', compact('type', 'options', 'result'));
        if ($event !== false) {
            $result = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('result') : $event->getResult();
        }

        return $result;
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
        if(BcUtil::isTest()) {
            $prefix = BcUtil::getCurrentDbConfig()['prefix'];
        } else {
            $prefix = $this->getConnection()->config()['prefix'];
        }
        if (!preg_match('/^' . $prefix . '/', $table)) {
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
        \Cake\I18n\DateTime::setToStringFormat('yyyy-MM-dd HH:mm:ss');
    }

    /**
     * 機種依存文字の変換処理
     *
     * 内部文字コードがUTF-8である必要がある。
     * 多次元配列には対応していない。
     *
     * @param string $str 変換対象文字列
     * @return string 変換後文字列
     * @checked
     * @noTodo
     * @unitTest
     */
    public function replaceText($str)
    {
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
     * 一つ位置を上げる
     * @param string $id
     * @param array $conditions
     * @return boolean
     * @checked
     * @noTodo
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
     * @checked
     * @noTodo
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
     * @checked
     * @noTodo
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
            ->orderBy($order)
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
     * @checked
     * @noTodo
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
            $eventManager->on($eventKey, [], $listener['callable']);
        }
        unset($this->tmpEvents[$eventKey]);
    }

}
