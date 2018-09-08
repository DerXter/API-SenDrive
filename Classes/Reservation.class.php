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
            $data = $reponse->fetch();

            //Affectation des données de la base à l'objet
            $this->idReservation = $id;
            $this->idClient = $data['idClient'];
            $this->idVehicule = $data['idVehicule'];
            $this->idChauffeur = $data['idChauffeur'];
            $this->idDate = $data['idDate'];
            $this->destination = $data['destination'];

            $reponse->closeCursor();
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
        public static function returnId($nomID, $table, $attribut, $valeur){
            global $bdd;
            $requete = "SELECT $nomID FROM $table WHERE $attribut='$valeur'";
            $reponse = $bdd->query($requete);
            $data = $reponse->fetch();
            $id = $data[$nomID];
            $reponse->closeCursor();
            return $id;
        } //End returnId()

        public static function ajoutReservation($idVehicule, $idChauffeur, $dateDepart, $dateArrivee){
            global $bdd;
            $statutReservation = 'En cours';
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

            $reponse->closeCursor();
        } //End ajoutReservation()

        public static function afficheReservations(){
            global $bdd;
            $reqAfficheReserv = 'SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, marque, modele, immatriculation, destination, ch.prenom AS prenomChauffeur, ch.nom AS nomChauffeur, dateDebut, dateFin, statut FROM Clientele cl, Vehicule v, Chauffeur ch, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur=ch.idChauffeur AND idDisponibilite=re.idDate';
            $reponse = $bdd->query($reqAfficheReserv);
            $reservations = $reponse->fetchAll();
            $reservations = json_encode($reservations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $reponse->closeCursor();

            return $reservations;
        } //End afficheReservations()

        public static function changerStatutReservation($idReservation, $statut){
            global $bdd;
            $reqAnnulleReserv = "UPDATE Reservation SET statut=? WHERE idReservation=?";
            $reponse = $bdd->prepare($reqAnnulleReserv);
            $reponse->execute(array($statut, $idReservation));
            $reponse->closeCursor();
            
        } //End annulerReservation()
    } //End Reservation
    
  