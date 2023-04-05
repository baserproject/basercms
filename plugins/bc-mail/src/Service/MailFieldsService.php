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

namespace BcMail\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Error\BcException;
use BaserCore\Utility\BcContainerTrait;
use BcMail\Model\Entity\MailField;
use BcMail\Model\Table\MailFieldsTable;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Throwable;

/**
 * MailFieldsService
 * @property MailFieldsTable $MailFields
 * @property MailMessagesService $MailMessagesService
 */
class MailFieldsService implements MailFieldsServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Constructor
     * @checked
     * @noTodo
     */
    public function __construct()
    {
        $this->MailFields = TableRegistry::getTableLocator()->get('BcMail.MailFields');
        $this->MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
    }

    /**
     * 単一データ取得
     * @param int $id
     * @param array $queryParams
     * @return EntityInterface|MailField
     * @checked
     * @noTodo
     */
    public function get(int $id, array $queryParams = [])
    {
        $queryParams = array_merge([
            'status' => ''
        ], $queryParams);

        $conditions = [];
        if ($queryParams['status'] === 'publish') {
            $conditions = $this->MailFields->MailContents->Contents->getConditionAllowPublish();
            $conditions['use_field'] = true;
        }

        return $this->MailFields->get($id, [
            'contain' => ['MailContents' => ['Contents']],
            'conditions' => $conditions
        ]);
    }

    /**
     * 一覧データ取得
     * @checked
     * @noTodo
     */
    public function getIndex(int $mailContentId, array $queryParams = [])
    {
        $options = array_merge([
            'use_field' => null,
            'status' => '',
            'contain' => ['MailContents' => ['Contents']]
        ], $queryParams);

        $conditions = ['MailFields.mail_content_id' => $mailContentId];
        if (!is_null($options['use_field'])) $conditions['use_field'] = $options['use_field'];

        $query = $this->MailFields->find()
            ->contain($options['contain'])
            ->order(['MailFields.sort']);
        if (!empty($queryParams['limit'])) {
            $query->limit($queryParams['limit']);
        }

        if ($options['status'] === 'publish') {
            $conditions['use_field'] = true;
            $conditions = array_merge($conditions, $this->MailFields->MailContents->Contents->getConditionAllowPublish());
        }

        return $query->where($conditions);
    }

    /**
     * リスト取得
     *
     * @param int $mailContentId
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList(int $mailContentId)
    {
        $conditions = [];
        if ($mailContentId) $conditions = ['MailFields.mail_content_id' => $mailContentId];
        return $this->MailFields->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where($conditions)->toArray();
    }

    /**
     * 初期データ取得
     *
     * @checked
     * @noTodo
     */
    public function getNew($mailContentId)
    {
        return $this->MailFields->newEntity([
            'mail_content_id' => $mailContentId,
            'type' => 'text',
            'use_field' => true,
            'no_send' => false,
            'valid' => false
        ]);
    }

    /**
     * 作成
     *
     * @checked
     * @noTodo
     */
    public function create(array $postData)
    {
        if (isset($postData['valid_ex']) && is_array($postData['valid_ex'])) $postData['valid_ex'] = implode(',', $postData['valid_ex']);
        $postData['no'] = $this->MailFields->getMax('no', ['mail_content_id' => $postData['mail_content_id'] ?? '']) + 1;
        $postData['sort'] = $this->MailFields->getMax('sort') + 1;
        $postData['source'] = $this->MailFields->formatSource($postData['source'] ?? null);
        $entity = $this->MailFields->patchEntity($this->MailFields->newEmptyEntity(), $postData);
        $this->MailFields->getConnection()->begin();
        try {
            if (!$entity->getErrors()) {
                if (!$this->MailMessagesService->addMessageField($postData['mail_content_id'], $postData['field_name'] ?? null)) {
                    $this->MailFields->getConnection()->rollback();
                    throw new BcException(__d('baser_core', 'データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。'));
                }
            }
            $result = $this->MailFields->saveOrFail($entity);
        } catch (Throwable $e) {
            $this->MailFields->getConnection()->rollback();
            throw $e;
        }
        $this->MailFields->getConnection()->commit();
        return $result;
    }

    /**
     * 編集
     * @param EntityInterface $entity
     * @param array $postData
     * @checked
     * @noTodo
     */
    public function update(EntityInterface $entity, array $postData)
    {
        $oldFieldName = $entity->field_name;
        if (isset($postData['valid_ex']) && is_array($postData['valid_ex'])) $postData['valid_ex'] = implode(',', $postData['valid_ex']);
        $postData['source'] = $this->MailFields->formatSource($postData['source'] ?? '');
        $entity = $this->MailFields->patchEntity($entity, $postData);
        $this->MailFields->getConnection()->begin();
        if (!$entity->getErrors() && $entity->field_name !== $oldFieldName) {
            if (!$this->MailMessagesService->renameMessageField($entity->mail_content_id, $oldFieldName, $entity->field_name)) {
                $this->MailFields->getConnection()->rollback();
                throw new BcException(__d('baser_core', 'データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。'));
            }
        }
        try {
            $result = $this->MailFields->saveOrFail($entity);
        } catch (Throwable $e) {
            $this->MailFields->getConnection()->rollback();
            throw $e;
        }
        $this->MailFields->getConnection()->commit();
        return $result;
    }

    /**
     * 削除
     * @param int $id
     * @checked
     * @noTodo
     */
    public function delete(int $id)
    {
        $entity = $this->MailFields->get($id);
        $this->MailFields->getConnection()->begin();
        if(!$this->MailMessagesService->deleteMessageField($entity->mail_content_id, $entity->field_name)) {
            throw new BcException(__d('baser_core', 'データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。'));
        }
        try {
            $result = $this->MailFields->delete($entity);
        } catch (Throwable $e) {
            $this->MailFields->getConnection()->rollback();
            throw $e;
        }
        $this->MailFields->getConnection()->commit();
        return $result;
    }

    /**
     * コピー
     * @param int $mailContentId
     * @param int $id
     * @checked
     * @noTodo
     */
    public function copy(int $mailContentId, int $id)
    {
        try {
            if (!$this->MailFields->copy($id)) return false;
            $this->MailMessagesService->construction($mailContentId);
        } catch (Throwable $e) {
            throw $e;
        }
        return true;
    }

    /**
     * 有効状態にする
     * @param int $id
     * @checked
     * @noTodo
     */
    public function publish(int $id)
    {
        $entity = $this->get($id);
        $entity->use_field = true;
        return $this->MailFields->save($entity);
    }

    /**
     * 無効状態にする
     *
     * @param int $id
     * @checked
     * @noTodo
     */
    public function unpublish(int $id)
    {
        $entity = $this->get($id);
        $entity->use_field = false;
        return $this->MailFields->save($entity);
    }

    /**
     * IDからタイトルリストを取得する
     *
     * @param array $ids
     * @param array $ids
     * @return array
     * @checked
     * @noTodo
     */
    public function getTitlesById(array $ids): array
    {
        return $this->MailFields->find('list')->select(['id', 'name'])->where(['id IN' => $ids])->toArray();
    }

    /**
     * 一括処理
     * @param string $method
     * @param array $ids
     * @return bool
     * @checked
     * @noTodo
     */
    public function batch(string $method, array $ids): bool
    {
        if (!$ids) return true;
        $db = $this->MailFields->getConnection();
        $db->begin();
        foreach($ids as $id) {
            if (!$this->$method($id)) {
                $db->rollback();
                throw new BcException(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
            }
        }
        $db->commit();
        return true;
    }

    /**
     * 並び順を変更する
     *
     * @param int $id
     * @param int $offset
     * @param array $conditions
     * @return bool
     * @checked
     * @noTodo
     */
    public function changeSort(int $id, int $offset, array $conditions = []): bool
    {
        $result = $this->MailFields->changeSort($id, $offset, [
            'conditions' => $conditions,
            'sortFieldName' => 'sort',
        ]);
        return $result;
    }

    /**
     * オートコンプリートオプションを取得する
     *
     * @return array
     * @checked
     * @noTodo
     */
	public function getAutoCompleteOptions(): array
	{
		$autoCompleteDatas = Configure::read('BcMail.autoComplete');

		$autoCompleteOptions = [];
		foreach ($autoCompleteDatas as $data) {
			$autoCompleteOptions[$data['name']] = $data['title'];
			if (isset($data['child'])) {
				foreach ($data['child'] as $dataChild1) {
					$autoCompleteOptions[$dataChild1['name']] = '　└' . $dataChild1['title'];
					if (isset($dataChild1['child'])) {
						foreach ($dataChild1['child'] as $dataChild2) {
							$autoCompleteOptions[$dataChild2['name']] = '　　└' . $dataChild2['title'];
							if (isset($dataChild2['child'])) {
								foreach ($dataChild2['child'] as $dataChild3) {
									$autoCompleteOptions[$dataChild3['name']] = '　　　└' . $dataChild3['title'];
								}
							}
						}
					}
				}
			}
		}

		return $autoCompleteOptions;
	}

}
