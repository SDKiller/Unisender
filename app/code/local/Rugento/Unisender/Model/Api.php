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
class Rugento_Unisender_Model_Api extends Mage_Core_Model_Abstract
{
    const UNISENDER_API_URL = 'https://api.unisender.com/ru/api/';
    
    /**
     * Метод для получения перечня всех имеющихся списков рассылок. 
     * @return array
     * @tutorial http://www.unisender.com/ru/help/api/getLists.html
     */
    public function getLists()
    {
        return $this->_getRest('getLists');
    }
    
    /**
     * Метод для создания нового списка рассылки. 
     * @param string $listName
     * @return array
     * @tutorial http://www.unisender.com/ru/help/api/createList.html
     */
    public function createList($listName)
    {
        return $this->_getRest('createList', array('title' => (string) $listName));
    }
    
    /**
     * Этот метод добавляет контакты (e-mail адрес и/или мобильный телефон) подписчика в один или несколько списков, а также позволяет добавить/поменять значения дополнительных полей и меток.
     * @param string|array $list_ids
     * @param array $fields
     * @param string $tags
     * @param string $request_ip
     * @param int|string $double_optin
     * @param int|string $overwrite
     * @throws Exception
     * @return array
     * @tutorial http://www.unisender.com/ru/help/api/subscribe.html
     */
    public function subscribe($list_ids, array $fields, $tags = null, $request_ip = null, $double_optin = 0, $overwrite = 0)
    {
        $data = array();
        
        if(is_array($list_ids))
        {
            $data['list_ids'] = implode(',', $list_ids);
        } elseif(is_string($list_ids) || is_int($list_ids))
        {
            $data['list_ids'] = $list_ids;
        } else {
            throw new Exception('list_ids not valid');
        }
        
        if(is_array($tags))
        {
            $data['tags'] = implode(',', $tags);
        } elseif(is_string($tags))
        {
            $data['tags'] = $tags;
        } 
        
        $data['fields'] = $fields;
        $data['double_optin'] = $double_optin;
        $data['overwrite'] = $overwrite;
        
        if($request_ip && Mage::helper('core/http')->validateIpAddr($request_ip)) $data['request_ip'] = $request_ip;
        
        return $this->_getRest('subscribe', $data);
    }
    
    /**
     * Метод исключает e-mail или телефон подписчика из одного или нескольких списков.
     * @param string $contact
     * @param string $contact_type
     * @param string|array $list_ids
     * @return array
     * @tutorial http://www.unisender.com/ru/help/api/exclude.html
     */
    public function exclude($contact, $contact_type = 'email', $list_ids = null)
    {
        $data = array();
        
        if(is_array($list_ids))
        {
            $data['list_ids'] = implode(',', $list_ids);
        } elseif(is_string($list_ids) || is_int($list_ids))
        {
            $data['list_ids'] = $list_ids;
        }
        
        $data['contact'] = $contact;
        $data['contact_type'] = $contact_type;
        
        return $this->_getRest('exclude', $data);
    }
    
    /**
     * Метод отписывает e-mail или телефон подписчика от одного или нескольких списков.
     * @param string $contact_type
     * @param string $contact
     * @param string|array $list_ids
     * @return array
     * @tutorial http://www.unisender.com/ru/help/api/unsubscribe.html
     */
    public function unsubscribe($contact_type, $contact, $list_ids = null)
    {
        $data = array();
        $data['contact_type'] = $contact_type;
        $data['contact'] = $contact;
        
        if(is_array($list_ids))
        {
            $data['list_ids'] = implode(',', $list_ids);
        } elseif(is_string($list_ids) || is_int($list_ids))
        {
            $data['list_ids'] = $list_ids;
        }        
        return $this->_getRest('unsubscribe', $data);
    }
    
    /**
     * Метод активации контактов. Прежде, чем посылать сообщение какому-либо контакту, он должен в статусе «активен».
     * @param string|array $list_ids
     * @param string $contacts
     * @param string $contact_type
     * @return array
     * @tutorial http://www.unisender.com/ru/help/api/activateContacts.html
     */
    public function activateContacts($list_ids = null, $contacts = null, $contact_type = 'email')
    {
        $data = array();
        $data['contact_type'] = $contact_type;
        if($contacts) $data['contacts'] = $contacts;
        
        if(is_array($list_ids))
        {
            $data['list_ids'] = implode(',', $list_ids);
        } elseif(is_string($list_ids) || is_int($list_ids))
        {
            $data['list_ids'] = $list_ids;
        }        
        return $this->_getRest('activateContacts', $data);
    } 
    
    /**
     * Метод массового импорта подписчиков.
     * @param array $data
     * @param array $field sort key
     * @return array
     * @tutorial http://www.unisender.com/ru/help/api/importContacts.html
     */
    public function importContacts(array $data, array $field = array())
    {
        $importData = array();

        if(!count($field))
        {
            $field = array('email','email_status','email_list_ids','phone','phone_status','firstname','lastname','tags');
        }
        
        sort($field);
        $importData['field_names'] = $field;
        $importData['data'] = $data;
        
        return $this->_getRest('importContacts', $importData);
    }
    
    /**
     * Экспорт данных подписчиков из UniSender.
     * @param string $offset
     * @todo $list_id
     * @tutorial http://www.unisender.com/ru/help/api/exportContacts.html
     */
    public function exportContacts($offset, $list_id = null)
    {
        return $this->_getRest('exportContacts', array('offset' => $offset));
    }
    
    /**
     * Метод для простой отправки одного SMS-сообщения одному адресату. 
     * @param string $phone
     * @param string $sender
     * @param string $text
     * @return array
     * @tutorial http://www.unisender.com/ru/help/api/sendSms.html
     */
    public function sendSms($phone, $sender, $text)
    {
        $data = array();
        $data['phone'] = $this->_normalizePhone($phone);
        $data['sender'] = $sender;
        $data['text'] = $text;
        
        return $this->_getRest('sendSms', $data);
    }
    
    /**
     * Возвращает строку — статус отправки SMS-сообщения методом sendSms. 
     * @param string $sms_id
     * @tutorial http://www.unisender.com/ru/help/api/checkSms.html
     */
    public function checkSms($sms_id)
    {
        return $this->_getRest('checkSms', array('sms_id' => $sms_id));
    }
    
    /**
     * Приведение номера телефона к нужному виду. Только РОССИЯ 11 цифр!
     * @param string|int $phone
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
    
    /**
     * Запрос к API
     * @param string $action
     * @param array $data
     * @return array || bool
     */
    protected function _getRest($action, array $data = array())
    {
        $data['api_key'] = Mage::helper('core')->decrypt(Mage::getStoreConfig('newsletter/unisender/api_key'));
        $data['format'] = 'json';
        
        $httpclient = new Zend_Http_Client();
        $httpclient->setUri(self::UNISENDER_API_URL.$action);
        $httpclient->setParameterPost($data);
        $httpclient->setConfig(array('timeout' => 30));
        $httpclient->setMethod(Zend_Http_Client::POST);
        $httpclient->setHeaders('accept-encoding', 'Accept-encoding: identity');
        $response = Mage::helper('core')->jsonDecode($httpclient->request()->getBody());
        
        if(!is_array($response) || !array_key_exists('result', $response))
        {
            throw new Exception('Unknown response status');
        }
        
        if(array_key_exists('error', $response))
        {
            $e = new Exception($response['error']);
            Mage::logException($e);
            throw $e;
        }
        return $response ;
    }
}