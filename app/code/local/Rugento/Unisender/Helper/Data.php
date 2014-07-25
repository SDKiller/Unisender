<?php
/**
 * @author RUGENTO
 *
 */
class Rugento_Unisender_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @param unknown $phone
     * @param unknown $sender
     * @param unknown $text
     */
    public function sendSms($phone, $sender, $text)
    {
        return Mage::getModel('unisender/api')->sendSms($phone, $sender, $text);
    }

    /**
     * Приведение номера телефона к нужному виду. Только РОССИЯ 11 цифр!
     * @param unknown_type $phone
     */
    public function normalizePhone($phone)
    {
        if(Mage::getSingleton('core/locale')->getLocaleCode() == 'RU') {
            static $filter = null;
            if (is_null($filter)) {
                $filter = new Zend_Filter_Digits();
            }

            $num = $filter->filter($phone);

            if (strlen($num) == 11) {
                return '7'.substr($num, 1);
            }
            elseif (strlen($num) == 10) {
                return '7'.$num;
            }
            return '';
        }
        return $phone ;
    }
}