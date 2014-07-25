<?php
/**
 * @author RUGENTO
 *
 */
class Rugento_Unisender_Model_Newsletter_Subscriber extends Mage_Newsletter_Model_Subscriber
{
    /* (non-PHPdoc)
     * @see Mage_Newsletter_Model_Subscriber::subscribe($email)
     */
    public function subscribe($email)
    {
        parent::subscribe($email);

        if($this->getActive()) {
            if($this->isSubscribed()) {
                Mage::getSingleton('unisender/unisender')->subscribeCustomerNewsletter($this);
            } else {
                Mage::getSingleton('unisender/unisender')->unsubscribeCustomerNewsletter($this);
            }
        }
        return $this->getStatus();
    }

    /* (non-PHPdoc)
     * @see Mage_Newsletter_Model_Subscriber::subscribeCustomer($customer)
     */
    public function subscribeCustomer($customer)
    {
        parent::subscribeCustomer($customer);

        if($this->getActive()) {
            if($this->isSubscribed()) {
                Mage::getSingleton('unisender/unisender')->subscribeCustomerNewsletter($this);
            } else {
                Mage::getSingleton('unisender/unisender')->unsubscribeCustomerNewsletter($this);
            }
        }
        return $this ;
    }

    /* (non-PHPdoc)
     * @see Mage_Newsletter_Model_Subscriber::unsubscribe()
     */
    public function unsubscribe()
    {
        parent::unsubscribe();

        if($this->getActive()) {
            Mage::getSingleton('unisender/unisender')->unsubscribeCustomerNewsletter($this);
        }
        return $this ;
    }

    /**
     * Блокируем системные вызовы отправки писем
     * @see Mage_Newsletter_Model_Subscriber::sendUnsubscriptionEmail()
     */
    public function sendUnsubscriptionEmail()
    {
        if(!$this->getActive()) {
            parent::sendUnsubscriptionEmail();
        }
        return $this ;
    }

    /* (non-PHPdoc)
     * @see Mage_Newsletter_Model_Subscriber::sendConfirmationSuccessEmail()
     */
    public function sendConfirmationSuccessEmail()
    {
        if(!$this->getActive()) {
            parent::sendConfirmationSuccessEmail();
        }
        return $this ;
    }

    /* (non-PHPdoc)
     * @see Mage_Newsletter_Model_Subscriber::sendConfirmationRequestEmail()
     */
    public function sendConfirmationRequestEmail()
    {
        if(!$this->getActive()) {
            parent::sendConfirmationRequestEmail();
        }
        return $this ;
    }

    /**
     * @return Ambigous <mixed, string, NULL, multitype:, multitype:Ambigous <string, multitype:, NULL> >
     */
    protected function getActive()
    {
        return Mage::getStoreConfig('newsletter/unisender/active');
    }
}