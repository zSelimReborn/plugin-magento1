<?php
class Trustpilot_Reviews_Block_Adminhtml_System_Config_Form_Admin
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    const CODE_TEMPLATE = 'trustpilot/system/config/admin.phtml';

    protected $_helper;
    protected $_integrationAppUrl;
    protected $_pastOrders;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate(static::CODE_TEMPLATE);
        $this->_helper     = Mage::helper('trustpilot/Data');
        $this->_pastOrders = Mage::helper('trustpilot/PastOrders');
        $this->_integrationAppUrl = Trustpilot_Reviews_Model_Config::TRUSTPILOT_INTEGRATION_APP_URL;
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    public function getIntegrationAppUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";
        $domainName = $protocol . $this->_integrationAppUrl;
        return $domainName;
    }

    public function getCustomTrustBoxes()
    {
        $customTrustboxes = $this->_helper->getConfig('custom_trustboxes');
        if ($customTrustboxes) {
            return $customTrustboxes;
        }
        return "{}";
    }

    public function getStartingUrl()
    {
        return $this->_helper->getPageUrl('trustpilot_trustbox_homepage');
    }

    public function getSettings() {
        return base64_encode($this->_helper->getConfig('master_settings_field'));
    }

    public function getPastOrdersInfo() {
            $info = $this->_pastOrders->getPastOrdersInfo($this->getWebsiteId(), $this->getStoreId());
            $info['basis'] = 'plugin';
            return json_encode($info);
    }

    public function getConfigurationScopeTree() {
        return base64_encode(json_encode($this->_helper->getConfigurationScopeTree()));
    }

    public function getProductIdentificationOptions()
    {
        return $this->_helper->getProductIdentificationOptions();
    }

    public function getWebsiteId() {
        return $this->_helper->getWebsiteId();
    }

    public function getStoreId() {
        return $this->_helper->getStoreId();
    }

    public function getSku() {
        try {
            $product = $this->_helper->getFirstProduct();
            if ($product) {
                $skuSelector = json_decode($this->_helper->getConfig('master_settings_field'))->skuSelector;
                if ($skuSelector == 'none') $skuSelector = 'sku';

                $productId = Trustpilot_Reviews_Model_Config::TRUSTPILOT_PRODUCT_ID_PREFIX . $product->getId();
                return $this->_helper->loadSelector($product, $skuSelector) . ', ' . $productId;
            }
        } catch (\Throwable $e) {
            $this->_helper->log('Error on getting product sku', $e, 'getSku');
            return '';
        } catch (\Exception $e) {
            $this->_helper->log('Error on getting product sku', $e, 'getSku');
            return '';
        }

    }

    public function getProductName() {
        try {
            return $this->_helper->getFirstProduct()->getName();
        } catch (Throwable $exception) {
            return '';
        } catch (Exception $exception) {
            return '';
        }
    }

    public function getPluginStatus() {
        return base64_encode($this->_helper->getConfig('plugin_status', $this->getWebsiteId(), $this->getStoreId()));
    }
}
