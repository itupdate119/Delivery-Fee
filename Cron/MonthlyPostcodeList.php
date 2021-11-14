<?php

namespace B2b\EdiFee\Cron;

use B2b\EdiFee\Helper\Data as Helper;
use B2b\EdiFee\Model\Import;
use Magento\Framework\Filesystem\Driver\File;

class MonthlyPostcodeList
{
    protected $scopeConfig;

    /**
     * @var Helper
     */
    protected $dataHelper;

    /**
     * @var File
     */
    protected $driverFile;

    /**
     * @var Import
     */
    protected $importModel;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Helper $dataHelper,
        File $driverFile,
        Import $importModel
    )
    {
        $this->importModel = $importModel;
        $this->scopeConfig = $scopeConfig;
        $this->dataHelper = $dataHelper;
        $this->driverFile = $driverFile;
    }

    public function execute()
    {
        $result = [];
        try {
            $filePath = $this->dataHelper->getCsvFilePath();

            if ($this->driverFile->isExists($filePath)) {
                $this->importModel->doProcess($filePath);
            }
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }
    }
}
