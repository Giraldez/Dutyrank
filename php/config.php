<?php
/**
 * Created by Cluster
 * Date: 12/05/2016
 * Time: 07:39 PM
 */
//cantidad de jugadores a mostrar en el ranking
define("limite", 50);

//Path del log del game
$path = "d:\\games\\cod\\games_mp.log";
if(!is_file($path)) {
    $path = "games_mp.log";
}
define("path", $path);

//Database
define("database","dutyrank.gz");
//Contraseña
define("password","dee04dffef77d81480bc1aa40d5c470b");