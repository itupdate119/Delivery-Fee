<?php

namespace B2b\EdiFee\Controller\Postcode;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator;

class Load extends \Magento\Framework\App\Action\Action
{
    /** @var Validator  */
    protected $formkeyValidator;

    /** @var \Magento\Framework\Json\Helper\Data  */
    protected $jsonHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \B2b\EdiFee\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param Validator $formkeyValidator
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \B2b\EdiFee\Helper\Data $dataHelper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        Validator $formkeyValidator,
        \Magento\Checkout\Model\Session $checkoutSession,
        \B2b\EdiFee\Helper\Data $dataHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        Context $context)
    {
        $this->formkeyValidator = $formkeyValidator;
        $this->jsonHelper = $jsonHelper;
        $this->dataHelper = $dataHelper;
        $this->checkoutSession = $checkoutSession;
        $this->customerRepository = $customerRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $request = $this->getRequest();
        $result = [
            'error' => 1
        ];
        if (!$this->formkeyValidator->validate($request)) {
            $this->messageManager->addErrorMessage(__('Invalid Form Key. Please refresh the page.'));
            return $this->jsonResponse($result);
        }
        if ($request->isPost() && $request->isAjax()) {
            try {
                $quote = $this->checkoutSession->getQuote();
                if (!$quote) {
                    $result['message'] = __('No quote exists');
                    return $this->jsonResponse($result);
                }
                $postcode = $request->getParam('postcode', '');
                $postcode = explode(' ', trim($postcode))[0];
                $customerId = $quote->getCustomerId();
                $customerNumber = '';
                if ($customerId) {
                    $customerNumber = $this->getCustomerNumber($customerId);
                }
                $soapRequest = $this->dataHelper->buildSoapRequestParams($postcode, $customerNumber, $quote);
                if (!$soapRequest) {
                    $result['message'] = __('No have Soap Request');
                    return $this->jsonResponse($result);
                }

                $feeAmount = $this->dataHelper->getFeeAmount($soapRequest);

                $quote->setDeliveryfee($feeAmount);
                $quote->setTotalsCollectedFlag(false)->collectTotals();
                $result['amount'] = $feeAmount;
                $result['error'] = 0;
                return $this->jsonResponse($result);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->messageManager->addErrorMessage(__('Unable to calculate Edi Fee.'));
            }
            return $this->jsonResponse($result);
        } else {
            $this->getRequest()->setControllerName('noroute');
            $request->setDispatched(false);
        }
    }

    /**
     * @param $customerId
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerNumber($customerId) {
        $customerNumber = '';
        $customer = $this->customerRepository->getById($customerId);
        if ($customer) {
            $attributeValue = $customer->getCustomAttribute('customer_bp');
            if ($attributeValue!== null) {
                if ($attributeValue->getValue() !== '') {
                    $customerNumber = $attributeValue->getValue();
                }
            }
        }
        return $customerNumber;
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}
