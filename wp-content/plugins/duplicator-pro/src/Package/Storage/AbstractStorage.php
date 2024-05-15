<?php

/**
 * @package Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace Duplicator\Package\Storage;

use DUP_PRO_U;
use Duplicator\Utils\IncrementalStatusMessage;

abstract class AbstractStorage implements StorageInterface
{
    const DUP_TEST_FILE_NAME_PREFIX  = 'dup_test';
    const DUP_TEST_FILE_NAME_POSTFIX = '.txt';

    private $status = false;
    private $message;
    private $statusMessages;
    private $inputData;
    private $testFileName;

    /**
     * @param string[] $inputData unfiltered array with the configuration of the storage
     */
    public function __construct($inputData)
    {
        $this->statusMessages = new IncrementalStatusMessage();
        $this->inputData      = filter_input_array(INPUT_POST, $inputData);
        $this->testFileName   = self::DUP_TEST_FILE_NAME_PREFIX . md5(uniqid(rand(), true)) . self::DUP_TEST_FILE_NAME_POSTFIX;
    }

    /**
     * @return bool Method for validating the cases that could cause failures for the file storage
     */
    public function isValid()
    {
        if (!$this->getStorageId() || !$this->getStoragePath()) {
            return false;
        }
        return true;
    }


    /**
     * @return mixed valid storage id
     */
    public function getStorageId()
    {
        return $this->inputData['storage_id'];
    }

    /**
     * @return mixed valid storage directory path
     */
    public function getStoragePath()
    {
        return $this->inputData['storage_folder'];
    }

    /**
     * @return string generated test file name
     */
    public function getTestFileName()
    {
        return $this->testFileName;
    }

    /**
     * @return void sets the operation status to success for the API response
     */
    public function setSuccessStatus()
    {
        $this->status = true;
    }

    /**
     * @return bool gets the status for the API response
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string gets the overall message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message overall message that should be set
     * @return void
     */
    public function setMessage($message)
    {
        $this->message = DUP_PRO_U::esc_html__($message);
    }

    /**
     * @param IncrementalStatusMessage $statusMessage adds an incremental message
     * @return void
     */
    public function addStatusMessage($statusMessage)
    {
        $this->statusMessages->addMessage($statusMessage);
    }

    /**
     * @return IncrementalStatusMessage gets all incremental messages
     */
    public function getStatusMessages()
    {
        return $this->statusMessages;
    }

    /**
     * @return string[] gets the construction filtered data
     */
    public function getInputData()
    {
        return $this->inputData;
    }

    /**
     * @return string gets the full file path for the test file
     */
    public function getFullTestFilePath()
    {
        return $this->getStoragePath() . '/' . $this->getTestFileName();
    }

    /**
     * @return string[] gets the response of the current local storage state for the API response
     */
    public function getResponseForAPI()
    {
        return array(
            'success' => $this->getStatus(),
            'message' => $this->getMessage(),
            'status_msgs' => strval($this->getStatusMessages()),
        );
    }
}
