<?php
    $login = 'sendrive';
    $password ='senv2016drivemababa';
    $host = 'vps618964.ovh.net';
    $db = 'sendrive';
   
    /*$login = 'root';
    $password = '';
    $host = 'localhost';
    $db = 'sendrive';
    */

    try{
        $bdd = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $login, $password);
    }
    catch(Exception $e){
        die('Erreur: ' . $e->getMessage());
    }