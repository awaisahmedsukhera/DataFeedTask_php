<?php

namespace DataFeedTask\Writer;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PDO;

class SQLiteWriter implements Writer
{
    private $connection;
    private $logger;
    private $dbPath;

    public function __construct($dbPath)
    {
        $this->dbPath = $dbPath;

        // Establishing a connection to the SQLite database using PDO
        $this->connection = new PDO("sqlite:$this->dbPath");
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Creating a Monolog logger instance with a stream handler
        $this->logger = new Logger('sqlite_writer');
        $this->logger->pushHandler(new StreamHandler('/home/awais/myphpproject/logs/error.log', Logger::ERROR));
    }

    public function writeData($data)
    {
        try {
            $tableName = $data->getName();

            foreach ($data->children() as $item) {
                $columnNames = array_keys((array)$item);

                // Creating a table (if not exists) for the data
                $this->connection->exec("CREATE TABLE IF NOT EXISTS $tableName (
                                id INTEGER PRIMARY KEY,
                                " . implode(', ', $columnNames) . "
                             )");

                // Inserting data into the table
                $values = array_values((array)$item);
                $placeholders = implode(', ', array_fill(0, count($values), '?'));

                $stmt = $this->connection->prepare("INSERT INTO $tableName (" . implode(', ', $columnNames) . ") VALUES ($placeholders)");
                $stmt->execute($values);
            }
        } catch (PDOException $e) {
            // Handling exceptions
            $errorMessage = "SQLite error while writing data to database: {$e->getMessage()}";
            $this->logger->error($errorMessage);
        }
    }

    public function __destruct()
    {
        // Closing the database connection when the object is destroyed
        $this->connection = null;
    }
}
