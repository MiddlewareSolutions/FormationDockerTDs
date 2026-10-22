# TD 2 : Primitives système (Réseau, Volumes, Sondes et Sécurité)

## Objectifs

- Comprendre pourquoi le réseau par défaut est à éviter.
- Décorréler le cycle de vie d'un conteneur de celui de ses données.
- Mettre en place un _health-check_ fonctionnel.

_Note : Tous ces exercices se font en pure ligne de commande (`docker run`), afin de bien comprendre ce que l'outil Docker Compose automatisera par la suite._

---

## Exercice 1 : Les réseaux bridge et la résolution DNS

Par défaut, Docker place les conteneurs sur un réseau nommé `bridge` (`docker0`). Voyons pourquoi ce n'est pas idéal en production.

### 1.1 Le bridge par défaut

1. Lancez un serveur web Nginx en tâche de fond sur le réseau par défaut :
   `docker run -d --name web-defaut nginx:alpine`
2. Lancez un conteneur utilitaire léger (Alpine) pour tenter de "pinger" le serveur web par son nom :
   `docker run --rm alpine ping -c 2 web-defaut`

**Note** : `-d` permet de lancer en mode "detach", et `--rm` tue l'image à la sortie.

**Observation :** La commande échoue (`bad address`). Pourquoi ?

### 1.2 Le bridge personnalisé

1. Créez votre propre réseau isolé :
   `docker network create reseau-td`
2. Lancez un second serveur Nginx sur ce nouveau réseau :
   `docker run -d --name web-custom --network reseau-td nginx:alpine`
3. Tentez à nouveau le ping depuis un conteneur placé sur ce même réseau :
   `docker run --rm --network reseau-td alpine ping web-custom`

**Observation :** Le ping réussit ! Pourquoi ? Observez vos vip ainsi que celles des pods.

**Optionnel** laissez le pod tourner, mais faites `docker networtk disconnect reseau-tp`

---

## Exercice 2 : Persistance des données (PostgreSQL)

Un conteneur est éphémère (un simple processus). Ses données internes disparaissent s'il est détruit.

```bash
# 1. Créez un volume nommé :
docker volume create demo-volume

# 2. Créez une BDD
docker run -d --name demo-bdd \
    -e POSTGRES_PASSWORD=admin \
    -v demo-volume:/var/lib/postgresql/data \
    postgres:17-alpine

# 3. Création d'une table de test
docker exec -it demo-bdd psql -U postgres -c "CREATE TABLE test (id INT); INSERT INTO test VALUES (42);"

# 4. Suppression du container
docker rm -f demo-bdd

# 5. Création d'un nouveau container, toujours avec le volume
docker run -d --name demo-bdd-retour \
    -e POSTGRES_PASSWORD=admin \
    -v pgdata:/var/lib/postgresql/data \
    postgres:17-alpine

# 6. Est-ce que la donnée sera toujours là ?
docker exec -it demo-bdd-retour psql -U postgres -c "SELECT * FROM test;"
```

### Nettoyage

```bash
docker rm -f demo-bdd
docker volume remove demo-bdd
```

---

## Exercice 3 : Health-check (Python)

Nous allons créer une petite application Python qui expose une route de santé (`/health`) et configurer Docker pour l'interroger.

### 3.1 Création de l'application

Créez un dossier `td2-health` et ajoutez ce fichier **`server.py`** :


### 3.2 Le Dockerfile avec Sonde

Dans le même dossier, créez le **`Dockerfile`** suivant :

```dockerfile
FROM python:3.11-alpine
WORKDIR /app
COPY server.py .

# Instruction de Health-check : Docker va executer 'wget' toutes les 5 secondes
HEALTHCHECK --interval=5s --timeout=3s --retries=3 \
  CMD wget -qO- http://localhost:8000/health || exit 1

CMD ["python", "server.py"]
```

### 3.3 Test de l'état de santé

1. Buildez l'image :
   `docker build -t app-python:health .`
2. Lancez le conteneur :
   `docker run -d --name app-sante app-python:health`
3. Observez l'état du conteneur en tapant plusieurs fois la commande (laissez passer quelques secondes) :
   `docker ps`
