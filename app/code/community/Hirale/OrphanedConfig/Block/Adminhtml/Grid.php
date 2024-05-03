<?php
class Hirale_OrphanedConfig_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor for the class.
     *
     * Initializes the parent constructor and sets the ID, default sort, default direction, and saves parameters in session.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('orphanedConfigGrid');
        $this->setDefaultSort('config_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepares the collection for the grid.
     *
     */
    protected function _prepareCollection()
    {
        $configSections = Mage::getSingleton('adminhtml/config')->getSections();
        $configFields = $configSections->xpath('//sections/*/groups/*/fields/*');

        foreach ($configFields as $configField) {
            $fieldName = $configField->getName();
            $groupName = $configField->getParent()->getParent()->getName();
            $sectionName = $configField->getParent()->getParent()->getParent()->getParent()->getName();
            $paths[] = implode('/', [$sectionName, $groupName, $fieldName]);
        }

        $paths = array_merge($paths, Mage::helper('orphanedconfig')->configurationsToExclude);
        $configCollection = Mage::getResourceModel('core/config_data_collection')
            ->addFieldToFilter('path', array('nin' => $paths))
            ->addFieldToFilter('path', array('nlike' => 'advanced/modules_disable_output/%'))
            ->addFieldToFilter('path', array('nlike' => 'payment/pay%')) // Mage Paypal
            ->addFieldToFilter('path', array('nlike' => 'payment/verisign%')) // Mage Paypal
            ->addFieldToFilter('path', array('nlike' => 'paypal/%')) // Mage Paypal
            ->addFieldToFilter('path', array('nlike' => 'design/watermark%')); // Mage Catalog


        $this->setCollection($configCollection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'config_id',
            array(
                'header' => Mage::helper('orphanedconfig')->__('Config ID'),
                'index' => 'config_id',
                'type' => 'number',
                'sortable' => false,
                'width' => '50px',
            )
        );

        $this->addColumn(
            'scope',
            array(
                'header' => Mage::helper('orphanedconfig')->__('Config Scope'),
                'index' => 'scope',
                'type' => 'text',
                'sortable' => false,
                'width' => '50px',
            )
        );

        $this->addColumn(
            'path',
            array(
                'header' => Mage::helper('orphanedconfig')->__('Config Path'),
                'index' => 'path',
                'type' => 'text',
                'sortable' => false,
                'width' => '200px',
            )
        );

        $this->addColumn(
            'value',
            array(
                'header' => Mage::helper('orphanedconfig')->__('Config Value'),
                'index' => 'value',
                'type' => 'text',
                'sortable' => false,
                'width' => '400px',
            )
        );


        $this->addColumn(
            'updated_at',
            array(
                'header' => Mage::helper('orphanedconfig')->__('Updated At'),
                'index' => 'updated_at',
                'type' => 'datetime',
                'width' => '200px',
                'sortable' => false,
            )
        );

        $this->addColumn(
            'action',
            array(
                'header' => Mage::helper('orphanedconfig')->__('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('orphanedconfig')->__('Delete'),
                        'url' => array('base' => '*/*/delete'),
                        'field' => 'config_id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'config_id',
                'width' => '100px',
            )
        );

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('config_id');
        $this->getMassactionBlock()->setFormFieldName('config_ids');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => Mage::helper('orphanedconfig')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('orphanedconfig')->__('Are you sure you want to delete the selected configs?')
            )
        );

        return $this;
    }

}
