<?php
/**
 * @author RUGENTO
 *
 */
class Rugento_Unisender_Adminhtml_ListsController extends Mage_Adminhtml_Controller_Action
{
    /**
     *
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('unisender/adminhtml_unisender_import'))
             ->_addLeft($this->getLayout()->createBlock('unisender/adminhtml_unisender_import_tabs'))
            ;
        $this->renderLayout();
    }

    /**
     * @return Rugento_Unisender_Adminhtml_ListsController
     */
    public function importAction()
    {
        try {
            if($this->getRequest()->getParam('new_list', false))
            {
                Mage::getModel('unisender/api')->createList($this->getRequest()->getParam('new_list'));
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('unisender')->__('List was created successfully!'));
            } elseif($this->getRequest()->getParam('list', false))
            {
                $listModel = Mage::getModel('unisender/unisender')->setListIds($this->getRequest()->getParam('list'));
                $usersCount = $listModel->importAllContacts(true);
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('unisender')->__('Successfully imported %s users', $usersCount));
            }
            $this->_redirect('*/*/index');
        }catch(Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirectReferer();
        }
        return $this;
    }
}