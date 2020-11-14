<?php
namespace BaserCore\Model\Validation;

use Cake\Validation\Validation;

class BcValidation extends Validation {

    public static function duplicate($data) {
        return false;
    }

}
