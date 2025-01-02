<?php

namespace Config;
class DB {
    // Static property to hold the single PDO connection instance
    private static $connection;

    // Method to initialize or retrieve the existing connection
    private static function getConnection() {
        // Check if the connection has already been established
        if (!self::$connection) {
            // Connection details
            $dsn = 'pgsql:host=localhost;port=5432;dbname=TODO';
            $username = 'postgres';
            $password = 'hamza';

            try {
                // Create a new PDO connection and store it in the static property
                self::$connection = new \PDO($dsn, $username, $password, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                ]);
            } catch (\PDOException $e) {
                // Handle connection errors
                throw new \Exception("Database connection failed: " . $e->getMessage());
            }
        }

        // Return the existing connection
        return self::$connection;
    }

    // Static method for executing queries
    public static function query($query, $params = []) {
        try {
            // Use the existing connection
            $connection = self::getConnection();

            // Prepare and execute the query
            $statement = $connection->prepare($query);
            $statement->execute($params);

            // Return the statement for further processing if needed
            return $statement;
        } catch (\PDOException $e) {
            // Handle query execution errors
            throw new \Exception("Query execution failed: " . $e->getMessage());
        }
    }
}



?>