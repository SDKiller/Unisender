<?php
/**
 * @author RUGENTO
 *
 */
class Rugento_Unisender_Block_Adminhtml_Unisender_Import_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /* (non-PHPdoc)
     * @see Mage_Adminhtml_Block_Widget_Form::_prepareForm()
     */
    protected function _prepareForm ()
    {
        $form = new Varien_Data_Form(
                    array('id' => 'import_form',
                          'action' => $this->getUrl('*/*/import'),
                          'method' => 'post',
                          'enctype' => 'multipart/form-data',
                          'name' => 'import_form')
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}