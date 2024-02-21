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

namespace BcCcFile\View\Helper;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\View\Helper\BcAdminFormHelper;
use BcCcFile\Utility\BcCcFileUtil;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Model\Entity\CustomLink;
use Cake\ORM\TableRegistry;
use Cake\View\Helper;

/**
 * Class BcCcFileHelper
 *
 * @property BcAdminFormHelper $BcAdminForm
 */
class BcCcFileHelper extends Helper
{

    /**
     * Helper
     * @var string[]
     */
    public array $helpers = [
        'BaserCore.BcAdminForm' => ['templates' => 'BaserCore.bc_form']
    ];

    /**
     * control
     *
     * @param string $fieldName
     * @param CustomField $field
     * @param array $options
     * @return string
     */
    public function control(CustomLink $link, array $options = []): string
    {
        $options = array_merge([
            'type' => 'file',
            'imgsize' => 'thumb'
        ], $options);
        return $this->BcAdminForm->control($link->name, $options);
    }

    /**
     * プレビュー
     *
     * @param CustomLink $link
     * @return string
     */
    public function preview(CustomLink $link)
    {
        return $this->control($link);
    }

    /**
     * Get
     *
     * @param mixed $fieldValue
     * @param CustomLink $link
     * @param array $options
	 * 	- output : 出力形式
	 * 		- tag : 画像の場合は画像タグ、ファイルの場合はリンク
	 * 		- url : ファイルのURL
     * @return mixed
     */
    public function get($fieldValue, CustomLink $link, array $options = [])
    {
		$options = array_merge([
			'output' => 'tag',
			'entity' => null,
			'table' => 'BcCustomContent.CustomEntries',
			'imgsize' => 'thumb'
		], $options);

		if($fieldValue) {
			if($options['output'] === 'tag') {
				$checkValue = $fieldValue;
				if(isset($options['tmp'])) {
					$checkValue = $options['tmp'];
				}
				if(is_string($checkValue) && in_array(pathinfo($checkValue, PATHINFO_EXTENSION), ['png', 'gif', 'jpeg', 'jpg'])) {
					$output = $this->BcAdminForm->BcUpload->uploadImage($link->name, $options['entity'], $options);
				} else {
					$options['label'] = $link->title;
					$output = $this->BcAdminForm->BcUpload->fileLink($link->name, $options['entity'], $options);
				}
			} elseif($options['output'] === 'url') {
			    if(is_string($fieldValue)) {
                    $entriesTable = TableRegistry::getTableLocator()->get('BcCustomContent.CustomEntries');
                    if(!$entriesTable->hasBehavior('BaserCore.BcUpload')) {
                        BcCcFileUtil::setupUploader($link->custom_table_id);
                    }
                    $setting = $entriesTable->getSettings();
			        $output = '/files/' . $setting['saveDir'] . '/' . $fieldValue;
			    } else {
			        $output = '';
			    }
			} else {
				$output = $fieldValue;
			}
		} else {
			$output = '';
		}
		return $output;
    }

}
