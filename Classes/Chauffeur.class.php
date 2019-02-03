<?php
    class Chauffeur{
        //Attributs
        private $idChauffeur;
        private $idDate;
        private $prenom;
        private $nom;
        private $dateNaissance;
        private $numeroIdentite;
        private $permis;
        private $adresse;
        private $telephone;
        private $commentaire;

        //Accesseurs

        //Autres fonctions
        public static function afficheChauffeur($dateDebut, $dateFin){
            global $bdd;
            //Changement du format de la date en yyyy-mm-dd
            $dateDebut = date("Y-m-d", strtotime($dateDebut));
            $dateFin = date("Y-m-d", strtotime($dateFin));
            //Vérification de la conformité de la période
            if ($dateDebut > $dateFin){  
                /* FRONT - Strictement superieur car dateDebut peut etre egal à dateFin*/
                echo "La date de d'arrivée ne peut être supérieure à la date de de départ !";
                return false;
            }
            else{
               //On récupère l'ensemble des idchauffeurs des chauffeurs qui ont été réservés
               $dataId = Chauffeur::getReserve($dateDebut, $dateFin);
               //var_dump($dataId);
                if (!empty($dataId)){ //Tableau contenant tous les 'idDisponibilité' correspondants aux chauffeurs réservés
                   //Récupération de l'ensemble des chauffeurs
                   $reqAfficheChauffeur = "SELECT idChauffeur, prenom, nom, permis, adresse, telephone, commentaire, cheminPhoto FROM Chauffeur WHERE 1";
                   for($i=0; $i<count($dataId); $i++) {
                        # Pour chaque 'idDisponibilité' trouvé, on enlève les chauffeurs réservés de la liste des chauffeurs à afficher
                        if(!empty($dataId[$i])){
                            $idChauffeur = $dataId[$i];
                            $reqAfficheChauffeur .= " AND idChauffeur!=$idChauffeur ";
                        }
                        
                    } //End While($dataId)

                     //Vérification et retour du résultat
                    if($reponse = $bdd->query($reqAfficheChauffeur)){
                        $chauffeurs = $reponse->fetchAll();
                        //Conversion du format du tableau en JSON
                        $chauffeurs = json_encode($chauffeurs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        $reponse->closeCursor();

                        return $chauffeurs;
                    }
                    else{
                        echo "Aucun chauffeur disponible à cette période !";
                        return false;
                    }         
                    
                } //End if($dataId)
                else{
                    //Récupération de l'ensemble des chauffeurs
                   $reqAfficheChauffeur = "SELECT idChauffeur, prenom, nom, permis, adresse, telephone, commentaire, cheminPhoto FROM Chauffeur WHERE 1";
                    //Vérification et retour du résultat
                    if($reponse = $bdd->query($reqAfficheChauffeur)){
                        $chauffeurs = $reponse->fetchAll();
                        //Conversion du format du tableau en JSON
                        $chauffeurs = json_encode($chauffeurs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        $reponse->closeCursor();

                        return $chauffeurs;
                    }
                    else{
                        echo "Aucun chauffeur disponible à cette période !";
                        return false;
                    }
                } //End else if ($dataId)
            } //End else (dateDebut >= dateFin)
        } //End afficheChauffeur($dateDebut, $dateFin)

        public static function afficheChauffeurs(){
            global $bdd;
            /* FRONT - Select * car besoin en front de toutes les donnees */
            $reqAfficheChauffeur = "SELECT * FROM chauffeur";
            if($reponse = $bdd->query($reqAfficheChauffeur)){
                $chauffeurs = $reponse->fetchAll();
                //Conversion du format du tableau en JSON
                $chauffeurs = json_encode($chauffeurs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $reponse->closeCursor();
                
                return $chauffeurs;
            } //End if($chauffeurs)
            else{
                echo "Aucun chauffeur trouvé !";
                return false;
            }
        } //End afficheChauffeurs()

        public static function ajoutChauffeur($prenom, $nom, $dateNaissance, $numeroIdentite, $permis, $adresse, $telephone, $commentaire){
            /*FRONT -  Suppression dateDebut et dateFin non prises en compte dans la bdd*/
            global $bdd;
            //Vérification de l'unicité du chauffeur ajouté
            if(Chauffeur::verifDoublons('numeroIdentite', 'Chauffeur', $numeroIdentite)){
                echo "Numéro d'identité déjà utilisé !";
                return false;
            }
            else{
                //Mie en conformité des dates
                $dateNaissance = date("Y-m-d", strtotime($dateNaissance));
                //Insertion du Chauffeur dans la base
                $reqInsertChauffeur = "INSERT INTO Chauffeur ( prenom, nom, dateNaissance, numeroIdentite, permis, adresse, telephone, commentaire) VALUES ( :prenom, :nom, :dateNaissance, :numeroIdentite, :permis, :adresse, :telephone, :commentaire)";
                $reponse = $bdd->prepare($reqInsertChauffeur);
                $reponse->execute(array(
                    'prenom' => $prenom, 
                    'nom' => $nom, 
                    'dateNaissance' => $dateNaissance, 
                    'numeroIdentite' => $numeroIdentite, 
                    'permis' => $permis, 
                    'adresse' => $adresse, 
                    'telephone' => $telephone, 
                    'commentaire' => $commentaire
                ));
                //Vérification de la réussite de l'ajout
                if($reponse->rowCount() > 0){
                    echo "Succes. Chauffeur ajouté !";
                } 
                else{
                    echo "Une erreur est survenue lors de l'ajout du chauffeur !";
                    return false;
                }
            } //End else if(verifDoublons)

        } //End ajoutChauffeur()

        public static function modifierChauffeur($idChauffeur, $prenom, $nom, $dateNaissance, $numeroIdentite, $permis, $adresse, $telephone, $commentaire){
            global $bdd;
            /* FRONT -  Verification des doublons mise en commentaire - Possibilité de changer les infos chauffeur sans modifier son numID */
            //Vérification de l'unicité du chauffeur ajouté
            //if(Chauffeur::verifDoublons('numeroIdentite', 'Chauffeur', $numeroIdentite)){
            //    echo "Numéro d'identité déjà utilisé !";
            //    return false;
            //}
            //else{
                //Mie en conformité des dates
                $dateNaissance = date("Y-m-d", strtotime($dateNaissance));

                $requete = 'UPDATE Chauffeur SET prenom=:prenom, nom=:nom, dateNaissance=:dateNaissance, numeroIdentite=:numeroIdentite, permis=:permis, adresse=:adresse, telephone=:telephone, commentaire=:commentaire WHERE idChauffeur=:idChauffeur';
                $reponse = $bdd->prepare($requete);
                $reponse->execute(array(
                    'prenom' => $prenom, 
                    'nom' => $nom, 
                    'dateNaissance' => $dateNaissance, 
                    'numeroIdentite' => $numeroIdentite, 
                    'permis' => $permis, 
                    'adresse' => $adresse, 
                    'telephone' => $telephone, 
                    'commentaire' => $commentaire,
                    'idChauffeur' => $idChauffeur
        
                ));
                //Vérification de la réussite de la mise à jour du chauffeur
                if($reponse->rowCount() > 0){
                    echo "Succes. Chauffeur mis à jour !";
                } 
                else{
                    echo "Une erreur est survenue lors de la modification du chauffeur !";
                return false;
                }
                     
                    $reponse->closeCursor();
            //} //End else if(verifDoublons)

        } //End modifierChauffeur()

        public static function supprimerChauffeur($id){
            global $bdd;
            if(Chauffeur::checkChauffeur($id)){
                echo "Ce chauffeur est en cours de réservation.";
                return false; 
            }
            else{
                $requete = 'DELETE FROM Chauffeur WHERE idChauffeur=?';
                $reponse = $bdd->prepare($requete);
                $reponse->execute(array($id));
                //Vérification de la réussite de la suppréssion du chauffeur
                if($reponse->rowCount() > 0){
                    echo "Succes. Chauffeur supprimé !";
                } 
                else{
                    echo "Une erreur est survenue lors de la suppréssion du chauffeur!";
                    return false;
                }
                $reponse->closeCursor();

            }
    
        } //End supprimerChauffeur($id)

        public static function checkChauffeur($id){
            global $bdd;
            $requete = "SELECT idChauffeur FROM Reservation WHERE statut='En cours' AND idChauffeur=?";
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            if($reponse->fetch()){
                return true;
            }
            else{
                return false;
            }
        }

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

        public static function verifDoublons($donnee, $table, $valeur){
            global $bdd;
            $result=false; //Flag me permettant de savoir s'il y'a un doublon ou pas
            $requete = "SELECT $donnee FROM $table";
            $reponse = $bdd->query($requete);
            while($data = $reponse->fetch()){
                if($valeur==$data[$donnee]){
                    $result = true;
                    break;
                } //End if
            } //End while ()
            $reponse->closeCursor();
            return $result==true ? true : false; //Retourne true s'il y'a un doublon et false dans le cas contraire
        } //End verifDoublons()
        
        public static function getReserve($dateDepart, $dateArrivee){
            global $bdd;
            $data = array();
            //On récupère l'ensemble des id$nature des vehicules qui ont été réservés à la période spécifiée
            $reqRecupIdDisponibilite = "SELECT DISTINCT idChauffeur FROM Reservation, Disponibilite WHERE dateDebut<=:dateFin AND dateFin>=:dateDebut AND statut='En cours' AND idDate=idDisponibilite";
            $reponse = $bdd->prepare($reqRecupIdDisponibilite);
            $reponse->execute(array(
                'dateDebut' => $dateDepart, 
                'dateFin' => $dateArrivee));
             //On retourne l'ensemble des chauffeurs réservés
             while ($dataId=$reponse->fetch()){
                if($dataId['idChauffeur']!=null){
                    array_push($data, $dataId['idChauffeur']);
                }
            }
            if(empty($data)){
                return false;
            }
            else{
                return $data;
            }
            
        } //End getReserve()

    } //End class Chauffeur