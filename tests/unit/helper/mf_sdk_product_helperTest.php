<?php

use Bepado\SDK\Struct as Struct;
use Bepado\SDK\Struct\Message;
use Bepado\SDK\Struct\Order;
use Bepado\SDK\Struct\Reservation;
use Bepado\SDK\Struct\SearchResult\Product;

require_once __DIR__ . '/../BaseTestCase.php';
require_once __DIR__ . '/../wrapper/sdkMock.php';

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@gmx.de>
 */
class mf_sdk_product_helperTest extends BaseTestCase
{
    protected $oxBasket;
    protected $oxBasketItem;
    protected $oxArticle;
    protected $sdkHelper;
    protected $sdk;
    protected $orderConverter;

    /**
     * @var mf_sdk_product_helper
     */
    private $helper;

    public function setUp()
    {
        $this->prepareVersionLayerWithConfig();

        $this->helper = new mf_sdk_product_helper();
        $this->helper->setVersionLayer($this->versionLayer);

        // objects from oxid
        $this->oxBasket = $this->getMockBuilder('oxBasket')->disableOriginalConstructor()->getMock();
        $this->oxBasketItem = $this->getMockBuilder('oxBasketItem')->disableOriginalConstructor()->getMock();
        $this->oxArticle = $this->getMockBuilder('mf_bepado_oxarticle')->disableOriginalConstructor()->getMock();
        $this->sdkHelper = $this->getMockBuilder('mf_sdk_helper')->disableOriginalConstructor()->getMock();
        $this->sdk = $this->getMockBuilder('sdkMock')->disableOriginalConstructor()->getMock();
        $this->orderConverter = $this->getMockBuilder('mf_sdk_order_converter')->disableOriginalConstructor()->getMock();
        $sdkConfig = new SDKConfig();
        $this->sdkHelper
            ->expects($this->any())
            ->method('createSdkConfigFromOxid')
            ->will($this->returnValue($sdkConfig));
        $this->oxBasket
            ->expects($this->any())
            ->method('getContents')
            ->will($this->returnValue(array($this->oxBasketItem)));
        $this->oxBasketItem
            ->expects($this->any())
            ->method('getArticle')
            ->will($this->returnValue($this->oxArticle));
        $this->sdkHelper
            ->expects($this->any())
            ->method('instantiateSdk')
            ->with($this->equalTo($sdkConfig))
            ->will($this->returnValue($this->sdk));
    }

    public function testCheckProductsInBasketWithNoChanges()
    {
        // expectations
        $this->oxBasketItem
            ->expects($this->once())
            ->method('getAmount')
            ->will($this->returnValue(3));
        $this->oxArticle
            ->expects($this->any())
            ->method('isImportedFromBepado')
            ->will($this->returnValue(true));
        $this->sdk
            ->expects($this->any())
            ->method('checkProducts')
            ->will($this->returnValue(true));

        $product = new Product();
        $product->availability = 3;
        $this->oxArticle
            ->expects($this->once())
            ->method('getSdkProduct')
            ->will($this->returnValue($product));

        $this->helper->checkProductsInBasket($this->oxBasket);

        // asserts
        $this->assertEquals(new oxField('', oxField::T_TEXT), $this->oxBasketItem->bepado_check);
    }

    public function testCheckProductsInBasketWithPriceChanges()
    {
        // expectations
        $this->oxBasketItem
            ->expects($this->once())
            ->method('getAmount')
            ->will($this->returnValue(3));
        $this->oxArticle
            ->expects($this->any())
            ->method('isImportedFromBepado')
            ->will($this->returnValue(true));
        $this->sdk
            ->expects($this->any())
            ->method('checkProducts')
            ->will($this->returnValue(
                array(
                    new \Bepado\SDK\Struct\Message(
                        array(
                            'message' =>'Price changed.',
                            'values'  => array('price' => 10)
                        )
                    )
                )
            ));

        $product = new Product();
        $product->availability = 3;
        $this->oxArticle
            ->expects($this->once())
            ->method('getSdkProduct')
            ->will($this->returnValue($product));
        $this->oxBasket
            ->expects($this->once())
            ->method('calculateBasket')
            ->with($this->equalTo(true));

        $this->helper->checkProductsInBasket($this->oxBasket);

        // asserts
        $this->assertEquals(new oxField('<ul><li><i>The price has changed.</i></li></ul>', oxField::T_TEXT), $this->oxBasketItem->bepado_check);
    }

    public function testCheckProductsInBasketWithAvailabilityChangesNoMatter()
    {
        // expectations
        $this->oxBasketItem
            ->expects($this->once())
            ->method('getAmount')
            ->will($this->returnValue(3));
        $this->oxArticle
            ->expects($this->any())
            ->method('isImportedFromBepado')
            ->will($this->returnValue(true));
        $this->sdk
            ->expects($this->any())
            ->method('checkProducts')
            ->will($this->returnValue(
                array(
                    new \Bepado\SDK\Struct\Message(
                        array(
                            'message' =>'availability changed.',
                            'values'  => array('availability' => 10)
                        )
                    )
                )
            ));

        $product = new Product();
        $product->availability = 3;
        $this->oxArticle
            ->expects($this->once())
            ->method('getSdkProduct')
            ->will($this->returnValue($product));

        $this->helper->checkProductsInBasket($this->oxBasket);

        // asserts
        $this->assertEquals(new oxField('', oxField::T_TEXT), $this->oxBasketItem->bepado_check);
    }

    public function testCheckProductsInBasketWithAvailabilityChangesDoesMatter()
    {
        // expectations
        $this->oxBasketItem
            ->expects($this->once())
            ->method('getAmount')
            ->will($this->returnValue(6));
        $this->oxArticle
            ->expects($this->any())
            ->method('isImportedFromBepado')
            ->will($this->returnValue(true));
        $this->sdk
            ->expects($this->any())
            ->method('checkProducts')
            ->will($this->returnValue(
                array(
                    new \Bepado\SDK\Struct\Message(
                        array(
                            'message' =>'availability changed.',
                            'values'  => array('availability' => 5)
                        )
                    )
                )
            ));

        $product = new Product();
        $product->availability = 3;
        $this->oxArticle
            ->expects($this->once())
            ->method('getSdkProduct')
            ->will($this->returnValue($product));
        $this->oxBasket
            ->expects($this->once())
            ->method('calculateBasket')
            ->with($this->equalTo(true));

        $this->helper->checkProductsInBasket($this->oxBasket);

        // asserts
        $this->assertEquals(
            new oxField(
                '<ul><li><i>This product is available only 5 times. Either delete the
                        product from your basket or purchase the reduced amount.</i></li></ul>',
                oxField::T_TEXT
            ),
            $this->oxBasketItem->bepado_check
        );
    }

    public function testReservationWithEmptyOrderItems()
    {
        $sdkOrder = new Struct\Order();
        $oxOrder = $this->getMockBuilder('oxOrder')->disableOriginalConstructor()->getMock();

        // expected method calls
        $this->orderConverter
            ->expects($this->once())
            ->method('fromShopToBepado')
            ->with($this->equalTo($oxOrder))
            ->will($this->returnValue($sdkOrder));

        $result = $this->helper->reserveProductsInOrder($oxOrder);

        $this->assertFalse($result);
    }

    /**
     * @expectedException \oxNoArticleException
     */
    public function testReservationDefaultException()
    {
        $sdkOrder = new Struct\Order();
        $orderItem = new Struct\OrderItem();
        $sdkOrder->orderItems = array($orderItem);
        $sdkReservation = new Reservation();
        $oxOrder = $this->getMockBuilder('oxOrder')->disableOriginalConstructor()->getMock();

        // expected method calls
        $this->orderConverter
            ->expects($this->once())
            ->method('fromShopToBepado')
            ->with($this->equalTo($oxOrder))
            ->will($this->returnValue($sdkOrder));
        $this->sdk
            ->expects($this->once())
            ->method('reserveProducts')
            ->with($this->equalTo($sdkOrder))
            ->will($this->returnValue($sdkReservation));
        $sdkReservation->success = false;

        $this->helper->reserveProductsInOrder($oxOrder);
    }

    /**
     * @expectedException \oxOutOfStockException
     * @expectedMessage "test message: 10"
     */
    public function testReservationStockExceeded()
    {
        $sdkOrder = new Struct\Order();
        $orderItem = new Struct\OrderItem();
        $sdkOrder->orderItems = array($orderItem);
        $sdkReservation = new Reservation();
        $oxOrder = $this->getMockBuilder('oxOrder')->disableOriginalConstructor()->getMock();

        // expected method calls
        $this->orderConverter
            ->expects($this->once())
            ->method('fromShopToBepado')
            ->with($this->equalTo($oxOrder))
            ->will($this->returnValue($sdkOrder));
        $this->sdk
            ->expects($this->once())
            ->method('reserveProducts')
            ->with($this->equalTo($sdkOrder))
            ->will($this->returnValue($sdkReservation));
        $sdkReservation->success = false;
        $sdkReservation->messages = array(
            new Message(array('message' => 'test message: %availability', 'values' => array('availability' => 10)))
        );

        $this->helper->reserveProductsInOrder($oxOrder);
    }


    /**
     * @expectedException \oxArticleInputException
     * @expectedMessage "test message: 10"
     */
    public function testReservationPriceChanged()
    {
        $sdkOrder = new Struct\Order();
        $orderItem = new Struct\OrderItem();
        $sdkOrder->orderItems = array($orderItem);
        $sdkReservation = new Reservation();
        $oxOrder = $this->getMockBuilder('oxOrder')->disableOriginalConstructor()->getMock();

        // expected method calls
        $this->orderConverter
            ->expects($this->once())
            ->method('fromShopToBepado')
            ->with($this->equalTo($oxOrder))
            ->will($this->returnValue($sdkOrder));
        $this->sdk
            ->expects($this->once())
            ->method('reserveProducts')
            ->with($this->equalTo($sdkOrder))
            ->will($this->returnValue($sdkReservation));
        $sdkReservation->success = false;
        $sdkReservation->messages = array(
            new Message(array('message' => 'test message: %price', 'values' => array('price' => 10)))
        );

        $this->helper->reserveProductsInOrder($oxOrder);
    }

    protected function getObjectMapping()
    {
        return array(
            'SDKConfig'              => new SDKConfig(),
            'mf_sdk_helper'          => $this->sdkHelper,
            'mf_sdk_order_converter' => $this->orderConverter
        );
    }
}