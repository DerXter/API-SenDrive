<?php
    class Vehicule{
        //Attributs
        private $_idVehicule;
        private $_idMarque;
        private $_idModele;
        private $_idType;
        private $_idProprietaire;
        private $_idCarburant;
        private $_idDate;
        private $_immatriculation;
        private $_climatisation;
        private $_nombreDePortes;
        private $_nombreDePlaces;
        private $_description;
        private $_prix;
        private $_boiteDeVitesse;

        //Accesseurs

        //Autres fonctions
        public static function afficheVehicule($dateDebut, $dateFin){
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
                $vehicules = array(); //Tableau qui va contenir les véhicules diponibles entre les deux dates spécifiées
                $reqRecupIdDisponibilite = "SELECT idDisponibilite FROM Disponibilite WHERE dateDebut<=? AND dateFin>=?";
                $reponse = $bdd->prepare($reqRecupIdDisponibilite);
                $reponse->execute(array($dateDebut, $dateFin));
                if ($dataId=$reponse->fetchAll()){ //Tableau contenant tous les 'idDisponibilité' correspondants aux dates spécifiées
                
                    foreach($dataId as $donnees) {
                        # Pour chaque 'idDisponibilité' trouvé, on retourne un tableau contenant l'ensemble des véhicules diponibles
                        $idDate = $donnees['idDisponibilite'];
                        $reqAfficheVehicule = "SELECT DISTINCT idVehicule, marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND v.idDate=idDisponibilite AND idDate=? AND statut=?";
                        $reponse1 = $bdd->prepare($reqAfficheVehicule);
                        $reponse1->execute(array($idDate, 'Libre'));
                        $vehicules = array_merge_recursive($vehicules, $reponse1->fetchAll());  //Puis ce tableau est concaténé avec le prochain tableau trouvé grâce à l'éventuel prochain idDisponibilité.
                        
                    } //End While($dataId)
                    $reponse->closeCursor();
                    if(empty($vehicules)){
                        echo "Aucun véhicule disponible à cette période !";
                        return false;
                    }
                    else{
                        //Conversion du format du tableau en JSON
                        $vehicules = json_encode($vehicules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        return $vehicules;
                    }
                    
                } //End if(dataID)
                else{
                    echo "Aucun véhicule disponible à cette période !";
                    return false;
                }
            } //End else(dateDebut>=dateFin)
    
        } //End afficheVehicules

        public static function afficheVehicules($statut){
            global $bdd;
            if(empty($statut)){
                $reqAfficheVehicule = "SELECT marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto, statut FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND idDate=idDisponibilite";
            }
            else if ($statut=='Libre' || $statut=='Réservé'){
                $reqAfficheVehicule = "SELECT marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto, statut FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND idDate=idDisponibilite AND statut=?";
            }
            else{
                echo 'Statut inconnu ! Les statuts disponibles sont : \'Libre\' et \'Réservé\' !';
                return false;
            }
            $reponse = $bdd->prepare($reqAfficheVehicule);
            $reponse->execute(array($statut));
            
            if($vehicules = $reponse->fetchAll()){
                $reponse->closeCursor();
                //Conversion du format du tableau en JSON
                $vehicules = json_encode($vehicules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); //Conversion du format du tableau en JSON
                return $vehicules;
            } //End if
            else{
                echo "Aucun véhicule trouvée !";
                return false;
            }
           
        } //End afficheVehicules()

        public static function filtreVehicule($filtre){ //Affiche des critères de véhicule selon le filtre
            global $bdd;
            if ($filtre=='marque' || $filtre=='modele' || $filtre=='typevehicule' || $filtre=='carburant'){
                $requete = "SELECT * FROM $filtre";
            }
            else{
                switch ($filtre){
                    case 'clim-oui':
                        $requete = "SELECT marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND idDate=idDisponibilite AND climatisation='oui' ";
                    break;
                    case 'clim-non':
                        $requete = "SELECT marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND idDate=idDisponibilite AND climatisation='non' ";
                    break;
                    default:
                        echo 'Filtre inconnu !';
                        return false;
                } //End switch
            } //End else
            $reponse = $bdd->query($requete);
            if ($vehicules = $reponse->fetchAll()){
                $reponse->closeCursor();
                $vehicules = json_encode($vehicules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); //Conversion du format du tableau en JSON
                return $vehicules;
            } //End if
            else{
                echo "Aucune données disponibles pour ce filtre !";
                return false;
            } //End else
           
        } //End filtreVehicule()

        public static function filtrage($idMarque, $idModele, $idType, $idCarburant, $climatisation){
            global $bdd;
            $val_interdit = -1; //Valeur prise si un critère n'est pas spécifié
            //Formalisation de la requête à transmettre à la bdd
            $suite1 = $idMarque==$val_interdit ? "v.idMarque=ma.idMarque AND " : "v.idMarque=:idMarque AND ";
            $suite2 = $idModele==$val_interdit ? "v.idModele=mo.idModele AND " : "v.idModele=:idModele AND ";
            $suite3 = $idType==$val_interdit ?   "v.idType=idTypeVehicule AND " : "v.idType=:idType AND ";
            $suite4 = $idCarburant==$val_interdit ? "v.idCarburant=ca.idCarburant AND " : "v.idCarburant=:idCarburant AND ";
            $suite5 = $climatisation==$val_interdit ? "climatisation=climatisation" : "climatisation=:climatisation";
            
            $requete_temp = "SELECT marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND idDate=idDisponibilite AND "; 
            $requete = $requete_temp . $suite1 . $suite2 . $suite3 . $suite4 . $suite5; //Requête finale
            //echo $requete;
            $reponse = $bdd->prepare($requete);
            $data = array(); //Tableau qui va recevoir le array associatif associé à la requête
         
            //Formalisation de l'éxécution
            if($idMarque!=$val_interdit){
                $data['idMarque'] = $idMarque;
            } //End if(Marque)
            if ($idModele!=$val_interdit){
                $data['idModele'] = $idModele; 
            } //End if(Modele)
            if($idType!=$val_interdit){
                $data['idType'] = $idType;
            } //End if(idType)
            if ($idCarburant!=$val_interdit){
                $data['idCarburant'] = $idCarburant;
            } //End if(idCarburant)
            if ($climatisation!=$val_interdit){
                $data['climatisation'] = $climatisation;
            } //End if(climatisation)
          
            $reponse->execute($data); 
            if($vehicules = $reponse->fetchAll()){
                $vehicules = json_encode($vehicules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); //Conversion du format du tableau en JSON
                return $vehicules;
            }
            else{
                echo 'Aucun Véhicule trouvé !';
                return false;
            }

            //$reponse->closeCursor();
        } //End filtrage

        public static function ajoutVehicule($idMarque, $idModele, $idType, $idProprietaire, $idCarburant, $dateDebut, $dateFin, $immatriculation, $climatisation, $nbPorte, $nbPlace, $description, $prix, $boiteDeVitesse){
            global $bdd;
            $statutVehicule = 'Libre';
            //Ajustement du format des dates
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
                $IdDisponibilite = $data['idDisponibilite'];

                if (!empty($IdDisponibilite)){
                    $requete = 'INSERT INTO Vehicule(idMarque, idModele, idType, idProprietaire, idCarburant, idDate, immatriculation, climatisation, nombreDePortes, nombreDePlaces, description, prix, boiteDeVitesse, statut) VALUES(:idMarque, :idModele, :idType, :idProprietaire, :idCarburant, :idDate, :immatriculation, :climatisation, :nbPorte, :nbPlace, :description, :prix, :boiteDeVitesse, :statut)';
                    $reponse = $bdd->prepare($requete);
                    $reponse->execute(array(
                    'idMarque' => $idMarque,
                    'idModele' => $idModele,
                    'idType' => $idType,
                    'idProprietaire' => $idProprietaire,
                    'idCarburant' => $idCarburant,
                    'idDate' => $IdDisponibilite,
                    'immatriculation' => $immatriculation,
                    'climatisation' => $climatisation,
                    'nbPorte' => $nbPorte,
                    'nbPlace' => $nbPlace,
                    'description' => $description,
                    'prix' => $prix,
                    'boiteDeVitesse' => $boiteDeVitesse,
                    'statut' => $statutVehicule
                    ));
                    //Vérification de l'ajout de véhicule
                    if($reponse->rowCount() > 0){
                        echo "Véhicule ajouté !";
                    } 
                    else{
                        echo "Une erreur est survenue lors de l'ajout du véhicule !";
                        return false;
                    }
                } //End if
                else{
                    echo "Une erreur est survenue lors de l'ajout du véhicule !";
                    return false;
                }
                
                $reponse->closeCursor();
            } //End else
            
        } //End ajoutVehicule()

        public static function modifierVehicule($idVehicule, $idMarque, $idModele, $idType, $idProprietaire, $idCarburant, $dateDebut, $dateFin, $immatriculation, $climatisation, $nbPorte, $nbPlace, $description, $prix, $boiteDeVitesse, $statutVehicule){
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
                $idDate = Vehicule::returnId('idDate', 'Vehicule', 'idVehicule', $idVehicule);
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

                    $requete = 'UPDATE Vehicule SET idMarque=:idMarque, idModele=:idModele, idType=:idType, idProprietaire=:idProprietaire, idCarburant=:idCarburant, idDate=:idDate, immatriculation=:immatriculation, climatisation=:climatisation, nombreDePortes=:nombreDePortes, nombreDePlaces=:nombreDePlaces, description=:description, prix=:prix, boiteDeVitesse=:boiteDeVitesse, statut=:statut WHERE idVehicule=:idVehicule';
                    $reponse = $bdd->prepare($requete);
                    $reponse->execute(array(
                    'idVehicule' => $idVehicule,
                    'idMarque' => $idMarque,
                    'idModele' => $idModele,
                    'idType' => $idType,
                    'idProprietaire' => $idProprietaire,
                    'idCarburant' => $idCarburant,
                    'idDate' => $idDate,
                    'immatriculation' => $immatriculation,
                    'climatisation' => $climatisation,
                    'nombreDePortes' => $nbPorte,
                    'nombreDePlaces' => $nbPlace,
                    'description' => $description,
                    'prix' => $prix,
                    'boiteDeVitesse' => $boiteDeVitesse,
                    'statut' => $statutVehicule
            
                    ));
                    //Vérification de la réussite de la mise à jour
                    if($reponse->rowCount() > 0){
                        echo "Véhicule mis à jour !";
                    } 
                    else{
                        echo "Une erreur est survenue lors de la modification du véhicule !";
                        return false;
                    }
                
                $reponse->closeCursor();
            } //End else
            
        } //End modifierVehicule()


        public static function supprimerVehicule($id){
            global $bdd;
            $requete = 'DELETE FROM Vehicule WHERE idVehicule=?';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            //Vérification de la réussite de la suppréssion
            if($reponse->rowCount() > 0){
                echo "Véhicule supprimé !";
            } 
            else{
                echo "Une erreur est survenue lors de la suppréssion du véhicule !";
                return false;
            }
            $reponse->closeCursor();

        } //End supprimerVehicule($id)

        public static function ajoutMarque($marque){
            global $bdd;
            $marque = strtoupper($marque); //Conversion en majuscule
            $requete = "INSERT INTO Marque(marque) VALUES (?)";
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($marque));
            if($reponse->rowCount() > 0){
                echo "Marque ajoutée.";
                return true;
            }
            else{
                echo "Une erreur est survenue lors de l'ajout de la marque !";
                return false;
            }
            $reponse->closeCursor();
        } //End ajoutmarque()

        public static function ajoutModele($modele, $idMarque){
            global $bdd;
            $modele = strtoupper($modele); //Conversion en majuscule
            $requete = "INSERT INTO Modele(idMarque, modele) VALUES (?, ?)";
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($idMarque, $modele));
            if($reponse->rowCount() > 0){
                echo "Modele ajouté.";
                return true;
            }
            else{
                echo "Une erreur est survenue lors de l'ajout du modele !";
                return false;
            }
            $reponse->closeCursor();
        } //End ajoutModele()
        public static function ajoutTypeVehicule($type){
            global $bdd;
            $type = strtoupper($type); //Conversion en majuscule
            $requete = "INSERT INTO TypeVehicule(typeVehicule) VALUES (?)";
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($type));
            if($reponse->rowCount() > 0){
                echo "Type de véhicule ajouté.";
                return true;
            }
            else{
                echo "Une erreur est survenue lors de l'ajout du type de véhicule !";
                return false;
            }
            $reponse->closeCursor();
        } //End ajoutType()
        public static function supprimerCaracVehicule($carac, $id){
            global $bdd;
            $idName = 'id'.$carac;
            $requete = "DELETE FROM $carac WHERE $idName=?";
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            //Vérification de la réussite de la suppréssion
            if($reponse->rowCount() > 0){
                echo "$carac supprimé(e) !";
            } 
            else{
                echo "Une erreur est survenue lors de la suppréssion de la/du $carac !";
                return false;
            }
            $reponse->closeCursor();

        } //End supprimerVehicule($id)

        public static function verifDoublons($table, $donnee){
            global $bdd;
            $result=false; //Flag me permettant de savoir s'il y'a un doublon ou pas
            $requete = "SELECT $donnee FROM $table";
            $reponse = $bdd->prepare($requete);
            while($data = $reponse->fetch()){
                if($donnee==$data[$donnee]){
                    echo "$donnee déjà existant(e).";
                    $result = true;
                    break;
                } //End if
            } //End while ()
            $reponse->closeCursor();
            return $result==true ? true : false; //Retourne true s'il y'a un doublon et false dans le cas contraire
        } //End verifDoublons()

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

    } //End Vehicule