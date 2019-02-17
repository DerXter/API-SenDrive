<?php
    class Navette{
        //Attributs
        private $idNavette;
        private $idClient;
        private $idVehicule;
        private $idChauffeur;
        private $idHoraire;
        private $date;
        private $destination;
        private $statut;

        //Fonctions
        public static function ajoutNavette($idClient, $idVehicule, $idChauffeur, $date, $destination, $heureDebut, $heureFin){
            global $bdd;
            //Formalisation de la date
            $date = date("Y-m-d", strtotime($date));
            //Vérification de la conformité de la tranche horaire
            if($heureDebut > $heureFin){
                echo "L'heure de début ne peut être supérieure à l'heure de fin.";
                return false;
            }
            else{
                //Vérification de la disponibilité du véhicule
                if(Navette::checkVehicule($idVehicule, $date, $heureDebut, $heureFin)){
                    #Le véhicule est disponible
                    $statut = 'En cours';
                    if($idChauffeur==-1){
                        #Pour une réservation sans chauffeur
                        $idChauffeur = NULL;
                        goto ajoutChauffeur;
                    } //End if(idChauffeur==-1)
                    else{
                        #Pour une réservation avec chauffeur
                        if(Navette::checkChauffeur($idChauffeur, $date, $heureDebut, $heureFin)){
                            #Le chauffeur est disponible
        ajoutChauffeur:     $idHoraire = Navette::ajoutHoraire($heureDebut, $heureFin);
                            $reqAjoutNavette = "INSERT INTO Navette(idClient, idHoraire, idVehicule, idChauffeur, date, destination, statut) VALUES (:idClient, :idHoraire, :idVehicule, :idChauffeur, :date, :destination, :statut)";
                            $reponse = $bdd->prepare($reqAjoutNavette);
                            $reponse->execute(array(
                                'idClient' => $idClient,
                                'idHoraire' => $idHoraire, 
                                'idVehicule' => $idVehicule, 
                                'idChauffeur' => $idChauffeur, 
                                'date' => $date, 
                                'destination' => $destination, 
                                'statut' => $statut
                            ));
                            if($reponse->rowCount() > 0){
                                echo "Succes. Navette ajoutée";
                                return true;
                            }
                            else{
                                echo "Une erreur est survenue lors de l'ajout de la navette";
                                return false;
                            }
                            $reponse->closeCursor();
                        } //End if(checkChauffeur)
                        else{
                            echo "Pour cette date, le chauffeur n'est pas disponible dans la tranche horaire indiquée.";
                            return false;
                        } //End else if(checkChauffeur)

                    } //End else if(idChauffeur)

                } //End if (checkVehicule)
                else{
                    echo "Pour cette date, le véhicule n'est pas disponible dans la tranche horaire indiquée.";
                    return false;
                } //End else if (checkVehicule)

            } //End else (heureDebut>heureFin)

        } //End ajoutNavette

        public static function modifierNavette($idNavette, $idClient, $idVehicule, $idChauffeur, $date, $destination, $heureDebut, $heureFin){
            global $bdd;
            //Formalisation de la date
            $date = date("Y-m-d", strtotime($date));
            //Vérification de la conformité de la tranche horaire
            if($heureDebut > $heureFin){
                echo "L'heure de début ne peut être supérieure à l'heure de fin.";
                return false;
            }
            else{
                //Vérification de la disponibilité du véhicule
                if(Navette::checkVehicule($idVehicule, $date, $heureDebut, $heureFin)){
                    #Le véhicule est disponible
                    $statut = 'En cours';
                    if($idChauffeur==-1){
                        #Pour une réservation sans chauffeur
                        $idChauffeur = NULL;
                        goto updateChauffeur;
                    } //End if(idChauffeur==-1)
                    else{
                        #Pour une réservation avec chauffeur
                        if(Navette::checkChauffeur($idChauffeur, $date, $heureDebut, $heureFin)){
                            #Le chauffeur est disponible    
        updateChauffeur:    Navette::updateHoraire($idNavette, $heureDebut, $heureFin);
                            $reqUpdateNavette = "UPDATE Navette SET idClient=:idClient, idVehicule=:idVehicule, idChauffeur=:idChauffeur, date=:date, destination=:destination, statut=:statut WHERE idNavette=:idNavette";
                            $reponse = $bdd->prepare($reqUpdateNavette);
                            $reponse->execute(array(
                                'idNavette' => $idNavette,
                                'idClient' => $idClient,
                                'idVehicule' => $idVehicule, 
                                'idChauffeur' => $idChauffeur, 
                                'date' => $date, 
                                'destination' => $destination, 
                                'statut' => $statut
                            ));
                            if($reponse->rowCount() > 0){
                                echo "Succes. Navette mise à jour";
                                return true;
                            }
                            else{
                                echo "Une erreur est survenue lors de la mise à jour de la navette";
                                return false;
                            }

                        } //End if(checkChauffeur)
                        else{
                            echo "Pour cette date, le chauffeur n'est pas disponible dans la tranche horaire indiquée.";
                            return false;
                        } //End else if(checkChauffeur)

                    } //End else if(idChauffeur)

                } //End if (checkVehicule)
                else{
                    echo "Pour cette date, le véhicule n'est pas disponible dans la tranche horaire indiquée.";
                    return false;
                } //End else if (checkVehicule)

            } //End else (heureDebut>heureFin)

        } //End modifierNavette

        public static function supprimerNavette($id){
            global $bdd;
            $requete = 'DELETE FROM Navette WHERE idNavette=?';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            //Vérification de la réussite de la suppréssion
            if($reponse->rowCount() > 0){
                echo "Succes. Navette supprimée !";
            } 
            else{
                echo "Une erreur est survenue lors de la suppression de la navette !";
                return false;
            }
            $reponse->closeCursor();

        } //End supprimerNavette($id)

        public static function changerStatutNavette($idNavette, $statut){
            global $bdd;
            if($statut != 'En cours' && $statut != 'Annulé' && $statut != 'Terminé'){
                echo "Statut non autorisé !";
                return false;
                
            } //End if
            else{
                $reqNavette = "UPDATE Navette SET statut=? WHERE idNavette=?";
                $reponse = $bdd->prepare($reqNavette);
                $reponse->execute(array($statut, $idNavette));
                //Vérification de la réussite de la mise à jour du statut
                if($reponse->rowCount() > 0){
                    echo "Succes. Statut de la navette mis à jour !";
                } 
                else{
                    echo "Une erreur est survenue lors de la mise à jour du statut de la navette !";
                    return false;
                }
                $reponse->closeCursor();
            } //End first else
            
            
        } //End changerStatutNavette()

        public static function afficheNavette($choix, $statut){
            global $bdd;
            if(!empty($statut)){
                $statut = 'En cours';
                if($statut != 'En cours' && $statut != 'Annulé' && $statut != 'Terminé'){
                    echo "Statut non autorisé !";
                    return false;
                    
                } //End if
                else{
                    if($choix=='avec'){
                       $reqAfficheNavette = "SELECT DISTINCT idNavette, v.idVehicule, ca.idChauffeur, c.prenom AS prenom_client, c.nom AS nom_client, marque, modele, immatriculation, v.cheminPhoto, ca.prenom AS prenom_chauffeur, ca.nom AS nom_chauffeur, DATE_FORMAT(date, '%d/%m/%Y') AS date, heureDebut, heureFin, destination, statut FROM Clientele c, Vehicule v, Chauffeur ca, Marque ma, Modele mo, Horaire h, Navette n WHERE v.idVehicule=n.idVehicule AND v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND n.idClient=c.idClient AND n.idHoraire=h.idHoraire AND ca.idChauffeur=n.idChauffeur AND statut=?";
                    }
                    else if($choix=='sans'){
                       $reqAfficheNavette = "SELECT DISTINCT idNavette, v.idVehicule, prenom AS prenom_client, nom AS nom_client, marque, modele, immatriculation, v.cheminPhoto, DATE_FORMAT(date, '%d/%m/%Y') AS date, heureDebut, heureFin, destination, statut FROM Clientele c, Vehicule v, Marque ma, Modele mo, Horaire h, Navette n WHERE v.idVehicule=n.idVehicule AND v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND n.idClient=c.idClient AND n.idHoraire=h.idHoraire AND n.idChauffeur IS NULL AND statut=?";
                    }
            
                } //End else if(!empty)
            } //End if(!empty)
            else{
                if($choix=='avec'){
                    $reqAfficheNavette = "SELECT DISTINCT idNavette, v.idVehicule, ca.idChauffeur, c.prenom AS prenom_client, c.nom AS nom_client, marque, modele, immatriculation, v.cheminPhoto, ca.prenom AS prenom_chauffeur, ca.nom AS nom_chauffeur, DATE_FORMAT(date, '%d/%m/%Y') AS date, heureDebut, heureFin, destination, statut FROM Clientele c, Vehicule v, Chauffeur ca, Marque ma, Modele mo, Horaire h, Navette n WHERE v.idVehicule=n.idVehicule AND v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND n.idClient=c.idClient AND n.idHoraire=h.idHoraire AND ca.idChauffeur=n.idChauffeur";
                }
                else{
                    $reqAfficheNavette = "SELECT DISTINCT idNavette, v.idVehicule, prenom AS prenom_client, nom AS nom_client, marque, modele, immatriculation, v.cheminPhoto, DATE_FORMAT(date, '%d/%m/%Y') AS date, heureDebut, heureFin, destination, statut FROM Clientele c, Vehicule v, Marque ma, Modele mo, Horaire h, Navette n WHERE v.idVehicule=n.idVehicule AND v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND n.idClient=c.idClient AND n.idHoraire=h.idHoraire AND n.idChauffeur IS NULL";
                }
            }
            $reponse = $bdd->prepare($reqAfficheNavette);
            $reponse->execute(array($statut));
            if($navettes = $reponse->fetchAll()){
                $navettes = json_encode($navettes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $reponse->closeCursor();
                return $navettes;
            }
            else{
                echo "Aucune navette trouvée.";
                return false;
            }
            
        } //End afficheNavette
        
        public static function checkVehicule($idVehicule, $date, $heureDebut, $heureFin){
            //Vérifie si le véhicule choisi est disponible dans la tranche horaire indiquée
            global $bdd;
            $result = true; #On suppose que le véhicule est disponible
            $reqCheckVehicule = "SELECT idVehicule FROM Navette, Horaire WHERE date=:date AND heureDebut<=:heureFin AND heureFin>=:heureDebut AND statut='En cours'";
            $reponse = $bdd->prepare($reqCheckVehicule);
            $reponse->execute(array(
                "heureDebut" => $heureDebut,
                "heureFin" => $heureFin,
                "date" => $date
            ));
            while($data = $reponse->fetch()){
                if($idVehicule==$data['idVehicule']){
                    #Le véhicule n'est pas disponible dans la tranche horaire indiquée
                    $result=false;
                    break;
                }
            } //End while
            $reponse->closeCursor();
            return $result==true ? true : false; 
        } //End checkVehicule

        public static function checkChauffeur($idChauffeur, $date, $heureDebut, $heureFin){
            //Vérifie si le chauffeur choisi est disponible dans la tranche horaire indiquée
            global $bdd;
            $result = true; #On suppose que le chauffeur est disponible
            $reqCheckChauffeur = "SELECT idChauffeur FROM Navette, Horaire WHERE date=:date AND heureDebut<=:heureFin AND heureFin>=:heureDebut AND statut='En cours'";
            $reponse = $bdd->prepare($reqCheckChauffeur);
            $reponse->execute(array(
                "heureDebut" => $heureDebut,
                "heureFin" => $heureFin,
                "date" => $date
            ));
            while($data = $reponse->fetch()){
                if($idChauffeur==$data['idChauffeur']){
                    #Le chauffeur n'est pas disponible dans la tranche horaire indiquée
                    $result=false;
                    break;
                }
            } //End while
            $reponse->closeCursor();
            return $result==true ? true : false; 
        } //End checkChauffeur

        public static function ajoutHoraire($heureDebut, $heureFin){
            global $bdd;
            //Ajout des horaires dans la base
            $requete = 'INSERT INTO Horaire (heureDebut, heureFin) VALUES(:heureDebut, :heureFin)';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array(
                'heureDebut' => $heureDebut,
                'heureFin' => $heureFin
            ));
            //Vérification de la réussite de l'ajout
            if($reponse->rowCount() > 0){
                echo "Horaires ajoutées / ";
                $reqLastId = "SELECT idHoraire FROM Horaire ORDER BY idHoraire DESC LIMIT 0, 1";
                $reponse = $bdd->query($reqLastId);
                if($data = $reponse->fetch()){
                    $lastId = $data['idHoraire'];
                    return $lastId; 
                }
                else{
                    echo "Impossible de récupérer l'Id de la dernière horaire / ";
                    return false;
                }
            } //End rowCount 
            else{
                echo "Une erreur est survenue lors de l'ajout des horaires / ";
                return false;
            }
            $reponse->closeCursor();
        } //End ajoutHoraire

        public static function updateHoraire($idNavette, $heureDebut, $heureFin){
            global $bdd;
            //Modification des horaires de la base
            $idHoraire = Navette::returnId('idHoraire', 'Navette', 'idNavette', $idNavette);
            $reqUpdateHoraire = "UPDATE Horaire SET heureDebut=?, heureFin=? WHERE idHoraire=?";
            $reponse = $bdd->prepare($reqUpdateHoraire);
            $reponse->execute(array($heureDebut, $heureFin, $idHoraire));
            //Vérification de la réussite de la mise à jour des horaires
            if($reponse->rowCount() > 0){
            echo "Horaires mises à jour / ";
            $reponse->closeCursor();
            return true;
            } 
            else{
                echo "Une erreur est survenue lors de la mise à jour des horaires !";
                return false;
            }

        } //End updateHoraire

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

    } //End Navette