<?php
    class Personnel{
        //Attributs
        private $idPersonnel;
        private $idCivilite;
        private $idFonction;
        private $nom;
        private $prenom;
        private $dateNaissance;
        private $numeroIdentite;
        private $adresse;
        private $telephone;
        private $email;

        //Fonctions
        public static function affichePersonnels(){
            global $bdd;
            $requete = 'SELECT idPersonnel, civilite, fonction, nom, prenom, dateNaissance, numeroIdentite, adresse, telephone, email FROM Personnel p, Civilite c, Fonction f WHERE p.idCivilite=c.idCivilite AND p.idFonction=f.idFonction';
            $reponse = $bdd->query($requete);
            if ($personnel = $reponse->fetchAll()){
                $personnel = json_encode($personnel, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                return $personnel;
            }
            else{
                echo "Aucun personnel trouvé !";
                return false;
            }

        } //End affichePersonnels()
        public static function affichePersonnel($id){
            global $bdd;
            $requete = 'SELECT prenom, nom, dateNaissance, numeroIdentite, civilite, fonction, adresse, telephone, email FROM Personnel p, Civilite c, Fonction f WHERE p.idCivilite=c.idCivilite AND p.idFonction=f.idFonction AND idPersonnel=?';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            if ($personnel = $reponse->fetchAll()){
                $personnel = json_encode($personnel, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                return $personnel;
            }
            else{
                echo "Aucun personnel trouvé !";
                return false;
            }

        } //End affichePersonnel($id)

        public static function ajoutPersonnel($civilite, $poste, $nom, $prenom, $dateNaissance, $numeroIdentite, $adresse, $telephone, $email){
            global $bdd;
            //Vérification de l'unicité du personnel ajouté
            if(Personnel::verifDoublons('numeroIdentite', 'Personnel', $numeroIdentite)){
                echo "Numéro d'identité déjà utilisé !";
                return false;
            }
            else{
                $dateNaissance = date("Y-m-d", strtotime($dateNaissance));
                $idCivilite = Personnel::returnId('idCivilite', 'Civilite', 'civilite', $civilite);
                $idFonction = Personnel::returnId('idFonction', 'Fonction', 'fonction', $poste);
                if($idCivilite!=false && $idFonction!=false){
                    $requete = "INSERT INTO Personnel (idCivilite, idFonction, nom, prenom, dateNaissance, numeroIdentite, adresse, telephone, email) VALUES (:idCivilite, :idFonction, :nom, :prenom, :dateNaissance, :numeroIdentite, :adresse, :telephone, :email)";
                    $reponse = $bdd->prepare($requete);
                    $reponse->execute(array(
                        'idCivilite' => $idCivilite,
                        'idFonction' => $idFonction, 
                        'nom' => $nom, 
                        'prenom' => $prenom, 
                        'dateNaissance' => $dateNaissance, 
                        'numeroIdentite' => $numeroIdentite, 
                        'adresse' => $adresse, 
                        'telephone' => $telephone,
                        'email' => $email
                    ));
                    //Vérification de la réussite de l'ajout
                    if($reponse->rowCount() > 0){
                        echo "Ajout réussi !";
                    } 
                    else{
                        echo "Une erreur est survenue lors de l'ajout du personnel !";
                        return false;
                    }
                
                } //End if
                else{
                    echo "Civilité ou Fonction choisi(e) indisponible !";
                    return false;
                }
                $reponse->closeCursor();
            } //End else if(verifDoublons)
        } //End ajoutPersonnel()

        public static function modifierPersonnel($idPersonnel, $civilite, $poste, $nom, $prenom, $dateNaissance, $numeroIdentite, $adresse, $telephone, $email){
            global $bdd;
            //Vérification de l'unicité du personnel ajouté
            if(Personnel::verifPersonnel($idPersonnel, $numeroIdentite)){
                echo "Numéro d'identité déjà utilisé !";
                return false;
            }
            else{
                $dateNaissance = date("Y-m-d", strtotime($dateNaissance));
                $idCivilite = Personnel::returnId('idCivilite', 'Civilite', 'civilite', $civilite);
                $idFonction = Personnel::returnId('idFonction', 'Fonction', 'fonction', $poste);
                if($idCivilite!=false && $idFonction!=false){
                    $requete = "UPDATE Personnel SET idCivilite=:idCivilite, idFonction=:idFonction, nom=:nom, prenom=:prenom, dateNaissance=:dateNaissance, numeroIdentite=:numeroIdentite, adresse=:adresse, telephone=:telephone, email=:email WHERE idPersonnel=:idPersonnel";
                    $reponse = $bdd->prepare($requete);
                    $reponse->execute(array(
                        'idCivilite' => $idCivilite,
                        'idFonction' => $idFonction, 
                        'nom' => $nom, 
                        'prenom' => $prenom, 
                        'dateNaissance' => $dateNaissance, 
                        'numeroIdentite' => $numeroIdentite, 
                        'adresse' => $adresse, 
                        'telephone' => $telephone,
                        'email' => $email,
                        'idPersonnel' => $idPersonnel
                    ));
                    //Vérification de la réussite de la modification
                    if($reponse->rowCount() > 0){
                        echo "Personnel mis à jour !";
                    } 
                    else{
                        echo "Une erreur est survenue lors de la mise à jour du personnel !";
                        return false;
                    }
                
                } //End if
                else{
                    echo "Civilité ou Fonction choisi(e) indisponible !";
                    return false;
                }
                $reponse->closeCursor();
            } //End else if (VerifPersonnel)
        } //End modifierPersonnel()

        public static function supprimerPersonnel($id){
            global $bdd;
            $requete = 'DELETE FROM Personnel WHERE idPersonnel=?';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            //Vérification de la réussite de la suppréssion
            if($reponse->rowCount() > 0){
                echo "Personnel supprimé !";
            } 
            else{
                echo "Une erreur est survenue lors de la suppréssion du personnel !";
                return false;
            }
            $reponse->closeCursor();

        } //End supprimerPersonnel($id)

        public static function verifPersonnel($idPersonnel, $numeroIdentite){
            global $bdd;
            $requete = "SELECT idPersonnel FROM Personnel WHERE numeroIdentite=? AND idPersonnel!=?";
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($numeroIdentite, $idPersonnel));
            if($reponse->fetch()){
                return true;
            } 
            else{
                return false;
            }
            $reponse->closeCursor();
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
                if(strcasecmp($valeur, $data[$donnee])==0){ //Comparaison insensible à la casse
                    $result = true;
                    break;
                } //End if
            } //End while ()
            $reponse->closeCursor();
            return $result;
        } //End verifDoublons()

        public function ajoutFonction($fonction){
            if(Personnel::verifDoublons("fonction", "Fonction", $fonction)){
                echo "Ce poste existe déjà !";
            }
            else{
                global $bdd;
                $ajoutFonction = "INSERT INTO Fonction SET fonction=?";
                $reponse = $bdd->prepare($ajoutFonction);
                $reponse->execute(array($fonction));
                if($reponse->rowCount()>0){
                    echo "Fonction ajoutée !";
                    return true;
                }
                else{
                    echo "Fonction non ajoutée !";
                    return false;
                }

            } //End else
            
            
        } //AjoutFonction
    } //End Personnel