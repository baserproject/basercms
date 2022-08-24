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

namespace BcSearchIndex\Controller;

use BaserCore\Controller\AppController;
use Cake\Event\EventInterface;

/**
 * SearchIndicesController
 */
class SearchIndicesController extends AppController
{

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(EventInterface $event):void
    {
        parent::beforeFilter($event);
        $Content = ClassRegistry::init('Content');
        $sites = $this->getTableLocator()->get('BaserCore.Sites');
        $currentSite = $sites->findByUrl($this->getRequest()->getPath());
        $url = '/';
        if ($this->request->getParam('action') !== 'search') {
            $prefix = str_replace('_search', '', $this->request->getParam('action'));
            if ($prefix == $currentSite->name) {
                $url = '/' . $currentSite->alias . '/';
                $this->request = $this->request->withParam('action', 'search');
                $this->action = 'search';
            }
        }
        $content = $Content->find('first', ['conditions' => ['Content.url' => $url], 'recursive' => 0]);
        if (is_null($content['Site']['id'])) {
            $content['Site'] = $this->Site->getRootMain();
        }
        $this->request = $this->request->withParam('Content', $content['Content']);
        $this->request = $this->request->withParam('Site', $content['Site']);
    }

    /**
     * コンテンツ検索
     *
     * @return void
     */
    public function search()
    {
        $datas = [];
        $query = [];

        $default = ['named' => ['num' => 10]];
        $this->setViewConditions('SearchIndex', ['default' => $default, 'type' => 'get']);

        if (!empty($this->request->getData('SearchIndex'))) {
            foreach($this->request->getData('SearchIndex') as $key => $value) {
                $this->request = $this->request->withData('SearchIndex.' . $key, h($value));
            }
        }
        if (isset($this->request->getQuery('q')[0])) {
            $this->paginate = [
                'conditions' => $this->_createSearchConditions($this->request->getData()),
                'order' => 'SearchIndex.priority DESC, SearchIndex.modified DESC, SearchIndex.id',
                'limit' => $this->passedArgs['num']
            ];

            $datas = $this->paginate('SearchIndex');
            $query = $this->_parseQuery($this->request->getQuery('q'));
        }
        $this->set('query', $query);
        $this->set('datas', $datas);
        $this->setTitle(__d('baser', '検索結果一覧'));
    }

    /**
     * [SMARTPHONE] コンテンツ検索
     */
    public function smartphone_search()
    {
        $this->setAction('search');
    }


    /**
     * 検索キーワードを分解し配列に変換する
     *
     * @param string $query
     * @return array
     */
    protected function _parseQuery($query)
    {
        $query = str_replace('　', ' ', $query);
        if (strpos($query, ' ') !== false) {
            $query = explode(' ', $query);
        } else {
            $query = [$query];
        }
        return h($query);
    }

    /**
     * 検索条件を生成する
     *
     * @param array $data
     * @return    array    $conditions
     * @access    protected
     */
    protected function _createSearchConditions($data)
    {
        $conditions = $this->SearchIndex->getConditionAllowPublish();
        $query = '';
        if (!empty($data['SearchIndex']['q'])) {
            $query = $data['SearchIndex']['q'];
        }
        if (!empty($data['SearchIndex']['cf'])) {
            $conditions['SearchIndex.content_filter_id'] = $data['SearchIndex']['cf'];
        }
        if (!empty($data['SearchIndex']['m'])) {
            $conditions['SearchIndex.model'] = $data['SearchIndex']['m'];
        }
        if (isset($data['SearchIndex']['s'])) {
            $conditions['SearchIndex.site_id'] = $data['SearchIndex']['s'];
        }
        if (isset($data['SearchIndex']['c'])) {
            $conditions['SearchIndex.content_id'] = $data['SearchIndex']['c'];
        }
        if (!empty($data['SearchIndex']['f'])) {
            $content = $this->Content->find('first', ['fields' => ['lft', 'rght'], 'conditions' => ['Content.id' => $data['SearchIndex']['f']], 'recursive' => -1]);
            $conditions['SearchIndex.rght <='] = $content['Content']['rght'];
            $conditions['SearchIndex.lft >='] = $content['Content']['lft'];
        }
        if ($query) {
            $query = $this->_parseQuery($query);
            foreach($query as $key => $value) {
                $conditions['and'][$key]['or'][] = ['SearchIndex.title LIKE' => "%{$value}%"];
                $conditions['and'][$key]['or'][] = ['SearchIndex.detail LIKE' => "%{$value}%"];
            }
        }

        return $conditions;
    }

}
