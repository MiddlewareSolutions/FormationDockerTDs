# TD 1 : Optimiser un dockerfile

## Objectifs

- Comprendre le fonctionnement du cache de layers.
- Appréhender l'impact de la syntaxe `CMD` sur l'arrêt des conteneurs.
- Utiliser le build "multi-stage" afin de réduire drastiquement la taille d'une image.

## Exercice 1 : Le piège du cache et les signaux POSIX

### 1.1 - Problème de caching de layers

1. Buildez l'image :
   `docker build -t app-node:bad .`

   *(Notez le temps pris par l'étape `npm install`)*
2. Modifiez le fichier `server.js` (remplacez `v1` par `v2` dans le `res.send`).
3. Relancez le build :
   `docker build -t app-node:bad .`

**Observation :**
L'étape `npm install` se relance entièrement ! Le cache a été invalidé dès la ligne `COPY . .` car un fichier source a changé.

**Objectif :**
Modifiez le `Dockerfile` réduire l'impact d'un changement dans le code source sur le temps de build.

### Q-1.2 - Le problème des signaux

1. Lancez le conteneur en tâche de fond :
   `docker run -d --name mon-app app-node:bad`
2. Regardez les logs en direct :
   `docker logs -f mon-app`
3. Dans un autre terminal, demandez l'arrêt du conteneur :
   `docker stop mon-app`

**Observation :**

- Le conteneur met environ 10 secondes à s'arrêter (le timeout par défaut de Docker).
- Dans les logs, le message `"Signal SIGTERM reçu..."` n'apparaît jamais.

**Objectif :**
Modifiez le `Dockerfile` afin que le conteneur s'arrête instantanément et proprement lors d'un docker-stop.

---

## Exercice 2 : Le build multi-étapes

### Q-2 Build unique

1. Buildez l'image : `docker build -t app-go:fat .`
2. Vérifiez sa taille : `docker images app-go:fat`

**Observation :**
L'image est bien trop grosse pour ce que nous avons voulu produire.

**Objectif :**
En utilisant ce qui a été présenté, modifiez le dockerfile afin de réduire la taille de l'image finale.
