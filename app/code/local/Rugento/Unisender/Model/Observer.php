<?php
/**
 * @author RUGENTO
 *
 */
class Rugento_Unisender_Model_Observer
{
    /**
     * @param unknown $observer
     * @return Rugento_Unisender_Model_Observer
     */
    public function excludeCustomer($observer)
    {
        Mage::getSingleton('unisender/api')->exclude($observer->getEvent()->getCustomer()->getEmail());
        return $this ;
    }
}