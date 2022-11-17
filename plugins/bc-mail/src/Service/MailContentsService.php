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
use BaserCore\Utility\BcContainerTrait;
use BcMail\Model\Table\MailContentsTable;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;

/**
 * MailContentsService
 * @property MailContentsTable $MailContents
 */
class MailContentsService implements MailContentsServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Construct
     *
     * @checked
     * @noTodo
     */
    public function __construct()
    {
        $this->MailContents = TableRegistry::getTableLocator()->get("BcMail.MailContents");
    }

    /**
     * 初期値を取得する
     *
     * @return EntityInterface
     * @checked
     * @noTodo
     */
    public function getNew()
    {
        return $this->MailContents->newEntity([
            'sender_name' => __d('baser', '送信先名を入力してください'),
            'subject_user' => __d('baser', 'お問い合わせ頂きありがとうございます'),
            'subject_admin' => __d('baser', 'お問い合わせを頂きました'),
            'layout_template' => 'default',
            'form_template' => 'default',
            'mail_template' => 'mail_default',
            'use_description' => true,
            'auth_captcha' => false,
            'ssl_on' => false,
            'save_info' => true
        ], [
            'validate' => false,
        ]);
    }

    /**
     * メールフォーム登録
     *
     * @param array $data
     * @param array $options
     * @return \Cake\Datasource\EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     */
    public function create(array $postData, $options = []): ?EntityInterface
    {
        $mailContent = $this->getNew();
        $mailContent = $this->MailContents->patchEntity($mailContent, $postData, $options);
        /* @var \BcMail\Model\Entity\MailContent $mailContent */
        return $this->MailContents->saveOrFail($mailContent);
    }
}
