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
        public static function affichePersonnel(){
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

        } //End affichePersonnel()

        public static function ajoutPersonnel($civilite, $poste, $nom, $prenom, $dateNaissance, $numeroIdentite, $adresse, $telephone, $email){
            global $bdd;
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
            
            } //End if
            else{
                echo "Civilité ou Fonction choisi(e) indisponible !";
                return flase;
            }
            $reponse->closeCursor();
        } //End ajoutPersonnel()

        public static function modifierPersonnel($idPersonnel, $civilite, $poste, $nom, $prenom, $dateNaissance, $numeroIdentite, $adresse, $telephone, $email){
            global $bdd;
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
            
            } //End if
            else{
                echo "Civilité ou Fonction choisi(e) indisponible !";
                return flase;
            }
            $reponse->closeCursor();
        } //End modifierPersonnel()

        public static function supprimerPersonnel($id){
            global $bdd;
            $requete = 'DELETE FROM Personnel WHERE idPersonnel=?';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            $reponse->closeCursor();

        } //End supprimerPersonnel($id)

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

    } //End Personnel