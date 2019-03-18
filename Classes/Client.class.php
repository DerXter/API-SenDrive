<?php
    class Client{
        //Attributs
        private $_idClient;
        private $_nom;
        private $_prenom;
        private $_email;
        private $_telephone;
        private $_adresse;
        private $_destination;

        //Methodes
        public static function ajoutClient($nom, $prenom, $telephone, $adresse, $mail){
            global $bdd;
            if(Client::checkDoublon("email", $mail) || Client::checkDoublon("telephone", $telephone)){
                echo "Numéro de téléphone ou mail déjà utilisé.";
                return false;
            }
            else{
                $requete = 'INSERT INTO Clientele (nom, prenom, email, telephone, adresse) VALUES(:nom, :prenom, :mail, :telephone, :adresse)';
                $reponse = $bdd->prepare($requete);
                $reponse->execute(array(
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'telephone' => $telephone,
                    'adresse' => $adresse,
                    'mail' => $mail,
                ));
                //Vérification de la réussite de l'ajout
                if($reponse->rowCount() > 0){
                    echo "Succes. Client ajouté !";
                    return $bdd->lastInsertId();
                } 
                else{
                    echo "Une erreur est survenue lors de l'ajout du client !";
                    return false;
                }
                $reponse->closeCursor();

            } //End else
            
        } //End ajoutClient()

        public static function afficheClients(){
            global $bdd;
            $requete = 'SELECT * FROM Clientele';
            $reponse = $bdd->query($requete);
            if($clients = $reponse->fetchAll()){
                $clients = json_encode($clients, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
                $reponse->closeCursor();
                return $clients;
            }
            else{
                echo "Aucun client trouvé !";
                return false;
            }
        } //End afficheClients()
        public static function checkDoublon($nature, $valeur){
            global $bdd;
            $doublon = false;
            $requete = "SELECT $nature FROM Clientele WHERE $nature=?";
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($valeur));
            while($data = $reponse->fetch()){
                $doublon = true;
                break;
            }
            return $doublon;
        } //End checkDoublon

    } //End class Client