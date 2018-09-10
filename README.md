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
  
## Pour les fonctions d'upload de données (qui doivent recevoir des données):  

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
* __afficheChauffeurs ()__ : Affichage de tous les chauffeurs  
* __afficheChauffeur(dateDebut, dateFin)__ : Affichage des chauffeurs disponibles entre les dates indiquées  
* __afficheVehicules()__ : Affichage de tous les véhicules  
* __afficheVehicule(dateDebut, dateFin)__ : Affichage des véhicules disponibles entre les dates indiquées  
* __filtreVehicule(filtre)__ : Affichage des critères de véhicule selon le filtre  
> __IMPORTANT:__ Utiliser comme filtre, *clim-oui* pour afficher les véhicules climatisés et *clim-non*,  
dans le cas contraire.
* __afficheClients()__ : Affichage des clients  
* __afficheReservations()__ : Affichage des réservations  
* __afficheUtilisateurs()__ : Affichage des utilisateurs  
  
> __IMPORTANT__ : Les fonctions d'affichage retournent *false* si aucune données à afficher n'a été trouvées.    
  
  
### Fonctions d'upload de données (post):    
* __ajoutClient(nom, prenom, telephone, adresse, mail, destination)__ : Ajout de clients    
* __ajoutReservation(idVehicule, idChauffeur, dateDebut, dateFin)__ : Ajout d'une réservation   
> __IMPORTANT:__ Il faudra ajouter d'abord le client ensuite la reservation vue que dans la table *Reservation*, il y'aura l'id du client en question.    
* __ajoutUtilisateur(login, password, statut, numIdentite)__ : Ajout d'utilisateurs   
> __IMPORTANT:__ L'utilisateur doit être au préalable dans la table *Personnel*  
* __changerStatutReservation(idReservation, statut)__ : Changement du statut d'une reservation    
> __IMPORTANT:__ Les statuts possibles sont: *En cours*, *En attente*, *Annulé*, *Terminé*.  
* __connexion(login, password)__ : connexion des utilisateurs  
> __IMPORTANT:__ Si réussie, la fonction retourne un *array* contenant les informations de l'utilisateur connecté et retoune *false* si non.  


