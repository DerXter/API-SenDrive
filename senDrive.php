<?php
    header('Access-Control-Allow-Origin: *'); //Accepte n'importe quelle requête venant de n'importe où
    include_once('Classes/connexion.php'); //Inclusion de la connexion à la base de données

    function nonVide($tableau){
        $compteur = 0;
        $taille = count($tableau); //Taille du tableau passé en paramètre
        foreach ($tableau as $cle => $valeur){
            if (isset($tableau[$cle])){
                $compteur+=1;
            }

        } //End foreach

        return $compteur == $taille ? true : false;

    } //End nonVide($tableau)

    //Début Traitement
    if (nonVide($_GET) || nonVide($_POST)){
        //Vérification de la façon dont les données sont envoyées
        if(isset($_GET['fonction'])){
            $fonction = htmlspecialchars($_GET['fonction']); //Sécurisation de la donnée reçue
        }
        else if(isset($_POST['fonction'])){
            $fonction = htmlspecialchars($_POST['fonction']); //Sécurisation de la donnée reçue
        }
        else{
            echo 'Veuillez renseigner une fonction svp !';
        }
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
            case 'filtreVehicule': //Affichage des véhicules selon les critères indiquées
                include_once('Classes/Vehicule.class.php');
                //Sécurisation des données reçues
                $marque = htmlspecialchars($_GET['marque']);
                $modele = htmlspecialchars($_GET['modele']);
                $type = htmlspecialchars($_GET['type']);
                $energie = htmlspecialchars($_GET['energie']);
                $climatisation = htmlspecialchars($_GET['climatisation']);

                echo Vehicule::filtreVehicule($marque, $modele, $type, $energie, $climatisation);
            break;

            //******************************Traitement des clients******************************
            case 'ajoutClient': //Ajout de clients
                include_once('Classes/Client.class.php');
                //Sécurisation des données reçues
                $nom = htmlspecialchars($_POST['nom']);
                $prenom = htmlspecialchars($_POST['prenom']);
                $telephone = htmlspecialchars($_POST['telephone']);
                $adresse = htmlspecialchars($_POST['adresse']);
                $mail = htmlspecialchars($_POST['mail']);
                $destination = htmlspecialchars($_POST['destination']);

                echo Client::ajoutClient($nom, $prenom, $telephone, $adresse, $mail, $destination);
            break;
            case 'afficheClients': //Affichage des clients
                include_once('Classes/Client.class.php');
                echo Client::afficheClients();
            break;

            //******************************Traitement des réservations******************************
            case 'ajoutReservation': //Ajout d'une réservation
                include_once('Classes/Reservation.class.php');
                //Sécurisation des données reçues
                $idVehicule = htmlspecialchars($_POST['idVehicule']);
                $idChauffeur = htmlspecialchars($_POST['idChauffeur']);
                $dateDepart = htmlspecialchars($_POST['dateDebut']);
                $dateArrivee = htmlspecialchars($_POST['dateFin']);
                
                echo Reservation::ajoutReservation($idVehicule, $idChauffeur, $dateDepart, $dateArrivee);
            break;
            case 'changerStatutReservation': //Changement du statut d'une reservation
                include_once('Classes/Reservation.class.php');
                //Sécurisation des données reçues
                $idReservation = htmlspecialchars($_POST['idReservation']);
                $statut = htmlspecialchars($_POST['statut']);
                
                echo Reservation::changerStatutReservation($idReservation, $statut);
            break;
            case 'afficheReservations': //Affichage des réservations
            include_once('Classes/Reservation.class.php');
            
            echo Reservation::afficheReservations();
            break;

            //******************************Traitement des utilisateurs******************************
            case 'ajoutUtilisateur': //Ajout d'utilisateurs
            include_once('Classes/Utilisateur.class.php');
            //Sécurisation des données reçues
            $login = htmlspecialchars($_POST['login']);
            $password = htmlspecialchars($_POST['password']);
            $statut = htmlspecialchars($_POST['statut']);
            $numIdentite = htmlspecialchars($_POST['numIdentite']);

            echo Utilisateur::ajoutUtilisateur($login, $password, $statut, $numIdentite);
            break;
            case 'afficheUtilisateurs': //Affichage des utilisateurs
            include_once('Classes/Utilisateur.class.php');
            
            echo Utilisateur::afficheUtilisateurs();
            break;
            case 'connexion': //connexion des utilisateurs
            include_once('Classes/Utilisateur.class.php');
            //Sécurisation des données reçues
            $login = htmlspecialchars($_POST['login']);
            $password = htmlspecialchars($_POST['password']);
            
            echo Utilisateur::connexion($login, $password);
            break;

            default :
                echo "La fonction demandée est inexistante !";
        } //End switch

    } //End nonVide
    else{
        echo "Aucune donnée reçue !";
    }