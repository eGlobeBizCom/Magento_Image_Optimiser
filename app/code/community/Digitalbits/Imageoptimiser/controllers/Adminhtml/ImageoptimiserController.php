<?php

class Digitalbits_Imageoptimiser_Adminhtml_ImageoptimiserController extends Mage_Adminhtml_Controller_action {

    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('imageoptimiser/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction() {

        $this->_initAction()
            ->renderLayout();
    }

    public function newAction() {

        Mage::helper('imageoptimiser')->compareList();
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('imageoptimiser/imageoptimiser');
                $model->load($this->getRequest()->getParam('id'));
                unlink('media/catalog/product' . $model->getFilename());
                $model->setId($this->getRequest()->getParam('id'))->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $imageoptimiserIds = $this->getRequest()->getParam('imageoptimiser');
        if (!is_array($imageoptimiserIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                $model = Mage::getModel('imageoptimiser/imageoptimiser');
                foreach ($imageoptimiserIds as $imageoptimiserId) {
                    $model->load($imageoptimiserId);
                    unlink('media/catalog/product' . $model->getFilename());
                    $model->setId($imageoptimiserId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($imageoptimiserIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

}