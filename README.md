# API-SenDrive
API gérant la partie backend de la plateforme S'en Drive Solutions.
Cet API, réalisé en PHP Objet, contient une multitude de fonctions renvoyant des données au format JSON.
Les requêtes devront être structurées de la sorte :
--> Pour les fonctions d'affichage (qui doivent retourner des données) :
	get('http://localhost/API-SenDrive/senDrive.php',{
                fonction:'nomDeLafonction',
                Paramètre_1: 'valeur_1',
                Paramètre_2: 'valeur_2',
                ...,
                Paramètre_n: 'valeur_n'
                
                } ,function(data){
                
                console.log(data)
            })

--> Pour les fonctions d'upload de données (qui doivent recevoir des données):
	post('http://localhost/API-SenDrive/senDrive.php',{
                fonction:'nomDeLafonction',
                Paramètre_1: 'valeur_1',
                Paramètre_2: 'valeur_2',
                ...,
                Paramètre_n: 'valeur_n'
                
                } ,function(data){
                
                console.log(data)
            })


#Fonctions disponibles :
##Fonctions d'affichage (get):
-> afficheChauffeurs () : Affichage de tous les chauffeurs
-> afficheChauffeur(dateDebut, dateFin) : Affichage des chauffeurs disponibles entre les dates indiquées
-> afficheVehicules() : Affichage de tous les véhicules
-> afficheVehicule(dateDebut, dateFin) : Affichage des véhicules disponibles entre les dates indiquées
-> filtreVehicule(marque, modele, type, energie, climatisation) : Affichage des véhicules selon les critères indiquées
-> afficheClients() : Affichage des clients
-> afficheReservations() : Affichage des réservations
-> afficheUtilisateurs() : Affichage des utilisateurs


##Fonctions d'upload de données (post):
-> ajoutClient(nom, prenom, telephone, adresse, mail, destination) : Ajout de clients
-> ajoutReservation(idVehicule, idChauffeur, dateDebut, dateFin) : Ajout d'une réservation (IMPORTANT: Il faudra ajouter d'abord le client ensuite la reservation vue que dans la table Reservation, il y'aura l'id du client en question)
-> ajoutUtilisateur(login, password, statut, numIdentite) : Ajout d'utilisateurs (l'utilisateur doit être au préalable dans la table 'Personnel')
-> changerStatutReservation(idReservation, statut) : Changement du statut d'une reservation (les statuts possibles sont: 'En cours', 'En attente', 'Annulé', 'Terminé')
-> connexion(login, password) : connexion des utilisateurs (si réussie, la fonction retourne un array contenant les informations de l'utilisateur connecté)


