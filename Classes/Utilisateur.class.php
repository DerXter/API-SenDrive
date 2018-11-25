<?php
    class Utilisateur{
        //Attributs
        private $idUtilisateur;
        private $login;
        private $password;
        private $statut;
        private $idPersonnel;
        private $cheminPhoto;

        //Constructeur
        public function __construct($id){
            global $bdd;
            $reponse = $bdd->prepare('SELECT * FROM Utilisateur WHERE idUtilisateur=?');
            $reponse->execute(array($id));
            $data = $reponse->fetch();

            //Affectation des données de la base à l'objet
            $this->idUtilisateur = $id;
            $this->login = $data['login'];
            $this->password = $data['password'];
            $this->statut = $data['statut'];
            $this->idPersonnel = $data['idPersonnel'];
            $this->cheminPhoto = $data['cheminPhoto'];

            $reponse->closeCursor();
        } //End __construct

        //Fonctions
        public static function ajoutUtilisateur($login, $password, $statut, $numIdentite){
            global $bdd;
            // Hachage du mot de passe
            $pass_hache = hash('sha256', $password);

            $idPersonnel = Utilisateur::returnId('idPersonnel', 'Personnel', 'numeroIdentite', $numIdentite);
            $reqAjoutUtilisateur = 'INSERT INTO Utilisateur (login, password, statut, idPersonnel) VALUES (:login, :password, :statut, :idPersonnel)';
            $reponse = $bdd->prepare($reqAjoutUtilisateur);
            $reponse->execute(array(
                'login' => $login,
                'password' => $pass_hache,
                'statut' => $statut,
                'idPersonnel' => $idPersonnel 
            ));
            //Vérification de la réussite de l'ajout
            if($reponse->rowCount() > 0){
                echo "Utilisateur ajouté !";
            } 
            else{
                echo "Une erreur est survenue lors de l'ajout de l'utilisateur' !";
                return false;
            }

            $reponse->closeCursor();
        } //End ajoutUtilisateur()

        public static function afficheUtilisateurs(){
            global $bdd;
            $reqAfficheUtilisateur = 'SELECT login, statut, idPersonnel, cheminPhoto FROM Utilisateur';
            $reponse = $bdd->query($reqAfficheUtilisateur);
            if ($utilisateurs = $reponse->fetchAll()){
                $utilisateurs = json_encode($utilisateurs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); //Conversion du format du tableau en JSON
                $reponse->closeCursor();
                return $utilisateurs;
            }
            else{
                echo "Aucun utilisateur trouvé !";
                return false;
            }
            
        } //End afficheUtilisateurs()
        
        public static function connexion($login, $password){
            global $bdd;
            // Hachage du mot de passe
            $pass_hache = hash('sha256', $password);

            // Vérification des identifiants
            $reqVerif = 'SELECT idUtilisateur FROM Utilisateur WHERE login = :login AND password = :password';
            $reponse = $bdd->prepare($reqVerif);
            $reponse->execute(array(
            'login' => $login,
            'password' => $pass_hache));
            //Vérification du résultat
            if ($resultat = $reponse->fetch()){
                $idUser = $resultat['idUtilisateur'];
                $user = new Utilisateur($idUser);
                $user = get_object_vars($user); //Conversion de l'obet $user en tableau associatif contenant tous les attributs et valeurs de l'objet
                $user = json_encode($user, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); //Conversion du format du tableau en JSON
                $reponse->closeCursor();
                return $user;       
            }
            else{
                echo 'Identifiant ou Mot de passe incorrect!';
                return false;
            }
            
        } //End connexion

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

    } //End Utilisateur