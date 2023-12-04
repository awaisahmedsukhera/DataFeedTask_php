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

    public function processData(Reader $reader, Writer $writer, $sourceFile, $destinationFile) {
        try {
            // Reading data from the XML reader
            $data = $reader->readData($sourceFile);

            // Checking if data is successfully read
            if ($data !== null) {

                // Writing the data to the SQLite writer
                $writer->writeData($data, $destinationFile);

                // Logging information about the processing
                $this->logProcessingInfo($reader, $writer, $sourceFile, $destinationFile);

            } else {
                $this->handleDataReadingError($sourceFile);
            }
        } catch (\Exception $e) {
            $this->handleProcessingError($e);
        }
    }

    private function logProcessingInfo(Reader $reader, Writer $writer, $sourceFile, $destinationFile) {
        $this->logger->info("Data processing details", [
            'reader' => get_class($reader),
            'writer' => get_class($writer),
            'sourceFile' => $sourceFile,
            'destinationFile' => $destinationFile,
        ]);
    }

    private function handleProcessingError(\Exception $e) {
        $this->logger->error("Error processing data: {$e->getMessage()}", ['exception' => $e]);
    }

    private function handleDataReadingError($sourceFile) {
        $this->logger->error("Error reading data from file '{$sourceFile}'");
    }
}
