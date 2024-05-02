<?php
class Hirale_OrphanedConfig_Adminhtml_OrphanedConfigController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title("Orphaned Config Cleaner");
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('orphanedconfig/adminhtml_grid'));
        $this->renderLayout();
    }

    public function deleteAction()
    {
        $configId = $this->getRequest()->getParam('config_id');

        try {
            $config = Mage::getModel('core/config_data')->load($configId);
            $config->delete();

            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__('Config id %d was deleted', $configId)
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {
        $configIds = $this->getRequest()->getParam('config_ids');

        if (!is_array($configIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('orphanedconfig')->__('Please select item(s)'));
        } else {
            try {
                $collection = Mage::getModel('core/config_data')->getCollection()
                    ->addFieldToFilter('config_id', array('in' => $configIds));

                foreach ($collection as $config) {
                    $config->delete();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('orphanedconfig')->__(
                        'Total of %d record(s) have been deleted',
                        count($configIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }
}