<?php
class Digitalbits_Imageoptimiser_Block_Adminhtml_Imageoptimiser extends Mage_Adminhtml_Block_Widget_Grid_Container {
    public function __construct() {
        $this->_controller     = 'adminhtml_imageoptimiser';
        $this->_blockGroup     = 'imageoptimiser';
        $this->_headerText     = Mage::helper('imageoptimiser')->__('Items Manager. These files are not in database.');
        $this->_addButtonLabel = Mage::helper('imageoptimiser')->__('Refresh');
        parent::__construct();

    }
}