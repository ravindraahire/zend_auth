<?php

class Application_Form_DuplicateRecord extends Zend_Validate_Db_NoRecordExists
{
    public function isValid($value, $context)
    {print_r($context);exit;
        $this->_setValue($value);

        if (!is_float($value)) {
            $this->_error(self::FLOAT);
            return false;
        }

        return true;
    }
}
