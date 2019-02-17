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
            case 'ajoutChauffeur': //Ajout de chauffeurs 
                include_once('Classes/Chauffeur.class.php');
                //Sécurisation des données reçues
                $prenom = htmlspecialchars($_POST['prenom']);
                $nom = htmlspecialchars($_POST['nom']);
                $dateNaissance = htmlspecialchars($_POST['dateNaissance']);
                $numeroIdentite = htmlspecialchars($_POST['numeroIdentite']);
                $permis = htmlspecialchars($_POST['permis']);
                $adresse = htmlspecialchars($_POST['adresse']);
                $telephone = htmlspecialchars($_POST['telephone']);
                $commentaire = htmlspecialchars($_POST['commentaire']);

                echo Chauffeur::ajoutChauffeur($prenom, $nom, $dateNaissance, $numeroIdentite, $permis, $adresse, $telephone, $commentaire);
            break;
            case 'modifierChauffeur': //Modification de chauffeurs 
                include_once('Classes/Chauffeur.class.php');
                //Sécurisation des données reçues
                $idChauffeur = htmlspecialchars($_POST['idChauffeur']);
                $prenom = htmlspecialchars($_POST['prenom']);
                $nom = htmlspecialchars($_POST['nom']);
                $dateNaissance = htmlspecialchars($_POST['dateNaissance']);
                $numeroIdentite = htmlspecialchars($_POST['numeroIdentite']);
                $permis = htmlspecialchars($_POST['permis']);
                $adresse = htmlspecialchars($_POST['adresse']);
                $telephone = htmlspecialchars($_POST['telephone']);
                $commentaire = htmlspecialchars($_POST['commentaire']);

                echo Chauffeur::modifierChauffeur($idChauffeur, $prenom, $nom, $dateNaissance, $numeroIdentite, $permis, $adresse, $telephone, $commentaire);
            break;
            case 'supprimerChauffeur': //Suppréssion de chauffeurs
                include_once('Classes/Chauffeur.class.php');
                //Sécurisation des données reçues
                $id = htmlspecialchars($_GET['id']);

                echo Chauffeur::supprimerChauffeur($id);
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
                $immatriculation = htmlspecialchars($_POST['immatriculation']);
                $climatisation = htmlspecialchars($_POST['climatisation']);
                $nbPorte = htmlspecialchars($_POST['nbPorte']);
                $nbPlace = htmlspecialchars($_POST['nbPlace']);
                $description = htmlspecialchars($_POST['description']);
                $prix = htmlspecialchars($_POST['prix']);
                $boiteDeVitesse = htmlspecialchars($_POST['boiteDeVitesse']);

                echo Vehicule::ajoutVehicule($idMarque, $idModele, $idType, $idProprietaire, $idCarburant, $immatriculation, $climatisation, $nbPorte, $nbPlace, $description, $prix, $boiteDeVitesse);
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
                $immatriculation = htmlspecialchars($_POST['immatriculation']);
                $climatisation = htmlspecialchars($_POST['climatisation']);
                $nbPorte = htmlspecialchars($_POST['nbPorte']);
                $nbPlace = htmlspecialchars($_POST['nbPlace']);
                $description = htmlspecialchars($_POST['description']);
                $prix = htmlspecialchars($_POST['prix']);
                $boiteDeVitesse = htmlspecialchars($_POST['boiteDeVitesse']);

                echo Vehicule::modifierVehicule($idVehicule, $idMarque, $idModele, $idType, $idProprietaire, $idCarburant, $immatriculation, $climatisation, $nbPorte, $nbPlace, $description, $prix, $boiteDeVitesse);
            break;
            case 'filtrage': //filtrage de véhicule selon plusieurs critères
                include_once('Classes/Vehicule.class.php');
                //Sécurisation des données reçues
                $idMarque = htmlspecialchars($_GET['idMarque']);
                $idModele = htmlspecialchars($_GET['idModele']);
                $idType = htmlspecialchars($_GET['idType']);
                $idCarburant = htmlspecialchars($_GET['idCarburant']);
                $climatisation = htmlspecialchars($_GET['climatisation']);
                $dateDebut = htmlspecialchars($_GET['dateDebut']);
                $dateFin = htmlspecialchars($_GET['dateFin']);

                echo Vehicule::filtrage($idMarque, $idModele, $idType, $idCarburant, $climatisation, $dateDebut, $dateFin);
            break;
            case 'supprimerVehicule': //Suppression de véhicule
                include_once('Classes/Vehicule.class.php');
                //Sécurisation des données reçues
                $id = htmlspecialchars($_GET['id']);

                echo Vehicule::supprimerVehicule($id);
            break;
            case 'ajoutMarque': //Ajout d'une marque de véhicule
                include_once('Classes/Vehicule.class.php');
                //Sécurisation des données reçues
                $marque = htmlspecialchars($_POST['marque']);

                echo Vehicule::ajoutMarque($marque);
            break;
            case 'ajoutModele': //Ajout d'un modele de véhicule
                include_once('Classes/Vehicule.class.php');
                //Sécurisation des données reçues
                $id = htmlspecialchars($_POST['id']);
                $modele = htmlspecialchars($_POST['modele']);

                echo Vehicule::ajoutModele($modele, $id);
            break;
            case 'afficheModele': //Affichage d'un modèle selon l'id de la marque
            include_once('Classes/Vehicule.class.php');
            //Sécurisation des données reçues
            if(isset($_GET['id'])){
                $id = htmlspecialchars($_GET['id']);
            }
            else{
                $id="";
            }

            echo Vehicule::afficheModele($id); 
            break;
            case 'afficheMarques': //Affichage des marques
            include_once('Classes/Vehicule.class.php');
            
            echo Vehicule::afficheMarques(); 
            break;
            case 'ajoutTypeVehicule': //Ajout d'un type de véhicule
                include_once('Classes/Vehicule.class.php');
                //Sécurisation des données reçues
                $type = htmlspecialchars($_POST['type']);

                echo Vehicule::ajoutTypeVehicule($type);
            break;
            case 'modifierCaracVehicule': //Modification d'un attribut de véhicule
                include_once('Classes/Vehicule.class.php');
                //Sécurisation des données reçues
                if(isset($_POST['idMarque'])){
                    $idMarque = htmlspecialchars($_POST['idMarque']);
                }
                else{
                    $idMarque = '';
                }
                $id = htmlspecialchars($_POST['idCarac']);
                $carac = htmlspecialchars($_POST['carac']);
                $valeur = htmlspecialchars($_POST['valeur']);

                echo Vehicule::modifierCaracVehicule($carac, $id, $valeur, $idMarque);
            break;
            case 'supprimerCaracVehicule': //Suppréssion d'un attribut de véhicule
                include_once('Classes/Vehicule.class.php');
                //Sécurisation des données reçues
                $id = htmlspecialchars($_GET['id']);
                $carac = htmlspecialchars($_GET['carac']);

                echo Vehicule::supprimerCaracVehicule($carac, $id);
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
            

                echo Client::ajoutClient($nom, $prenom, $telephone, $adresse, $mail);
            break;
            case 'afficheClients': //Affichage des clients
                include_once('Classes/Client.class.php');
                echo Client::afficheClients();
            break;

            //******************************Traitement des réservations******************************
            case 'ajoutReservation': //Ajout d'une réservation
                include_once('Classes/Reservation.class.php');
                //Vérification de l'id du chauffeur
                if(!isset($_POST['idChauffeur'])){
                    $idChauffeur = -1;
                }
                else{
                    //Sécurisation de l'id du chauffeur
                    $idChauffeur = htmlspecialchars($_POST['idChauffeur']);
                }
                //Vérification de l'id du client - Client déjà connu /*FRONT - Ajout idClient */
                if(!isset($_POST['idClient'])){
                    $idClient = -1;
                }
                else{
                    //Sécurisation de l'id du chauffeur
                    $idClient = htmlspecialchars($_POST['idClient']);
                }
                if(!isset($_POST['statut'])){
                    $statut = "En cours";
                }
                else{
                    $statut = htmlspecialchars($_POST['statut']);
                }
                if(!isset($_POST['idReservation'])){
                    $idReservation = 0;
                }
                else{
                    $idReservation = htmlspecialchars($_POST['idReservation']);
                }
                //Sécurisation des données reçues
                $idVehicule = htmlspecialchars($_POST['idVehicule']); 
                $dateDepart = htmlspecialchars($_POST['dateDebut']);
                $dateArrivee = htmlspecialchars($_POST['dateFin']);
                $destination = htmlspecialchars($_POST['destination']);
                
                echo Reservation::ajoutReservation($idReservation, $idVehicule, $idChauffeur, $idClient, $dateDepart, $dateArrivee, $destination, $statut);
            break;
            case 'changerStatutReservation': //Annulation d'une reservation
                include_once('Classes/Reservation.class.php');
                //Sécurisation des données reçues
                $idReservation = htmlspecialchars($_GET['idReservation']);
                $statut = htmlspecialchars($_GET['statut']);
                
                echo Reservation::changerStatutReservation($idReservation, $statut);
            break;
            case 'filtreReservationA': //filtrage des reservations avec chauffeur
                include_once('Classes/Reservation.class.php');
                //Sécurisation des données reçues
                if(isset($_GET['element']))
                    $element = htmlspecialchars($_GET['element']);
                else
                    $element = null;
                if(isset($_GET['statut']))
                    $statut = htmlspecialchars($_GET['statut']);
                else
                    $statut = null;
                if(isset($_GET['id']))
                    $id = htmlspecialchars($_GET['id']);
                else    
                    $id = null;
                
                echo Reservation::filtreReservationA($element, $id, $statut);
            break;
            case 'filtreReservationS': //filtrage des reservations avec chauffeur
                include_once('Classes/Reservation.class.php');
                //Sécurisation des données reçues
                if(isset($_GET['element']))
                    $element = htmlspecialchars($_GET['element']);
                else
                    $element = null;
                if(isset($_GET['statut']))
                    $statut = htmlspecialchars($_GET['statut']);
                else
                    $statut = null;
                if(isset($_GET['id']))
                    $id = htmlspecialchars($_GET['id']);
                else    
                    $id = null;
                
                echo Reservation::filtreReservationS($element, $id, $statut);
            break;
            case 'modifierReservation': //modificaction d'une réservation
                include_once('Classes/Reservation.class.php');
                //Sécurisation des données reçues
                $idReservation = htmlspecialchars($_POST['idReservation']);
                $idVehicule = htmlspecialchars($_POST['idVehicule']); 
                $idClient = htmlspecialchars($_POST['idClient']);
                if(!isset($_POST['idChauffeur'])){
                    $idChauffeur = -1;
                }
                else{
                    //Sécurisation de l'id du chauffeur
                    $idChauffeur = htmlspecialchars($_POST['idChauffeur']);
                }                
                $dateDepart = htmlspecialchars($_POST['dateDebut']);
                $dateArrivee = htmlspecialchars($_POST['dateFin']);
                $statut = htmlspecialchars($_POST['statut']);
                
                $destination = htmlspecialchars($_POST['destination']);
                
                echo Reservation::modifierReservation($idReservation, $idClient, $idVehicule, $idChauffeur, $dateDepart, $dateArrivee, $statut, $destination);
            break;
            case 'supprimerReservation': //Suppréssion de réservations
            include_once('Classes/Reservation.class.php');
            //Sécurisation des données reçues
            $id = htmlspecialchars($_GET['id']);
            echo Reservation::supprimerReservation($id); 
            break;

            case 'afficheReservations': //Affichage des réservations
            include_once('Classes/Reservation.class.php');
            //Sécurisation des données reçues
            $choix = htmlspecialchars($_GET['choix']);
            echo Reservation::afficheReservations($choix); 
            break;

            case 'afficheReservationSelonStatut': //Affichage des réservations
            include_once('Classes/Reservation.class.php');
            //Sécurisation des données reçues
            $statut = htmlspecialchars($_GET['statut']);
            echo Reservation::afficheReservationSelonStatut($statut); 
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
            case 'modifierUtilisateur': //Modification d'un utilisateur
            include_once('Classes/Utilisateur.class.php');
            //Sécurisation des données reçues
            $login = htmlspecialchars($_POST['login']);
            $id = htmlspecialchars($_POST['id']);
            $statut = htmlspecialchars($_POST['statut']);
            $idPersonnel = htmlspecialchars($_POST['idPersonnel']);

            echo Utilisateur::modifierUtilisateur($id, $login, $statut, $idPersonnel);
            break;
            case 'changePassword': //changement de mot de passe
            include_once('Classes/Utilisateur.class.php');
            //Sécurisation des données reçues
            $id = htmlspecialchars($_POST['id']);
            $oldPassword = htmlspecialchars($_POST['oldPassword']);
            $newPassword = htmlspecialchars($_POST['newPassword']);
            
            echo Utilisateur::changePassword($id, $oldPassword, $newPassword);
            break;
            case 'afficheUtilisateurs': //Affichage des utilisateurs
            include_once('Classes/Utilisateur.class.php');
            
            echo Utilisateur::afficheUtilisateurs();
            break;

            case 'afficheUtilisateur': //Affichage d'un utilisateur spécifié par son id
            include_once('Classes/Utilisateur.class.php');
            if(isset($_GET['id'])){
                $id = htmlspecialchars($_GET['id']);
            }
            echo Utilisateur::afficheUtilisateur($id);
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
            $idProprietaire = htmlspecialchars($_GET['id']);
            
            echo Proprietaire::supprimerProprio($idProprietaire);
            break;

            //******************************Traitement du personnel******************************
            case 'affichePersonnels': //Affichage du personnel
            include_once('Classes/Personnel.class.php');
            
            echo Personnel::affichePersonnels();
            break;
            case 'affichePersonnel': //Affichage d'un membre du personnel selon son id
            include_once('Classes/Personnel.class.php');
            if(isset($_GET['id'])){
                $id = htmlspecialchars($_GET['id']);

                echo Personnel::affichePersonnel($id);
            }
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
            $id = htmlspecialchars($_GET['id']);

            echo Personnel::supprimerPersonnel($id);
            break;
            case 'ajoutFonction': //Ajout d'une fonction d'un personnel
            include_once('Classes/Personnel.class.php');
            if(isset($_POST['poste'])){
                $poste = htmlspecialchars($_POST['poste']);

                echo Personnel::ajoutFonction($poste);
            }
            break;
            case 'supprimerFonction': //Ajout d'une fonction d'un personnel
            include_once('Classes/Personnel.class.php');
            if(isset($_GET['id'])){
                $id = htmlspecialchars($_GET['id']);

                echo Personnel::supprimerFonction($id);
            }
            break;

            //******************************Traitement de la documentation******************************
            case 'afficheDoc': //Affichage des fichiers de documentation dont la nature est spécifiée
            include_once('Classes/Doc.class.php');
            //Sécurisation des données reçues
            $nature = htmlspecialchars($_GET['nature']);

            echo Doc::afficheDoc($nature);
            break;
            case 'supprimerDoc': //Suppression du fichier de documentation dont l'id est spécifié
                include_once('Classes/Doc.class.php');
                //Sécurisation des données reçues
                $id = htmlspecialchars($_GET['id']);

                echo Doc::supprimerDoc($id);
            break;


            //******************************Traitement des promotions******************************
            case 'affichePromo': //Affichage des promotions selon le statut spécifié
                include_once('Classes/Promotion.class.php');
                //Sécurisation des données reçues
                if(isset($_GET['statut'])){
                    $statut = htmlspecialchars($_GET['statut']);
                }
                else{
                    $statut='';
                }

                echo Promotion::affichePromo($statut);
            break;
            case 'affichePromos': //Affichage des promotions comprises entre deux dates
                include_once('Classes/Promotion.class.php');
                //Sécurisation des données reçues
                $dateDebut = htmlspecialchars($_GET['dateDebut']);
                $dateFin = htmlspecialchars($_GET['dateFin']);

                echo Promotion::affichePromos($dateDebut, $dateFin);
            break;
            case 'afficheToutesPromos': //Affichage de toutes les promotions
                include_once('Classes/Promotion.class.php');

                echo Promotion::afficheToutesPromos();
            break;
            case 'ajoutPromo': //Ajout d'une promotion
                include_once('Classes/Promotion.class.php');
                //Sécurisation des données reçues
                $idVehicule = htmlspecialchars($_POST['idVehicule']);
                $nom = htmlspecialchars($_POST['nom']);
                $taux = htmlspecialchars($_POST['taux']);
                $dateDebut = htmlspecialchars($_POST['dateDebut']);
                $dateFin = htmlspecialchars($_POST['dateFin']);

                echo Promotion::ajoutPromo($idVehicule, $nom, $taux, $dateDebut, $dateFin);
            break;
            case 'modifierPromo': //Modification de promotion
                include_once('Classes/Promotion.class.php');
                //Sécurisation des données reçues
                $idPromo = htmlspecialchars($_POST['idPromo']);
                $idVehicule = htmlspecialchars($_POST['idVehicule']);
                $nom = htmlspecialchars($_POST['nom']);
                $statut = htmlspecialchars($_POST['statut']);
                $taux = htmlspecialchars($_POST['taux']);
                $dateDebut = htmlspecialchars($_POST['dateDebut']);
                $dateFin = htmlspecialchars($_POST['dateFin']);

                echo Promotion::modifierPromo($idPromo, $idVehicule, $nom, $taux, $statut, $dateDebut, $dateFin);
            break;
            case 'supprimerPromo': //Suppréssion de promotion
                include_once('Classes/Promotion.class.php');
                //Sécurisation des données reçues
                $id = htmlspecialchars($_GET['id']);

                echo Promotion::supprimerPromo($id);
            break;

            //******************************Traitement des navettes******************************
            case 'ajoutNavette': //Ajout d'une navette
                include_once('Classes/Navette.class.php');
                //Sécurisation des données reçues
                $idClient = htmlspecialchars($_POST['idClient']);
                $idVehicule = htmlspecialchars($_POST['idVehicule']);
                $idChauffeur = htmlspecialchars($_POST['idChauffeur']);
                $date = htmlspecialchars($_POST['date']);
                $depart = htmlspecialchars($_POST['depart']);
                $destination = htmlspecialchars($_POST['destination']);
                $heureDebut = htmlspecialchars($_POST['heureDebut']);
                $heureFin = htmlspecialchars($_POST['heureFin']);

                echo Navette::ajoutNavette($idClient, $idVehicule, $idChauffeur, $date, $depart, $destination, $heureDebut, $heureFin);
            break;
            case 'modifierNavette': //Modification d'une navette
                include_once('Classes/Navette.class.php');
                //Sécurisation des données reçues
                $idNavette = htmlspecialchars($_POST['idNavette']);
                $idClient = htmlspecialchars($_POST['idClient']);
                $idVehicule = htmlspecialchars($_POST['idVehicule']);
                $idChauffeur = htmlspecialchars($_POST['idChauffeur']);
                $date = htmlspecialchars($_POST['date']);
                $depart = htmlspecialchars($_POST['depart']);
                $destination = htmlspecialchars($_POST['destination']);
                $heureDebut = htmlspecialchars($_POST['heureDebut']);
                $heureFin = htmlspecialchars($_POST['heureFin']);

                echo Navette::modifierNavette($idNavette, $idClient, $idVehicule, $idChauffeur, $date, $depart, $destination, $heureDebut, $heureFin);
            break;
            case 'afficheNavette': //Affichage des navettes
                include_once('Classes/Navette.class.php');
                //Sécurisation des données reçues
                if(isset($_GET['statut'])){
                    $statut = htmlspecialchars($_GET['statut']);
                }
                else{
                    $statut = '';
                }
                $choix = htmlspecialchars($_GET['choix']);

                echo Navette::afficheNavette($choix, $statut);
            break;
            case 'filtreNavette': //Affichage des navettes selon le vehicule ou le chauffeur
                include_once('Classes/Navette.class.php');
                //Sécurisation des données reçues
                if(isset($_GET['idVehicule'])){
                    $idVehicule = htmlspecialchars($_GET['idVehicule']);
                }
                else
                    $idVehicule = '';
                if(isset($_GET['idChauffeur'])){
                    $idChauffeur = htmlspecialchars($_GET['idChauffeur']);
                }
                else
                    $idChauffeur = '';
                if(isset($_GET['choix'])){
                    $choix = htmlspecialchars($_GET['choix']);
                }
                else
                    $choix = '';

                echo Navette::filtreNavette($idVehicule, $idChauffeur, $choix);
            break;
            case 'supprimerNavette': //Suppression de navette
                include_once('Classes/Navette.class.php');
                //Sécurisation des données reçues
                $id = htmlspecialchars($_GET['id']);

                echo Navette::supprimerNavette($id);
            break;
            case 'annulerNavette': //Annulation de navette
                include_once('Classes/Navette.class.php');
                //Sécurisation des données reçues
                $id = htmlspecialchars($_GET['id']);

                echo Navette::changerStatutNavette($id, 'Annulé');
            break;

            default :
                echo "La fonction demandée est inexistante !";
        } //End switch

    } //End nonVide
    else{
        echo "Aucune donnée reçue !";
    }