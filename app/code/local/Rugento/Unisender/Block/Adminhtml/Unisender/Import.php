<?php
/**
 * @author RUGENTO
 *
 */
class Rugento_Unisender_Block_Adminhtml_Unisender_Import extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'unisender';
        $this->_controller = 'adminhtml_unisender';
        $this->_mode = 'import';

        $this->removeButton('save');
        $this->removeButton('reset');
    }

    /* (non-PHPdoc)
     * @see Mage_Adminhtml_Block_Widget_Container::getHeaderText()
     */
    public function getHeaderText()
    {
        return Mage::helper('unisender')->__('Mailing Lists UniSender');
    }
}