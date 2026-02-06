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

