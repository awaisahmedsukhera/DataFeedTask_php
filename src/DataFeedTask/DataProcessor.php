<?php

namespace DataFeedTask;

use DataFeedTask\Reader\Reader;
use DataFeedTask\Writer\Writer;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class DataProcessor {
    private $logger;

    public function __construct() {
        // Creating a Monolog logger instance with a stream handler
        $this->logger = new Logger('data_processor');
        $this->logger->pushHandler(new StreamHandler('/home/awais/myphpproject/logs/error.log', Logger::INFO));
    }

    public function processData(Reader $reader, Writer $writer) {
        try {
            // Reading data from the XML reader
            $data = $reader->readData();

            // Checking if data is successfully read
            if ($data !== null) {

                // Writing the data to the SQLite writer
                $writer->writeData($data);

                // Logging information about the processing
                $this->logProcessingInfo($reader, $writer);

            } else {
                $this->handleDataReadingError();
            }
        } catch (\Exception $e) {
            $this->handleProcessingError($e);
        }
    }

    private function logProcessingInfo(Reader $reader, Writer $writer) {
        $this->logger->info("Data processing details", [
            'reader' => get_class($reader),
            'writer' => get_class($writer),
        ]);
    }

    private function handleProcessingError(\Exception $e) {
        $this->logger->error("Error processing data: {$e->getMessage()}", ['exception' => $e]);
    }

    private function handleDataReadingError() {
        $this->logger->error("Error reading data from reader file");
    }
}
