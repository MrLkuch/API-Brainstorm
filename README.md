# BrainSpark AI - Application de Chat Symfony avec Fonctionnalités Avancées

## Description
BrainSpark AI est une application web développée avec Symfony qui permet aux utilisateurs de créer des comptes, de rejoindre ou de créer des chatrooms, et d'échanger avec d'autres utilisateurs. Grâce à des intégrations API avancées, l'application offre des fonctionnalités uniques pour enrichir les discussions.

---

## Fonctionnalités

### Gestion des utilisateurs :
- Création de compte avec validation par e-mail.
- Connexion sécurisée et gestion des sessions.
- Ajout d'utilisateurs à une chatroom via leur adresse e-mail.

### Chatrooms :
- Création et gestion de chatrooms.
- Discussions en temps réel avec d'autres utilisateurs connectés à la même chatroom.

### Boutons API GROQ dans chaque chatroom :
1. **Résumé** : Résume la discussion actuelle.
2. **Générer une idée** : Propose une idée pertinente en fonction des échanges.
3. **Critique** : Fournit une critique constructive sur le contenu de la discussion.
4. **Prompt personnalisé** : Permet d'envoyer une requête API personnalisée.

---

## Prérequis
- **PHP** >= 7.1
- **Composer**
- **Node.js** et **npm** (pour les dépendances front-end)
- **Symfony CLI**
- Une clé API pour l'intégration avec GROQ (configurable dans `.env`).

---

## Installation

1. **Cloner le dépôt :**
   ```bash
   git clone https://github.com/nom-utilisateur/chatconnect.git
   cd API-Brainstorm
   
2. **Installer les dépendances :**
   ```bash
   composer install
   npm install

3. **Configurer les variables d'environnement :**
   Copier le fichier .env.example en .env.
   Remplir les champs pour la base de données et l'API GROQ.

4. **Initialiser la base de donnée :**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate

5. **Lancer le serveur :**
   ```bash
   symfony serve

## Utilisation
- Inscrivez-vous ou connectez-vous pour accéder à vos chatrooms.
- Créez ou rejoignez une chatroom existante.
- Ajoutez des utilisateurs en saisissant leur adresse e-mail.
- Utilisez les boutons API pour interagir avec les discussions en temps réel.

## Technologies utilisées
- Backend : Symfony
- Base de données : MySQL / MariaDB
- Frontend : Twig + JavaScript
- APIs : GROQ

## Contribuer
Les contributions sont les bienvenues ! Merci de soumettre une issue ou une pull request.

