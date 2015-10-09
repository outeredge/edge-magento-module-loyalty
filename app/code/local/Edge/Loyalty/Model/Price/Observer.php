<?php

class Edge_Loyalty_Model_Price_Observer
{
    public function getFinalPrice(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $loyaltyType = $product->getLoyaltyType();
        if ($loyaltyType) {
            if ($product->getData($loyaltyType) !== $product->getLoyaltyPrice()) {
                $product->setData($loyaltyType, $product->getLoyaltyPrice());
            }
        }
    }

    public function catalogProductLoadAfter(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getData('no_loyalty_calculation')) {
            return;
        }

        $helper = Mage::helper('loyalty');
        if ($helper->isEligible()) {
            $helper->applyLoyaltyDiscount($product, 'special');
        }
    }

    public function catalogProductCollectionLoadAfter(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('loyalty');
        if ($helper->isEligible()) {
            foreach ($observer->getEvent()->getCollection() as $product) {
                $helper->applyLoyaltyDiscount($product, 'final');
            }
        }
    }
}