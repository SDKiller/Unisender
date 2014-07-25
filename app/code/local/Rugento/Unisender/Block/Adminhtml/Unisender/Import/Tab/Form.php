<?php
/**
 * @author RUGENTO
 *
 */
class Rugento_Unisender_Block_Adminhtml_Unisender_Import_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /* (non-PHPdoc)
     * @see Mage_Adminhtml_Block_Widget_Form::_prepareForm()
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('form_form', array('legend'=>Mage::helper('unisender')->__('Mailing Lists (subscribers will be imported in marked list)')));
        $list = $this->_getList();

        if($list && count($list)) {
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

    /**
     *
     */
    protected function _addNewButton()
    {
        $block = $this->getLayout()->getBlock('content.child0');
        $block->addButton('import', array(
            'label'     => Mage::helper('unisender')->__('Add List'),
            'onclick'   => 'import_form.submit()',
            'class'     => 'import',
        ), 1);
    }

    /**
     *
     */
    protected function _addImportButton()
    {
        $block = $this->getLayout()->getBlock('content.child0');
        $block->addButton('import', array(
        'label'     => Mage::helper('unisender')->__('Import List'),
        'onclick'   => 'import_form.submit()',
        'class'     => 'import',
        ), 1);
    }

    /**
     * @return unknown|boolean
     */
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