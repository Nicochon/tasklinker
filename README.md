
# Projet Symfony avec Docker

## Prérequis

Avant de démarrer, assurez-vous d'avoir installé :

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Composer](https://getcomposer.org/)

## Installation

### 1. Cloner le projet

Clonez le dépôt Git de ce projet :

```bash

```

### 2. Installation des dépendances

Installez les dépendances avec Composer. Si Composer n'est pas installé localement, utilisez Docker :

```bash
docker run --rm -v $(pwd):/app -w /app composer install
```

### 3. Démarrer les services Docker

Construisez et démarrez les conteneurs Docker :

```bash
docker-compose up -d 
```

### 4. Initialiser la base de données

Créez la base de données et exécutez les migrations avec les commandes Symfony :

```bash
docker-compose exec php bin/console doctrine:database:create
docker-compose exec php bin/console doctrine:migrations:migrate
```

### 5. Charger les fixtures

```bash
docker-compose exec php bin/console doctrine:fixtures:load
```

## Fonctionnalités

.


