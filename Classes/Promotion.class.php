<?php
    class Promotion{
        //Attributs
        private $idPromo;
        private $idVehicule;
        private $idDate;
        private $nom;
        private $taux;
        private $statut;

        //Fonctions
        public static function affichePromo($statut){
            global $bdd;
            if(empty($statut)){
                $reqAffichePromo = "SELECT DISTINCT idPromo, nom, CONCAT(taux, ' %') AS taux, marque, modele, immatriculation, dateDebut, dateFin, statut FROM Promotion p, Vehicule v, Disponibilite, Marque ma, Modele mo WHERE idDate=idDisponibilite AND p.idVehicule=v.idVehicule AND v.idMarque=ma.idMarque AND v.idModele=mo.idModele";
                $reponse = $bdd->query($reqAffichePromo);
            }
            else{
                $statutAutorise = array('En cours', 'Annulé', 'Terminé');
                if(in_array($statut, $statutAutorise)){
                    $reqAffichePromo = "SELECT DISTINCT idPromo, nom, CONCAT(taux, ' %') AS taux, marque, modele, immatriculation, dateDebut, dateFin, statut FROM Promotion p, Vehicule v, Disponibilite, Marque ma, Modele mo WHERE idDate=idDisponibilite AND p.idVehicule=v.idVehicule AND v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND statut=?";
                    $reponse = $bdd->prepare($reqAffichePromo);
                    $reponse->execute(array($statut));
                }
                else{
                    echo "Statut non autorisé !";
                    return false;
                }
                
            } //End else empty
            if($promo = $reponse->fetchAll()){
                $promo = json_encode($promo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                return $promo;
            }
            else{
                echo "Aucune promotion trouvée !";
                return false;
            }

        } //End affichePromo(statut)

        public static function affichePromos($dateDebut, $dateFin){
            global $bdd;
            //Changement du format de la date en yyyy-mm-dd
            $dateDebut = date("Y-m-d", strtotime($dateDebut));
            $dateFin = date("Y-m-d", strtotime($dateFin));
            $reqAffichePromo = "SELECT DISTINCT idPromo, nom, CONCAT(taux, ' %') AS taux, marque, modele, immatriculation, dateDebut, dateFin, statut FROM Promotion p, Vehicule v, Disponibilite, Marque ma, Modele mo WHERE idDate=idDisponibilite AND p.idVehicule=v.idVehicule AND v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND dateDebut<=? AND dateFin>=?";
             //Vérification de la conformité de la période
             if ($dateDebut >= $dateFin){
                echo "La date d'arrivée ne peut être supérieure à la date de départ !";
                return false;
            }
            else{
                $reponse = $bdd->prepare($reqAffichePromo);
                $reponse->execute(array($dateDebut, $dateFin));
                if($promo = $reponse->fetchAll()){
                    $promo = json_encode($promo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    return $promo;
                }
                else{
                    echo "Aucune promotion trouvée à cette période!";
                    return false;
                }
            } //End else(dateDebut, dateFin)

        } //End affichePromo(dateDebut, dateFin)

        /* FRONT - Besoin liste de toutes les promos pour la gestion */
        public static function afficheToutesPromos(){
            global $bdd;
            $reqAffichePromo = "SELECT DISTINCT idPromo, nom, CONCAT(taux, ' %') AS taux, marque, modele, immatriculation, dateDebut, dateFin, statut FROM Promotion p, Vehicule v, Disponibilite, Marque ma, Modele mo WHERE idDate=idDisponibilite AND p.idVehicule=v.idVehicule AND v.idMarque=ma.idMarque AND v.idModele=mo.idModele";

                $reponse = $bdd->prepare($reqAffichePromo);
                $reponse->execute(array());
                if($promo = $reponse->fetchAll()){
                    $promo = json_encode($promo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    return $promo;
                }
                else{
                    echo "Aucune promotion trouvée !";
                    return false;
                }

        } //End affichePromo(dateDebut, dateFin)

        public static function ajoutPromo($idVehicule, $nom, $taux, $dateDebut, $dateFin){
            global $bdd;
            //Vérification de l'élligibilité du taux
            if($taux>0 && $taux<100){
                //Changement du format de la date en yyyy-mm-dd
                $dateDebut = date("Y-m-d", strtotime($dateDebut));
                $dateFin = date("Y-m-d", strtotime($dateFin));
                //Vérification de la conformité de la période
                if ($dateDebut >= $dateFin){
                    echo "La date d'arrivée ne peut être supérieure à la date de départ !";
                    return false;
                }
                else{
                    //Ajout des dates dans la base
                    $idDate = Promotion::ajoutDate($dateDebut, $dateFin);
                    $statut = 'En cours';
                    $reqAjoutPromo = "INSERT INTO Promotion(idVehicule, nom, taux, idDate, statut) VALUES (:idVehicule, :nom, :taux, :idDate, :statut)";
                    $reponse = $bdd->prepare($reqAjoutPromo);
                    $reponse->execute(array(
                        'idVehicule' => $idVehicule,
                        'nom' => $nom,
                        'taux' => $taux,
                        'idDate' => $idDate,
                        'statut' => $statut
                    ));
                    if($reponse->rowCount() > 0){
                        echo "Promotion ajoutée";
                        return true;
                    }
                    else{
                        echo "Une erreur est survenue lors de l'ajout de la promotion";
                        return false;
                    }

                } //End else(dateDebut, dateFin)

            } //End if(taux)
            else{
                echo "Le taux doit être compris entre 0 et 100.";
                return false;
            }
            
        } //End ajoutPromo

        public static function modifierPromo($idPromo, $idVehicule, $nom, $taux, $statut, $dateDebut, $dateFin){
            global $bdd;
            //Vérification du statut
            $statut_autorises = array('En cours', 'Annulé', 'Terminé');
            if(!in_array($statut, $statut_autorises)){
                echo "Statut non autorisé. Les statuts autorisés sont: 'En cours', 'Annulé' et 'Terminé'";
                return false;
            }
            //Vérification de l'élligibilité du taux
            if($taux>0 && $taux<100){
                //Changement du format de la date en yyyy-mm-dd
                $dateDebut = date("Y-m-d", strtotime($dateDebut));
                $dateFin = date("Y-m-d", strtotime($dateFin));
                //Vérification de la conformité de la période
                if ($dateDebut >= $dateFin){
                    echo "La date d'arrivée ne peut être supérieure à la date de départ !";
                    return false;
                }
                else{
                    //Ajout des dates dans la base
                    if(Promotion::sameDate($idPromo, $dateDebut, $dateFin)){
                        echo "der";
                        $idDate = Promotion::modifierDate($idPromo, $dateDebut, $dateFin);
                    }
                    $reqModifPromo = "UPDATE Promotion SET idVehicule=:idVehicule, nom=:nom, taux=:taux, statut=:statut WHERE idPromo=:idPromo";
                    $reponse = $bdd->prepare($reqModifPromo);
                    $reponse->execute(array(
                        'idVehicule' => $idVehicule,
                        'nom' => $nom,
                        'taux' => $taux,
                        'idPromo' => $idPromo,
                        'statut' => $statut
                    ));
                    if($reponse->rowCount() > 0){
                        echo "Promotion mise à jour";
                        return true;
                    }
                    else{
                        echo "Une erreur est survenue lors de la mise à jour de la promotion";
                        return false;
                    }

                } //End else(dateDebut, dateFin)

            } //End if(taux)
            else{
                echo "Le taux doit être compris entre 0 et 100.";
                return false;
            }
            
        } //End modifierPromo

        public static function sameDate($id, $debut, $fin){
            global $bdd;
            $requete = "SELECT dateDebut, dateFin FROM Disponibilite, Promotion WHERE idDate=idDisponibilite AND idPromo=? AND dateDebut=? AND dateFin=?";
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id, $debut, $fin));
            if($data=$reponse->fetch()){
                return true;
            }
            else{
                return false;
            }
        }

        public static function supprimerPromo($id){
            global $bdd;
            $requete = 'DELETE FROM Promotion WHERE idPromo=?';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            //Vérification de la réussite de la suppréssion de la promotion
            if($reponse->rowCount() > 0){
                echo "Promotion supprimée !";
            } 
            else{
                echo "Une erreur est survenue lors de la suppréssion de la promotion !";
                return false;
            }
            $reponse->closeCursor();

        } //End supprimerPromo($id)


        public static function ajoutDate($dateDebut, $dateFin){
            global $bdd;
            //Ajout des dates dans la base
            $requete = 'INSERT INTO Disponibilite (dateDebut, dateFin) VALUES(:dateDebut, :dateFin)';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array(
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin
            ));
            //Vérification de la réussite de l'ajout
            if($reponse->rowCount() > 0){
                echo "Dates ajoutées / ";
                $reqLastId = "SELECT idDisponibilite FROM disponibilite ORDER BY idDisponibilite DESC LIMIT 0, 1";
                $reponse = $bdd->query($reqLastId);
                if($data = $reponse->fetch()){
                    $lastId = $data['idDisponibilite'];
                    return $lastId; 
                }
                else{
                    echo "Impossible de récupérer l'Id de la dernière date / ";
                    return false;
                }
            } //End rowCount 
            else{
                echo "Une erreur est survenue lors de l'ajout des dates / ";
                return false;
            }
            $reponse->closeCursor();
        } //End ajoutDate

        public static function modifierDate($idPromo, $dateDebut, $dateFin){
            global $bdd;
            //Modification des dates de la base
            $idDate = Promotion::returnId('idDate', 'Promotion', 'idPromo', $idPromo);
            $reqIdDisponibilite = "UPDATE Disponibilite SET dateDebut=?, dateFin=? WHERE idDisponibilite=?";
            $reponse = $bdd->prepare($reqIdDisponibilite);
            $reponse->execute(array($dateDebut, $dateFin, $idDate));
            //Vérification de la réussite de la mise à jour des dates
            if($reponse->rowCount() > 0){
            echo "Dates mises à jour / ";
            $reponse->closeCursor();
            return true;
            } 
            else{
                echo "Une erreur est survenue lors de la mise à jour des dates !";
                return false;
            }
        
        } //End modifierDate

        public static function checkPromo($idVehicule){
            //Fonction qui vérifie si un véhicule est en promotion ou pas
            global $bdd;
            $result=-1;
            $reqCheckPromo = "SELECT idVehicule, idDate FROM Promotion";
            $reponse = $bdd->query($reqCheckPromo);
            while($data = $reponse->fetch()){
                if($idVehicule==$data['idVehicule']){
                    $result=$data['idDate'];
                    break;
                }
            } //End while
            $reponse->closeCursor();
            return $result;
        } //End checkPromo(idVehicule)

        public static function getTaux($idVehicule){
            //Fonction qui récupère le taux de promotion d'un véhicule
            global $bdd;
            $reqTaux = "SELECT taux FROM Promotion WHERE idVehicule=?";
            $reponse = $bdd->prepare($reqTaux);
            $reponse->execute(array($idVehicule));
            if($data = $reponse->fetch()){
                return intval($data['taux']);
            }
            else{
                echo "Aucun taux trouvé !";
                return -1;
            }
            $reponse->closeCursor();
        } //End getTaux(idVehicule)

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
                echo "$table choisi(e) non disponible !";
                return false;
            }
        } //End returnId()

    } //End Promotion