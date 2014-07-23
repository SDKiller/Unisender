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
class Rugento_Unisender_Model_Newsletter_Subscriber extends Mage_Newsletter_Model_Subscriber
{
    public function subscribe($email)
    {
        parent::subscribe($email);
        
        if($this->getActive())
        {
            if($this->isSubscribed())
            {
                Mage::getSingleton('unisender/unisender')->subscribeCustomerNewsletter($this);
            } else {
                Mage::getSingleton('unisender/unisender')->unsubscribeCustomerNewsletter($this);
            }
        }      
        return $this->getStatus();
    }
    
    public function subscribeCustomer($customer)
    {
        parent::subscribeCustomer($customer);
        
        if($this->getActive())
        {
            if($this->isSubscribed())
            {
                Mage::getSingleton('unisender/unisender')->subscribeCustomerNewsletter($this);
            } else {
                Mage::getSingleton('unisender/unisender')->unsubscribeCustomerNewsletter($this);
            }
        }
        return $this ;
    }
    
    public function unsubscribe()
    {
        parent::unsubscribe();
        
        if($this->getActive())
        {
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
        if(!$this->getActive())
        {
            parent::sendUnsubscriptionEmail();
        }
        return $this ;
    }
    
    public function sendConfirmationSuccessEmail()
    {
        if(!$this->getActive())
        {
            parent::sendConfirmationSuccessEmail();
        }
        return $this ;
    }
    
    public function sendConfirmationRequestEmail()
    {
        if(!$this->getActive())
        {
            parent::sendConfirmationRequestEmail();
        }   
        return $this ;
    }
    
    protected function getActive()
    {
        return Mage::getStoreConfig('newsletter/unisender/active');
    }
}