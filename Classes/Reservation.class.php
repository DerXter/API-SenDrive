<?php

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
        public static function afficheReservationSelonChauffeur($choix){
            global $bdd;
            $statutActuel = Reservation::returnData('statut', 'vehicule', 'idVehicule', $idVehicule);
            if ($statutActuel=='Réservé'){
                echo "Ce véhicule a déjà été réservé !";
                return false;
            }
            else{
                //Mise à jour des statuts
                $statutReservation = 'En cours';
                $statutVehicule = 'Réservé';
                if($idChauffeur == 'NULL'){        
                    $statutChauffeur = 'Libre';
                }
                else{
                    $statutChauffeur = 'Réservé';
                }
            
                //Récupération de l'Id du dernier client entré
                $reqLastIdClient = 'SELECT idClient FROM Clientele ORDER BY idClient DESC LIMIT 0,1';
                $reponse = $bdd->query($reqLastIdClient);
                $data = $reponse->fetch();
                $lastIdClient = $data['idClient'];
                //Ajout des dates dans la base            
                $reqAjoutDates = 'INSERT INTO Disponibilite (dateDebut, dateFin) VALUES (DATE :dateDebut, :dateFin)';
                $reponse = $bdd->prepare($reqAjoutDates);
                $reponse->execute(array(
                    'dateDebut' => $dateDepart,
                    'dateFin' => $dateArrivee
                ));
                //Vérification de la réussite de l'ajout
                if($reponse->rowCount() > 0){
                    echo "Dates ajoutées !";
                } 
                else{
                    echo "Une erreur est survenue lors de l'ajout des dates !";
                }
            //Avec chauffeur
            if($choix=='avec'){
                $reqAfficheReserv = "SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, cl.email, marque, modele, immatriculation, ch.prenom AS prenomChauffeur, ch.nom AS nomChauffeur, destination, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, statut FROM Clientele cl, Vehicule v, Chauffeur ch, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur=ch.idChauffeur AND idDisponibilite=re.idDate";
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
                $reqAfficheReserv = "SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, cl.email, marque, modele, immatriculation, destination, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, statut FROM Clientele cl, Vehicule v, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur IS NULL AND idDisponibilite=re.idDate";
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
            }
            
        } //End afficheReservations(choix)

        public static function afficheReservationSelonStatut($statut){
            global $bdd;
            $statut_autorises = array('En cours', 'Annulé', 'Terminé');
            if(in_array($statut, $statut_autorises)){
                $reqAfficheReserv = "SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, cl.email, marque, modele, immatriculation, ch.prenom AS prenomChauffeur, ch.nom AS nomChauffeur, destination, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin FROM Clientele cl, Vehicule v, Chauffeur ch, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur=ch.idChauffeur AND idDisponibilite=re.idDate AND statut=?";
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

        public static function ajoutReservation($idVehicule, $idChauffeur, $idClient, $dateDepart, $dateArrivee){
            global $bdd;
            //Ajustement du format des dates
            $dateDepart = date("Y-m-d", strtotime($dateDepart));
            $dateArrivee = date("Y-m-d", strtotime($dateArrivee));
            //On vérifie si l'id du véhicule choisi fait ou non partie des véhicules réservés à cette période
            if (Reservation::checkReserve($idVehicule, 'vehicule', $dateDepart, $dateArrivee)){
                // Le véhicule n'est pas disponible
                echo "Ce véhicule a déjà été réservé à cette période !";
                return false;
            }
            
            else{
                //Le véhicule choisi est disponible
                if($idChauffeur != 'NULL'){   
                    //On vérifie si l'id du chauffeur choisi fait ou non partie des chauffeurs réservés à cette période
                    if (Reservation::checkReserve($idChauffeur, 'chauffeur', $dateDepart, $dateArrivee)){
                        echo "Ce chauffeur a déjà été réservé à cette période !";
                        return false;
                        }    
                    
                    else{
                        #Le chauffeur choisi est disponible
                        //Mise à jour du statut de la réservation
    insertReservation:  $statutReservation = 'En cours';
                        if($idClient != 'NULL'){
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
                            echo "Une erreur est survenue lors de l'ajout des dates / ";
                            return false;
                        }
                        //Récupération de l'Id de la dernière date entrée
                        $lastIdDisponibilite = Reservation::returnLastId('idDisponibilite', 'Disponibilite');
                        #Cette deuxiéme vérification aura du sens quand l'éxécution du programme sera positionnée ici par le goto insertReservation
                        #se trouvant à la fin de cette fonction. Dans ce cas, on se retrouve dans le bloc 'if($idChauffeur!='NULL')' alors que $idChauffeur vaut 'NULL'
                        if ($idChauffeur != 'NULL'){
                            //Ajout de la reservation avec chauffeur
                            $reqAjoutReserv = 'INSERT INTO Reservation (idClient, idVehicule, idChauffeur, idDate, statut) VALUES (:idClient, :idVehicule, :idChauffeur, :idDate, :statut)';
                            $reponse = $bdd->prepare($reqAjoutReserv);
                            $reponse->execute(array(
                                'idClient' => $clientID,
                                'idDate' => $lastIdDisponibilite,
                                'idVehicule' => $idVehicule,
                                'idChauffeur' => $idChauffeur,
                                'statut' => $statutReservation
                            ));
                            //Vérification de la réussite de l'ajout
                            if($reponse->rowCount() > 0){
                                echo "Réservation ajoutée / ";
                            } 
                            else{
                                echo "Une erreur est survenue lors de l'ajout de la reservation / ";
                                return false;
                            }
                        } //End second if($idChauffeur!='NULL')
                        else{
                            //Ajout de la reservation sans chauffeur
                            $reqAjoutReserv = 'INSERT INTO Reservation (idClient, idVehicule, idDate, statut) VALUES (:idClient, :idVehicule, :idDate, :statut)';
                            $reponse = $bdd->prepare($reqAjoutReserv);
                            $reponse->execute(array(
                                'idClient' => $clientID,
                                'idDate' => $lastIdDisponibilite,
                                'idVehicule' => $idVehicule,
                                'statut' => $statutReservation
                            ));
                            //Vérification de la réussite de l'ajout
                            if($reponse->rowCount() > 0){
                                echo "Réservation ajoutée / ";
                            } 
                            else{
                                echo "Une erreur est survenue lors de l'ajout de la reservation / ";
                                return false;
                            }
                        } //End else second if($idChauffeur!='NULL')
                        //Mise à jour du nombre de location du véhicule choisi
                        $nbLocation = Reservation::returnData('nbLocation', 'Vehicule', 'idVehicule', $idVehicule);
                        $nbLocation+=1; //Incrément du nombre de fois où le véhicule a été loué
                        $reqUpdateVehic = "UPDATE Vehicule SET nbLocation=? WHERE idVehicule=?";
                        $reponse = $bdd->prepare($reqUpdateVehic);
                        $reponse->execute(array($nbLocation, $idVehicule));
                        //Vérification de la réussite de la mise à jour
                        if($reponse->rowCount() > 0){
                            echo "Nombre de location du véhicule mis à jour / ";
                        } 
                        else{
                            echo "Une erreur est survenue lors de la mise à jour du nombre de location / ";
                            return false;
                        }

                    } //End else if($dataId chauffeur)

                } //End if($idChauffeur!='NULL')
                else{
                    //Aucun chauffeur n'est choisi, on passe directement à l'insertion de la réservation dans la base de données
                    goto insertReservation;
                } 
            

                $reponse->closeCursor();
            } //End else if ($dataId vehicule)
        } //End ajoutReservation()

        public static function modifierReservation($idReservation, $idClient, $idVehicule, $idChauffeur, $dateDebut, $dateFin, $statut){
            global $bdd;
            //Vérification du statut
            $statut_autorises = array('En cours', 'Annulé', 'Terminé');
            if(!in_array($statut, $statut_autorises)){
                echo "Statut non autorisé. Les statuts autorisés sont: 'En cours', 'Annulé' et 'Terminé'";
                return false;
            }
            //Ajustement du format des dates
            $dateDebut = date("Y-m-d", strtotime($dateDebut));
            $dateFin = date("Y-m-d", strtotime($dateFin));

            //On vérifie si l'id du véhicule choisi fait ou non partie des véhicules réservés à cette période
            if (Reservation::checkReserve($idVehicule, 'vehicule', $dateDebut, $dateFin)){
                //Si le véhicule est réservé
                    echo "Ce véhicule a déjà été réservé à cette période !";
                    return false;
            }
            //Le véhicule est disponible, on passe à la vérification de la disponibilité du chauffeur
 
            else{
            //Le véhicule choisi est disponible
verifChauffeur: if($idChauffeur != 'NULL'){   
                    //On vérifie si l'id du chauffeur choisi fait ou non partie des chauffeurs réservés à cette période
                    if (Reservation::checkReserve($idChauffeur, 'chauffeur', $dateDebut, $dateFin)){
                        # Le chauffeur est réservé
                        echo "Ce chauffeur a déjà été réservé à cette période !";
                        return false;
                    }    
                    
                    else{
                        #Le chauffeur choisi est disponible
                        //Modification des dates de la base
    updateReservation:  $idDate = Reservation::returnId('idDate', 'Reservation', 'idReservation', $idReservation);
                        $reqIdDisponibilite = "UPDATE Disponibilite SET dateDebut=?, dateFin=? WHERE idDisponibilite=?";
                        $reponse = $bdd->prepare($reqIdDisponibilite);
                        $reponse->execute(array($dateDebut, $dateFin, $idDate));
                        //Vérification de la réussite de l'ajout
                        if($reponse->rowCount() > 0){
                            echo "Dates mises à jour / ";
                        } 
                        else{
                            echo "Une erreur est survenue lors de la mise à jour des dates !";
                            return false;
                        }
                        //Mise à jour de la réservation
                        $requete = 'UPDATE Reservation SET idVehicule=:idVehicule, idClient=:idClient, idChauffeur=:idChauffeur, idDate=:idDate, statut=:statut WHERE idReservation=:idReservation';
                        $reponse = $bdd->prepare($requete);
                        $reponse->execute(array(
                            'idReservation' => $idReservation,
                            'idVehicule' => $idVehicule,
                            'idClient' => $idClient,
                            'idDate' => $idDate,
                            'idChauffeur' => $idChauffeur,
                            'statut' => $statut
                            
                        ));
                        //Vérification de la réussite de la mise à jour
                        if($reponse->rowCount() > 0){
                            echo "Réservation mise à jour !";
                        } 
                        else{
                            echo "Une erreur est survenue lors de la modification de la réservation !";
                            return false;
                        }

                    } //End else if($dataId chauffeur)

                } //End if($idChauffeur!='NULL')
                else{
                    //Aucun chauffeur n'est choisi, on passe directement à l'insertion de la réservation dans la base de données
                    goto updateReservation;
                } 
            

                $reponse->closeCursor();
            } //End else if ($dataId vehicule)
            
        } //End modifierReservation()

        public static function checkReserve($id, $nature, $dateDepart, $dateArrivee){
            global $bdd;
            $nameId = 'id'.$nature;
            $result=false; //Flag nous permettant de savoir si l'id indiqué correspond ou pas à un chauffeur/véhicule réservé
            //On récupère l'ensemble des id$nature des $nature qui ont été réservés à la période spécifiée
            $reqRecupIdDisponibilite = "SELECT DISTINCT $nameId FROM Reservation, Disponibilite WHERE dateDebut<=:dateFin AND dateFin>=:dateDebut AND statut='En cours' AND idDate=idDisponibilite";
            $reponse = $bdd->prepare($reqRecupIdDisponibilite);
            $reponse->execute(array(
                'dateDebut' => $dateDepart, 
                'dateFin' => $dateArrivee));
             //On vérifie si l'id du véhicule/chauffeur choisi fait ou non partie des $nature réservés à cette période
             while ($dataId=$reponse->fetch()){
                // $dataId contient les id de tous les $nature qui ont été réservés
                // Et chaque id est comparé avec l'id fourni      
                if($dataId[$nameId]==$id){
                    $result=true; //Alors le véhicule/chauffeur est réservé
                    break;
                }
              
            }
            return $result==true ? true : false;
            
        } //End checkReserve()


        public static function supprimerReservation($id){
            global $bdd;
            $requete = 'DELETE FROM Reservation WHERE idReservation=?';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            //Vérification de la réussite de la suppréssion
            if($reponse->rowCount() > 0){
                echo "Réservation supprimée !";
            } 
            else{
                echo "Une erreur est survenue lors de la suppréssion de la réservation !";
                return false;
            }
            $reponse->closeCursor();

        } //End supprimerProprio($id)

        public static function afficheReservations(){
            global $bdd;
            $reqAfficheReserv = 'SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, cl.email, marque, modele, immatriculation, v.cheminPhoto, ch.prenom AS prenomChauffeur, ch.nom AS nomChauffeur, destination, re.statut FROM Clientele cl, Vehicule v, Chauffeur ch, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur=ch.idChauffeur AND idDisponibilite=re.idDate';
            $reponse = $bdd->query($reqAfficheReserv);
            if ($reservations = $reponse->fetchAll()){
                $reservations = json_encode($reservations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $reponse->closeCursor();

                return $reservations;
            }
            else {
                echo "Aucune reservation trouvée !";
                return false;
            }
            
        } //End afficheReservations()

        public static function changerStatutReservation($idReservation, $statut){
            global $bdd;
            $reqAnnulleReserv = "UPDATE Reservation SET statut=? WHERE idReservation=?";
            $reponse = $bdd->prepare($reqAnnulleReserv);
            $reponse->execute(array($statut, $idReservation));
            //Vérification de la réussite de la mise à jour du statut
            if($reponse->rowCount() > 0){
                echo "Statut de la réservation mis à jour !";
            } 
            else{
                echo "Une erreur est survenue lors de la mise à jour du statut de la réservation !";
                return false;
            }
            $reponse->closeCursor();
            
        } //End annulerReservation()
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
    } //End Reservation
    
  