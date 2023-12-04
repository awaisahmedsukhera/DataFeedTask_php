<?php

require_once 'vendor/autoload.php';

use DataFeedTask\DataProcessor;
use DataFeedTask\Reader\XMLReader;
use DataFeedTask\Writer\SQLiteWriter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Checking if command-line arguments are provided
if ($argc !== 5) {
    die("Usage: php main.php <reader_type> <writer_type> <source_file> <destination_file>\n");
}

$readerType = $argv[1];
$writerType = $argv[2];
$sourceFile = $argv[3];
$destinationFile = $argv[4];

// Setting up a logger for the main script
$mainLogger = new Logger('main_script');
$mainLogger->pushHandler(new StreamHandler('/home/awais/myphpproject/logs/error.log', Logger::INFO));

try {
    // Creating instances of reader and writer based on provided types
    $reader = createReaderInstance($readerType, $sourceFile);
    $writer = createWriterInstance($writerType, $destinationFile);

    // Creating a DataProcessor instance
    $dataProcessor = new DataProcessor();

    // Processing data using the DataProcessor
    $dataProcessor->processData($reader, $writer, $sourceFile, $destinationFile);

    $mainLogger->info("Script completed successfully");
} catch (\Exception $e) {
    // Logging errors at the script level
    $mainLogger->error("Error in script: {$e->getMessage()}", ['exception' => $e]);
}

function createReaderInstance($readerType, $sourceFile): XMLReader {
    switch ($readerType) {
        case 'xml':
            return new XMLReader($sourceFile);
        // Add other reader types as needed
        default:
            throw new \InvalidArgumentException("Invalid reader type: $readerType");
    }
}

function createWriterInstance($writerType, $destinationFile): SQLiteWriter {
    switch ($writerType) {
        case 'sqlite':
            $dbPath = $destinationFile;
            return new SQLiteWriter($dbPath);
        // Add other writer types as needed
        default:
            throw new \InvalidArgumentException("Invalid writer type: $writerType");
    }
}

