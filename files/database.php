<?php
    #Savienojums ar datubazi
    $serveris = "localhost";
    $lietotajs = "grobina1_fedotova";
    $parole = "kWq02UUc3kpX@";
    $datubaze = "grobina1_fedotova";

    $savienojums = mysqli_connect($serveris, $lietotajs, $parole, $datubaze);

    if(!$savienojums){
        echo "Nav izveidots savienojums ar db!";
    }
?>