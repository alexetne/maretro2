# Application de gestion des rétrocessions médicales / paramédicales

## Présentation

Ce projet a pour objectif de développer une application web simple permettant de gérer les rétrocessions financières entre professionnels libéraux du secteur médical et paramédical.

Dans de nombreux cabinets, un praticien utilise les locaux, le matériel ou la structure administrative d'un autre praticien. En échange, une rétrocession est appliquée sur les encaissements réalisés. Cette application a vocation à faciliter ce suivi, à automatiser les calculs et à centraliser les informations importantes.

Le projet est développé dans une première version avec une architecture PHP classique, en s'appuyant sur une base de données MySQL.

---

## Objectifs du projet

- centraliser les encaissements déclarés par les praticiens
- calculer automatiquement les rétrocessions
- suivre les paiements effectués
- offrir un tableau de bord simple et lisible
- fournir une base solide pour une future évolution de l'application

---

## Technologies utilisées

### Back-end
- PHP

### Base de données
- MySQL

### Front-end
- HTML
- CSS
- JavaScript

### Serveur local conseillé
- XAMPP
- WAMP
- LAMP

---

## Architecture du projet

Le projet repose sur une architecture simple avec un fichier PHP par fonctionnalité principale.

Exemple d'organisation :

```bash
project/
│
├── index.php
├── login.php
├── register.php
├── logout.php
├── dashboard.php
├── profile.php
├── cabinet.php
├── users.php
├── encaissements.php
├── add_encaissement.php
├── retrocessions.php
├── paiements.php
├── reports.php
├── config.php
│
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── db.php
│   ├── auth.php
│   └── functions.php
│
└── sql/
    └── database.sql