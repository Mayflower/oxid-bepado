<?php

use Bepado\SDK\ProductFromShop;
use Bepado\SDK\Struct\Order;

class oxidProductFromShop implements ProductFromShop
{
    /** @var  mf_sdk_converter */
    private $_oModuleDskConverter;

    /**
     * @param array $ids
     *
     * @return array|\Bepado\SDK\Struct\Product[]
     */
    public function getProducts(array $ids)
    {
        $sdkProducts = array();

        foreach ($ids as $id) {
            // load oxid article
            /**
             * @var oxarticle $oxProduct
             */
            $oxProduct = oxNew('oxarticle');
            $oxProduct->load($id);

            // @TODO: check if article is marked for bepado

            $sdkProducts[] = $this->_oModuleDskConverter->oxArticleToBepardo($oxProduct);
        }

        return $sdkProducts;
    }

    /**
     * @throws \BadMethodCallException
     *
     * @return string[]|void
     */
    public function getExportedProductIDs()
    {
        throw new \BadMethodCallException('Not needed in oxid module.');
    }

    /**
     * @param Order $order
     */
    public function reserve(Order $order)
    {
        // not using explicit reservation handling.
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    public function buy(Order $order)
    {
        // Hier muss die Bepado Order in eine Oxid Bestellung umgewandelt
        // werden. Rückgabewert ist die ID der Bestellung
        //
        $oxOrder = oxNew('oxorder'); // ??

        return $oxOrder->getOxID();
    }
}

