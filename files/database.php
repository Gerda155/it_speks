<?php
    #Savienojums ar datubazi
    $serveris = "localhost";
    $lietotajs = "grobina1_k8";
    $parole = "f73a15M#9";
    $datubaze = "grobina1_k8";

    $savienojums = mysqli_connect($serveris, $lietotajs, $parole, $datubaze);

    if(!$savienojums){
        echo "Nav izveidots savienojums ar db!";
    }
?>