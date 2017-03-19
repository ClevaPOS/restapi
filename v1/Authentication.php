<?php
require_once(dirname(__FILE__) . '/config.php');

/**
 * Created by PhpStorm.
 * User: vphucpham
 * Date: 3/7/17
 * Time: 10:16 PM
 */
class Authentication
{

    public function __construct(){

    }

    /**
     *
     * Generate and add authentication key to
     *
     * @param $id
     * @param $usr
     * @return string
     */
    public function addKey($id, $usr) {


        $session = '';
        $session = $this->isInSession($id);
        if (!empty($session)) $this->removeSession($id);

        $nowtime = time();
        $expiried_time = $this->isInSession($id);
        $key = $nowtime . $usr;
        $time = date('Y-m-d H:i:s', strtotime('now +60 minutes'));
        $authash = md5($key);
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $query = "INSERT INTO session(authkey, expired, userid) values ('$authash' ,'$time', '$id')";

        if ($conn->query($query) === TRUE) {
            return $authash;
        } else {
                return '';
        }
        
    }

    public function removeSession($uid) {

        $key = $this->isInSession($uid);
        if (!empty($key)) {
            try {
                $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
                $query = "DELETE FROM session WHERE userid=" . $uid;
                $conn->query($query);

            }catch (mysqli_sql_exception $e) {}

        }


    }

    public function isInSession($uid) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $query = "SELECT * FROM session WHERE userid=" . $uid;
        $key = '';
        $id = '';


        if (($conn->query($query) == TRUE)) {
            $result = $conn->query($query);
            if ($result->num_rows == 1) {
                print 'here';
                while($r = $result->fetch_assoc()) {
                    $key = $r['expired'];
                }

                $conn->close();

                $key = strtotime($key);


                return $key;

            }

             return $key;

        }
    }

    public function checkSession($uid, $key) {

    }

}