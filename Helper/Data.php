<?php

namespace B2b\EdiFee\Helper;

use Magento\Config\App\Config\Type\System;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Soap\ClientFactory;

class Data extends AbstractHelper
{
    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ClientFactory|mixed
     */
    protected $soapClientFactory;

    CONST WSDL_URL = 'soap_endpoint';

    CONST PATH_TO_KEY = 'path_to_key';

    /**
     * @param Context $context
     * @param DirectoryList $directoryList
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Context $context,
        DirectoryList $directoryList,
        ClientFactory $soapClientFactory = null,
        ObjectManagerInterface $objectManager
    ) {
        parent::__construct($context);
        $this->soapClientFactory = $soapClientFactory ?: ObjectManager::getInstance()->get(ClientFactory::class);
        $this->directoryList = $directoryList;
        $this->objectManager = $objectManager;
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    public function getDirPath()
    {
        $varDir = $this->directoryList->getPath('var');
        $dir = $varDir . DIRECTORY_SEPARATOR . 'postcode_list';

        return $dir;
    }

    /**
     * @param $type
     * @return string
     * @throws FileSystemException
     */
    public function getCsvFilePath()
    {
        $dir = $this->getDirPath();
        $file = $dir . DIRECTORY_SEPARATOR . $this->scopeConfig->getValue('edi_fee/general/file_name');

        return $file;
    }

    /**
     *
     */
    public function flushConfigCache()
    {
        if (class_exists(System::class)) {
            $this->objectManager->get(System::class)->clean();
        } else {
            $this->objectManager->get(Config::class)
                ->clean(
                    \Zend_Cache::CLEANING_MODE_MATCHING_TAG,
                    ['config_scopes']
                );
        }
    }

    public function buildSoapRequestParams($postcode, $customerNumber, $quote)
    {
        $allItems = $quote->getAllVisibleItems();
        if (empty($allItems)) {
            return null;
        }
        $currency = $quote->getQuoteCurrencyCode();
        $items = '';
        foreach ($allItems as $item) {
            $items .= '<DeliveryFeeItem>
                            <OrderArticle>'.$item->getSku().'</OrderArticle>
                            <OrderQuantity>'.$item->getQty().'</OrderQuantity>
                            <NetValue>'. round($item->getPrice(), 2) .'</NetValue>
                            <Currency>'. $currency .'</Currency>
                            <UnitOfMeasure>EA</UnitOfMeasure>
                        </DeliveryFeeItem>';

        }
        return '
         <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:b2be="http://www.officeworks.com.au/erp/B2Be">
            <soapenv:Header/>
            <soapenv:Body>
                <b2be:DeliveryFee>
                    <City/>
                    <PostalCode>'.$postcode.'</PostalCode>
                    <DocumentType/>
                    <SamedayReq/>
                    <DelivOption/>
                    <SoldTo>'.$customerNumber.'</SoldTo>
                    <SalesOrg/>
                    <DistributionChannel/>' . $items . '
                </b2be:DeliveryFee>
            </soapenv:Body>
        </soapenv:Envelope>';
    }

    public function getFeeAmount($soapRequest)
    {
        $amount = 0;
        $wsdl = $this->getVariableValueByCode(self::WSDL_URL);
        if (!$wsdl) {
            $wsdl = 'https://wst.officeworks.com.au/pi/B2BEEDI/DeliveryFee';
        }
        $keyFile = $this->getVariableValueByCode(self::PATH_TO_KEY);;
        if (!$keyFile) {
            $keyFile = getcwd() . DIRECTORY_SEPARATOR. 'gatewayuat_prikey.txt';
        }
        if ($wsdl && file_exists($keyFile)) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $wsdl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_RETURNTRANSFER => '1',
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_CAINFO         => $keyFile,
                CURLOPT_SSLKEY        => $keyFile,
                CURLOPT_SSLCERT        => $keyFile,
                CURLOPT_POSTFIELDS => $soapRequest,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/xml; charset=utf-8', 'Content-Length: '.strlen($soapRequest)
                ),
            ));

            $response = curl_exec($curl);

            $pattern = "/<Amount>([^<>]*)<\/Amount>/";
            preg_match($pattern, $response, $matches, PREG_OFFSET_CAPTURE);
            if (count($matches) > 1) {
                $amount = reset($matches[1]);
            }
        }

        return $amount;
    }

    public function getVariableValueByCode($code)
    {
        $variable = $this->objectManager->get('Magento\Variable\Model\Variable')->loadByCode($code);
        $variableValue = $variable->getPlainValue();
        return $variableValue;
    }
}
