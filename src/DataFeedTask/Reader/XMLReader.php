<?php

namespace DataFeedTask\Reader;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class XMLReader implements Reader
{
    private $logger;
    private $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;

        // Creating a Monolog logger instance with a stream handler
        $this->logger = new Logger('xml_reader');
        $this->logger->pushHandler(new StreamHandler('/home/awais/myphpproject/logs/error.log', Logger::ERROR));
    }

    public function readData()
    {
        try {
            $tree = simplexml_load_file($this->filePath);
            return $tree;
        } catch (\Exception $e) {
            $errorMessage = "Error parsing XML in file '{$this->filePath}': {$e->getMessage()}";
            $this->logger->error($errorMessage);
            return null;
        }
    }
}

