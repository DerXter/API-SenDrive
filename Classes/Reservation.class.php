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
        public static function ajoutReservation($idVehicule, $idChauffeur, $dateDepart, $dateArrivee){
            global $bdd;
            $statutActuel = Reservation::returnData('statut', 'Vehicule', 'idVehicule', $idVehicule);
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
                $reqAjoutDates = 'INSERT INTO Disponibilite (dateDebut, dateFin) VALUES (:dateDebut, :dateFin)';
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
                    return false;
                }
                //Récupération de l'Id de la dernière date entrée
                $reqLastIdDate = 'SELECT idDisponibilite FROM Disponibilite ORDER BY idDisponibilite DESC LIMIT 0,1';
                $reponse = $bdd->query($reqLastIdDate);
                $data = $reponse->fetch();
                $lastIdDisponibilite = $data['idDisponibilite'];
                //Ajout de la reservation
                $reqAjoutReserv = 'INSERT INTO Reservation (idClient, idVehicule, idChauffeur, idDate, statut) VALUES (:idClient, :idVehicule, :idChauffeur, :idDate, :statut)';
                $reponse = $bdd->prepare($reqAjoutReserv);
                $reponse->execute(array(
                    'idClient' => $lastIdClient,
                    'idDate' => $lastIdDisponibilite,
                    'idVehicule' => $idVehicule,
                    'idChauffeur' => $idChauffeur,
                    'statut' => $statutReservation
                ));
                //Vérification de la réussite de l'ajout
                if($reponse->rowCount() > 0){
                    echo "Réservation ajoutée !";
                } 
                else{
                    echo "Une erreur est survenue lors de l'ajout de la reservation !";
                    return false;
                }
                //Mise à jour du nombre de location et du statut du véhicule reservé
                $nbLocation = Reservation::returnData('nbLocation', 'Vehicule', 'idVehicule', $idVehicule);
                $nbLocation+=1; //Incrément du nombre de fois où le véhicule a été loué
                $reqUpdateVehic = "UPDATE Vehicule SET nbLocation=?, statut=? WHERE idVehicule=?";
                $reponse = $bdd->prepare($reqUpdateVehic);
                $reponse->execute(array($nbLocation, $statutVehicule, $idVehicule));
                //Vérification de la réussite de la mise à jour
                if($reponse->rowCount() > 0){
                    echo "Nombre de location du véhicule mis à jour !";
                } 
                else{
                    echo "Une erreur est survenue lors de la mise à jour du nombre de location !";
                    return false;
                }
                //Mise à jour du statut du chauffeur
                if($idChauffeur!='NULL'){
                    $reqUpdateChauff = "UPDATE Chauffeur SET statut=$statutChauffeur WHERE idChauffeur=?";
                    $reponse = $bdd->prepare($reqUpdateChauff);
                    $reponse->execute(array($idChauffeur));
                    //Vérification de la réussite de la mise à jour du statut du chauffeur
                    if($reponse->rowCount() > 0){
                        echo "Statut chauffeur mis à jour !";
                    } 
                    else{
                        echo "Une erreur est survenue lors de la mise à jour du statut du chauffeur !";
                        return false;
                    }
                } //End if(chauffeur)
            

                $reponse->closeCursor();
            } //End else (statutActuel)
        } //End ajoutReservation()

        public static function modifierReservation($idReservation, $idClient, $idVehicule, $idChauffeur, $dateDebut, $dateFin, $statut){
            global $bdd;
            //Ajustement du format des dates
            $dateDebut = date("Y-m-d", strtotime($dateDebut));
            $dateFin = date("Y-m-d", strtotime($dateFin));
            //Vérification de la conformité de la période
            if ($dateDebut >= $dateFin){
                echo "La date d'arrivée ne peut être supérieure à la date de départ !";
                return false;
            }
            else{  
                //Modification des dates de la base
                $idDate = Reservation::returnId('idDate', 'Reservation', 'idReservation', $idReservation);
                $reqIdDisponibilite = "UPDATE Disponibilite SET dateDebut=?, dateFin=? WHERE idDisponibilite=?";
                $reponse = $bdd->prepare($reqIdDisponibilite);
                $reponse->execute(array($dateDebut, $dateFin, $idDate));
                //Vérification de la réussite de l'ajout
                if($reponse->rowCount() > 0){
                    echo "Dates mises à jour !";
                } 
                else{
                    echo "Une erreur est survenue lors de la mise à jour des dates !";
                    return false;
                }

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
                
                $reponse->closeCursor();
            } //End else
            
        } //End modifierReservation()

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
            $reqAfficheReserv = 'SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, cl.email, marque, modele, immatriculation, ch.prenom AS prenomChauffeur, ch.nom AS nomChauffeur, destination, statut FROM Clientele cl, Vehicule v, Chauffeur ch, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur=ch.idChauffeur AND idDisponibilite=re.idDate';
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
    
  