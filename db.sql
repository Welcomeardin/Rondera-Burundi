-- =====================================================
-- RONDERA INZU - House Rental Database Schema
-- MySQL Database: location_maisons
-- =====================================================

CREATE DATABASE IF NOT EXISTS location_maisons CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE location_maisons;

-- =====================================================
-- TABLE: utilisateurs (Users)
-- Roles: finder, owner, admin
-- =====================================================
CREATE TABLE utilisateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telephone VARCHAR(20),
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('finder', 'owner', 'admin') NOT NULL DEFAULT 'finder',
    photo_profil VARCHAR(255) DEFAULT NULL,
    adresse TEXT,
    date_naissance DATE,
    statut ENUM('actif', 'bloque', 'en_attente') DEFAULT 'actif',
    email_verifie BOOLEAN DEFAULT FALSE,
    token_verification VARCHAR(255),
    token_reset_password VARCHAR(255),
    token_expiration DATETIME,
    dark_mode BOOLEAN DEFAULT FALSE,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion DATETIME,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_statut (statut)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: maisons (Houses)
-- Status: disponible, reservee, occupee
-- =====================================================
CREATE TABLE maisons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    proprietaire_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    type_maison ENUM('appartement', 'villa', 'studio', 'maison', 'duplex', 'chambre') NOT NULL,
    prix_mensuel DECIMAL(12, 2) NOT NULL,
    caution DECIMAL(12, 2),
    superficie DECIMAL(10, 2),
    nombre_chambres INT DEFAULT 1,
    nombre_salles_bain INT DEFAULT 1,
    nombre_etages INT DEFAULT 1,
    meublee BOOLEAN DEFAULT FALSE,
    
    -- Location
    ville VARCHAR(100) NOT NULL,
    quartier VARCHAR(100),
    adresse_complete TEXT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    
    -- Amenities (as JSON or individual fields)
    wifi BOOLEAN DEFAULT FALSE,
    parking BOOLEAN DEFAULT FALSE,
    climatisation BOOLEAN DEFAULT FALSE,
    chauffage BOOLEAN DEFAULT FALSE,
    cuisine_equipee BOOLEAN DEFAULT FALSE,
    machine_laver BOOLEAN DEFAULT FALSE,
    balcon BOOLEAN DEFAULT FALSE,
    jardin BOOLEAN DEFAULT FALSE,
    piscine BOOLEAN DEFAULT FALSE,
    securite BOOLEAN DEFAULT FALSE,
    ascenseur BOOLEAN DEFAULT FALSE,
    animaux_acceptes BOOLEAN DEFAULT FALSE,
    
    -- Status and dates
    statut ENUM('en_attente', 'disponible', 'reservee', 'occupee', 'inactive') DEFAULT 'en_attente',
    date_disponibilite DATE,
    duree_minimum INT DEFAULT 1 COMMENT 'Minimum months',
    vues INT DEFAULT 0,
    est_vedette BOOLEAN DEFAULT FALSE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (proprietaire_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_ville (ville),
    INDEX idx_quartier (quartier),
    INDEX idx_prix (prix_mensuel),
    INDEX idx_type (type_maison),
    INDEX idx_statut (statut),
    INDEX idx_chambres (nombre_chambres),
    INDEX idx_vedette (est_vedette),
    FULLTEXT idx_recherche (titre, description, ville, quartier)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: photos_maison (House Photos)
-- =====================================================
CREATE TABLE photos_maison (
    id INT PRIMARY KEY AUTO_INCREMENT,
    maison_id INT NOT NULL,
    chemin VARCHAR(255) NOT NULL,
    est_principale BOOLEAN DEFAULT FALSE,
    ordre INT DEFAULT 0,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (maison_id) REFERENCES maisons(id) ON DELETE CASCADE,
    INDEX idx_maison (maison_id)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: reservations (Bookings)
-- =====================================================
CREATE TABLE reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    maison_id INT NOT NULL,
    locataire_id INT NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE,
    statut ENUM('en_attente', 'confirmee', 'annulee', 'terminee') DEFAULT 'en_attente',
    message TEXT,
    montant_total DECIMAL(12, 2),
    date_demande DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_reponse DATETIME,
    reponse_proprietaire TEXT,
    FOREIGN KEY (maison_id) REFERENCES maisons(id) ON DELETE CASCADE,
    FOREIGN KEY (locataire_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_maison (maison_id),
    INDEX idx_locataire (locataire_id),
    INDEX idx_statut (statut)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: occupations (Active Rentals)
-- =====================================================
CREATE TABLE occupations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reservation_id INT NOT NULL,
    maison_id INT NOT NULL,
    locataire_id INT NOT NULL,
    date_entree DATE NOT NULL,
    date_sortie_prevue DATE,
    date_sortie_effective DATE,
    loyer_mensuel DECIMAL(12, 2) NOT NULL,
    jour_paiement INT DEFAULT 1 COMMENT 'Day of month for payment',
    statut ENUM('active', 'terminee', 'en_cours_depart') DEFAULT 'active',
    notes TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id),
    FOREIGN KEY (maison_id) REFERENCES maisons(id) ON DELETE CASCADE,
    FOREIGN KEY (locataire_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_locataire (locataire_id),
    INDEX idx_maison (maison_id),
    INDEX idx_statut (statut)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: contrats (Contracts)
-- Types: owner_admin, location, depart
-- =====================================================
CREATE TABLE contrats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type_contrat ENUM('owner_admin', 'location', 'depart') NOT NULL,
    occupation_id INT,
    maison_id INT NOT NULL,
    proprietaire_id INT NOT NULL,
    locataire_id INT,
    
    -- Contract details
    date_debut DATE,
    date_fin DATE,
    loyer_mensuel DECIMAL(12, 2),
    caution DECIMAL(12, 2),
    conditions TEXT,
    
    -- Document
    fichier_pdf VARCHAR(255),
    
    -- Signatures
    signature_proprietaire BOOLEAN DEFAULT FALSE,
    date_signature_proprietaire DATETIME,
    signature_locataire BOOLEAN DEFAULT FALSE,
    date_signature_locataire DATETIME,
    signature_admin BOOLEAN DEFAULT FALSE,
    date_signature_admin DATETIME,
    
    statut ENUM('brouillon', 'en_attente', 'signe', 'expire', 'annule') DEFAULT 'brouillon',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (occupation_id) REFERENCES occupations(id),
    FOREIGN KEY (maison_id) REFERENCES maisons(id) ON DELETE CASCADE,
    FOREIGN KEY (proprietaire_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (locataire_id) REFERENCES utilisateurs(id),
    INDEX idx_type (type_contrat),
    INDEX idx_statut (statut)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: paiements (Payments)
-- =====================================================

-- =====================================================
-- TABLE: pages (Static content for footer and legal pages)
-- stores slug, title, content and whether to show in footer
-- =====================================================
CREATE TABLE IF NOT EXISTS pages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slug VARCHAR(255) UNIQUE,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT,
    afficher_footer BOOLEAN DEFAULT TRUE,
    ordre INT DEFAULT 0,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE paiements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    occupation_id INT NOT NULL,
    locataire_id INT NOT NULL,
    montant DECIMAL(12, 2) NOT NULL,
    mois_concerne DATE NOT NULL COMMENT 'Month being paid for',
    mode_paiement ENUM('especes', 'virement', 'mobile_money', 'cheque') DEFAULT 'especes',
    reference_paiement VARCHAR(100),
    bordereau_pdf VARCHAR(255),
    statut ENUM('en_attente', 'paye', 'refuse', 'rembourse') DEFAULT 'en_attente',
    date_paiement DATETIME,
    commentaire TEXT,
    valide_par INT,
    date_validation DATETIME,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (occupation_id) REFERENCES occupations(id),
    FOREIGN KEY (locataire_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (valide_par) REFERENCES utilisateurs(id),
    INDEX idx_occupation (occupation_id),
    INDEX idx_locataire (locataire_id),
    INDEX idx_statut (statut),
    INDEX idx_mois (mois_concerne)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: favoris (Favorites)
-- =====================================================
CREATE TABLE favoris (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    maison_id INT NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (maison_id) REFERENCES maisons(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favori (utilisateur_id, maison_id),
    INDEX idx_utilisateur (utilisateur_id)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: messages (Internal Messaging)
-- =====================================================
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    expediteur_id INT NOT NULL,
    destinataire_id INT NOT NULL,
    maison_id INT,
    sujet VARCHAR(255),
    contenu TEXT NOT NULL,
    lu BOOLEAN DEFAULT FALSE,
    date_lecture DATETIME,
    supprime_expediteur BOOLEAN DEFAULT FALSE,
    supprime_destinataire BOOLEAN DEFAULT FALSE,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expediteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (maison_id) REFERENCES maisons(id) ON DELETE SET NULL,
    INDEX idx_expediteur (expediteur_id),
    INDEX idx_destinataire (destinataire_id),
    INDEX idx_lu (lu),
    INDEX idx_date (date_envoi)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: notifications
-- =====================================================
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    type ENUM('reservation', 'message', 'paiement', 'contrat', 'systeme') NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT,
    lien VARCHAR(255),
    lue BOOLEAN DEFAULT FALSE,
    date_lecture DATETIME,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_lue (lue),
    INDEX idx_date (date_creation)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: avis (Reviews)
-- =====================================================
CREATE TABLE avis (
    id INT PRIMARY KEY AUTO_INCREMENT,
    maison_id INT NOT NULL,
    finder_id INT NOT NULL,
    note INT NOT NULL CHECK (note >= 1 AND note <= 5),
    commentaire TEXT,
    date_avis DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (maison_id) REFERENCES maisons(id) ON DELETE CASCADE,
    FOREIGN KEY (finder_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_avis (maison_id, finder_id),
    INDEX idx_maison (maison_id),
    INDEX idx_finder (finder_id)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: signalements (Reports)
-- =====================================================
CREATE TABLE signalements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    emetteur_id INT NOT NULL,
    maison_id INT,
    utilisateur_id INT,
    type_signalement ENUM('contenu_inapproprie', 'fraude', 'mauvaise_experience', 'autre') NOT NULL,
    description TEXT,
    statut ENUM('en_attente', 'traite', 'rejete') DEFAULT 'en_attente',
    date_signalement DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emetteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (maison_id) REFERENCES maisons(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_emetteur (emetteur_id),
    INDEX idx_statut (statut)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: preferences_recherche (Search Preferences)
-- =====================================================
CREATE TABLE preferences_recherche (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    nom_preference VARCHAR(100),
    ville VARCHAR(100),
    quartier VARCHAR(100),
    type_maison ENUM('appartement', 'villa', 'studio', 'maison', 'duplex', 'chambre'),
    prix_min DECIMAL(12, 2),
    prix_max DECIMAL(12, 2),
    chambres_min INT,
    chambres_max INT,
    meublee BOOLEAN,
    alertes_email BOOLEAN DEFAULT FALSE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_utilisateur (utilisateur_id)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: statistiques_vues (View Statistics)
-- =====================================================
CREATE TABLE statistiques_vues (
    id INT PRIMARY KEY AUTO_INCREMENT,
    maison_id INT NOT NULL,
    utilisateur_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    date_vue DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (maison_id) REFERENCES maisons(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    INDEX idx_maison (maison_id),
    INDEX idx_date (date_vue)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: site_settings
-- =====================================================
CREATE TABLE site_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_site VARCHAR(100) DEFAULT 'Rondera Inzu',
    tagline VARCHAR(255),
    email_contact VARCHAR(255),
    telephone_contact VARCHAR(20),
    adresse_physique TEXT,
    logo_path VARCHAR(255),
    favicon_path VARCHAR(255),
    facebook_url VARCHAR(255),
    twitter_url VARCHAR(255),
    instagram_url VARCHAR(255),
    linkedin_url VARCHAR(255),
    meta_description TEXT,
    meta_keywords TEXT,
    google_analytics_id VARCHAR(50),
    maintenance_mode BOOLEAN DEFAULT FALSE,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: faqs
-- =====================================================
CREATE TABLE faqs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question TEXT NOT NULL,
    reponse TEXT NOT NULL,
    categorie VARCHAR(100) DEFAULT 'Général',
    ordre INT DEFAULT 0,
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- SAMPLE DATA
-- =====================================================

-- Admin user (password: Admin@123)
INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role, statut, email_verifie) VALUES
('Admin', 'System', 'admin@rondera-inzu.com', '+25779000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'actif', TRUE);

-- Sample owners (password: Password@123)
INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role, statut, email_verifie) VALUES
('Ndayishimiye', 'Jean', 'jean.ndayishimiye@email.bi', '+25779111111', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'owner', 'actif', TRUE),
('Niyonzima', 'Marie', 'marie.niyonzima@email.bi', '+25779222222', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'owner', 'actif', TRUE);

-- Sample finders (password: Password@123)
INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role, statut, email_verifie) VALUES
('Bukuru', 'Pierre', 'pierre.bukuru@email.bi', '+25779333333', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'finder', 'actif', TRUE),
('Irakoze', 'Claire', 'claire.irakoze@email.bi', '+25779444444', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'finder', 'actif', TRUE);

-- Sample site settings
INSERT INTO site_settings (nom_site, tagline, email_contact, telephone_contact, adresse_physique) VALUES
('Rondera Inzu', 'Trouvez votre maison idéale au Burundi', 'contact@rondera-inzu.com', '+257 79 000 000', 'Bujumbura, Burundi');

-- Sample FAQs
INSERT INTO faqs (question, reponse, categorie, ordre) VALUES
('Qu\'est-ce que Rondera Inzu ?', 'Rondera Inzu est une plateforme de location immobilière au Burundi.', 'Général', 1),
('Comment publier une annonce ?', 'Inscrivez-vous en tant que propriétaire et cliquez sur "Ajouter une propriété".', 'Propriétaire', 2),
('Comment contacter un propriétaire ?', 'Connectez-vous et utilisez le bouton de contact sur la page de détails.', 'Locataire', 3);

-- Sample houses
INSERT INTO maisons (proprietaire_id, titre, description, type_maison, prix_mensuel, caution, superficie, nombre_chambres, nombre_salles_bain, meublee, ville, quartier, adresse_complete, wifi, parking, climatisation, cuisine_equipee, securite, statut, est_vedette) VALUES
(2, 'Appartement Moderne Rohero I', 'Superbe appartement avec vue panoramique sur Bujumbura. Proche du centre-ville.', 'appartement', 850000, 850000, 85.50, 2, 1, TRUE, 'Bujumbura', 'Rohero', 'Avenue de la JRR, Rohero I', TRUE, TRUE, TRUE, TRUE, TRUE, 'disponible', TRUE),
(2, 'Villa de Luxe Kinindo', 'Magnifique villa à Kinindo. Grand jardin, piscine, 4 chambres. Sécurisée.', 'villa', 2500000, 5000000, 350.00, 4, 3, TRUE, 'Bujumbura', 'Kinindo', 'Avenue de la Plage, Kinindo', TRUE, TRUE, TRUE, TRUE, TRUE, 'disponible', TRUE),
(3, 'Studio Confortable Kiriri', 'Studio idéal pour célibataire. Calme et vue sur le lac Tanganyika.', 'studio', 450000, 450000, 35.00, 1, 1, TRUE, 'Bujumbura', 'Kiriri', 'Avenue de l\'Université, Kiriri', TRUE, TRUE, FALSE, TRUE, TRUE, 'disponible', FALSE),
(3, 'Maison Familiale Ngagara', 'Belle maison avec 3 chambres à Ngagara. Quartier calme et sécurisé.', 'maison', 600000, 600000, 150.00, 3, 2, FALSE, 'Bujumbura', 'Ngagara', 'Quartier 2, Ngagara', FALSE, TRUE, FALSE, FALSE, TRUE, 'disponible', FALSE),
(2, 'Duplex Moderne Kigobe', 'Duplex neuf de standing à Kigobe Nord. Architecture contemporaine.', 'duplex', 1200000, 1200000, 120.00, 3, 2, FALSE, 'Bujumbura', 'Kigobe', 'Kigobe Nord', TRUE, TRUE, TRUE, TRUE, TRUE, 'disponible', TRUE),
(3, 'Chambre Meublée Bwiza', 'Chambre meublée. Proche commerces. Idéal étudiant.', 'chambre', 150000, 150000, 18.00, 1, 1, TRUE, 'Bujumbura', 'Bwiza', 'Avenue de l\'Amitié, Bwiza', TRUE, FALSE, FALSE, FALSE, TRUE, 'disponible', FALSE);

-- =====================================================
-- TRIGGERS
-- =====================================================

-- Trigger: Update house status when reservation is confirmed
DELIMITER //
CREATE TRIGGER after_reservation_confirmed
AFTER UPDATE ON reservations
FOR EACH ROW
BEGIN
    IF NEW.statut = 'confirmee' AND OLD.statut != 'confirmee' THEN
        UPDATE maisons SET statut = 'reservee' WHERE id = NEW.maison_id;
        
        -- Create notification for tenant
        INSERT INTO notifications (utilisateur_id, type, titre, contenu, lien)
        VALUES (NEW.locataire_id, 'reservation', 'Réservation Confirmée', 
                'Votre réservation a été confirmée par le propriétaire.', 
                CONCAT('/house-details.php?id=', NEW.maison_id));
    END IF;
END//
DELIMITER ;

-- Trigger: Create notification for new message
DELIMITER //
CREATE TRIGGER after_message_insert
AFTER INSERT ON messages
FOR EACH ROW
BEGIN
    INSERT INTO notifications (utilisateur_id, type, titre, contenu, lien)
    VALUES (NEW.destinataire_id, 'message', 'Nouveau Message', 
            'Vous avez reçu un nouveau message.', 
            CONCAT('/messages.php?conversation=', NEW.expediteur_id));
END//
DELIMITER ;

-- Trigger: Increment view count
DELIMITER //
CREATE TRIGGER after_view_insert
AFTER INSERT ON statistiques_vues
FOR EACH ROW
BEGIN
    UPDATE maisons SET vues = vues + 1 WHERE id = NEW.maison_id;
END//
DELIMITER ;