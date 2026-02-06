# Projet Championnat

Application de gestion de championnats de football.

## Installation

### Prérequis
- PHP 8.2+
- Composer
- Docker & Docker Compose

### Étapes

1. **Installer les dépendances**
```bash
composer install
```

2. **Démarrer la base de données**
```bash
docker compose up -d
```

3. **Configurer la base de données**
```bash
php bin/console doctrine:migrations:migrate
```

4. **Lancer le serveur**
```bash
symfony server:start
```

L'application est accessible sur `http://localhost:8000`

## Utilisation

1. Créer un compte utilisateur
2. Créer des pays et des équipes
3. Créer un championnat
4. Ajouter des équipes au championnat
5. Créer des journées et des matchs
6. Saisir les scores des matchs

## Commandes utiles

- `php bin/console doctrine:migrations:migrate` - Appliquer les migrations
- `php bin/console cache:clear` - Vider le cache
- `docker compose down` - Arrêter la base de données







Tutoriel pour tester

Étape 1 : Créer des pays
    Aller sur la page d'accueil
    Cliquer sur "Créer un pays"
    Créer au moins 2 pays (ex: "France", "Espagne")

Étape 2 : Créer des équipes
    Cliquer sur "Créer une équipe"
    Créer plusieurs équipes (ex: "Paris-SG", "Marseille", "Barcelone")
    Associer chaque équipe à un pays

Étape 3 : Créer un championnat
    Cliquer sur "Créer un championnat"
    Remplir le formulaire :
    Nom : "Ligue 1 2024"
    Date de début : 01/01/2024
    Date de fin : 31/12/2024
    Points victoire : 3
    Points défaite : 0
    Points nul : 1
    Type de classement : "Points"

Étape 4 : Associer des équipes au championnat
    Cliquer sur "Associer équipe à championnat"
    Sélectionner le championnat créé
    Sélectionner une équipe
    Répéter pour toutes les équipes

Étape 5 : Créer des journées
    Cliquer sur "Créer une journée"
    Sélectionner le championnat
    Entrer un numéro (ex: "1", "2", "3")
    Répéter pour créer plusieurs journées

Étape 6 : Créer des matchs
    Cliquer sur "Créer un résultat"
    Sélectionner une journée
    Sélectionner deux équipes différentes
    Entrer les scores (ex: Équipe 1: 2, Équipe 2: 1)
    Répéter pour créer plusieurs matchs

Étape 7 : Vérifier le classement
    Aller sur "Liste des championnats"
    Cliquer sur le championnat créé
    Vérifier le classement : les équipes doivent apparaître avec leurs points calculés automatiquement
