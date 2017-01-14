<?php
    require_once(dirname(__FILE__) . '/config.php');

    class Database {
        public static function query($sql) {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            $res = $conn->query($sql);
            $conn->close();
            Database::formatResults($res);

        }

        public static function formatResults($res) {
            $results = array();

            foreach ($res as $r) {
                array_push($results, $r);
            }
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($results);
        }


    }

    $userid = $_GET['userid'];

    echo $userid;
    Database::query("SELECT * FROM useracc WHERE useracc.`id` = '$userid'");


?>