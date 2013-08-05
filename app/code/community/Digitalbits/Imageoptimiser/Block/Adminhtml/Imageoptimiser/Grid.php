<?php

class Digitalbits_Imageoptimiser_Block_Adminhtml_Imageoptimiser_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('imageoptimiserGrid');
        $this->setDefaultSort('imageoptimiser_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);

    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('imageoptimiser/imageoptimiser')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('filename', array(
            'header'   => Mage::helper('imageoptimiser')->__('Filename'),
            'renderer' => 'Digitalbits_Imageoptimiser_Block_Adminhtml_Renderer_Image',
            'align'    => 'left',
            'index'    => 'filename'

        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('imageoptimiser')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('imageoptimiser')->__('delete'),
                        'url'     => array('base' => '*/*/delete'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            ));

        return parent::_prepareColumns();
    }


    protected function _prepareMassaction() {

        $this->setMassactionIdField('imageoptimiser_id');
        $this->getMassactionBlock()->setFormFieldName('imageoptimiser');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'   => Mage::helper('imageoptimiser')->__('Delete'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('imageoptimiser')->__('Are you sure?')
        ));
        return $this;
    }


}
