# Hypertube, Dernier projet du Cursus Web 42.

## Introduction

Ce projet propose de créer une application web permettant à un utilisateur
de rechercher et visionner des vidéos.
Le lecteur sera directement intégré au site, et les vidéos seront téléchargées
au travers du protocole BitTorrent.
Le moteur de recherche interrogera plusieurs sources externes.
Une fois un élément sélectionné, il sera téléchargé sur le serveur et diffusé
sur le player web en même temps. Autrement dit, le lecteur ne se contentera pas d’afficher la vidéo une fois le téléchargement complété, mais sera capable de
streamer directement le flux.

## Auteurs
### Reelbour - Vgula - Nraziano - Staeter - Ahammou
 

## Installation

1.  S'assurer d'avoir composer, npm ainsi qu'un serveur web en localhost sur le
    port 8080 et au minimum la version 7.4.*. PHP
    (https://getcomposer.org/, https://www.npmjs.com/,
    https://www.mamp.info/en/windows/)
    Afin  d'envoyer/recevoir des mails votre php.ini doit mentionné le path de
    sendmail.

2.  Git clone le repo à la racine de votre dossier selon votre configuration
    (htdocs pour apache)

3.  Dans votre terminal favori, à la racine du dossier cloné lancer la commande
    " composer update ",

4.  Aller dans le fichier de configuration .env à la racine du dépot et
    configurer le nom d'utilisateur et le mot de passe ainsi que le port pour se connecter à la base de donnée.

5.  Ajouter manuellement (pour le moment) une table dans votre base de donnée
    qui se nomme hypertube. Ensuite, revenez sur votre terminal et lancer la
    commande " php artisan migrate"
    (Une erreur proviendrais surement d'un soucis de configuration du fichier
    .env)

6.  Aller dans le dossier node_real_stream qui se trouve à la racine du dossier
    et lancer la commande " npm install ", des warnings peuvent subvenir ignorer
    les, une fois la commande terminé lancer la commande " npm start" et laisser
    ce terminal ouvert(celui ci gère le download/streaming).

7. Rendez-vous sur localhost:8080/public & Enjoy !
