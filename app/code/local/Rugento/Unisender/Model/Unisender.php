<?php
/**
 * Copyright (c) <2012>, <Rugento.ru>
 * ЭТА ПРОГРАММА ПРЕДОСТАВЛЕНА ВЛАДЕЛЬЦАМИ АВТОРСКИХ ПРАВ И/ИЛИ ДРУГИМИ
 * СТОРОНАМИ "КАК ОНА ЕСТЬ" БЕЗ КАКОГО-ЛИБО ВИДА ГАРАНТИЙ, ВЫРАЖЕННЫХ ЯВНО
 * ИЛИ ПОДРАЗУМЕВАЕМЫХ, ВКЛЮЧАЯ, НО НЕ ОГРАНИЧИВАЯСЬ ИМИ, ПОДРАЗУМЕВАЕМЫЕ
 * ГАРАНТИИ КОММЕРЧЕСКОЙ ЦЕННОСТИ И ПРИГОДНОСТИ ДЛЯ КОНКРЕТНОЙ ЦЕЛИ. НИ В
 * КОЕМ СЛУЧАЕ, ЕСЛИ НЕ ТРЕБУЕТСЯ СООТВЕТСТВУЮЩИМ ЗАКОНОМ, ИЛИ НЕ УСТАНОВЛЕНО
 * В УСТНОЙ ФОРМЕ, НИ ОДИН ВЛАДЕЛЕЦ АВТОРСКИХ ПРАВ И НИ ОДНО  ДРУГОЕ ЛИЦО,
 * КОТОРОЕ МОЖЕТ ИЗМЕНЯТЬ И/ИЛИ ПОВТОРНО РАСПРОСТРАНЯТЬ ПРОГРАММУ, КАК БЫЛО
 * СКАЗАНО ВЫШЕ, НЕ НЕСЁТ ОТВЕТСТВЕННОСТИ, ВКЛЮЧАЯ ЛЮБЫЕ ОБЩИЕ, СЛУЧАЙНЫЕ,
 * СПЕЦИАЛЬНЫЕ ИЛИ ПОСЛЕДОВАВШИЕ УБЫТКИ, ВСЛЕДСТВИЕ ИСПОЛЬЗОВАНИЯ ИЛИ
 * НЕВОЗМОЖНОСТИ ИСПОЛЬЗОВАНИЯ ПРОГРАММЫ (ВКЛЮЧАЯ, НО НЕ ОГРАНИЧИВАЯСЬ
 * ПОТЕРЕЙ ДАННЫХ, ИЛИ ДАННЫМИ, СТАВШИМИ НЕПРАВИЛЬНЫМИ, ИЛИ ПОТЕРЯМИ
 * ПРИНЕСЕННЫМИ ИЗ-ЗА ВАС ИЛИ ТРЕТЬИХ ЛИЦ, ИЛИ ОТКАЗОМ ПРОГРАММЫ РАБОТАТЬ
 * СОВМЕСТНО С ДРУГИМИ ПРОГРАММАМИ), ДАЖЕ ЕСЛИ ТАКОЙ ВЛАДЕЛЕЦ ИЛИ ДРУГОЕ
 * ЛИЦО БЫЛИ ИЗВЕЩЕНЫ О ВОЗМОЖНОСТИ ТАКИХ УБЫТКОВ.
 */
class Rugento_Unisender_Model_Unisender extends Mage_Core_Model_Abstract
{
    const LIMIT = 400; //лимит импорта за шаг, макс. 500
    protected $_list_ids;
    
    public function importAllContacts($activate = false)
    {
        $count = 0;
        $collectionSize = $this->_getCollection()->getSize();
        $totalPages = ceil( $collectionSize / self::LIMIT );
        
        for ($step = $totalPages; $step > 0; $step--)
        {
            $collection = $this->_getCollection()->setPageSize(self::LIMIT)->setCurPage($step)->load();            
            $data = $this->_prepareForImport($collection);            
            $result = Mage::getSingleton('unisender/api')->importContacts($data);            
            $count = $count + (int) $result['result']['total'];
        }
        
        if($activate)// и активировать
        {
            try {
                Mage::getSingleton('unisender/api')->activateContacts($this->getListIds());
            } catch (Exception $e)
            {
                Mage::logException($e);
            }
        }        
        return $count;
    }
    
    protected function _prepareForImport($collection)
    {
        $data = array();
        
        foreach ($collection as $key => $item)
        {      
            $num_array = array();            
            $item_data = $this->_prepareCustomer($item);            
            ksort($item_data);
            
            foreach ($item_data as $value)
            {
                $num_array[] = $value;
            }            
            $data[$key] = $num_array;
        }        
        return $data;
    }
    
    protected function _prepareCustomer($item)
    {
        $item_data = array();
        $item_data['email'] = $item->getEmail();
        
        if($item->getCustomerId() && $item->isSubscribed())
        {
            $item_data['email_status'] = 'active'; //пользователь и подписан
            $item_data['phone_status'] = 'active';
        }
        elseif($item->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED)
        {
            $item_data['email_status'] = 'unsubscribed'; //отписан
            $item_data['phone_status'] = 'unsubscribed';
        }
        elseif($item->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE)
        {
            $item_data['email_status'] = 'inactive'; //не активен
            $item_data['phone_status'] = 'inactive';
        }
        else
        {
            $item_data['email_status'] = 'active'; 
            $item_data['phone_status'] = 'active';
        }
        
        $item_data['email_list_ids'] = $this->getListIds();         
        $item_data['phone'] = $this->_normalizePhone($item->getSms());        
        $item_data['firstname'] = $item->getCustomerFirstname();
        $item_data['lastname'] = $item->getCustomerLastname();
        $item_data['tags'] = $this->getTags($item);        
        return $item_data;
    }
    
    public function subscribeCustomerNewsletter($item)
    {
        $list_ids = array();
        $list = Mage::getSingleton('unisender/api')->getLists();
        
        foreach ($list['result'] as $key => $value)
        {
            $list_ids[] = $value['id'];
        }
        
        Mage::getSingleton('unisender/api')->subscribe($list_ids, $this->_prepareCustomer($item), $this->getTags($item), Mage::helper('core/http')->getRemoteAddr(), $this->getOptin($item));
        return $this;
    }
    
    public function unsubscribeCustomerNewsletter($item)
    {
        Mage::getSingleton('unisender/api')->unsubscribe('email', $item->getEmail());
        return $this;
    }
    
    public function getTags($item)
    {
        $store = Mage::app()->getStore($item->getStoreId())->getName();
        $website = Mage::app()->getStore($item->getStoreId())->getWebsite()->getName();
        $item->getCustomerId() ? $tag = Mage::helper('unisender')->__('Customer') : $tag = Mage::helper('unisender')->__('Guest');
        return $website.','.$store.','.$tag;
    }
    
    public function getOptin($item)
    {
        $optin_mode = Mage::getStoreConfig('newsletter/unisender/mode');        
        if($optin_mode == '0' || ($optin_mode == '1' && $item->getCustomerId()))
        {
            return 1;
        }
        return 0;
    }    
    
    public function getListIds()
    {
        if(is_array($this->_list_ids))
        {
            return implode(',', $this->_list_ids);
        }
        return $this->_list_ids;
    }
    
    public function setListIds($list_ids)
    {
        $this->_list_ids = $list_ids;
        return $this;
    }
    
    /**
     * Приведение номера телефона к нужному виду. Только РОССИЯ 11 цифр!
     * @param unknown_type $phone
     */
    protected function _normalizePhone ($phone)
    {
        if(Mage::getSingleton('core/locale')->getLocaleCode() == 'RU')
        {
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
    
    protected function _getCollection()
    {
        $collection = Mage::getModel('newsletter/subscriber')->getCollection();
        /* @var $collection Mage_Newsletter_Model_Mysql4_Subscriber_Collection */
        $collection
        ->showCustomerInfo(true)
        ->addSubscriberTypeField()
        ->showStoreInfo();
        return $collection ;
    }
}