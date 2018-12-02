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
            if(Utilisateur::checkLogin($login)){
                echo "Ce nom d'utilisateur est déjà utilisé !";
            }
            else{
                // Hachage du mot de passe
                $pass_hache = hash('sha256', $password);
                $idPersonnel = Utilisateur::returnId('idPersonnel', 'Personnel', 'numeroIdentite', $numIdentite);
                $idPersonnel = (int)$idPersonnel;
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
            } //End else checkLogin
            
        } //End ajoutUtilisateur()

        public static function modifierUtilisateur($id, $login, $statut, $idPersonnel){
            global $bdd;
            $loginActuel = Utilisateur::getLogin($id);
            if($loginActuel!=-1){
                if(strcasecmp($login, $loginActuel)==0 || ($login!=$loginActuel && !Utilisateur::checkLogin($login))){
                    $reqAjoutUtilisateur = 'UPDATE Utilisateur SET login=:login, statut=:statut, idPersonnel=:idPersonnel WHERE idUtilisateur=:id';
                    $reponse = $bdd->prepare($reqAjoutUtilisateur);
                    $reponse->execute(array(
                        'login' => $login,
                        'id' => $id,
                        'statut' => $statut,
                        'idPersonnel' => $idPersonnel 
                    ));
                    //Vérification de la réussite de la modification
                    if($reponse->rowCount() > 0){
                        echo "Utilisateur modifié !";
                    } 
                    else{
                        echo "Une erreur est survenue lors de la modification de l'utilisateur' !";
                        return false;
                    }
    
                    $reponse->closeCursor();
                    
                } //End first if
                else{
                    echo "Ce nom d'utilisateur est déjà utilisé !";
                    return false;
                }
                
            } //End if (Utilisateur::getLogin)
            else{
                echo "Aucun utilisateur ne correspond à cet identifiant !";
                return false;
            }
            
            
        } //End modifierUtilisateur()

        public static function changePassword($id, $oldPassword, $newPassword){
            // Hachage de l'ancien mot de passe
            $oldPass_hache = hash('sha256', $oldPassword);
            $currentPass = Utilisateur::getPassword($id);
            if($currentPass!=-1 && $currentPass!=$oldPass_hache){
                echo "Mot de passe incorrect !";
                return false;
            }
            else if($currentPass!=-1 && $currentPass==$oldPass_hache){
                global $bdd;
                // Hachage du nouveau mot de passe
                $newPass_hache = hash('sha256', $newPassword);
                $requete = "UPDATE Utilisateur SET password=? WHERE idUtilisateur=?";
                $reponse = $bdd->prepare($requete);
                $reponse->execute(array($newPass_hache, $id));
                if($reponse->rowCount() > 0){
                    echo "Mot de passe modifié !";
                    return true;
                }
                else{
                    echo "Une erreur est survenue lors de la modification du mot de passe !";
                    return false;
                }
            } //End else if
            else{
                echo "Aucun utilisateur ne correspond à cet identifiant !";
                return false; 
            }

        } //End changePassword()

        public static function afficheUtilisateurs(){
            global $bdd;
            $reqAfficheUtilisateur = 'SELECT prenom, nom, dateNaissance, numeroIdentite, fonction, adresse, telephone, email, login, statut, cheminPhoto FROM Utilisateur NATURAL JOIN (Personnel NATURAL JOIN Fonction)';
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

        public static function afficheUtilisateur($id){
            global $bdd;
            $reqAfficheUtilisateur = 'SELECT prenom, nom, dateNaissance, numeroIdentite, fonction, adresse, telephone, email, login, statut, cheminPhoto FROM Utilisateur NATURAL JOIN (Personnel NATURAL JOIN Fonction) WHERE idUtilisateur=?';
            $reponse = $bdd->prepare($reqAfficheUtilisateur);
            $reponse->execute(array($id));
            if ($utilisateurs = $reponse->fetchAll()){
                $utilisateurs = json_encode($utilisateurs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); //Conversion du format du tableau en JSON
                $reponse->closeCursor();
                return $utilisateurs;
            }
            else{
                echo "Aucun utilisateur ne correspond à cet identifiant !";
                return false;
            }
        }
        
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

        public static function checkLogin($login){
            global $bdd;
            $requete = "SELECT login FROM Utilisateur WHERE login LIKE ? ";
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($login));
            if($reponse->fetch()){
                $reponse->closeCursor();
                return true;
            }
            else{
                $reponse->closeCursor();
                return false;
            }

        }

        public static function getLogin($id){
            global $bdd;
            $requete = "SELECT login FROM Utilisateur WHERE idUtilisateur=? ";
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            if($data=$reponse->fetch()){
                $reponse->closeCursor();
                return $data['login'];
            }
            else{
                $reponse->closeCursor();
                return -1;
            }

        } //End getLogin
        public static function getPassword($id){
            global $bdd;
            $requete = "SELECT password FROM Utilisateur WHERE idUtilisateur=? ";
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            if($data=$reponse->fetch()){
                $reponse->closeCursor();
                return $data['password'];
            }
            else{
                $reponse->closeCursor();
                return -1;
            }

        } //End getPassword

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