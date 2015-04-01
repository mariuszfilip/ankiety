<?php
require_once 'Zend/Validate/Abstract.php';

/**
 * waliduje czy podany ciągjest numerem telefonu
 * (XX) XXX-XXX-XXX, XXX-XXX-XXX lub (XX) XXX-XX-XX.
 *	
 */
require_once 'Zend/Validate/Abstract.php';

class My_Validate_Phone extends Zend_Validate_Abstract
{
    const NOT_TELEFON = 'notTelefon - format((XX) XXX-XXX-XXX, XXX-XXX-XXX lub (XX) XXX-XX-XX)';

    protected $_messageTemplates = array(
       self::NOT_TELEFON    => "'%value%' nie jest numerem telefonu",
    );

    public function isValid($value,$isCountryPrefixRequired=false)
    {
        	$this->_setValue($value);
            $phone = preg_replace("/[^0-9\(\)]/","",$value);
            if(!preg_match("/[0-9]{3}/",$phone) && !preg_match("/\([0-9]{2}\)[0-9]{7}/",$phone) && !preg_match("/\([0-9]{2}\)[0-9]{9}/",$phone)){
                $this->_error(self::NOT_TELEFON);
                return false;
            }
             return true;
          
      
     
    }
}