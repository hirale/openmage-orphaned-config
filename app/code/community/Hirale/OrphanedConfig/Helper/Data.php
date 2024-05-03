<?php

class Hirale_OrphanedConfig_Helper_Data extends Mage_Core_Helper_Abstract
{
    public $configurationsToExclude = [
        'catalog/category/root_id', // Mage Catalog
        'currency/webservicex/timeout', // Mage Directory
    ];

}
