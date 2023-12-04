<?php

namespace DataFeedTask\Reader;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class XMLReader implements Reader
{
    private $logger;

    public function __construct()
    {
        // Creating a Monolog logger instance with a stream handler
        $this->logger = new Logger('xml_reader');
        $this->logger->pushHandler(new StreamHandler('/home/awais/myphpproject/logs/error.log', Logger::ERROR));
    }

    public function readData($filePath)
    {
        try {
            $tree = simplexml_load_file($filePath);
            return $tree;
        } catch (\Exception $e) {
            $errorMessage = "Error parsing XML in file '{$filePath}': {$e->getMessage()}";
            $this->logger->error($errorMessage);
            return null;
        }
    }
}

