<?php
/**
 * @author RUGENTO
 *
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
     * @param string|array $listIds
     * @param array $fields
     * @param string $tags
     * @param string $requestIp
     * @param int|string $doubleOptin
     * @param int|string $overwrite
     * @throws Exception
     * @return array
     * @tutorial http://www.unisender.com/ru/help/api/subscribe.html
     */
    public function subscribe($listIds, array $fields, $tags = null, $requestIp = null, $doubleOptin = 0, $overwrite = 0)
    {
        $data = array();

        if(is_array($listIds)) {
            $data['list_ids'] = implode(',', $listIds);
        } elseif(is_string($listIds) || is_int($listIds)) {
            $data['list_ids'] = $listIds;
        } else {
            throw new Exception('list_ids not valid');
        }

        if(is_array($tags)) {
            $data['tags'] = implode(',', $tags);
        } elseif(is_string($tags)) {
            $data['tags'] = $tags;
        }

        $data['fields']       = $fields;
        $data['double_optin'] = $doubleOptin;
        $data['overwrite']    = $overwrite;

        if($requestIp && Mage::helper('core/http')->validateIpAddr($requestIp)) {
            $data['request_ip'] = $requestIp;
        }

        return $this->_getRest('subscribe', $data);
    }

    /**
     * Метод исключает e-mail или телефон подписчика из одного или нескольких списков.
     * @param string $contact
     * @param string $contactType
     * @param string|array $listIds
     * @return array
     * @tutorial http://www.unisender.com/ru/help/api/exclude.html
     */
    public function exclude($contact, $contactType = 'email', $listIds = null)
    {
        $data = array();

        if(is_array($listIds)) {
            $data['list_ids'] = implode(',', $listIds);
        } elseif(is_string($listIds) || is_int($listIds)) {
            $data['list_ids'] = $listIds;
        }

        $data['contact']      = $contact;
        $data['contact_type'] = $contactType;

        return $this->_getRest('exclude', $data);
    }

    /**
     * Метод отписывает e-mail или телефон подписчика от одного или нескольких списков.
     * @param string $contactType
     * @param string $contact
     * @param string|array $listIds
     * @return array
     * @tutorial http://www.unisender.com/ru/help/api/unsubscribe.html
     */
    public function unsubscribe($contactType, $contact, $listIds = null)
    {
        $data                 = array();
        $data['contact_type'] = $contactType;
        $data['contact']      = $contact;

        if(is_array($listIds)) {
            $data['list_ids'] = implode(',', $listIds);
        } elseif(is_string($listIds) || is_int($listIds)) {
            $data['list_ids'] = $listIds;
        }
        return $this->_getRest('unsubscribe', $data);
    }

    /**
     * Метод активации контактов. Прежде, чем посылать сообщение какому-либо контакту, он должен в статусе «активен».
     * @param string|array $listIds
     * @param string $contacts
     * @param string $contactType
     * @return array
     * @tutorial http://www.unisender.com/ru/help/api/activateContacts.html
     */
    public function activateContacts($listIds = null, $contacts = null, $contactType = 'email')
    {
        $data                           = array();
        $data['contact_type']           = $contactType;
        if($contacts) $data['contacts'] = $contacts;

        if(is_array($listIds)) {
            $data['list_ids'] = implode(',', $listIds);
        } elseif(is_string($listIds) || is_int($listIds)) {
            $data['list_ids'] = $listIds;
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

        if(!count($field)) {
            $field = array('email','email_status','email_list_ids','phone','phone_status','firstname','lastname','tags');
        }

        sort($field);
        $importData['field_names']  = $field;
        $importData['data']         = $data;

        return $this->_getRest('importContacts', $importData);
    }

    /**
     * Экспорт данных подписчиков из UniSender.
     * @param string $offset
     * @todo $list_id
     * @tutorial http://www.unisender.com/ru/help/api/exportContacts.html
     */
    public function exportContacts($offset, $listId = null)
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
        $data           = array();
        $data['phone']  = Mage::helper('unisender')->normalizePhone($phone);
        $data['sender'] = $sender;
        $data['text']   = $text;

        return $this->_getRest('sendSms', $data);
    }

    /**
     * Возвращает строку — статус отправки SMS-сообщения методом sendSms.
     * @param string $smsId
     * @tutorial http://www.unisender.com/ru/help/api/checkSms.html
     */
    public function checkSms($smsId)
    {
        return $this->_getRest('checkSms', array('sms_id' => $smsId));
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
        $data['format']  = 'json';

        $httpclient = new Zend_Http_Client();
        $httpclient->setUri(self::UNISENDER_API_URL.$action);
        $httpclient->setParameterPost($data);
        $httpclient->setConfig(array('timeout' => 30));
        $httpclient->setMethod(Zend_Http_Client::POST);
        $httpclient->setHeaders('accept-encoding', 'Accept-encoding: identity');
        $response = Mage::helper('core')->jsonDecode($httpclient->request()->getBody());

        if(!is_array($response) || !array_key_exists('result', $response)) {
            throw new Exception('Unknown response status');
        }

        if(array_key_exists('error', $response)) {
            $e = new Exception($response['error']);
            Mage::logException($e);
            throw $e;
        }
        return $response ;
    }
}