<?php
namespace BaserCore\Model\Validation;

use Cake\Validation\Validation;

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
