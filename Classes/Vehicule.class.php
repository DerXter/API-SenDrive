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
            if ($dateDebut > $dateFin){
                echo "La date de départ ne peut être supérieure à la date de d'arrivée !";
                return false;
            }
            else{
                //On récupère l'ensemble des idVehicules des véhicules qui ont été réservés
                $dataId = Vehicule::getReserve($dateDebut, $dateFin);
                if (!empty($dataId)){ //Tableau contenant tous les 'idDisponibilité' correspondants aux dates spécifiées
                    //Récupération de l'ensemble des véhicules
                    $reqAfficheVehicule = "SELECT DISTINCT idVehicule, marque, modele, typeVehicule, CONCAT(prix, ' FCFA') AS prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, cheminPhoto, v.description FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire";
                    for($i=0; $i<count($dataId); $i++) {
                        # Pour chaque 'idDisponibilité' trouvé, on enlève les véhicules réservés de la liste des véhicules à afficher
                        $idVehicule = $dataId[$i];
                        $reqAfficheVehicule .= " AND idVehicule!=$idVehicule ";
                       
                    } //End foreach($dataId)  
                    
                    //Vérification et retour du résultat
                    if($reponse = $bdd->query($reqAfficheVehicule) ){
                        #Pour se retrouver au final, qu'avec les véhicules libres entre les dates spécifiées
                        $vehicules = $reponse->fetchAll();
                        //Conversion du format du tableau en JSON
                        $vehicules = json_encode($vehicules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);     
                        $reponse->closeCursor();
                        return $vehicules;
                     }
                    else{
                        echo "Aucun véhicule disponible à cette période !";
                        return false;
                    }
                } //End if($dataID)
                else{
                    //Récupération de l'ensemble des véhicules car aucun véhicule n'est reservé
                    $reqAfficheVehicule = "SELECT DISTINCT idVehicule, marque, modele, typeVehicule, CONCAT(prix, ' FCFA') AS prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, cheminPhoto, v.description FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire";
                    //Vérification et retour du résultat
                    if($reponse = $bdd->query($reqAfficheVehicule) ){
                        #Pour se retrouver au final, qu'avec les véhicules libres entre les dates spécifiées
                        $vehicules = $reponse->fetchAll();
                        //Conversion du format du tableau en JSON
                        $vehicules = json_encode($vehicules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);     
                        $reponse->closeCursor();
                        return $vehicules;
                    }
                    else{
                        echo "Aucun véhicule trouvé !";
                        return false;
                    } 
                    
                } //End else if($dataId)
            } //End else(dateDebut>=dateFin)
            
        } //End afficheVehicule(dateDebut, dateFin)

        public static function afficheVehicules(){
            global $bdd;
            $reqAfficheVehicule = "SELECT DISTINCT v.idVehicule, ma.idMarque, marque, mo.idModele, modele, typeVehicule, CONCAT(prix, ' FCFA') AS prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, cheminPhoto, v.description FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire";
            $reponse = $bdd->query($reqAfficheVehicule);
            
            if($vehicules = $reponse->fetchAll()){
                //Conversion du format du tableau en JSON
                $vehicules = json_encode($vehicules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); //Conversion du format du tableau en JSON
                $reponse->closeCursor();
                return $vehicules;
            } //End if
            else{
                echo "Aucun véhicule trouvée !";
                return false;
            }
           
        } //End afficheVehicules()

        public static function filtreVehicule($filtre){ //Affiche des critères de véhicule selon le filtre
            global $bdd;
            if ($filtre=='marque' || $filtre=='modele' || $filtre=='typevehicule' || $filtre=='carburant' || $filtre=='proprietaire' ||  $filtre=='raisonSociale' || $filtre=='fonction'){
                $requete = "SELECT * FROM $filtre";
            }
            else{
                switch ($filtre){
                    case 'clim-oui':
                        $requete = "SELECT marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, cheminPhoto FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND idDate=idDisponibilite AND climatisation='oui' ";
                    break;
                    case 'clim-non':
                        $requete = "SELECT marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, cheminPhoto FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND idDate=idDisponibilite AND climatisation='non' ";
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

        public static function filtrage($idMarque, $idModele, $idType, $idCarburant, $climatisation, $dateDebut, $dateFin){
            global $bdd;
            $val_interdit = -1; //Valeur prise si un critère n'est pas spécifié
            $data = array(); //Tableau qui va recevoir le array associatif associé à la requête
            //Base de la requête
            $requete_temp = "SELECT DISTINCT marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, cheminPhoto FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND "; 
            //Formalisation de la requête à transmettre à la bdd
            $suite1 = $idMarque==$val_interdit ? "v.idMarque=ma.idMarque AND " : "v.idMarque=:idMarque AND ";
            $suite2 = $idModele==$val_interdit ? "v.idModele=mo.idModele AND " : "v.idModele=:idModele AND ";
            $suite3 = $idType==$val_interdit ?   "v.idType=idTypeVehicule AND " : "v.idType=:idType AND ";
            $suite4 = $idCarburant==$val_interdit ? "v.idCarburant=ca.idCarburant AND " : "v.idCarburant=:idCarburant AND ";
            $suite5 = $climatisation==$val_interdit ? "climatisation=climatisation" : "climatisation=:climatisation";
            if($dateDebut==$val_interdit || $dateFin==$val_interdit){
                $suite6 = '';
            } //End if($dateDebut || $dateFin)
            else{
                //Ajustement du format des dates
                $dateDebut = date("Y-m-d", strtotime($dateDebut));
                $dateFin = date("Y-m-d", strtotime($dateFin));
                //Vérification de la conformité de la période
                if ($dateDebut > $dateFin){
                    echo "La date de début ne peut être supérieure à la date de fin !";
                    return false;
                }
                else{
                    $suite6 = ' ';
                    //On récupère les ids de l'ensemble des véhicules réservés
                    $vehicReserve = Vehicule::getReserve($dateDebut, $dateFin);
                    if($vehicReserve){
                        //echo "Aucun véhicule de ce type n'est reservé entre les dates spécifiées";
                        //die;
                    //}else {
                        //Formalisation de la requête
                        for($i=0; $i<count($vehicReserve); $i++){
                            $cle = 'idVehic'.$i;
                            $suite6 .= "AND v.idVehicule!=:$cle ";
                            //Formalisation de la requête
                            $data[$cle] = $vehicReserve[$i];
                            
                        } //End for
                    }
                    
                } //End else (date)
            } //End else
            //Requête finale
            $requete = $requete_temp . $suite1 . $suite2 . $suite3 . $suite4 . $suite5 . $suite6; //Requête finale
            $reponse = $bdd->prepare($requete);

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

        public static function ajoutVehicule($idMarque, $idModele, $idType, $idProprietaire, $idCarburant, $immatriculation, $climatisation, $nbPorte, $nbPlace, $description, $prix, $boiteDeVitesse){
            global $bdd;

            $requete = 'INSERT INTO Vehicule(idMarque, idModele, idType, idProprietaire, idCarburant, immatriculation, climatisation, nombreDePortes, nombreDePlaces, description, prix, boiteDeVitesse) VALUES(:idMarque, :idModele, :idType, :idProprietaire, :idCarburant, :immatriculation, :climatisation, :nbPorte, :nbPlace, :description, :prix, :boiteDeVitesse)';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array(
            'idMarque' => $idMarque,
            'idModele' => $idModele,
            'idType' => $idType,
            'idProprietaire' => $idProprietaire,
            'idCarburant' => $idCarburant,
            'immatriculation' => $immatriculation,
            'climatisation' => $climatisation,
            'nbPorte' => $nbPorte,
            'nbPlace' => $nbPlace,
            'description' => $description,
            'prix' => $prix,
            'boiteDeVitesse' => $boiteDeVitesse
            ));
            //Vérification de l'ajout de véhicule
            if($reponse->rowCount() > 0){
                echo "Succes. Véhicule ajouté !";
            } 
            else{
                echo "Une erreur est survenue lors de l'ajout du véhicule !";
                return false;
            }
                
                $reponse->closeCursor();
            
        } //End ajoutVehicule()

        public static function modifierVehicule($idVehicule, $idMarque, $idModele, $idType, $idProprietaire, $idCarburant, $immatriculation, $climatisation, $nbPorte, $nbPlace, $description, $prix, $boiteDeVitesse){
            global $bdd;

            $requete = 'UPDATE Vehicule SET idMarque=:idMarque, idModele=:idModele, idType=:idType, idProprietaire=:idProprietaire, idCarburant=:idCarburant, immatriculation=:immatriculation, climatisation=:climatisation, nombreDePortes=:nombreDePortes, nombreDePlaces=:nombreDePlaces, description=:description, prix=:prix, boiteDeVitesse=:boiteDeVitesse WHERE idVehicule=:idVehicule';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array(
            'idVehicule' => $idVehicule,
            'idMarque' => $idMarque,
            'idModele' => $idModele,
            'idType' => $idType,
            'idProprietaire' => $idProprietaire,
            'idCarburant' => $idCarburant,
            'immatriculation' => $immatriculation,
            'climatisation' => $climatisation,
            'nombreDePortes' => $nbPorte,
            'nombreDePlaces' => $nbPlace,
            'description' => $description,
            'prix' => $prix,
            'boiteDeVitesse' => $boiteDeVitesse
    
            ));
            //Vérification de la réussite de la mise à jour
            if($reponse->rowCount() > 0){
                echo "Succes. Véhicule mis à jour !";
            } 
            else{
                echo "Une erreur est survenue lors de la modification du véhicule !";
                return false;
            }
                
                $reponse->closeCursor();
            
        } //End modifierVehicule()

        public static function supprimerVehicule($id){
            global $bdd;
            if(Vehicule::checkVehicule($id)){
                echo "Ce véhicule est en cours de réservation.";
                return false;      
            }
            else{
                $requete = 'DELETE FROM Vehicule WHERE idVehicule=?';
                $reponse = $bdd->prepare($requete);
                $reponse->execute(array($id));
                //Vérification de la réussite de la suppréssion
                if($reponse->rowCount() > 0){
                    echo "Succes. Véhicule supprimé !";
                    return true;
                } 
                else{
                    echo "Une erreur est survenue lors de la suppréssion du véhicule !";
                    return false;
                }
                $reponse->closeCursor();
            }

        } //End supprimerVehicule($id)

        public static function checkVehicule($id){
            global $bdd;
            $requete = "SELECT idVehicule FROM Reservation WHERE statut='En cours' AND idVehicule=?";
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            if($reponse->fetch()){
                return true;
            }
            else{
                return false;
            }
        }

        public static function ajoutMarque($marque){
            global $bdd;
            $marque = strtoupper($marque); //Conversion en majuscule
            if (Vehicule::verifDoublons('marque', 'Marque', $marque)){
                echo "Cette marque existe déja !";
                return false;
            }
            else{
                $requete = "INSERT INTO Marque(marque) VALUES (?)";
                $reponse = $bdd->prepare($requete);
                $reponse->execute(array($marque));
                if($reponse->rowCount() > 0){
                    echo "Succes. Marque ajoutée.";
                    return true;
                }
                else{
                    echo "Une erreur est survenue lors de l'ajout de la marque !";
                    return false;
                }
                $reponse->closeCursor();
            } //End else
            
        } //End ajoutmarque()

        public static function ajoutModele($modele, $idMarque){
            global $bdd;
            $modele = strtoupper($modele); //Conversion en majuscule
            if (Vehicule::verifDoublons('modele', 'Modele', $modele)){
                echo "Ce modele existe déja !";
                return false;
            }
            else{
                $requete = "INSERT INTO Modele(idMarque, modele) VALUES (?, ?)";
                $reponse = $bdd->prepare($requete);
                $reponse->execute(array($idMarque, $modele));
                if($reponse->rowCount() > 0){
                    echo "Succes. Modele ajouté.";
                    return true;
                }
                else{
                    echo "Une erreur est survenue lors de l'ajout du modele !";
                    return false;
                }
                $reponse->closeCursor();
            } //End first else
            
        } //End ajoutModele()

        public static function ajoutTypeVehicule($type){
            global $bdd;
            $type = strtoupper($type); //Conversion en majuscule
            if (Vehicule::verifDoublons('typeVehicule', 'TypeVehicule', $type)){
                echo "Ce type de véhicule existe déja !";
                return false;
            }
            else{
                $requete = "INSERT INTO TypeVehicule(typeVehicule) VALUES (?)";
                $reponse = $bdd->prepare($requete);
                $reponse->execute(array($type));
                if($reponse->rowCount() > 0){
                    echo "Succes. Type de véhicule ajouté.";
                    return true;
                }
                else{
                    echo "Une erreur est survenue lors de l'ajout du type de véhicule !";
                    return false;
                }
                $reponse->closeCursor();
            } //End first else
            
        } //End ajoutType()

        public static function afficheModele($idMarque){
            global $bdd;
            if($idMarque==""){
                $requete = "SELECT * FROM Modele";
                $reponse = $bdd->query($requete);
            }
            else{
                $requete = "SELECT * FROM Modele WHERE idMarque=?";
                $reponse = $bdd->prepare($requete);
                $reponse->execute(array($idMarque));
            }
            if($modele = $reponse->fetchAll()){
                $modele = json_encode($modele, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); 
                $reponse->closeCursor();
                return $modele;
            }
            else{
                echo "Aucun modéle trouvé.";
                return false;
            }
        } //End afficheModele

        public static function afficheMarques(){
            global $bdd;
            $requete = "SELECT * FROM Marque";
            $reponse = $bdd->query($requete);
            if($marque=$reponse->fetchAll()){
                $marque = json_encode($marque, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);     
                $reponse->closeCursor();
                return $marque;
            }
            else{
                echo "Aucune marque trouvée.";
                return false;
            }
        }

        public static function supprimerCaracVehicule($carac, $id){
            global $bdd;
            $type_authorise = array('marque', 'modele', 'typevehicule', 'typeVehicule');
            if(in_array($carac, $type_authorise)){
                $idName = 'id'.$carac;
                $requete = "DELETE FROM $carac WHERE $idName=?";
                $reponse = $bdd->prepare($requete);
                $reponse->execute(array($id));
                //Vérification de la réussite de la suppréssion
                if($reponse->rowCount() > 0){
                    echo "Succes. $carac supprimé(e) !";
                    return true;
                } 
                else{
                    echo "Une erreur est survenue lors de la suppression de la/du $carac !";
                    return false;
                }
                $reponse->closeCursor();

            } //End if in_array()
            else{
                echo "Type choisi non autorisé !";
                return false;
            }
            
        } //End supprimerVehicule($id)

        public static function modifierCaracVehicule($carac, $id, $valeur, $id2){
            global $bdd;
            $idName = 'id'.$carac;
            $valeur = strtoupper($valeur); //Conversion en majuscule
            if($carac=='marque' || $carac=='typeVehicule' || $carac=='typevehicule' ){
                if (Vehicule::verifDoublons($carac, $carac, $valeur)){
                    echo "Ce/Cette $carac existe déja !";
                    return false;
                }
                else{
                    $requete = "UPDATE $carac SET $carac=? WHERE $idName=?";
                    $reponse = $bdd->prepare($requete);
                    $reponse->execute(array($valeur, $id));
                    if ($reponse->rowCount() > 0){
                        echo "$carac mis à jour !";
                        return true;
                    }
                    else{
                        echo "Une erreur est survenue lors de l'ajout du caractère $carac";
                        return false;
                        }
                } //End else if(verifDoublons) 
                
            } //End if(carac==marque)
            else if($carac=='modele'){
                if (Vehicule::verifDoublons($carac, $carac, $valeur)){
                    echo "Ce/Cette $carac existe déja !";
                    return false;
                }
                else{
                    $requete = "UPDATE $carac SET idMarque=? ,$carac=? WHERE $idName=?";
                    $reponse = $bdd->prepare($requete);
                    $reponse->execute(array($id2, $valeur, $id));
                    if ($reponse->rowCount() > 0){
                        echo "$carac mis à jour !";
                        return true;
                    }
                    else{
                        echo "Une erreur est survenue lors de l'ajout du caractère $carac";
                        return false;
                    }
                } //End else if(verifDoublons)
            } //End else if(carac==modele)
            else{
                echo "Caractère non autorisé !";
                return false;
            }
            $reponse->closeCursor();
        } //End modifierCaracVehicule()

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
            $reqRecupIdDisponibilite = "SELECT DISTINCT idVehicule FROM Reservation, Disponibilite WHERE dateDebut<=:dateFin AND dateFin>=:dateDebut AND statut='En cours' AND idDate=idDisponibilite";
            $reponse = $bdd->prepare($reqRecupIdDisponibilite);
            $reponse->execute(array(
                'dateDebut' => $dateDepart, 
                'dateFin' => $dateArrivee));
             //On retourne l'ensemble des vehicules réservés
             while ($dataId=$reponse->fetch()){
                array_push($data, $dataId['idVehicule']);
            }
            if(empty($data)){
                //echo "Aucun vehicule trouvé";
                return false;
            }
            else{
                return $data;
            }
            
        } //End getReserve()

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