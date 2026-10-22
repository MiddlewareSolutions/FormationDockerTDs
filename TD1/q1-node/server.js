const express = require('express');
const app = express();

// Route principale.
app.get('/', (req, res) => {
    res.send('Bonjour le monde ! v1\n');
});

// Démarrage du serveur sur le port 8080.
const server = app.listen(8080, () => {
    console.log('Serveur démarré sur le port 8080');
});

// Interception du signal d'arrêt pour quitter proprement
process.on('SIGTERM', () => {
    console.log('Signal SIGTERM reçu : Arrêt propre en cours...');
    server.close(() => {
        console.log('Processus terminé.');
        process.exit(0);
    });
});
