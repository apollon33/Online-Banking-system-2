<?php
namespace agilman\a2\model;

use mysqli;

/**
 * Class Model
 *
 * @package agilman/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 */
class Model
{
    protected $db;

    // is this the best place for these constants?
    const DB_HOST = 'mysql';
    const DB_USER = 'root';
    const DB_PASS = 'root';
    const DB_NAME = 'agilman_a2';

    public function __construct()
    {
        $this->db = new mysqli(
            Model::DB_HOST,
            Model::DB_USER,
            Model::DB_PASS
            //            Model::DB_NAME
        );

        if (!$this->db) {
            error_log("Failed to connect to mysql!", 0);
            die("Failed to connect to database");
            // can't connect to MYSQL???
            // handle it...
            // e.g. throw new someException($this->db->connect_error, $this->db->connect_errno);
        }

        // This is to make our life easier
        // Create your database and populate it with sample data
        $this->db->query("CREATE DATABASE IF NOT EXISTS " . Model::DB_NAME . ";");

        if (!$this->db->select_db(Model::DB_NAME)) {
            // somethings not right.. handle it
            error_log("Mysql database not available!", 0);
        }

        $result = $this->db->query("SHOW TABLES LIKE 'bank_accounts';");
        if ($result->num_rows == 0) {
            // table doesn't exist create it

            $result = $this->db->query(
                "CREATE TABLE `bank_accounts` (
                                          `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
                                          `name` varchar(256) NOT NULL,
                                          `balance` bigint NOT NULL,
                                          `userID` int(8) unsigned NOT NULL,
                                          PRIMARY KEY (`id`),
                                          FOREIGN KEY (`userID`) REFERENCES `user_accounts`(`id`)
                                           );"
            );

            if (!$result) {
                // handle appropriately
                error_log("Failed creating table account", 0);
            }
        }


        $result = $this->db->query("SHOW TABLES LIKE 'user_accounts';");
        if ($result->num_rows == 0) {
            // table doesn't exist create it

            $result = $this->db->query(
                "CREATE TABLE `user_accounts` (
                                          `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
                                          `name` varchar(256) NOT NULL,
                                          `password` varchar(256) NOT NULL,
                                          PRIMARY KEY (`id`) );"
            );

            if (!$result) {
                // handle appropriately
                error_log("Failed creating table user_accounts", 0);
            }
        }


        $result = $this->db->query("SHOW TABLES LIKE 'transactions';");
        if ($result->num_rows == 0) {
            // table doesn't exist create it

            $result = $this->db->query(
                "CREATE TABLE `transactions` (
                                          `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
                                          `accountID` int(8) unsigned NOT NULL,
                                          `userID` int(8) unsigned NOT NULL,
                                          `time` varchar(100) NOT NULL,
                                          `amount` bigint NOT NULL,
                                          `type` varchar(1) NOT NULL,
                                          PRIMARY KEY (`id`)
                                          );"
            );

            if (!$result) {
                // handle appropriately
                error_log("Failed creating table transactions", 0);
                die($this->db->error);
            }
        }
    }

    public function close() {
        $this->db->close();
    }
}
