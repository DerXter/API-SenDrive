<?php
    $login = 'derguene';
    $password = 'ordinateur97';
    $host = 'localhost';
    $db = 'sendrive';

    try{
        $bdd = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $login, $password);
    }
    catch(Exception $e){
        die('Erreur: ' . $e->getMessage());
    }