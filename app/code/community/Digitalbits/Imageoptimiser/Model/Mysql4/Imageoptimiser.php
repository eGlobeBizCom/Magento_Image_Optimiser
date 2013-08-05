<?php

class Digitalbits_Imageoptimiser_Model_Mysql4_Imageoptimiser extends Mage_Core_Model_Mysql4_Abstract {
    public function _construct() {
        // Note that the imageoptimiser_id refers to the key field in your database table.
        $this->_init('imageoptimiser/imageoptimiser', 'imageoptimiser_id');
    }

}