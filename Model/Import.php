<?php

namespace B2b\EdiFee\Model;

use B2b\EdiFee\Helper\Data;
use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Model\AbstractModel;

class Import extends AbstractModel
{
    /**
     * Database write connection
     *
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var Data $helper
     */
    protected $helper;

    /**
     * @var File
     */
    private $driverFile;

    /**
     * Import constructor.
     *
     */
    public function __construct(
        ResourceConnection $resource,
        Data $helper,
        File $driverFile
    ) {
        $this->resource = $resource;
        $this->helper = $helper;
        $this->driverFile = $driverFile;
    }

    /**
     * @param $filePath
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function doProcess($filePath)
    {
        $write = $this->resource->getConnection();
        $columns = $write->getTables('b2b_postcode_list_');

        $oldTemporary = implode(', ', $columns);

        if (!empty($oldTemporary)) {
            $write->dropTable($oldTemporary);
        }

        if (($handle = $this->driverFile->fileOpen($filePath, "r")) !== false) {
            try {
                $tmpTableName = $this->_prepareImport();
                $this->helper->flushConfigCache();
                $ignoredLines = 1; //Ignore first row
                while ($ignoredLines > 0 && ($this->driverFile->fileGetCsv($handle, 0, ",")) !== false) {
                    $ignoredLines--;
                }

                $dataForImport = [];
                while (($data = $this->driverFile->fileGetCsv($handle, 0, ",")) !== false) {
                    $dataForImport[] = $data;
                }

                if (count($dataForImport)) {
                    $this->_importItem( $tmpTableName, $dataForImport);
                }

                $this->_doneImport($tmpTableName);
            } catch (Exception $e) {
                $this->_destroyImport($tmpTableName);
                throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
            }
        }
    }

    /**
     * @return string
     */
    protected function _prepareImport()
    {
        $write = $this->resource->getConnection();

        $targetTable = $this->resource->getTableName('b2b_postcode_list');

        $tmpTableName = uniqid($targetTable . '_');

        //@codingStandardsIgnoreStart
        $query = 'create table ' . $tmpTableName . ' like ' . $targetTable;
        //@codingStandardsIgnoreEnd
        $write->query($query);

        $write->changeTableEngine($tmpTableName, 'innodb');

        return $tmpTableName;
    }

    /**
     * @param $tmpTableName
     * @param $dataForImport
     */
    protected function _importItem($tmpTableName, &$dataForImport)
    {
        $dataForInsert = [];
        $write = $this->resource->getConnection();

        foreach ($dataForImport as $data) {
            if (count($data) >= 5) {
                $dataForInsert[] = [
                    'pcode'     => $data[0],
                    'locality'  => $data[1],
                    'state'     => $data[2],
                    'comments'  => $data[3],
                    'category'  => $data[4]
                ];
            }
        }
        $write->insertMultiple($tmpTableName, $dataForInsert);
    }

    /**
     * @param $tmpTableName
     */
    protected function _destroyImport($tmpTableName)
    {
        $write = $this->resource->getConnection();
        $write->dropTable($tmpTableName);
    }

    /**
     * @param $tmpTableName
     */
    protected function _doneImport($tmpTableName)
    {
        $write = $this->resource->getConnection();

        $targetTable = $this->resource->getTableName('b2b_postcode_list');

        if ($write->isTableExists($tmpTableName)) {
            $write->dropTable($targetTable);

            $write->renameTable($tmpTableName, $targetTable);
        }
    }
}
