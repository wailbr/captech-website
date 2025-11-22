-- Création de la base de données
DROP DATABASE IF EXISTS gestion_projets;
CREATE DATABASE gestion_projets;
USE gestion_projets;

-- Configuration du mode SQL pour MySQL
SET SESSION sql_mode = 'STRICT_TRANS_TABLES';

-- Table Utilisateur
CREATE TABLE Utilisateur (
   id_utilisateur INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   prenom VARCHAR(50) NOT NULL,
   email VARCHAR(50) NOT NULL UNIQUE,
   mot_de_passe VARCHAR(255) NOT NULL,
   photo_profil VARCHAR(255) DEFAULT NULL,
   role ENUM('developpeur', 'chef_de_projet', 'responsable_equipe') NOT NULL,
   PRIMARY KEY(id_utilisateur)
);

-- Table Projet
CREATE TABLE Projet (
   id_projet INT AUTO_INCREMENT,
   titre VARCHAR(50) NOT NULL,
   description TEXT NOT NULL,
   date_de_debut DATE NOT NULL,
   date_de_fin_prevue DATE NOT NULL,
   budget DECIMAL(10, 2) NOT NULL,
   id_utilisateur INT NOT NULL,
   PRIMARY KEY(id_projet),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
);

-- Table Tache
CREATE TABLE Tache (
   id_tache INT AUTO_INCREMENT,
   titre VARCHAR(50) NOT NULL,
   description TEXT NOT NULL,
   date_debut DATE NOT NULL,
   date_fin DATE NOT NULL,
   statut VARCHAR(50) DEFAULT 'Non commencé',
   id_projet INT NOT NULL,
   id_utilisateur INT NOT NULL,
   PRIMARY KEY(id_tache),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur),
   FOREIGN KEY(id_projet) REFERENCES Projet(id_projet)
);

-- Table Equipe
CREATE TABLE Equipe (
   id_equipe INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   id_projet INT NOT NULL,
   PRIMARY KEY(id_equipe),
   FOREIGN KEY(id_projet) REFERENCES Projet(id_projet)
);

-- Table Commentaire
CREATE TABLE Commentaire (
   id_commentaire INT AUTO_INCREMENT,
   contenu TEXT NOT NULL,
   date_commentaire TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   id_utilisateur INT NOT NULL,
   PRIMARY KEY(id_commentaire),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
);

-- Table Document
CREATE TABLE Document (
   id_document INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   chemin_acces VARCHAR(255) NOT NULL,
   date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY(id_document)
);


-- Table Commentaire_Tache
CREATE TABLE Commentaire_Tache (
   id_tache INT,
   id_commentaire INT,
   PRIMARY KEY(id_tache, id_commentaire),
   FOREIGN KEY(id_tache) REFERENCES Tache(id_tache),
   FOREIGN KEY(id_commentaire) REFERENCES Commentaire(id_commentaire)
);

-- Table Document_Projet
CREATE TABLE Document_Projet (
   id_projet INT,
   id_document INT,
   PRIMARY KEY(id_projet, id_document),
   FOREIGN KEY(id_projet) REFERENCES Projet(id_projet),
   FOREIGN KEY(id_document) REFERENCES Document(id_document)
);

-- Table Membre
CREATE TABLE Membre (
   id_utilisateur INT,
   id_equipe INT,
   PRIMARY KEY(id_utilisateur, id_equipe),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur),
   FOREIGN KEY(id_equipe) REFERENCES Equipe(id_equipe)
);


-- Table Developpeur
CREATE TABLE Developpeur (
   id_dev INT AUTO_INCREMENT,
   id_utilisateur INT NOT NULL,
   specialite VARCHAR(50),
   langages_maitrises VARCHAR(255),
   PRIMARY KEY(id_dev),
   UNIQUE(id_utilisateur),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
);

-- Table Chef_de_Projet
CREATE TABLE Chef_de_Projet (
   id_chef INT AUTO_INCREMENT,
   id_utilisateur INT NOT NULL,
   projets_diriges VARCHAR(255),
   niveau_experience VARCHAR(50),
   PRIMARY KEY(id_chef),
   UNIQUE(id_utilisateur),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
);

-- Table Responsable_Equipe
CREATE TABLE Responsable_Equipe (
   id_responsable INT AUTO_INCREMENT,
   id_utilisateur INT NOT NULL,
   date_nomination DATE,
   liste_projets_responsables VARCHAR(255),
   PRIMARY KEY(id_responsable),
   UNIQUE(id_utilisateur),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
);

-- Nouvelle Table Budget
CREATE TABLE Budget (
   id_budget INT AUTO_INCREMENT,
   montant DECIMAL(10, 2) NOT NULL,
   date_allocation DATE NOT NULL,
   etat VARCHAR(10),
   id_projet INT NOT NULL,
   PRIMARY KEY(id_budget),
   FOREIGN KEY(id_projet) REFERENCES Projet(id_projet) ON DELETE CASCADE
);

-- Insertion de quelques utilisateurs
INSERT INTO Utilisateur (nom, prenom, email, mot_de_passe, role) VALUES 
('Ramos', 'Sergio', 'sergio@ramos.com', 'SR4', 'responsable_equipe'),
('Smith', 'John', 'john@smith.com', 'JS123', 'chef_de_projet'),
('Johnson', 'Emily', 'emily@johnson.com', 'EJ123', 'developpeur'),
('Doe', 'Jane', 'jane@doe.com', 'JD123', 'developpeur'),
('Davis', 'Sarah', 'sarah@davis.com', 'SD123', 'developpeur');

-- Associer des utilisateurs à des rôles
INSERT INTO Responsable_Equipe (id_utilisateur, date_nomination, liste_projets_responsables) VALUES 
(1, '2024-01-01', 'Système de Gestion de Contenu, Analyse des Données');

INSERT INTO Chef_de_Projet (id_utilisateur, projets_diriges, niveau_experience) VALUES 
(2, 'Application Mobile de Suivi de Fitness, Développement d\'un CRM', 'Expert');

INSERT INTO Developpeur (id_utilisateur, specialite, langages_maitrises) VALUES 
(3, 'Frontend', 'HTML, CSS, JavaScript'),
(4, 'Backend', 'PHP, MySQL'),
(5, 'Full Stack', 'React, Node.js');

-- Insertion de quelques projets
INSERT INTO Projet (titre, description, date_de_debut, date_de_fin_prevue, budget, id_utilisateur) VALUES 
('Système de Gestion de Contenu', 'Développement d\'un CMS pour la gestion des sites web d\'entreprise.', '2024-01-01', '2024-06-30', 10000.00, 1),
('Analyse des Données', 'Création d\'une plateforme pour l\'analyse des données en temps réel.', '2024-02-01', '2024-07-31', 15000.00, 1),
('Application Mobile de Suivi de Fitness', 'Développement d\'une application mobile pour suivre les activités de fitness et les performances.', '2024-03-01', '2024-08-31', 20000.00, 2),
('Développement d\'un CRM', 'Conception et développement d\'un système de gestion de la relation client.', '2024-04-01', '2024-09-30', 25000.00, 2);

-- Insertion de quelques tâches
INSERT INTO Tache (titre, description, date_debut, date_fin, statut, id_projet, id_utilisateur) VALUES 
('Création du Dashboard', 'Développer le tableau de bord pour le CMS.', '2024-01-01', '2024-01-15', 'En cours', 1, 3),
('Intégration API', 'Intégrer l\'API pour la gestion des contenus.', '2024-01-16', '2024-01-31', 'Non commencé', 1, 4),
('Analyse des Données Brutes', 'Écrire des scripts pour analyser les données brutes.', '2024-02-01', '2024-02-15', 'En cours', 2, 3),
('Développement des Widgets', 'Développer des widgets pour l\'interface utilisateur.', '2024-02-16', '2024-02-28', 'Non commencé', 2, 4),
('Interface Utilisateur Mobile', 'Concevoir l\'interface utilisateur de l\'application mobile.', '2024-03-01', '2024-03-15', 'En cours', 3, 5),
('Backend pour Suivi de Fitness', 'Créer les API backend pour le suivi des activités.', '2024-03-16', '2024-03-31', 'Non commencé', 3, 4);

-- Insertion de quelques budgets
INSERT INTO Budget (montant, date_allocation, etat, id_projet) VALUES 
(5000.00, '2024-01-01', 'Approuvé', 1),
(7500.00, '2024-02-01', 'En attente', 2),
(10000.00, '2024-03-01', 'Approuvé', 3),
(12500.00, '2024-04-01', 'En attente', 4);

-- Insertion de quelques équipes
INSERT INTO Equipe (nom, id_projet) VALUES 
('Équipe CMS', 1),
('Équipe Analyse', 2),
('Équipe Fitness', 3),
('Équipe CRM', 4);

-- Insertion de membres d'équipes
INSERT INTO Membre (id_utilisateur, id_equipe) VALUES 
(3, 1),
(4, 1),
(3, 2),
(4, 2),
(5, 3),
(4, 3),
(3, 4),
(5, 4);

-- Insertion de quelques commentaires
INSERT INTO Commentaire (contenu, id_utilisateur) VALUES 
('Le design du dashboard a besoin de quelques ajustements.', 3),
('Nous devrions améliorer l\'intégration API.', 4),
('Les scripts d\'analyse des données semblent efficaces.', 5),
('Le frontend de l\'application est fluide.', 3);



-- Insertion de quelques documents
INSERT INTO Document (nom, chemin_acces) VALUES 
('Guide_Utilisateur_CMS.pdf', 'guides/Guide_Utilisateur_CMS.pdf'),
('Spec_Technique_Analyse.pdf', 'specs/Spec_Technique_Analyse.pdf');

-- Lier des documents aux projets
INSERT INTO Document_Projet (id_projet, id_document) VALUES
(1, 1),
(2, 2);

