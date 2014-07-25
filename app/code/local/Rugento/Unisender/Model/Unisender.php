<?php
/**
 * @author RUGENTO
 *
 */
class Rugento_Unisender_Model_Unisender extends Mage_Core_Model_Abstract
{
    const LIMIT = 400; //лимит импорта за шаг, макс. 500
    protected $_list_ids;

    /**
     * @param string $activate
     * @return number
     */
    public function importAllContacts($activate = false)
    {
        $count          = 0;
        $collectionSize = $this->_getCollection()->getSize();
        $totalPages     = ceil( $collectionSize / self::LIMIT );

        for ($step = $totalPages; $step > 0; $step--) {
            $collection = $this->_getCollection()->setPageSize(self::LIMIT)->setCurPage($step)->load();
            $data   = $this->_prepareForImport($collection);
            $result = Mage::getSingleton('unisender/api')->importContacts($data);
            $count  = $count + (int) $result['result']['total'];
        }

        if($activate) {
            try {
                Mage::getSingleton('unisender/api')->activateContacts($this->getListIds());
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        return $count;
    }

    /**
     * @param unknown $collection
     * @return multitype:multitype:Ambigous <string, NULL, unknown_type>
     */
    protected function _prepareForImport($collection)
    {
        $data = array();

        foreach ($collection as $key => $item) {
            $numArray = array();
            $itemData = $this->_prepareCustomer($item);
            ksort($itemData);

            foreach ($itemData as $value) {
                $numArray[] = $value;
            }
            $data[$key] = $numArray;
        }
        return $data;
    }

    /**
     * @param unknown $item
     * @return multitype:string NULL Ambigous <string, unknown_type>
     */
    protected function _prepareCustomer($item)
    {
        $itemData          = array();
        $itemData['email'] = $item->getEmail();

        if($item->getCustomerId() && $item->isSubscribed()) {
            $itemData['email_status'] = 'active'; //пользователь и подписан
            $itemData['phone_status'] = 'active';
        }
        elseif($item->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED) {
            $itemData['email_status'] = 'unsubscribed'; //отписан
            $itemData['phone_status'] = 'unsubscribed';
        }
        elseif($item->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
            $itemData['email_status'] = 'inactive'; //не активен
            $itemData['phone_status'] = 'inactive';
        }
        else {
            $itemData['email_status'] = 'active';
            $itemData['phone_status'] = 'active';
        }

        $itemData['email_list_ids'] = $this->getListIds();
        $itemData['phone']          = Mage::helper('unisender')->normalizePhone($item->getSms());
        $itemData['firstname']      = $item->getCustomerFirstname();
        $itemData['lastname']       = $item->getCustomerLastname();
        $itemData['tags']           = $this->getTags($item);
        return $itemData;
    }

    /**
     * @param unknown $item
     * @return Rugento_Unisender_Model_Unisender
     */
    public function subscribeCustomerNewsletter($item)
    {
        $listIds = array();
        $list = Mage::getSingleton('unisender/api')->getLists();

        foreach ($list['result'] as $key => $value) {
            $listIds[] = $value['id'];
        }

        Mage::getSingleton('unisender/api')->subscribe($listIds, $this->_prepareCustomer($item), $this->getTags($item), Mage::helper('core/http')->getRemoteAddr(), $this->getOptin($item));
        return $this;
    }

    /**
     * @param unknown $item
     * @return Rugento_Unisender_Model_Unisender
     */
    public function unsubscribeCustomerNewsletter($item)
    {
        Mage::getSingleton('unisender/api')->unsubscribe('email', $item->getEmail());
        return $this;
    }

    /**
     * @param unknown $item
     * @return string
     */
    public function getTags($item)
    {
        $store   = Mage::app()->getStore($item->getStoreId())->getName();
        $website = Mage::app()->getStore($item->getStoreId())->getWebsite()->getName();
        $item->getCustomerId() ? $tag = Mage::helper('unisender')->__('Customer') : $tag = Mage::helper('unisender')->__('Guest');
        return $website.','.$store.','.$tag;
    }

    /**
     * @param unknown $item
     * @return number
     */
    public function getOptin($item)
    {
        $optinMode = Mage::getStoreConfig('newsletter/unisender/mode');
        if($optinMode == '0' || ($optinMode == '1' && $item->getCustomerId())) {
            return 1;
        }
        return 0;
    }

    /**
     * @return string
     */
    public function getListIds()
    {
        if(is_array($this->_list_ids)) {
            return implode(',', $this->_list_ids);
        }
        return $this->_list_ids;
    }

    /**
     * @param unknown $list_ids
     * @return Rugento_Unisender_Model_Unisender
     */
    public function setListIds($listIds)
    {
        $this->_list_ids = $listIds;
        return $this;
    }

    /**
     * @return Mage_Newsletter_Model_Mysql4_Subscriber_Collection
     */
    protected function _getCollection()
    {
        $collection = Mage::getModel('newsletter/subscriber')->getCollection();
        /* @var $collection Mage_Newsletter_Model_Mysql4_Subscriber_Collection */
        $collection
            ->showCustomerInfo(true)
            ->addSubscriberTypeField()
            ->showStoreInfo()
        ;
        return $collection ;
    }
}