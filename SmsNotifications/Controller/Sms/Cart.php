<?php
namespace GoMage\SmsNotifications\Controller\Sms;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Cart as CustomerCart;

class Cart extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @param Context $context
     * @param CustomerCart $cart
     */
    public function __construct(
        Context $context,
        CustomerCart $cart
    ) {
        parent::__construct($context);
        $this->_cart = $cart;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $product = $this->getProduct();

        $params        = $this->getRequest()->getParams();
        $productParams = $this->getProductParams($product);
        if (!empty($productParams)) {
            $params = array_merge_recursive($params, $productParams);
        }

        $this->_cart->addProduct($product, $params);
        $this->_cart->save();

        $this->_eventManager->dispatch(
            'checkout_cart_add_product_complete',
            ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
        );

        $resultRedirect = $this->resultRedirectFactory->create();
        $cartUrl        = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
        $resultRedirect->setUrl($cartUrl);

        return $resultRedirect;
    }

    /**
     * @param  Product $product
     * @return array
     */
    protected function getProductParams($product)
    {
        $params                 = parent::getProductParams($product);
        $params['custom_price'] = $product->getLoggedInPrice();
        return $params;
    }

}
