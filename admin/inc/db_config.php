<?php

    $hname = 'localhost';
    $uname = 'root';
    $pass = '';
    $db = 'hbwebsite';

    $con = mysqli_connect($hname,$uname,$pass,$db);

    if(!$con){
        die("Cannot connect to db....".mysqli_connect_error());
    }

    function filteration($data){
        foreach($data as $key => $value){
            $data[$key] = trim($value);
            $data[$key] = stripcslashes($value);
            $data[$key] = htmlspecialchars($value);
            $data[$key] = strip_tags($value);
        }
        return $data;
    }

?>