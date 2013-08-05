<?php

class Digitalbits_Imageoptimiser_Model_Imageoptimiser extends Mage_Core_Model_Abstract {
    public function _construct() {
        parent::_construct();
        $this->_init('imageoptimiser/imageoptimiser');
    }
}