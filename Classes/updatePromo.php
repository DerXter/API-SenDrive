<?php
    include_once('connexion.php');
    $dateDuJour = date('Y-m-d');
    $datesFinPromo = "SELECT DISTINCT idPromo, dateFin FROM Disponibilite, Promotion WHERE idDate=idDisponibilite AND statut='En cours'";
    $rapport="";
    $cpSucces = 0; //Nombre de mise à jour réussie
    $cpEchec = 0; //Nombre de mise à jour échouée

    global $bdd;
    $reponse = $bdd->query($datesFinPromo);
    echo "Mise à jour des promotions en cours .";
    while($promotion=$reponse->fetch()){
        echo ".";
        if($dateDuJour>=$promotion['dateFin']){
            $updatePromotion = "UPDATE Promotion SET statut='Terminé' WHERE idPromo=?";
            $rep = $bdd->prepare($updatePromotion);
            $rep->execute(array($promotion['idPromo']));
            if($rep->rowCount() > 0){
                $rapport .= "\nPromotion no". $promotion['idPromo']." mise à jour !\r\n";
                $cpSucces++;
            }
            else{
                $rapport .= "****************Promotion no". $promotion['idPromo']." non mise à jour !****************\r\n";
                $cpEchec++;
            }
            $rep->closeCursor();
        } //End if
    } //End while
    $reponse->closeCursor();
    $rapport .= "\nMise à jour Terminée ! \n";
    $rapport .= "\r\nMise à jour réussie: ".$cpSucces."\r\n";
    $rapport .= "Mise à jour échouée: ".$cpEchec."\r\n";
    echo $rapport;

    // ***************************** Génération et envoie du mail *****************************
    // Destinataire
    $to = "mbayederguene97@gmail.com";
    // Sujet
    $subject = 'Rapport de mise à jour des promotions: Tâche Cron';
    
    // Message
    $message = "
    <html>
    <head>
        <title>Test Cron</title>
    </head>
    <body>
        <p>
            <h1> Rapport de mise à jour des promotions : </h1> <br />
            $rapport
        </p>
    </body>
    </html>
    ";
    
    // Pour envoyer un mail HTML, l en-tête Content-type doit être défini
    $headers = "MIME-Version: 1.0" . "\n";
    $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
    
    // En-têtes additionnels
    $headers .= 'From: Mail de la plateforme Sen\'Drive <no-reply@sendrive.com>' . "\r\n";
    
    // Envoie
    $resultat = mail($to, $subject, $message, $headers);
    if($resultat)
        echo "Rapport de la mise à jour envoyé à ".$to."\n";
    else
        echo "Erreur dans l'envoi du rapport\n";