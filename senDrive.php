<?php
    header('Access-Control-Allow-Origin: *'); //Accepte n'importe quelle requête venant de n'importe où
    include_once('Classes/connexion.php'); //Inclusion de la connexion à la base de données

    function nonVide($tableau){
        $compteur = 0;
        $taille = count($tableau); //Taille du tableau passé en paramètre
        foreach ($tableau as $cle => $valeur){
            if (tableau[$cle] != ""){
                $compteur+=1;
            }
            else{
                return false;
            }

        } //End foreach
       return true;

    } //End nonVide($tableau)

    //Début Traitement
    if (nonVide($_GET)){
        $fonction = htmlspecialchars($_GET['fonction']); //Sécurisation de la donnée reçue

        switch ($fonction){ 
        //******************************Traitement des chauffeurs******************************
            case 'afficheChauffeurs': //Affichage de tous les chauffeurs
                include_once('Classes/Chauffeur.class.php');
                echo Chauffeur::afficheChauffeurs();
                
            break;
            case 'afficheChauffeur': //Affichage des chauffeurs disponibles entre les dates indiquées
                include_once('Classes/Chauffeur.class.php');
                //Sécurisation des données reçues
                $dateDebut = htmlspecialchars($_GET['dateDebut']);
                $dateFin = htmlspecialchars($_GET['dateFin']);
                echo Chauffeur::afficheChauffeur($dateDebut, $dateFin);
            break;

        //******************************Traitement des véhicules******************************
            case 'afficheVehicules': //Affichage de tous les véhicules
                include_once('Classes/Vehicule.class.php');
                echo Vehicule::afficheVehicules();
            break;
            case 'afficheVehicule': //Affichage des véhicules disponibles entre les dates indiquées
                include_once('Classes/Vehicule.class.php');
                //Sécurisation des données reçues
                $dateDebut = htmlspecialchars($_GET['dateDebut']);
                $dateFin = htmlspecialchars($_GET['dateFin']);
                echo Vehicule::afficheVehicule($dateDebut, $dateFin);
            break;

            default :
                echo "La fonction demandée est inexistante !";
        } //End switch

    } //End nonVide
    else{
        echo "Aucune donnée reçue !";
    }