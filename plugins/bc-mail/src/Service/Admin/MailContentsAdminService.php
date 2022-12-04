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

namespace BcMail\Service\Admin;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Utility\BcSiteConfig;
use BcMail\Model\Entity\MailContent;
use BcMail\Service\MailContentsService;
use Cake\Datasource\EntityInterface;

/**
 * MailContentsAdminService
 */
class MailContentsAdminService extends MailContentsService implements MailContentsAdminServiceInterface
{

    /**
     * 編集画面用の View 変数を取得する
     * @param EntityInterface $entity
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForEdit(EntityInterface $entity)
    {
        return [
            'mailContent' => $entity,
            'publishLink' => $this->getPublishLink($entity),
            'editorEnterBr' => BcSiteConfig::get('editor_enter_br')
        ];
    }

    /**
     * 公開用のリンクを取得する
     * @param MailContent|EntityInterface $entity
     * @return null|string
     * @checked
     * @noTodo
     */
    public function getPublishLink(EntityInterface $entity) {
        $contentsService = $this->getService(ContentsServiceInterface::class);
        if($entity->content->status) {
            return $contentsService->getUrl(
                $entity->content->url,
                true,
                $entity->content->site->useSubDomain
            );
        }
        return null;
    }

}
