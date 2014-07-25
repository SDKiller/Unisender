<?php
/**
 * @author RUGENTO
 *
 */
class Rugento_Unisender_Block_Adminhtml_Unisender_Import_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     *
     */
    public function __construct()
    {
      parent::__construct();
      $this->setId('unisender_tabs');
      $this->setDestElementId('import_form');
      $this->setTitle(Mage::helper('unisender')->__('Settings'));
    }

    /* (non-PHPdoc)
     * @see Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml()
     */
    protected function _beforeToHtml()
    {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('unisender')->__('Mailing Lists'),
          'title'     => Mage::helper('unisender')->__('Mailing Lists'),
          'content'   => $this->getLayout()->createBlock('unisender/adminhtml_unisender_import_tab_form')->toHtml(),
      ));
      return parent::_beforeToHtml();
    }
}