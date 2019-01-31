<?php
   /* $login = 'sendrive';
    $password = 'sdsgestion*';
    $host = 'den1.mysql4.gear.host';
    $db = 'sendrive';*/
    $login = 'root';
    $password = '';
    $host = 'localhost';
    $db = 'sendrive';

    try{
        $bdd = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $login, $password);
    }
    catch(Exception $e){
        die('Erreur: ' . $e->getMessage());
    }