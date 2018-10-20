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
        public static function afficheReservations($choix){
            global $bdd;
            //Avec chauffeur
            if($choix=='avec'){
                $reqAfficheReserv = "SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, cl.email, marque, modele, immatriculation, v.cheminPhoto, CONCAT(re.prix, ' FCFA') AS prix, ch.prenom AS prenomChauffeur, ch.nom AS nomChauffeur, destination, re.statut, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin FROM Clientele cl, Vehicule v, Chauffeur ch, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur=ch.idChauffeur AND idDisponibilite=re.idDate";
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
                $reqAfficheReserv = "SELECT idReservation, cl.prenom AS prenomClient, cl.nom AS nomClient, cl.email, marque, modele, immatriculation,CONCAT(re.prix, ' FCFA') AS prix, v.cheminPhoto, ch.prenom AS prenomChauffeur, ch.nom AS nomChauffeur, destination, re.statut, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin FROM Clientele cl, Vehicule v, Chauffeur ch, Reservation re, Marque ma, Modele mo, Disponibilite where cl.idClient=re.idClient AND re.idVehicule=v.idVehicule AND ma.idMarque=v.idMarque AND mo.idModele=v.idModele AND re.idChauffeur IS NULL AND idDisponibilite=re.idDate";

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


        public static function ajoutReservation($idVehicule, $idChauffeur, $idClient, $dateDepart, $dateArrivee, $destination){
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
                //On calcule le montant que va couter la reservation
                $prix = Reservation::calculPrix($idVehicule, $dateDepart, $dateArrivee);

                if($idChauffeur != -1){   
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
                        } 
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
                            echo "Nombre de location du véhicule mis à jour / ";
                        } 
                        else{
                            echo "Une erreur est survenue lors de la mise à jour du nombre de location / ";
                            return false;
                        }

                    } //End else if($dataId chauffeur)

                } //End if($idChauffeur!=-1)
                else{
                    //Aucun chauffeur n'est choisi, on passe directement à l'insertion de la réservation dans la base de données
                    $idChauffeur = NULL;
                    goto insertReservation;
                } 
            

                $reponse->closeCursor();
            } //End else if ($dataId vehicule)
        } //End ajoutReservation()

        public static function modifierReservation($idReservation, $idClient, $idVehicule, $idChauffeur, $dateDebut, $dateFin, $statut, $prix, $destination){
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
verifChauffeur: if($idChauffeur != -1){   
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
                        $requete = 'UPDATE Reservation SET idVehicule=:idVehicule, idClient=:idClient, idChauffeur=:idChauffeur, idDate=:idDate, statut=:statut, prix=:prix, destination=:destination WHERE idReservation=:idReservation';
                        $reponse = $bdd->prepare($requete);
                        $reponse->execute(array(
                            'idReservation' => $idReservation,
                            'idVehicule' => $idVehicule,
                            'idClient' => $idClient,
                            'idDate' => $idDate,
                            'idChauffeur' => $idChauffeur,
                            'statut' => $statut,
                            'prix' => $prix,
                            'destination' => $destination
                            
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

                } //End if($idChauffeur!=-1)
                else{
                    //Aucun chauffeur n'est choisi, on passe directement à l'insertion de la réservation dans la base de données
                    $idChauffeur=NULL;
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

        } //End supprimerReservation($id)

        public static function changerStatutReservation($idReservation, $statut){
            global $bdd;
            if($statut != 'En cours' && $statut != 'Annulé' && $statut != 'Terminé'){
                echo "Statut non autorisé !";
                return false;
                
            } //End if
            else{
                $reqReserv = "UPDATE Reservation SET statut=? WHERE idReservation=?";
                $reponse = $bdd->prepare($reqReserv);
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
    } //End Reservation
    
  