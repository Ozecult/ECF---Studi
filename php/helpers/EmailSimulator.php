<?php
/**
 * Simulateur d'envoi d'emails
 * En production, remplacer par PHPMailer ou Symfony Mailer
 */

class EmailSimulator 
{
    /**
     * Simuler l'envoi d'un email
     * @param string $destinataire Email du destinataire
     * @param string $sujet Sujet de l'email
     * @param string $message Corps du message
     * @return bool Toujours true (simulation)
     */
    public static function envoyerEmail($destinataire, $sujet, $message) 
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "\n" . str_repeat("=", 80) . "\n";
        $logMessage .= "SIMULATION ENVOI EMAIL - $timestamp\n";
        $logMessage .= str_repeat("=", 80) . "\n";
        $logMessage .= "À: $destinataire\n";
        $logMessage .= "Sujet: $sujet\n";
        $logMessage .= str_repeat("-", 80) . "\n";
        $logMessage .= "Message:\n$message\n";
        $logMessage .= str_repeat("=", 80) . "\n";
        
        error_log($logMessage);
        
        return true;
    }
    
    /**
     * Email de confirmation d'inscription
     */
    public static function emailInscription($email, $prenom, $pseudo) 
    {
        $sujet = "Bienvenue sur EcoRide !";
        $message = "Bonjour $prenom,\n\n";
        $message .= "Votre compte EcoRide a été créé avec succès !\n\n";
        $message .= "Pseudo : $pseudo\n";
        $message .= "Vous disposez de 20 crédits de bienvenue.\n\n";
        $message .= "Bon covoiturage écologique !\n\n";
        $message .= "L'équipe EcoRide";
        
        return self::envoyerEmail($email, $sujet, $message);
    }
    
    /**
     * Email de fin de trajet aux participants
     */
    public static function emailFinTrajet($email, $prenom, $adresseDepart, $adresseArrivee, $dateDepart) 
    {
        $sujet = "Votre trajet est terminé - Laissez un avis !";
        $message = "Bonjour $prenom,\n\n";
        $message .= "Le trajet suivant vient de se terminer :\n\n";
        $message .= "De : $adresseDepart\n";
        $message .= "À : $adresseArrivee\n";
        $message .= "Date : " . date('d/m/Y à H:i', strtotime($dateDepart)) . "\n\n";
        $message .= "Rendez-vous sur votre espace personnel pour :\n";
        $message .= "- Valider que tout s'est bien passé\n";
        $message .= "- Laisser un avis sur le conducteur\n\n";
        $message .= "Merci de votre confiance !\n\n";
        $message .= "L'équipe EcoRide";
        
        return self::envoyerEmail($email, $sujet, $message);
    }
    
    /**
     * Email d'annulation de trajet
     */
    public static function emailAnnulationTrajet($email, $prenom, $adresseDepart, $adresseArrivee, $dateDepart) 
    {
        $sujet = "Trajet annulé - EcoRide";
        $message = "Bonjour $prenom,\n\n";
        $message .= "Le trajet suivant a été annulé :\n\n";
        $message .= "De : $adresseDepart\n";
        $message .= "À : $adresseArrivee\n";
        $message .= "Date : " . date('d/m/Y à H:i', strtotime($dateDepart)) . "\n\n";
        $message .= "Aucun crédit n'a été débité de votre compte.\n\n";
        $message .= "Nous vous invitons à rechercher un autre covoiturage.\n\n";
        $message .= "L'équipe EcoRide";
        
        return self::envoyerEmail($email, $sujet, $message);
    }
    
    /**
     * Email de confirmation de réservation
     */
    public static function emailConfirmationReservation($email, $prenom, $adresseDepart, $adresseArrivee, $dateDepart, $prixTotal) 
    {
        $sujet = "Réservation confirmée - EcoRide";
        $message = "Bonjour $prenom,\n\n";
        $message .= "Votre réservation a été confirmée !\n\n";
        $message .= "De : $adresseDepart\n";
        $message .= "À : $adresseArrivee\n";
        $message .= "Date : " . date('d/m/Y à H:i', strtotime($dateDepart)) . "\n";
        $message .= "Prix : $prixTotal crédits (débités à la fin du trajet)\n\n";
        $message .= "Le conducteur démarrera le trajet depuis son espace personnel.\n\n";
        $message .= "Bon voyage !\n\n";
        $message .= "L'équipe EcoRide";
        
        return self::envoyerEmail($email, $sujet, $message);
    }
}