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
class Rugento_Unisender_Block_Adminhtml_Unisender_Import_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('form_form', array('legend'=>Mage::helper('unisender')->__('Mailing Lists (subscribers will be imported in marked list)')));        
        $list = $this->_getList();
        
        if($list && count($list))
        {
            foreach ($list as $id => $data) 
            {
                $fieldset->addField('lists'.$id, 'checkbox', array(
                    'label'     => $data['title'],
                    'name'      => 'list['.$id.']',
                    'value' => $data['id'],
                ));
            }
                       
            $this->_addImportButton();
            
        } else {
            $fieldset->addField('text', 'label', array(
                'label' => Mage::helper('unisender')->__('No lists. Create?'),
                'bold' => true,
            ));
            
            $fieldset->addField('list_name', 'text', array(
                'label' => Mage::helper('unisender')->__('Name List'),
                'name' => 'new_list',
                'required' => true,
                'class'     => 'required-entry',
            ));
            
           $this->_addNewButton();
        }                
        return parent::_prepareForm();
    }   
    
    protected function _addNewButton()
    {
        $block = $this->getLayout()->getBlock('content.child0');
        $block->addButton('import', array(
            'label'     => Mage::helper('unisender')->__('Add List'),
            'onclick'   => 'import_form.submit()',
            'class'     => 'import',
        ), 1);
    }
    
    protected function _addImportButton()
    {
        $block = $this->getLayout()->getBlock('content.child0');
        $block->addButton('import', array(
        'label'     => Mage::helper('unisender')->__('Import List'),
        'onclick'   => 'import_form.submit()',
        'class'     => 'import',
        ), 1);
    }
    
    protected function _getList()
    {
        try {
            $response = Mage::getModel('unisender/api')->getLists();
            return $response['result'];
        } catch (Exception $e)
        {
            Mage::logException($e);
//          Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        return false;
    }
}