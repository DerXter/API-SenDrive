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
                $filtre = htmlspecialchars($_GET['filtre']);

                echo Vehicule::filtreVehicule($filtre);
            break;
            case 'ajoutVehicule': //Ajout de véhicule
                include_once('Classes/Vehicule.class.php');
                //Sécurisation des données reçues
                $idMarque = htmlspecialchars($_POST['idMarque']);
                $idModele = htmlspecialchars($_POST['idModele']);
                $idType = htmlspecialchars($_POST['idType']);
                $idProprietaire = htmlspecialchars($_POST['idProprietaire']);
                $idCarburant = htmlspecialchars($_POST['idCarburant']);
                $dateDebut = htmlspecialchars($_POST['dateDebut']);
                $dateFin = htmlspecialchars($_POST['dateFin']);
                $immatriculation = htmlspecialchars($_POST['immatriculation']);
                $climatisation = htmlspecialchars($_POST['climatisation']);
                $nbPorte = htmlspecialchars($_POST['nbPorte']);
                $nbPlace = htmlspecialchars($_POST['nbPlace']);
                $description = htmlspecialchars($_POST['description']);
                $prix = htmlspecialchars($_POST['prix']);
                $boiteDeVitesse = htmlspecialchars($_POST['boiteDeVitesse']);

                echo Vehicule::ajoutVehicule($idMarque, $idModele, $idType, $idProprietaire, $idCarburant, $dateDebut, $dateFin, $immatriculation, $climatisation, $nbPorte, $nbPlace, $description, $prix, $boiteDeVitesse);
            break;
            case 'modifierVehicule': //Modification de véhicule
                include_once('Classes/Vehicule.class.php');
                //Sécurisation des données reçues
                $idVehicule = htmlspecialchars($_POST['idVehicule']);
                $idMarque = htmlspecialchars($_POST['idMarque']);
                $idModele = htmlspecialchars($_POST['idModele']);
                $idType = htmlspecialchars($_POST['idType']);
                $idProprietaire = htmlspecialchars($_POST['idProprietaire']);
                $idCarburant = htmlspecialchars($_POST['idCarburant']);
                $dateDebut = htmlspecialchars($_POST['dateDebut']);
                $dateFin = htmlspecialchars($_POST['dateFin']);
                $immatriculation = htmlspecialchars($_POST['immatriculation']);
                $climatisation = htmlspecialchars($_POST['climatisation']);
                $nbPorte = htmlspecialchars($_POST['nbPorte']);
                $nbPlace = htmlspecialchars($_POST['nbPlace']);
                $description = htmlspecialchars($_POST['description']);
                $prix = htmlspecialchars($_POST['prix']);
                $boiteDeVitesse = htmlspecialchars($_POST['boiteDeVitesse']);

                echo Vehicule::modifierVehicule($idVehicule, $idMarque, $idModele, $idType, $idProprietaire, $idCarburant, $dateDebut, $dateFin, $immatriculation, $climatisation, $nbPorte, $nbPlace, $description, $prix, $boiteDeVitesse);
            break;
            case 'supprimerVehicule': //Suppression de véhicule
                include_once('Classes/Vehicule.class.php');
                //Sécurisation des données reçues
                $id = htmlspecialchars($_POST['id']);

                echo Vehicule::supprimerVehicule($id);
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

            //******************************Traitement des propriétaires******************************
            case 'afficheProprio': //Affichage des proprietaires
            include_once('Classes/Proprietaire.class.php');
            
            echo Proprietaire::afficheProprio();
            break;
            case 'ajoutProprio': //Ajout de propriétaire
            include_once('Classes/Proprietaire.class.php');
            //Sécurisation des données reçues
            $raisonSociale = htmlspecialchars($_POST['raisonSociale']);
            $proprietaire = htmlspecialchars($_POST['proprietaire']);
            $dateNaissance = htmlspecialchars($_POST['dateNaissance']);
            $numIdentite = htmlspecialchars($_POST['numIdentite']);
            $telephone = htmlspecialchars($_POST['telephone']);
            $adresse= htmlspecialchars($_POST['adresse']);
            $email= htmlspecialchars($_POST['email']);
            
            echo Proprietaire::ajoutProprio($raisonSociale, $proprietaire, $dateNaissance, $numIdentite, $telephone, $adresse, $email);
            break;
            case 'modifProprio': //Modification de propriétaire
            include_once('Classes/Proprietaire.class.php');
            //Sécurisation des données reçues
            $idProprietaire = htmlspecialchars($_POST['idProprietaire']);
            $raisonSociale = htmlspecialchars($_POST['raisonSociale']);
            $proprietaire = htmlspecialchars($_POST['proprietaire']);
            $dateNaissance = htmlspecialchars($_POST['dateNaissance']);
            $numIdentite = htmlspecialchars($_POST['numIdentite']);
            $telephone = htmlspecialchars($_POST['telephone']);
            $adresse= htmlspecialchars($_POST['adresse']);
            $email= htmlspecialchars($_POST['email']);
            
            echo Proprietaire::modifProprio($idProprietaire, $raisonSociale, $proprietaire, $dateNaissance, $numIdentite, $telephone, $adresse, $email);
            break;
            case 'supprimerProprio': //Suppression de proprietaires
            include_once('Classes/Proprietaire.class.php');
            //Sécurisation des données reçues
            $idProprietaire = htmlspecialchars($_POST['id']);
            
            echo Proprietaire::supprimerProprio($idProprietaire);
            break;

            //******************************Traitement du personnel******************************
            case 'affichePersonnel': //Affichage du personnel
            include_once('Classes/Personnel.class.php');
            
            echo Personnel::affichePersonnel();
            break;
            case 'ajoutPersonnel': //Ajout du personnel
                include_once('Classes/Personnel.class.php');
                //Sécurisation des données reçues
                $civilite = htmlspecialchars($_POST['civilite']);
                $poste = htmlspecialchars($_POST['poste']);
                $nom = htmlspecialchars($_POST['nom']);
                $prenom = htmlspecialchars($_POST['prenom']);
                $dateNaissance = htmlspecialchars($_POST['dateNaissance']);
                $numeroIdentite = htmlspecialchars($_POST['numeroIdentite']);
                $adresse= htmlspecialchars($_POST['adresse']);
                $telephone= htmlspecialchars($_POST['telephone']);
                $email= htmlspecialchars($_POST['email']);
                
                echo Personnel::ajoutPersonnel($civilite, $poste, $nom, $prenom, $dateNaissance, $numeroIdentite, $adresse, $telephone, $email);
            break;
            case 'modifierPersonnel': //Modification du personnel
                include_once('Classes/Personnel.class.php');
                //Sécurisation des données reçues
                $idPersonnel = htmlspecialchars($_POST['idPersonnel']);
                $civilite = htmlspecialchars($_POST['civilite']);
                $poste = htmlspecialchars($_POST['poste']);
                $nom = htmlspecialchars($_POST['nom']);
                $prenom = htmlspecialchars($_POST['prenom']);
                $dateNaissance = htmlspecialchars($_POST['dateNaissance']);
                $numeroIdentite = htmlspecialchars($_POST['numeroIdentite']);
                $adresse= htmlspecialchars($_POST['adresse']);
                $telephone= htmlspecialchars($_POST['telephone']);
                $email= htmlspecialchars($_POST['email']);
                
                echo Personnel::modifierPersonnel($idPersonnel, $civilite, $poste, $nom, $prenom, $dateNaissance, $numeroIdentite, $adresse, $telephone, $email);
            break;
            case 'supprimerPersonnel': //Suppression de personnel
            include_once('Classes/Personnel.class.php');
            //Sécurisation des données reçues
            $id = htmlspecialchars($_POST['id']);

            echo Personnel::supprimerPersonnel($id);
            break;


            default :
                echo "La fonction demandée est inexistante !";
        } //End switch

    } //End nonVide
    else{
        echo "Aucune donnée reçue !";
    }