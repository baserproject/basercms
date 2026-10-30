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
#[\AllowDynamicProperties]
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
    public array $helpers = [
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
			// タイプ変更前の値などで文字列が保存されている場合も、選択肢のキーとして扱う
			if (!is_array($fieldValue)) $fieldValue = [$fieldValue];
			foreach($fieldValue as $check) {
				if (!is_scalar($check)) continue;
				$label = $this->arrayValue($check, $selector);
				// 選択肢にない値は表示しない
				if ($label === '') continue;
				$checked[] = $label;
			}
		}
		return implode($options['separator'], $checked);
    }

}
