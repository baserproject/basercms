<?php
/**
 * CuCustomField : baserCMS Custom Field Text Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfText.View.Helper
 * @license          MIT LICENSE
 */

namespace BcCcMultiple\View\Helper;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\View\Helper\BcAdminFormHelper;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Model\Entity\CustomLink;
use BcCustomContent\View\Helper\CustomContentArrayTrait;
use Cake\View\Helper;

/**
 * Class BcCcMultipleHelper
 *
 * @property BcAdminFormHelper $BcAdminForm
 */
class BcCcMultipleHelper extends Helper
{

    /**
     * Trait
     */
    use CustomContentArrayTrait;

    /**
     * Helper
     * @var string[]
     */
    public $helpers = [
        'BaserCore.BcAdminForm' => ['templates' => 'BaserCore.bc_form'],
        'BaserCore.BcBaser'
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
            'type' => 'multiCheckbox',
            'options' => $this->textToArray($link->custom_field->source),
        ], $options);
        return $this->BcAdminForm->control($link->name, $options);
    }

    /**
     * プレビュー
     *
     * @param CustomLink $link
     * @return mixed
     */
    public function preview(CustomLink $link)
    {
        return $this->BcBaser->getElement('BcCcMultiple.preview');
    }

    /**
     * Search Control
     * @param string $fieldName
     * @param CustomField $field
     * @param array $options
     * @return string
     */
    public function searchControl(CustomLink $link, array $options = []): string
    {
        return $this->control($link, $options);
    }

    /**
     * Get
     *
     * @param mixed $fieldValue
     * @param CustomLink $link
     * @param array $options
     * @return mixed
     */
    public function get($fieldValue, CustomLink $link, array $options = [])
    {
		$options = array_merge([
			'separator' => ' / ',
		], $options);
		$selector = $this->textToArray($link->custom_field->source);
		$checked = [];
		if (!empty($fieldValue)) {
			if (is_array($fieldValue)) {
				foreach($fieldValue as $check) {
					$checked[] = $this->arrayValue($check, $selector);
				}
			} else {
				$checked[] = $fieldValue;
			}
		}
		return implode($options['separator'], $checked);
    }

}
