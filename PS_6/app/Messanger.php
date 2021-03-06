<?php
session_start();
require_once '../config/config.php';
require_once 'Connection.php';
date_default_timezone_set ("Europe/Kiev");

class Messanger {

    public function getMsg()
    {
        $data = [];
        $conn = Connection::connect();
        $message_table = DATA_BASE['message_table'];

        if(!isset($_SESSION['last_msg_index'])) {
            $sql = "SELECT * FROM  $message_table WHERE time >=
            DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        } else {
            $next_msg = $_SESSION['last_msg_index'] + 1;
            $sql = "SELECT * FROM  $message_table WHERE id >= $next_msg";
        }

        $res = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_assoc($res)) {
            $data[$row['id']] = $row;
        }

        mysqli_close($conn);
        return $data;
    }

    public function saveMsg($msg) {
        $user = $_SESSION['user'];
        $conn = Connection::connect();
        $message_table = DATA_BASE['message_table'];
        $sql = "INSERT INTO $message_table VALUES (NULL, '$user', NOW(), '$msg')";
        mysqli_query($conn, $sql);
        mysqli_close($conn);
    }

    public function prepareCurrentMsg()
    {
        $response = '';
        $data = self::getMsg();

        if(empty($data)) {
            return;
        }

        foreach ($data as $message) {
            $response .= self::createOneMsg($message);
        }

        $_SESSION['last_msg_index'] = array_key_last($data);
        return $response;
    }

    private function createOneMsg($message) {
        //split date to y-m-d and time fragments
        $time = preg_split('/\s/', $message['time']);
        return "<p>[<span class='time'>" . $time[1] . "</span>]  <span class='user'>"
        . $message['author'] .  " : </span>" . self::makeEmoji($message['message']) . "</p>";
    }

    private function makeEmoji($msg) {
        $msg = str_replace(":)", "<img src='images/happy.png'>" , $msg);
        $msg = str_replace(":(", "<img src='images/sad.png'>", $msg);
        return $msg;
    }
}
