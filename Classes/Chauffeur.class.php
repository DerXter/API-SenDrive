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
            if ($dateDebut >= $dateFin){
                echo "La date d'arrivée ne peut être supérieure à la date de départ !";
                return false;
            }
            else{
                $reqRecupIdDisponibilite = "SELECT DISTINCT idChauffeur FROM Reservation, Disponibilite WHERE (dateDebut<=:dateDebut AND dateFin>=:dateFin AND statut='En cours') OR (dateDebut>:dateDebut AND dateFin<:dateFin AND statut='En cours') OR (dateDebut>:dateDebut AND dateFin>:dateFin AND statut='En cours') OR (dateDebut<:dateDebut AND dateFin<:dateFin AND statut='En cours') AND idDisponibilite=idDate ";
                $reponse = $bdd->prepare($reqRecupIdDisponibilite);
                $reponse->execute(array(
                    'dateDebut' => $dateDebut, 
                    'dateFin' => $dateFin));
                if ($dataId=$reponse->fetchAll()){ //Tableau contenant tous les 'idDisponibilité' correspondants aux chauffeurs réservés
                   //Récupération de l'ensemble des chauffeurs
                   $reqAfficheChauffeur = "SELECT idChauffeur, prenom, nom, permis, adresse, telephone, commentaire, cheminPhoto FROM Chauffeur WHERE 1";
                    foreach($dataId as $donnees) {
                        # Pour chaque 'idDisponibilité' trouvé, on enlève les chauffeurs réservés de la liste des chauffeurs à afficher
                        $idChauffeur = $donnees['idChauffeur'];
                        $reqAfficheChauffeur .= " AND idChauffeur!=$idChauffeur ";
                        
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
            $reqAfficheChauffeur = "SELECT DISTINCT idChauffeur, prenom, nom, permis, adresse, telephone, cheminPhoto, statut FROM Chauffeur";
            $reponse = $bdd->query($reqAfficheChauffeur);

            if ($chauffeurs = $reponse->fetchAll()){
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

        public static function ajoutChauffeur($prenom, $nom, $dateNaissance, $numeroIdentite, $permis, $adresse, $telephone, $dateDebut, $dateFin, $commentaire){
            global $bdd;
            //Vérification de l'unicité du chauffeur ajouté
            if(Chauffeur::verifDoublons('numeroIdentite', 'Chauffeur', $numeroIdentite)){
                echo "Numéro d'identité déjà utilisé !";
                return false;
            }
            else{
                //Mie en conformité des dates
                $dateNaissance = date("Y-m-d", strtotime($dateNaissance));
                $dateDebut = date("Y-m-d", strtotime($dateDebut));
                $dateFin = date("Y-m-d", strtotime($dateFin));
                //Vérification de la conformité de la période
                if ($dateDebut >= $dateFin){
                    echo "La date de fin ne peut être supérieure à la date de début de disponibilité !";
                    return false;
                }
                else{  
                    //Ajout des dates dans la base
                    $requete = 'INSERT INTO Disponibilite (dateDebut, dateFin) VALUES(:dateDebut, :dateFin)';
                    $reponse = $bdd->prepare($requete);
                    $reponse->execute(array(
                        'dateDebut' => $dateDebut,
                        'dateFin' => $dateFin
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
                    $idDisponibilite = $data['idDisponibilite'];
                    //Insertion du Chauffeur dans la base
                    $reqInsertChauffeur = "INSERT INTO Chauffeur (idDate, prenom, nom, dateNaissance, numeroIdentite, permis, adresse, telephone, commentaire) VALUES (:idDate, :prenom, :nom, :dateNaissance, :numeroIdentite, :permis, :adresse, :telephone, :commentaire)";
                    $reponse = $bdd->prepare($reqInsertChauffeur);
                    $reponse->execute(array(
                        'idDate' => $idDisponibilite,
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
                        echo "Chauffeur ajouté !";
                    } 
                    else{
                        echo "Une erreur est survenue lors de l'ajout du chauffeur !";
                        return false;
                    }
                } //End else
            } //End else if(verifDoublons)

        } //End ajoutChauffeur()

        public static function modifierChauffeur($idChauffeur, $prenom, $nom, $dateNaissance, $numeroIdentite, $permis, $adresse, $telephone, $dateDebut, $dateFin, $commentaire){
            global $bdd;
            //Vérification de l'unicité du chauffeur ajouté
            if(Chauffeur::verifDoublons('numeroIdentite', 'Chauffeur', $numeroIdentite)){
                echo "Numéro d'identité déjà utilisé !";
                return false;
            }
            else{
                //Mie en conformité des dates
                $dateNaissance = date("Y-m-d", strtotime($dateNaissance));
                $dateDebut = date("Y-m-d", strtotime($dateDebut));
                $dateFin = date("Y-m-d", strtotime($dateFin));
                //Vérification de la conformité de la période
                if ($dateDebut >= $dateFin){
                    echo "La date de fin ne peut être supérieure à la date de début de disponibilité !";
                    return false;
                }
                else{  
                    //Modification des dates de la base
                    $idDate = Chauffeur::returnId('idDate', 'Chauffeur', 'idChauffeur', $idChauffeur);
                    $reqIdDisponibilite = "UPDATE Disponibilite SET dateDebut=?, dateFin=? WHERE idDisponibilite=?";
                    $reponse = $bdd->prepare($reqIdDisponibilite);
                    $reponse->execute(array($dateDebut, $dateFin, $idDate));
                    //Vérification de la réussite de la mise à jour des dates
                    if($reponse->rowCount() > 0){
                        echo "Dates mises à jour !";
                    } 
                    else{
                        echo "Une erreur est survenue lors de la mise à jour des dates !";
                        return false;
                    }

                        $requete = 'UPDATE Chauffeur SET idDate=:idDate, prenom=:prenom, nom=:nom, dateNaissance=:dateNaissance, numeroIdentite=:numeroIdentite, permis=:permis, adresse=:adresse, telephone=:telephone, commentaire=:commentaire WHERE idChauffeur=:idChauffeur';
                        $reponse = $bdd->prepare($requete);
                        $reponse->execute(array(
                            'idDate' => $idDate,
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
                            echo "Chauffeur mis à jour !";
                        } 
                        else{
                            echo "Une erreur est survenue lors de la modification du chauffeur !";
                        return false;
                        }
                     
                    $reponse->closeCursor();
                    } //End else (dateDebut, dateFin)
            } //End else if(verifDoublons)

        } //End modifierChauffeur()

        public static function supprimerChauffeur($id){
            global $bdd;
            $requete = 'DELETE FROM Chauffeur WHERE idChauffeur=?';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            //Vérification de la réussite de la suppréssion du chauffeur
            if($reponse->rowCount() > 0){
                echo "Chauffeur supprimé !";
            } 
            else{
                echo "Une erreur est survenue lors de la suppréssion du chauffeur!";
                return false;
            }
            $reponse->closeCursor();

        } //End supprimerChauffeur($id)


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
    

    } //End class Chauffeur