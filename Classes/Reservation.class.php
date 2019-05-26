<?php
    include_once('Promotion.class.php');
    class Reservation{
        //Attributs
        private $idReservation;
        private $idClient;
        private $idVehicule;
        private $idChauffeur;
        private $idDate;
        private $destination;

        //Constructeur
        public function __construct($id){
            global $bdd;
            $reponse = $bdd->prepare('SELECT * FROM Reservation WHERE id=?');
            $reponse->execute(array($id));
            if ($data = $reponse->fetch()){
                //Affectation des données de la base à l'objet
                $this->idReservation = $id;
                $this->idClient = $data['idClient'];
                $this->idVehicule = $data['idVehicule'];
                $this->idChauffeur = $data['idChauffeur'];
                $this->idDate = $data['idDate'];
                $this->destination = $data['destination'];

                $reponse->closeCursor();
            }
            else{
                echo 'Client inexistant';
            }
        } //End __construct
        
        //Accesseurs
            //Getters
        public function getIdClient(){
            return $this->idClient;
        }
        public function getIdVehicule(){
            return $this->idVehicule;
        }
        public function getIdChauffeur(){
            return $this->idChauffeur;
        }
        public function getIdDate(){
            return $this->idDate;
        }
        public function getDestination(){
            return $this->destination;
        }
            //setters
        public function setIdClient($idClient){
            $this->idClient = $idClient;        }
        public function setIdVehicule($idVehicule){
            $this->idVehicule = $idVehicule;
        }
        public function setIdChauffeur($idChauffeur){
            $this->idChauffeur = $idChauffeur;
        }
        public function setIdDate($idDate){
            $this->idDate = $idDate;
        }
        public function setDestination($destination){
            $this->destination = $destination;
        }

        //Autres fonctions
        /*FRONT - Besoin infos vehicules dans l'affichage des reservations */
        public static function afficheReservations($choix){
            global $bdd;
            //Avec chauffeur
            if($choix=='avec'){
                $reqAfficheReserv = "SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, cl.email, v.idVehicule, marque, modele, immatriculation, v.cheminPhoto, CONCAT(re.prix, ' FCFA') AS prix, ch.prenom AS prenomChauffeur, ch.nom AS nomChauffeur, ch.idChauffeur, destination, re.statut, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin FROM Clientele cl, Vehicule v, Chauffeur ch, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur=ch.idChauffeur AND idDisponibilite=re.idDate";
                $reponse = $bdd->query($reqAfficheReserv);
                if ($reservations = $reponse->fetchAll()){
                    $reservations = json_encode($reservations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    $reponse->closeCursor();
                    return $reservations;
                }
                else {
                    echo "Aucune reservation avec chauffeur trouvée !";
                    return false;
                }        

            } //End avec chauffeur
            elseif ($choix=='sans'){
                //Sans Chauffeur
                $reqAfficheReserv = "SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, cl.email, v.idVehicule, marque, modele, immatriculation, v.cheminPhoto,  CONCAT(re.prix, ' FCFA') AS prix, destination, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, re.statut FROM Clientele cl, Vehicule v, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur IS NULL AND idDisponibilite=re.idDate";
                $reponse = $bdd->query($reqAfficheReserv);
                if ($reservations = $reponse->fetchAll()){
                    $reservations = json_encode($reservations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    $reponse->closeCursor();
                    return $reservations;
                }
                else {
                    echo "Aucune reservation sans chauffeur trouvée !";
                    return false;
                }
                
            } //End sans chauffeur
            else{
                echo "Choix non autorisé !";
                return false;
            }
            
        } //End afficheReservations(choix)

        /*FRONT - fonction renommee */
        public static function afficheReservationSelonStatut($statut){
            global $bdd;
            $statut_autorises = array('En cours', 'Annulé', 'Terminé');
            if(in_array($statut, $statut_autorises)){

                $reqAfficheReserv = "SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, cl.email, marque, modele, immatriculation,CONCAT(re.prix, ' FCFA') AS prix, v.cheminPhoto, ch.prenom AS prenomChauffeur, ch.nom AS nomChauffeur, destination, re.statut, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin FROM Clientele cl, Vehicule v, Chauffeur ch, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur=ch.idChauffeur AND idDisponibilite=re.idDate AND statut=?";
                $reponse = $bdd->prepare($reqAfficheReserv);
                $reponse->execute(array($statut));
                //var_dump ($reponse->fetchAll());
                if ($reponse->rowCount() > 0){
                    $reservations = $reponse->fetchAll();
                    $reservations = json_encode($reservations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    $reponse->closeCursor();

                    return $reservations;
                }
                else {
                    echo "Aucune réservation dont le statut est '$statut' n'a été trouvée !";
                    return false;
                }
            } //End if(array)
            else{
                echo "Statut non disponible. Les statuts disponibles sont: 'En cours', 'Annulé' ou 'Terminé'.";
                return false;
            }
            
            
        } //End afficheReservation(statut)

        public static function filtreReservationA($element, $id, $statut){
            //Filtre les reservations avec chauffeur
            global $bdd;
            $data = array();
            if($element!=null){
                //$element doit être vehicule ou chauffeur
                if($element=="vehicule"){
                    $element = "v.id".ucfirst($element);
                }
                else if($element=="chauffeur"){
                    $element = "ch.id".ucfirst($element);
                }
                else{
                    echo "Veuillez spécifier comme élément: vehicule ou chauffeur";
                    return false;
                }
            } //End if($element)
            if($id!=null){
                if($statut!=null){
                    $statut_autorises = array('En cours', 'Annulé', 'Terminé');
                    if(in_array($statut, $statut_autorises)){
                        $reqAfficheReserv = "SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, cl.email, marque, modele, immatriculation,CONCAT(re.prix, ' FCFA') AS prix, v.cheminPhoto, ch.prenom AS prenomChauffeur, ch.nom AS nomChauffeur, destination, re.statut, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin FROM Clientele cl, Vehicule v, Chauffeur ch, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur=ch.idChauffeur AND idDisponibilite=re.idDate AND statut=? AND $element=?";                  
                        $data = array($statut, $id);
                    } //End if(array)
                    else{
                        echo "Statut non disponible. Les statuts disponibles sont: 'En cours', 'Annulé' ou 'Terminé'.";
                        return false;
                    }
                    
                } //End if(statut)
                else{
                    $reqAfficheReserv = "SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, cl.email, marque, modele, immatriculation,CONCAT(re.prix, ' FCFA') AS prix, v.cheminPhoto, ch.prenom AS prenomChauffeur, ch.nom AS nomChauffeur, destination, re.statut, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin FROM Clientele cl, Vehicule v, Chauffeur ch, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur=ch.idChauffeur AND idDisponibilite=re.idDate AND $element=?";
                    $data = array($id);
                }
            } //End if($id)
            //Id null et statut non null
            else if($statut!=null){
                $reqAfficheReserv = "SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, cl.email, marque, modele, immatriculation,CONCAT(re.prix, ' FCFA') AS prix, v.cheminPhoto, ch.prenom AS prenomChauffeur, ch.nom AS nomChauffeur, destination, re.statut, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin FROM Clientele cl, Vehicule v, Chauffeur ch, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur=ch.idChauffeur AND idDisponibilite=re.idDate AND statut=?";                  
                $data = array($statut);
            }
            else
                $reqAfficheReserv = "SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, cl.email, marque, modele, immatriculation,CONCAT(re.prix, ' FCFA') AS prix, v.cheminPhoto, ch.prenom AS prenomChauffeur, ch.nom AS nomChauffeur, destination, re.statut, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin FROM Clientele cl, Vehicule v, Chauffeur ch, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur=ch.idChauffeur AND idDisponibilite=re.idDate";                  


            //************************Fin Vérification********************** */
            $reponse = $bdd->prepare($reqAfficheReserv);
            $reponse->execute($data);
            //var_dump ($reponse->fetchAll());
            if ($reponse->rowCount() > 0){
                $reservations = $reponse->fetchAll();
                $reservations = json_encode($reservations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $reponse->closeCursor();

                return $reservations;
            }
            else {
                echo "Aucune réservation n'a été trouvée !";
                return false;
            }
        } //End filtreReservationA(element, statut)

        public static function filtreReservationS($element, $id, $statut){
            //Filtre les reservations sans chauffeur
            global $bdd;
            $data = array();
            if($element!=null){
                //$element doit être vehicule ou chauffeur
                if($element=="vehicule"){
                    $element = "v.id".ucfirst($element);
                }
                else if($element=="chauffeur"){
                    $element = "ch.id".ucfirst($element);
                }
                else{
                    echo "Veuillez spécifier comme élément: vehicule ou chauffeur";
                    return false;
                }
            } //End if($element)
            if($id!=null){
                if($statut!=null){
                    $statut_autorises = array('En cours', 'Annulé', 'Terminé');
                    if(in_array($statut, $statut_autorises)){
                        $reqAfficheReserv = "SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, cl.email, v.idVehicule, marque, modele, immatriculation, v.cheminPhoto,  CONCAT(re.prix, ' FCFA') AS prix, destination, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, re.statut FROM Clientele cl, Vehicule v, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur IS NULL AND idDisponibilite=re.idDate AND statut=? AND $element=?";                  
                        $data = array($statut, $id);
                    } //End if(array)
                    else{
                        echo "Statut non disponible. Les statuts disponibles sont: 'En cours', 'Annulé' ou 'Terminé'.";
                        return false;
                    }
                    
                } //End if(statut)
                else{
                    $reqAfficheReserv = "SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, cl.email, v.idVehicule, marque, modele, immatriculation, v.cheminPhoto,  CONCAT(re.prix, ' FCFA') AS prix, destination, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, re.statut FROM Clientele cl, Vehicule v, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur IS NULL AND idDisponibilite=re.idDate AND $element=?";
                    $data = array($id);
                }
            } //End if($id)
            //Id null et statut non null
            else if($statut!=null){
                $reqAfficheReserv = "SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, cl.email, v.idVehicule, marque, modele, immatriculation, v.cheminPhoto,  CONCAT(re.prix, ' FCFA') AS prix, destination, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, re.statut FROM Clientele cl, Vehicule v, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur IS NULL AND idDisponibilite=re.idDate AND statut=?";                  
                $data = array($statut);
            }
            else
                $reqAfficheReserv = "SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, cl.email, v.idVehicule, marque, modele, immatriculation, v.cheminPhoto,  CONCAT(re.prix, ' FCFA') AS prix, destination, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, re.statut FROM Clientele cl, Vehicule v, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur IS NULL AND idDisponibilite=re.idDate";                  


            //************************Fin Vérification********************** */
            $reponse = $bdd->prepare($reqAfficheReserv);
            $reponse->execute($data);
            //var_dump ($reponse->fetchAll());
            if ($reponse->rowCount() > 0){
                $reservations = $reponse->fetchAll();
                $reservations = json_encode($reservations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $reponse->closeCursor();

                return $reservations;
            }
            else {
                echo "Aucune réservation n'a été trouvée !";
                return false;
            }
        } //End filtreReservationS(element, statut)


        /*FRONT - Possibilité de choisir un client déjà present -> rajout attribut idClient */
        public static function ajoutReservation($idReservation, $idVehicule, $idChauffeur, $idClient, $dateDepart, $dateArrivee, $destination, $statut){
            global $bdd;
            //Ajustement du format des dates
            $dateDepart = date("Y-m-d", strtotime($dateDepart));
            $dateArrivee = date("Y-m-d", strtotime($dateArrivee));
            //On vérifie si l'id du véhicule choisi fait ou non partie des véhicules réservés à cette période
            if (Reservation::checkReserve($idReservation, $idVehicule, 'vehicule', $dateDepart, $dateArrivee)){
                // Le véhicule n'est pas disponible
                echo "Ce véhicule a déjà été réservé à cette période !";
                return false;
            }
            
            else{
                //Le véhicule choisi est disponible
                //On calcule le montant que va couter la reservation
                $prix = Reservation::calculPrix($idVehicule, $dateDepart, $dateArrivee);
                
                if($idChauffeur != -1){  //Avec chauffeur
                    //Récupération des infos de la réservtaion avec chauffeur pour besoin d'envoie de mail
                    $reqAfficheReserv = "SELECT cl.prenom AS prenomClient, cl.nom AS nomClient, cl.adresse, cl.email, marque, modele, immatriculation, CONCAT(re.prix, ' FCFA') AS prix, ch.prenom AS prenomChauffeur, ch.nom AS nomChauffeur, destination, re.statut, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin FROM Clientele cl, Vehicule v, Chauffeur ch, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur=ch.idChauffeur AND idDisponibilite=re.idDate ORDER BY idReservation DESC LIMIT 0,1";
                    //On vérifie si l'id du chauffeur choisi fait ou non partie des chauffeurs réservés à cette période
                    if (Reservation::checkReserve($idReservation, $idChauffeur, 'chauffeur', $dateDepart, $dateArrivee)){
                        echo "Ce chauffeur a déjà été réservé à cette période !";
                        return false;
                        }    
                    
                    else{
                        #Le chauffeur choisi est disponible
                        //Mise à jour du statut de la réservation
    insertReservation:  $statutReservation =$statut;
                        if($idClient != -1){
                            $clientID = $idClient;
                        }else{
                            //Récupération de l'Id du dernier client entré
                            $clientID = Reservation::returnLastId('idClient', 'Clientele');
                        }
                        
                        //Ajout des dates dans la base            
                        $reqAjoutDates = 'INSERT INTO Disponibilite (dateDebut, dateFin) VALUES (:dateDebut, :dateFin)';
                        $reponse = $bdd->prepare($reqAjoutDates);
                        $reponse->execute(array(
                            'dateDebut' => $dateDepart,
                            'dateFin' => $dateArrivee
                        ));
                        //Vérification de la réussite de l'ajout
                        if($reponse->rowCount() > 0){
                            echo "Dates ajoutées / ";
                        } 
                        else{
                            $errorA = $response->error;
                            echo "Une erreur est survenue lors de l'ajout des dates / ";
                            return false;
                        }
                        //Récupération de l'Id de la dernière date entrée
                        $lastIdDisponibilite = Reservation::returnLastId('idDisponibilite', 'Disponibilite');
                        //Ajout de la reservation avec chauffeur
                        $reqAjoutReserv = 'INSERT INTO Reservation (idClient, idVehicule, idChauffeur, idDate, statut, prix, destination) VALUES (:idClient, :idVehicule, :idChauffeur, :idDate, :statut, :prix, :destination)';
                        $reponse = $bdd->prepare($reqAjoutReserv);
                        $reponse->execute(array(
                            'idClient' => $clientID,
                            'idDate' => $lastIdDisponibilite,
                            'idVehicule' => $idVehicule,
                            'idChauffeur' => $idChauffeur,
                            'statut' => $statutReservation,
                            'prix' => $prix,
                            'destination' => $destination
                        ));
                        //Vérification de la réussite de l'ajout
                        if($reponse->rowCount() > 0){
                            echo "Réservation ajoutée / ";
                            //Récupération des informations de la réservation et envoie du mail
                            $reponse = $bdd->query($reqAfficheReserv);
                            if ($data = $reponse->fetch()){
                                $message = "\n Résumé de votre réservation: \n";
                                $message .= "\n\tPrénom du client: ".$data['prenomClient'];
                                $message .= "\n\tNom du client: ".$data['nomClient'];
                                $message .= "\n\tAdresse: ".$data['adresse'];
                                $message .= "\n\tPériode de réservation: ".date('d-m-Y', strtotime($dateDepart)).' / '.date('d-m-Y', strtotime($dateArrivee));
                                $message .= "\n\tVéhicule réservée: ".$data['marque'].' '.$data['modele'];
                                $message .= "\n\tPrix: ".$data['prix'];
                                $message .= "\n\tDestination: ".$data['destination'];
                                if($idChauffeur != -1){
                                    $message .= "\n\tPrénom du chauffeur: ".$data['prenomChauffeur'];
                                    $message .= "\n\tNom du chauffeur: ".$data['nomChauffeur'];
                                }
                                $to = $data['email'];
                                $reponse->closeCursor();
                                #Client
                                envoieMail($to, "Résumé de votre réservation", "Réservation :", $message);
                                #Gestionnaires SDS
                                envoieMail("contact@sendrivesolutions.com, contacts.sdsolutions@gmail.com", "Résumé de la réservation", "Réservation :", $message);
                            } //End if
                            //Récupération des informations du véhicule
                            
                        } //End if reponse 
                        else{
                            echo "Une erreur est survenue lors de l'ajout de la reservation / ";
                            return false;
                        }

                        //Mise à jour du nombre de location du véhicule choisi
                        $nbLocation = Reservation::returnData('nbLocation', 'Vehicule', 'idVehicule', $idVehicule);
                        $nbLocation+=1; //Incrément du nombre de fois où le véhicule a été loué
                        $reqUpdateVehic = "UPDATE Vehicule SET nbLocation=? WHERE idVehicule=?";
                        $reponse = $bdd->prepare($reqUpdateVehic);
                        $reponse->execute(array($nbLocation, $idVehicule));
                        //Vérification de la réussite de la mise à jour
                        if($reponse->rowCount() > 0){
                            echo "Nombre de location du véhicule mis à jour / Succes.";
                        } 
                        else{
                            echo "Une erreur est survenue lors de la mise à jour du nombre de location / ";
                            return false;
                        }

                    } //End else if($dataId chauffeur)

                } //End if($idChauffeur!=-1)
                else{
                    $reqAfficheReserv = "SELECT cl.prenom AS prenomClient, cl.nom AS nomClient, cl.adresse, cl.email, marque, modele, immatriculation, CONCAT(re.prix, ' FCFA') AS prix, destination, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, re.statut FROM Clientele cl, Vehicule v, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur IS NULL AND idDisponibilite=re.idDate ORDER BY idReservation DESC LIMIT 0,1";
                    //Aucun chauffeur n'est choisi, on passe directement à l'insertion de la réservation dans la base de données
                    $idChauffeur = NULL;
                     //Récupération des infos de la réservtaion sans chauffeur pour besoin d'envoie de mail
                     $reqAfficheReserv = "SELECT cl.prenom AS prenomClient, cl.nom AS nomClient, cl.adresse, cl.email, marque, modele, immatriculation, CONCAT(re.prix, ' FCFA') AS prix, destination, re.statut, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin FROM Clientele cl, Vehicule v, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND idDisponibilite=re.idDate AND re.idChauffeur IS NULL ORDER BY idReservation DESC LIMIT 0,1";
                    goto insertReservation;
                } 

                $reponse->closeCursor();
            } //End else if ($dataId vehicule)
        } //End ajoutReservation()

        public static function modifierReservation($idReservation, $idClient, $idVehicule, $idChauffeur, $dateDebut, $dateFin, $statut, $destination){
            //Annulation de la réservation
            Reservation::changerStatutReservation($idReservation, 'Annulé');
            //Ajout d'une nouvelle
            Reservation::ajoutReservation($idReservation, $idVehicule, $idChauffeur, $idClient, $dateDebut, $dateFin, $destination, $statut);
            
        } //End modifierReservation()

        public static function sameDate($id, $debut, $fin){
            global $bdd;
            $requete = "SELECT dateDebut, dateFin FROM Disponibilite WHERE idDisponibilite=?";
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            if($data=$reponse->fetch()){
                if($data['dateDebut']==$debut && $data['dateFin']==$fin){
                    return true;
                }
                else{
                    return false;
                }
            }
            else{
                return false;
            }
        }


        public static function checkReserve($idReservation, $id, $nature, $dateDepart, $dateArrivee){
            global $bdd;
            $nameId = 'id'.$nature;
            $result=false; //Flag nous permettant de savoir si l'id indiqué correspond ou pas à un chauffeur/véhicule réservé
            //On récupère l'ensemble des id$nature des $nature qui ont été réservés à la période spécifiée
            $reqRecupIdDisponibilite = "SELECT DISTINCT $nameId FROM Reservation, Disponibilite WHERE dateDebut<=:dateFin AND dateFin>=:dateDebut AND statut='En cours' AND idDate=idDisponibilite AND idReservation!=:idReservation";
            $reponse = $bdd->prepare($reqRecupIdDisponibilite);
            $reponse->execute(array(
                'dateDebut' => $dateDepart, 
                'dateFin' => $dateArrivee,
                'idReservation' => $idReservation));
             //On vérifie si l'id du véhicule/chauffeur choisi fait ou non partie des $nature réservés à cette période
             while ($dataId=$reponse->fetch()){
                // $dataId contient les id de tous les $nature qui ont été réservés
                // Et chaque id est comparé avec l'id fourni  
                /*FRONT - Prise en compte du cas ou idChauffeur == null */    
                if($dataId[$nameId]==$id && $id!=null){
                    $result=true; //Alors le véhicule/chauffeur est réservé
                    break;
                }
              
            }
            return $result;
            
        } //End checkReserve()


        public static function supprimerReservation($id){
            global $bdd;
            $requete = 'DELETE FROM Reservation WHERE idReservation=?';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            //Vérification de la réussite de la suppréssion
            if($reponse->rowCount() > 0){
                echo "Succes. Réservation supprimée !";
            } 
            else{
                echo "Une erreur est survenue lors de la suppréssion de la réservation !";
                return false;
            }
            $reponse->closeCursor();

        } //End supprimerReservation($id)

        public static function changerStatutReservation($idReservation, $statut){
            global $bdd;
            if($statut != 'En cours' && $statut != 'Annulé' && $statut != 'Terminé'){
                echo "Statut non autorisé !";
                return false;
                
            } //End if
            else{
                if(Reservation::getStatut($idReservation)==$statut){
                    echo "Cette réservation est déjà annulée";
                    return true;
                }
                else{
                    $reqReserv = "UPDATE Reservation SET statut=? WHERE idReservation=?";
                    $reponse = $bdd->prepare($reqReserv);
                    $reponse->execute(array($statut, $idReservation));
                    //Vérification de la réussite de la mise à jour du statut
                    if($reponse->rowCount() > 0){
                        echo "Succes. Statut de la réservation mis à jour !";
                    } 
                    else{
                        echo "Une erreur est survenue lors de la mise à jour du statut de la réservation !";
                        return false;
                    }
                    $reponse->closeCursor();
                }   
                
            } //End first else
            
            
        } //End changerStatutReservation()

        public static function getNbJours($date1, $date2){
            //Changement du format de la date en yyyy-mm-dd
            $date1 = date("Y-m-d", strtotime($date1));
            $date2 = date("Y-m-d", strtotime($date2));
            //Conversion en secondes
            $date1 = strtotime($date1);
            $date2 = strtotime($date2);
            //Calcul de la différence entre les deux dates
            $diff = abs($date2 - $date1);
            $nbJours = round($diff / (60 * 60 * 24)); #Conversion en jours

            return intval($nbJours);
        } //End getNbJours(date1, date2)

        

        public static function getPrix($idVehicule){
            global $bdd;
            $reqPrix = "SELECT prix FROM Vehicule WHERE idVehicule=?";
            $reponse = $bdd->prepare($reqPrix);
            $reponse->execute(array($idVehicule));
            if($data = $reponse->fetch()){
                return intval($data['prix']);
            }
            else{
                echo "Aucun prix trouvé !";
                return -1;
            }

        } //End getPrix

        public static function getStatut($idReservation){
            global $bdd;
            $reqPrix = "SELECT statut FROM Reservation WHERE idReservation=?";
            $reponse = $bdd->prepare($reqPrix);
            $reponse->execute(array($idReservation));
            if($data = $reponse->fetch()){
                return $data['statut'];
            }
            else{
                echo "Aucun statut trouvé !";
                return -1;
            }

        } //End getPrix

        function check_in_range($dateDebut, $dateFin, $dateChoisi){
            // Convertion en timestamp
            $dateDebut = strtotime($dateDebut);
            $dateFin = strtotime($dateFin);
            $dateChoisi = strtotime($dateChoisi);

            //Vérifie si une date se trouve entre deux dates
            return (($dateChoisi >= $dateDebut) && ($dateChoisi <= $dateFin));
        }
      

        public static function calculPrix($idVehicule, $dateDepart, $dateArrivee){
            global $bdd;
            #On récupère le prix journalier du véhicule
            $prixJournalier = Reservation::getPrix($idVehicule);
            //Vérification si le véhicule est en promo ou pas
            $check = Promotion::checkPromo($idVehicule);
            if($check==-1){
                #Le véhicule n'est pas en promotion
                #On récupère le nombre de jours sur lequel le véhicule sera réservé
                $nbJours = Reservation::getNbJours($dateDepart, $dateArrivee);
                $nbJours+=1; //Inclusion du jour de la réservtaion
                #On calcul le montant à payer pour cette réservation
                $prix = $nbJours*$prixJournalier;
                return $prix;
            } //End if(checkPromo)
            else{
                #Le véhicule est en promotion
                #Récupération de la période de promotion
                $reqPromo = "SELECT dateDebut, dateFin FROM Disponibilite WHERE idDisponibilite=?";
                $reponse = $bdd->prepare($reqPromo);
                $reponse->execute(array($check));
                if($data = $reponse->fetch()){
                    $dateDebutPromo = $data['dateDebut'];
                    $dateFinPromo = $data['dateFin'];
                }
                else{
                    echo "Aucune promotion trouvée !";
                    return -1;
                }
                #Calcul du prix à payer en fonction de la période de promotion
                if(Reservation::check_in_range($dateDebutPromo, $dateFinPromo, $dateArrivee) && $dateDepart<=$dateDebutPromo){
                    #La période de promotion se trouve entre la date de début de la promo et la date de fin de la réservation
                    $nbJoursPromo = Reservation::getNbJours($dateDebutPromo, $dateArrivee);
                    $nbJoursPromo+=1;
                    $nbJourRestant = Reservation::getNbJours($dateDepart, $dateDebutPromo);
                    
                } //End if
                if(Reservation::check_in_range($dateDebutPromo, $dateFinPromo, $dateDepart) && Reservation::check_in_range($dateDebutPromo, $dateFinPromo, $dateArrivee)){
                    #La période de réservation se trouve entre la date de début et la date de fin de la promo
                    $nbJoursPromo = Reservation::getNbJours($dateDepart, $dateArrivee);
                    $nbJoursPromo+=1;
                    $nbJourRestant = 0; //Le client bénéficie de la promo durant toute la durée de la réservation

                } //End if
                if(Reservation::check_in_range($dateDebutPromo, $dateFinPromo, $dateDepart) && $dateArrivee>=$dateFinPromo){
                    #La période de promotion se trouve entre la date de début de la réservation et la date de fin de la promotion
                    $nbJoursPromo = Reservation::getNbJours($dateDepart, $dateFinPromo);
                    $nbJoursPromo+=1;
                    $nbJourRestant = Reservation::getNbJours($dateFinPromo, $dateArrivee);

                } //End if
                
                if(Reservation::check_in_range($dateDebutPromo, $dateFinPromo, $dateDepart)==false && Reservation::check_in_range($dateDebutPromo, $dateFinPromo, $dateArrivee)==false){
                    #La période de réservation est hors promotion
                    $nbJoursPromo = 0;
                    $nbJourRestant = Reservation::getNbJours($dateDepart, $dateArrivee);
                    $nbJourRestant+=1;

                }

            } //End else if(check)

            #On calcul le montant à payer pour cette réservation
            $taux = Promotion::getTaux($idVehicule); //Taux de réduction
            $prixPromoJournalier = $prixJournalier - (($prixJournalier*$taux)/100);
            $prixPromo = $nbJoursPromo*$prixPromoJournalier;
            $prix = $prixPromo + ($prixJournalier*$nbJourRestant);

            return $prix;
        } //End CalculPrix(idVehicule, dateDepart, dateArrivee)

        public static function returnId($nomID, $table, $attribut, $valeur){
            global $bdd;
            $requete = "SELECT $nomID FROM $table WHERE $attribut='$valeur'";
            $reponse = $bdd->query($requete);
            if ($data = $reponse->fetch()){
                $id = $data[$nomID];
                $reponse->closeCursor();
                return $id;
            }
            else{
                echo "Pas de $table trouvé(e) !";
                return false;
            }
        } //End returnId()

        public static function returnLastId($nomID, $table){
            global $bdd;
            $requete = "SELECT $nomID FROM $table ORDER BY $nomID DESC LIMIT 0,1";
            $reponse = $bdd->query($requete);
            if ($data = $reponse->fetch()){
                $id = $data[$nomID];
                $reponse->closeCursor();
                return $id;
            }
            else{
                echo "Pas de $table trouvé(e) !";
                return false;
            }
        } //End returnId()

        public static function returnData($nomID, $table, $attribut, $valeur){
            global $bdd;
            $requete = "SELECT $nomID FROM $table WHERE $attribut='$valeur'";
            $reponse = $bdd->query($requete);
            if ($data = $reponse->fetch()){
                $id = $data[$nomID];
                $reponse->closeCursor();
                return $id;
            }
            else{
                echo "$table choisi(e) non disponible !";
                return false;
            }
        } //End returnData()

        /**
         * Fonction d'envoi de mail avec intégration des headers.
         */
        public function envoieMail($to, $sujet, $titre, $message){
            $message = nl2br($message);
            
            // Mise en forme du méssage
            $msg = "
            <html>
            <head>
                <title>$titre</title>
            </head>
            <body>
                <p>
                    <h1> Résumé : </h1> <br />
                    $message
                </p>
            </body>
            </html>
            ";
            
            // Pour envoyer un mail HTML, l en-tête Content-type doit être défini
            $headers = "MIME-Version: 1.0" . "\n";
            $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
            
            // En-têtes additionnels
            $headers .= 'From: Sen\'Drive Solutions <no-reply@sendrive.com>' . "\r\n";
            
            // Envoie
            $resultat = mail($to, $sujet, $msg, $headers);
            if($resultat)
                echo "Mail envoyé à ".$to."\n";
            else
                echo "Erreur dans l'envoi du mail\n";
        } //End envoieMail
    } //End Reservation
    
  