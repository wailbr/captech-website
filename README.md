🌐 CAPTECH – Plateforme de Gestion de Projets

Projet Web complet – PHP, HTML, CSS, SQL

Ce projet est une application web permettant de gérer des projets en entreprise, avec différents rôles utilisateurs :

Chef de projet

Développeur

Responsable d’équipe

Administrateur

Il inclut la gestion des tâches, des commentaires, de l’avancement et des tableaux de bord personnalisés.

🧱 Fonctionnalités principales
👥 Gestion des rôles

Login / Logout

Redirection selon rôle

Permissions spécifiques

📁 Gestion de projets

Création / édition / suppression

Attribution d’équipes

Suivi du budget

Avancement du projet

📝 Gestion des tâches

Ajout / édition / suppression

Commentaires par tâche

Statut de progression

📊 Dashboards dynamiques

Un tableau de bord par rôle :
✔ Chef de projet
✔ Développeur
✔ Responsable d’équipe
✔ etc.

🗄️ Base de données MySQL

Structure SQL incluse

Tables pour : projets, utilisateurs, tâches, commentaires

📁 Structure du projet
captech-website/
│
├── assets/
│   ├── css/
│   ├── images/
│   └── js/
│
├── database/
│   └── captech.sql
│
├── src/
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── dashboard_chef_de_projet.php
│   ├── dashboard_developpeur.php
│   ├── dashboard_responsable_equipe.php
│   ├── create_project.php
│   ├── edit_project.php
│   ├── delete_project.php
│   ├── project.php
│   ├── task.php
│   ├── comment_task.php
│   ├── comment_project.php
│   ├── validate_budget.php
│   ├── config.php
│   └── etc.
│
└── README.md

🛠️ Technologies utilisées

PHP

HTML5 / CSS3

MySQL

Architecture MVC simple

Sessions & gestion des rôles

Structure modulaire

👤 Auteur

Wail Brimesse
Bachelor Data & IA – ECE Paris
Projet Web – 2024

🚀 Améliorations possibles

Version mobile responsive

API REST pour les projets

Dashboard Vue.js / React

Sécurité avancée (hash, tokens, rôles granulaires)
