<?php
class Digitalbits_Imageoptimiser_Block_Imageoptimiser extends Mage_Core_Block_Template {
    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getImageoptimiser() {
        if (!$this->hasData('imageoptimiser')) {
            $this->setData('imageoptimiser', Mage::registry('imageoptimiser'));
        }
        return $this->getData('imageoptimiser');

    }
}