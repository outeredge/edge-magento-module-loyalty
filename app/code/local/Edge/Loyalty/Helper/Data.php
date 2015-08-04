<?php

class Edge_Loyalty_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_eligible = null;
    protected $_discount = null;

    public function isEligible()
    {
        if (is_null($this->_eligible)) {
            if (!$this->getDiscount() || !Mage::getSingleton('customer/session')->isLoggedIn()) {
                $this->_eligible = false;
            } else {
                $this->_eligible = $this->_checkEligible();
            }
        }
        return $this->_eligible;
    }

    public function getDiscount()
    {
        if (is_null($this->_discount)) {
            $discountType = Mage::getStoreConfig('loyalty/discount/type');
            $discountAmount = Mage::getStoreConfig('loyalty/discount/amount');
            if (!$discountType || !$discountAmount) {
                $this->_discount = false;
            } else {
                $this->_discount =  array(
                    'type' => $discountType ? $discountType : 'by_percent',
                    'amount' => $discountAmount ? $discountAmount : 0
                );
            }
        }
        return $this->_discount;
    }

    public function applyLoyaltyDiscount($product, $priceType)
    {
        $discount = $this->getDiscount();
        $price = $product->getPrice();
        if ($product->getSpecialPrice()) {
            $price = $product->getSpecialPrice();
        }
        $rulePrice = Mage::getModel('catalogrule/rule')->calcProductPriceRule($product, $product->getPrice());
        if ($rulePrice && $rulePrice < $price) {
            $price = $rulePrice;
        }

        switch ($discount['type']) {
            case "by_percent":
                $loyaltyPrice = ($price / 100) * (100 - $discount['amount']);
                break;
            case "by_fixed":
                $loyaltyPrice = $price - $discount['amount'];
                break;
            case "to_percent":
                $loyaltyPrice = ($price / 100) * $discount['amount'];
                break;
            case "to_fixed":
                $loyaltyPrice = $discount['amount'];
                break;
        }

        $product->setData("{$priceType}_price", $loyaltyPrice);
        $product->setData('loyalty_type', "{$priceType}_price");
        $product->setData('loyalty_price', $loyaltyPrice);
    }

    public function buildLoyaltyQuery()
    {
        $orders = Mage::getResourceModel('sales/order_collection');

        $periodValue = Mage::getStoreConfig('loyalty/period/value');
        $periodType = Mage::getStoreConfig('loyalty/period/type');
        if ($periodType && $periodValue) {
            $date = strtotime("-{$periodValue} {$periodType}", time());
            $orders->addFieldToFilter('created_at', array('gteq' => date('Y-m-d H:i:s', $date)));
        }

        $ordersTotal = Mage::getStoreConfig('loyalty/orders/total');
        $ordersCount = Mage::getStoreConfig('loyalty/orders/count');
        if ($ordersTotal || $ordersCount) {
            $orders->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId());
            $orders->getSelect()->reset(Zend_Db_Select::COLUMNS);
        }
        if ($ordersTotal) {
            $orders->getSelect()->columns(array('total' => 'SUM(grand_total)'));
            $orders->getSelect()->having("total >= $ordersTotal");
        }
        if ($ordersCount) {
            $orders->getSelect()->columns(array('count' => 'COUNT(*)'));
            $orders->getSelect()->having("count >= $ordersCount");
        }

        return $orders;
    }

    protected function _checkEligible()
    {
        return (bool)$this->buildLoyaltyQuery()->count();
    }
}