<?php
/**
 * Created by PhpStorm.
 * User: vphucpham
 * Date: 1/6/17
 * Time: 6:32 PM
 */

require_once(dirname(__FILE__) . '/config.php');


class API {

    /**
     * @var string
     *
     * The HTTP Method this request was made in, either GET, POST, PUT or DELETE
     */
    protected $method = '';


    /**
     * @var string
     *
     * Response status.
     */
    protected $status = Array();

    /**
     * @var Array().
     *
     * Data from client.
     */
    protected $request = Array();



    public function __construct($method, $request) {
        $this->method = $method;
        $this->request = $request;
    }


    private function getStatus($code) {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code])? $status[$code]: $status[500];
    }

    /**
     * @return string
     *
     * Return data to client.
     */
    public function getResponse() {
        $response = '';
        $request = '';

        switch($this->method) {
            case 'DELETE':
            case 'POST':
                //var_dump($this->request);
                //$request = $this->cleanInputs($this->request);
                $this->insertUser($this->request);


                break;
            case 'GET':
                $this->authenticateUser($this->request);
                break;
            case 'PUT':
                break;
            default:
                $this->getResponse(405);
                break;
        }
    }

    private function response($request) {
        $userid = $request['userid'];
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        //$res = $conn->query($sql);
        $conn->close();
    }

    /**
     * @param $request
     *
     * Insert user into database and response status to client.
     */
    public function insertUser($request) {

        $name = $request['name'];
        $usr = $request['username'];
        $pwd = $request['pwd'];
        $email = $request['email'];
        $phone = $request['phone'];
        $status = $request['status'];


        if (isset($name) && isset($usr) && isset($pwd) && isset($email) && isset($status) && isset($phone)) {

            try {
                $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);


                $usr = mysqli_real_escape_string($conn, $usr);
                $pwd = mysqli_real_escape_string($conn, $pwd);
                $pwd = md5($pwd);

                $name = mysqli_real_escape_string($conn, $name);
                $email = mysqli_real_escape_string($conn, $email);
                $status = mysqli_real_escape_string($conn, $status);
                $phone = mysqli_real_escape_string($conn, $phone);


                $query = "Select * from account where account.`username` = '$usr' OR account.`email` = '$email' ";

                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    $json = array("status" => 0, "msg" => "Username or Email is already existed");
                } elseif ($result->num_rows == 0) {
                    $query = "INSERT INTO account( name, email, phone, username, password, status) values ('$name', '$email', '$phone', '$usr', '$pwd', '$status') ";
                    if (!$conn->multi_query($query)) {
                        throw new mysqli_sql_exception();
                    }

                    $json = array("status" => 1, "msg" => "Registration Completed");
                }

                $conn->close();

            } catch (mysqli_sql_exception $e) {

                $this->reponse('',500);
            }

            echo $this->reponse($json);
        }

    }




    private function reponse($data, $status = 200) {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

        header("HTTP/1.1 " . $status . " " . $this->getStatus($status));
        return json_encode($data);
    }


    public function authenticateUser($request) {
        $usr = $request['username'];
        $pwd = $request['pwd'];

        $json = array("status" => 0, "msg" => "Incorrect username or password");

        if (isset($usr) && isset($pwd)) {
            try {
                $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
                $usr = mysqli_real_escape_string($conn, $usr);
                $pwd = mysqli_real_escape_string($conn, $pwd);
                $pwd = md5($pwd);
                $query = "Select * from account where account.`username` = '$usr' OR account.`password` = '$pwd' ";
                $result = $conn->query($query);
                if ($result->num_rows > 0) {
                    $json = array("status" => 1, "msg" => "Authenticated");
                }

                $conn->close();
            } catch (mysqli_sql_exception $e) {
                $this->reponse('',500);
            }

        }

        echo $this->reponse($json);


    }

    private function cleanInputs($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }



    private function createUser() {

    }




}