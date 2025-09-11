<?php
    $connection = mysqli_connect("localhost","root","","parking_spaces");

    // found error here: if database can't connect you won't be able to switch tabs

    // to do: add error handling if so

    // grgrgrgrgrrrrr

    if($connection) {
        echo "Connected Successfully!";
    }

?>