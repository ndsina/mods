<?php
namespace GoMage\SmsNotifications\Controller\Sms;

use Magento\Framework\App\Action\Context;
use GoMage\SmsNotifications\Helper\Data as SmsHelper;
use GoMage\SmsNotifications\Model\Clx\ApiClient;
use GoMage\SmsNotifications\Model\SmsFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class Send extends \Magento\Framework\App\Action\Action
{
    /**
     * @var SmsHelper
     */
    protected $smsHelper;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var ApiClient
     */
    protected $apiClient;

    /**
     * @var SmsFactory
     */
    protected $smsFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Send constructor.
     * @param Context $context
     * @param SmsHelper $smsHelper
     * @param JsonHelper $jsonHelper
     * @param ApiClient $apiClient
     * @param SmsFactory $smsFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        SmsHelper $smsHelper,
        JsonHelper $jsonHelper,
        ApiClient $apiClient,
        SmsFactory $smsFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->smsHelper    = $smsHelper;
        $this->jsonHelper   = $jsonHelper;
        $this->apiClient    = $apiClient;
        $this->smsFactory   = $smsFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Send sms action
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function execute()
    {
        $result = [
            'success' => true,
            'content' => __('Sms was sent successfully'),
        ];

        try {
            $phone = (string)$this->getRequest()->getParam('phone');
            $country = (string)$this->getRequest()->getParam('country');
            $phone = (!$this->smsHelper->getEnabledCountryCode() ?
                $this->smsHelper->getDefaultCountryCode() :
                $country) . $phone;

            if (!\Zend_Validate::is($phone, 'NotEmpty')) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Please enter a phone number.')
                );
            }

            $requestParams = $this->getRequest()->getParams();
            unset($requestParams['phone'], $requestParams['country']);

            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->smsHelper->getCurrentProduct($requestParams['product_id']);

            $response = $this->sendSms($phone, $product, $requestParams);

            /** @var \GoMage\SmsNotifications\Model\Sms $model */
            $model = $this->smsFactory->create();
            $data  = [
                'batch_id' => $response['id'],
                'from' => $response['from'],
                'phone_list' => implode(',', $response['to']),
                'message_text' => $response['body'],
                'created_at' => $response['created_at'],
                'expired_at' => $response['expire_at'],
                'product_name' => $product->getName(),
                'product_sku' => $product->getSku(),
                'price' => $product->getLoggedInPrice(),
            ];
            $model->addData($data);
            $model->save();
            $this->messageManager->addSuccessMessage(__('Sms was sent successfully'));
        } catch (\Exception $e) {
            $result['success'] = false;
            $message = $result['content'] = __('Something went wrong. %1', $e->getMessage());
            $this->messageManager->addExceptionMessage(
                $e, $message
            );
        }

        $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($result)
        );

    }

    /**
     * @param string $number
     * @param \Magento\Catalog\Model\Product $product
     * @param array $requestParams
     * @return mixed
     */
    protected function sendSms(
        $number,
        \Magento\Catalog\Model\Product $product,
        $requestParams = []
    )
    {
        $messageBody = str_replace(
            [
                '{PRODUCT_NAME}',
                '{PRODUCT_URL}',
                '{PRODUCT_SKU}',
                '{PRODUCT_PRICE}',
                '{YOUR_PRICE}',
                '{CART_URL}'
            ],
            [
                $product->getName(),
                $product->getProductUrl(),
                $product->getSku(),
                $product->getPrice(),
                $product->getLoggedInPrice() ?: $product->getFinalPrice(),
                $this->storeManager
                    ->getStore()
                    ->getUrl('gomage_sms/sms/cart') . "?" . http_build_query($requestParams)
            ],
            $this->smsHelper->getSmsBody()
        );
        $data = [
            'from' => $this->smsHelper->getSenderNumber(),
            'body' => $messageBody,
            'to' => [$number],
            'type' => 'mt_text'
        ];

        $response = $this->apiClient->request(
            $this->smsHelper->getSendUrl() . '/batches',
            'POST',
            [
                'Authorization: Bearer ' . $this->smsHelper->getToken(),
                'Content-Type: application/json'
            ],
            $data
        );

        return $response;
    }
}
