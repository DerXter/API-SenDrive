# API-SenDrive
API gérant la partie backend de la plateforme S'en Drive Solutions.
------------------------------------------------------------------
Cet API, réalisé en __PHP Objet__, contient une multitude de *fonctions* renvoyant des données au format __JSON__.  
Les requêtes devront être structurées de la sorte :  
## Pour les fonctions d'affichage (qui doivent retourner des données) :  

	get('http://localhost/API-SenDrive/senDrive.php',{  
                fonction:'nomDeLafonction',  
                Paramètre_1: 'valeur_1',  
                Paramètre_2: 'valeur_2',  
                ...,  
                Paramètre_n: 'valeur_n'  
                
                } ,function(data){  
                
                console.log(data)  
            })  
  
## Pour les fonctions d'upload de données (qui doivent recevoir des données) :  

	post('http://localhost/API-SenDrive/senDrive.php',{
                fonction:'nomDeLafonction',
                Paramètre_1: 'valeur_1',
                Paramètre_2: 'valeur_2',
                ...,
                Paramètre_n: 'valeur_n'
                
                } ,function(data){
                
                console.log(data)
            })
  
  
## Fonctions disponibles :  
### Fonctions d'affichage (get):  
* __afficheChauffeurs (statut)__ : Affichage de tous les chauffeurs selon leurs statuts de disponibilité.   
> __INFO__: Le statut est facultatif. S'il n'est pas spécifié, tous les véhicules, *Libres* comme *Réservé*  seront affichés.    
* __afficheChauffeur(dateDebut, dateFin)__ : Affichage des chauffeurs disponibles entre les dates indiquées  
* __connexion(login, password)__ : connexion des utilisateurs  
> __IMPORTANT:__ Si réussie, la fonction retourne un *array* contenant les informations de l'utilisateur connecté et retoune *false* si non.  
* __afficheVehicules(statut)__ : Affichage de tous les véhicules leurs statuts de disponibilité   
> __INFO__: Le statut est facultatif. S'il n'est pas spécifié, tous les véhicules, *Libres* comme *Réservé*  seront affichés.
* __afficheVehicule(dateDebut, dateFin)__ : Affichage des véhicules disponibles entre les dates indiquées  
* __filtreVehicule(filtre)__ : Affichage des critères de véhicule selon __un__ filtre indiqué à la fois    
> __IMPORTANT :__ Utiliser comme filtre, *clim-oui* pour afficher les véhicules climatisés et *clim-non*,  
dans le cas contraire.  
* __filtrage(idMarque, idModele, idType, idCarburant, climatisation) :__  Affichage des véhicules selon les critères indiqués.  
> __NOTE :__ Indiquer la valeur __-1__ à la place d'un critère pour l'ignorer.  
* __afficheClients()__ : Affichage des clients  
* __afficheReservations()__ : Affichage des réservations  
* __afficheUtilisateurs()__ : Affichage des utilisateurs  
* __afficheProprio()__ : Affichage des proprietaires
  
> __IMPORTANT__ : Les fonctions d'affichage retournent *false* si aucune donnée à afficher n'a été trouvée.    
* __affichePersonnel__ : Affichage du personnel   
* __afficheDoc(nature)__ : Affichage des fichiers de documentation dont la nature est spécifiée  
> __NOTE :__ Les natures disponibles sont :  
* contrat    
* fiche  
* processus  
* facture  
* gestion  
> __NOTE :__ *fiche* indique les fiches d'état des lieux.  
  
### Fonctions d'upload de données (post):    
* __ajoutClient(nom, prenom, telephone, adresse, mail, destination)__ : Ajout de clients    
* __ajoutReservation(idVehicule, idChauffeur, dateDebut, dateFin)__ : Ajout d'une réservation   
> __IMPORTANT:__ Il faudra ajouter d'abord le client ensuite la reservation vue que dans la table *Reservation*, il y'aura l'id du client en question.
* __modifierReservation(idReservation, idClient, idVehicule, idChauffeur, dateDebut, dateFin, statut)__ : Modification d'une réservation  
* __supprimerReservation(id)__ : Suppréssion d'une réservation  
* __changerStatutReservation(idReservation, statut)__ : Changement du statut d'une reservation    
> __IMPORTANT:__ Les statuts possibles sont: *En cours*, *En attente*, *Annulé*, *Terminé*.      
* __ajoutUtilisateur(login, password, statut, numIdentite)__ : Ajout d'utilisateurs   
> __IMPORTANT:__ L'utilisateur doit être au préalable dans la table *Personnel*   
* __ajoutProprio(raisonSociale, proprietaire, dateNaissance, numIdentite, telephone, adresse, email)__ : Ajout de propriétaire  
> __IMPORTANT:__ Les raisons sociales possibles sont: *Particulier* et  *Professionnel*. 
* __modifProprio(idProprietaire, raisonSociale, proprietaire, dateNaissance, numIdentite, telephone, adresse, email)__ : Modification de propriétaire  
* __supprimerProprio(id)__ : Suppréssion d'un propriétaire  
> __NOTE__ : Le propriétaire est aussi le *Partenaire*.  
* __ajoutVehicule(idMarque, idModele, idType, idProprietaire, idCarburant, dateDebut, dateFin, immatriculation, climatisation, nbPorte, nbPlace, description, prix, boiteDeVitesse)__: Ajout de véhicule  
* __modifierVehicule(idVehicule, idMarque, idModele, idType, idProprietaire, idCarburant, dateDebut, dateFin, immatriculation, climatisation, nbPorte, nbPlace, description, prix, boiteDeVitesse, statut)__ : Modification de véhicule  
* __supprimerVehicule(id)__ : Suppression de véhicule  
* __ajoutPersonnel(civilite, poste, nom, prenom, dateNaissance, numeroIdentite, adresse, telephone, email)__: Ajout du personnel  
* __modifierPersonnel(idPersonnel, civilite, poste, nom, prenom, dateNaissance, numeroIdentite, adresse, telephone, email)__: Modification du personnel  
* __supprimerPersonnel(id)__: Suppression de personnel  
* __ajoutChauffeur (prenom, nom, dateNaissance, numeroIdentite, permis, adresse, telephone, dateDebut, dateFin, commentaire)__: Ajout de chauffeurs  
* __modifierChauffeur(idChauffeur, prenom, nom, dateNaissance, numeroIdentite, permis, adresse, telephone, dateDebut, dateFin, commentaire, statut)__: Modification de chauffeurs  
* __supprimerChauffeur (id)__: Suppréssion de chauffeurs  
  

  
> __*IMPORTANT*__ : Dans le fichier *connexion.class.php*, changer la ligne au niveau du bloc "*Try*" en le remplaçant par :  

    $bdd = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $login, $password, array(PDO::MYSQL_ATTR_FOUND_ROWS => true));  
  
> Cela permet de prendre en compte les lignes affectées par les modifications lors de l'utilisation de la fonction *rowCount()*.  
  
## Module documentation :  
Un fichier *traitement.php* est dédié à ce module, il est structuré comme suit :  

    traitement.php?nature=valeur1&cible=valeur2&id=valeur3  
  
* La *nature* est soit __photo__ pour indiquer que le fichier uploadé est une photo, soit __doc__ pour indiquer que c'est un fichier de documentation.
* En fonction de la *nature* indiquée, la *cible* peut prendre comme valeurs :  
    1. Pour la nature __photo__ :
        * utilisateur  
        * chauffeur  
        * vehicule
    2. Pour la nature __doc__ :
        * contrat  
        * fiche  
        * processus
        * facture  
        * gestion  
> __NOTE :__ *fiche* indique les fiches d'état des lieux.  
* Lorsque la nature *photo* est choisie, il faudra préciser l'__id__ de l'élément (utilisateur, chaufeur ou véhicule) à qui on souhaite associer cette photo. L'id n'est obligatoire que pour ce cas.  
> __NOTE :__ À chaque *upload* d'un fichier, son chemin dans le serveur est mis à jour dans la base de données.  
  
> __IMPORTANT :__ Les dossiers *Images* et *documentation* ainsi que l'ensemble de leurs sous-dossiers doivent posséder les droits d'écriture. Un __CHMOD__ à __733__ sera ainsi nécéssaire. La taille des fichiers est limitée à __5 Mo__ extensible jusqu'à __8 Mo__.  
* __supprimerDoc(id)__ : Suppression du fichier de documentation dont l'id est spécifié  
