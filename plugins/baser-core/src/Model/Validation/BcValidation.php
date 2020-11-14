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
namespace BaserCore\Model\Validation;

use Cake\Validation\Validation;

/**
 * Class BcValidation
 * @package BaserCore\Model\Validation
 */
class BcValidation extends Validation {

    /**
     * 英数チェックプラス
     *
     * ハイフンアンダースコアを許容
     *
     * @param array $check チェック対象文字列
     * @param array $options 他に許容する文字列
     * @return boolean
     */
	public function alphaNumericPlus($value, $context = []) {
		if (!$value) {
			return true;
		}
		if($context) {
            if (is_array($context)) {
                if(array_key_exists('data', $context)) {
                    $context = [];
                }
            } else {
                $context = [$context];
            }
            $context = preg_quote(implode('', $context), '/');
        }
		if (preg_match("/^[a-zA-Z0-9\-_" . $context . "]+$/", $value)) {
			return true;
		} else {
			return false;
		}
	}

}
