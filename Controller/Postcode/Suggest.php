<?php

namespace B2b\EdiFee\Controller\Postcode;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator;

class Suggest extends \Magento\Framework\App\Action\Action
{
    /** @var Validator  */
    protected $formkeyValidator;

    /** @var \Magento\Framework\Json\Helper\Data  */
    protected $jsonHelper;

    /**
     * @var \B2b\EdiFee\Model\Lookup
     */
    protected $postcodeLookup;

    /**
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param Validator $formkeyValidator
     * @param \B2b\EdiFee\Model\Lookup $postcodeLookup
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        Validator $formkeyValidator,
        \B2b\EdiFee\Model\Lookup $postcodeLookup,
        Context $context)
    {
        $this->formkeyValidator = $formkeyValidator;
        $this->jsonHelper = $jsonHelper;
        $this->postcodeLookup = $postcodeLookup;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $request = $this->getRequest();
        $result = [
            'error' => 1,
            'suggestions' => []
        ];
        if (!$this->formkeyValidator->validate($request)) {
            $this->messageManager->addErrorMessage(__('Invalid Form Key. Please refresh the page.'));
            return $this->jsonResponse($result);
        }

        if ($request->isPost() && $request->isAjax()) {
            try {
                $requestString = $request->getParam('query', '');
                $postcodeData = [];
                $postcodeList = $this->postcodeLookup->lookupByPostcode($requestString);
                if ($postcodeList) {
                    foreach ($postcodeList as $item) {
                        if (isset($item['pcode']) && isset($item['locality']) && isset($item['state'])) {
                            $postcodeData[] = $item['pcode'] . ' - ' . $item['locality'] . ', ' . $item['state'];
                        }
                    }
                }
                $result['suggestions'] = array_values( $postcodeData );
                $result['error'] = 0;
                return $this->jsonResponse($result);
            }
            catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->messageManager->addErrorMessage(__('Unable to find postcode.'));
            }
            return $this->jsonResponse($result);
        } else{
            $this->getRequest()->setControllerName('noroute');
            $request->setDispatched(false);
        }
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
