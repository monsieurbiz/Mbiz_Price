<?php
/**
 * This file is part of Mbiz_Price for Magento.
 *
 * @license All rights reserved
 * @author Jacques Bodin-Hullin <j.bodinhullin@monsieurbiz.com> <@jacquesbh>
 * @category Mbiz
 * @package Mbiz_Price
 * @copyright Copyright (c) 2015 Monsieur Biz (http://monsieurbiz.com)
 */

/**
 * Data Helper
 * @package Mbiz_Price
 */
class Mbiz_Price_Helper_Data extends Mage_Core_Helper_Abstract
{

// Monsieur Biz Tag NEW_CONST

    /**
     * Original configuration used in reset()
     * @var array
     */
    protected $_originalConfig = [];

    /**
     * Initial configuration used in rollback()
     * @var array
     */
    protected $_initialConfig = [];

// Monsieur Biz Tag NEW_VAR

    /**
     * Enable Weee for current execution script
     * @return self
     */
    public function enableWeee()
    {
        $this->_setStoreConfig(Mage_Weee_Helper_Data::XML_PATH_FPT_ENABLED, 1);
        return $this;
    }

    /**
     * Disable Weee for current execution script
     * @return self
     */
    public function disableWeee()
    {
        $this->_setStoreConfig(Mage_Weee_Helper_Data::XML_PATH_FPT_ENABLED, 0);
        return $this;
    }

    /**
     * Show excluding tax prices only
     * @return self
     */
    public function showExcludingTaxPrices()
    {
        $this->_setStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE, 1);
        return $this;
    }

    /**
     * Show including tax prices only
     * @return self
     */
    public function showIncludingTaxPrices()
    {
        $this->_setStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE, 2);
        return $this;
    }

    /**
     * Show including and excluding prices
     * @return self
     */
    public function showBothPrices()
    {
        $this->_setStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE, 3);
        return $this;
    }

    /**
     * Rollback all settings to the initial configuration
     * @return self
     */
    public function rollback()
    {
        foreach ($this->_initialConfig as $configPath => $valuePerStore) {
            foreach ($valuePerStore as $storeId => $value) {
                Mage::app()->getStore($storeId)->setConfig($configPath, $value);
            }
        }
        $this->_initialConfig = [];
        return $this;
    }

    /**
     * Reset all settings to the original configuration
     * @return self
     */
    public function reset()
    {
        foreach ($this->_originalConfig as $configPath => $valuePerStore) {
            foreach ($valuePerStore as $storeId => $value) {
                Mage::app()->getStore($storeId)->setConfig($configPath, $value);
            }
        }
        // Reset initial config
        // Note: We don't touch to the original config because it's not necessary ;)
        $this->_initialConfig = [];
        return $this;
    }

    /**
     * "Save" the current configuration to avoid rollback.
     * <p>It's only for the current execution. Use reset()
     * to put the original configuration back.</p>
     * @return self
     */
    public function save()
    {
        // Without initial config, the rollback won't have any effect
        $this->_initialConfig = [];
        return $this;
    }

    /**
     * Set store config for current execution
     * @param $configPath string
     * @param $value mixed
     * @return self
     */
    protected function _setStoreConfig($configPath, $value, $store = null)
    {
        // Get store as object
        if (null === $store) {
            $store = Mage::app()->getStore();
        } elseif (is_string($store) || is_int($store)) {
            $store = Mage::app()->getStore($store);
        }

        // Store old configuration
        if (!isset($this->_initialConfig[$configPath])) {
            $this->_initialConfig[$configPath] = [];
        }
        if (!isset($this->_initialConfig[$configPath][$store->getId()])) {
            $this->_initialConfig[$configPath][$store->getId()] = Mage::getStoreConfig($configPath, $store);
        }

        // Store old configuration
        if (!isset($this->_originalConfig[$configPath])) {
            $this->_originalConfig[$configPath] = [];
        }
        if (!isset($this->_originalConfig[$configPath][$store->getId()])) {
            $this->_originalConfig[$configPath][$store->getId()] = Mage::getStoreConfig($configPath, $store);
        }

        // Change configuration of the store for the current execution only
        $store->setConfig($configPath, $value);

        return $this;
    }

// Monsieur Biz Tag NEW_METHOD

}