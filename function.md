# Récapitulatif des fonctionnalités

## Présentation
L'application a pour objectif d'aider les professionnels du secteur médical et paramédical exerçant en libéral à gérer les rétrocessions financières entre praticiens.

Dans ce contexte, certains praticiens utilisent le cabinet, les locaux ou les ressources d'un autre professionnel. En contrepartie, une rétrocession est due selon des règles définies à l'avance, généralement sous forme de pourcentage ou de montant fixe.

L'application permet donc de suivre les encaissements, calculer automatiquement les rétrocessions, enregistrer les paiements et fournir une vision claire de la situation financière entre les parties.

---

## Objectifs principaux

- Suivre les actes ou encaissements réalisés par les praticiens
- Déterminer automatiquement les montants de rétrocession
- Gérer les règles de calcul selon les accords entre praticiens
- Suivre les paiements effectués ou restant dus
- Générer un historique clair par période

---

## Utilisateurs

### 1. Praticien hébergé
Professionnel qui utilise les locaux ou le cabinet d'un autre praticien.

Fonctionnalités :
- déclarer ses encaissements
- consulter les rétrocessions dues
- suivre ses paiements
- consulter son historique

### 2. Praticien hébergeur / propriétaire
Professionnel qui met à disposition son cabinet ou ses locaux.

Fonctionnalités :
- consulter les encaissements déclarés
- voir les rétrocessions à recevoir
- suivre les paiements reçus
- gérer les paramètres de rétrocession

### 3. Administrateur (optionnel)
Utilisateur chargé de la gestion globale de la plateforme.

Fonctionnalités :
- gérer les comptes utilisateurs
- superviser les cabinets
- corriger ou valider certaines données

---

## Fonctionnalités principales

## Authentification
- inscription utilisateur
- connexion
- déconnexion
- gestion du profil
- modification du mot de passe

---

## Gestion des utilisateurs
- création de compte
- modification des informations personnelles
- définition du rôle utilisateur
- consultation du profil

---

## Gestion des cabinets
- création d'un cabinet
- modification des informations du cabinet
- ajout de praticiens à un cabinet
- association entre praticien hébergé et praticien hébergeur

---

## Gestion des règles de rétrocession
- définition d'un taux de rétrocession en pourcentage
- possibilité d'utiliser un montant fixe
- paramétrage spécifique selon le praticien
- historique des règles appliquées

---

## Déclaration des encaissements
- ajout d'un encaissement
- saisie de la date
- saisie du montant encaissé
- saisie du type d'acte ou de prestation
- ajout d'un commentaire si besoin
- modification ou suppression d'un encaissement

---

## Calcul des rétrocessions
- calcul automatique selon les règles définies
- affichage du montant dû
- affichage du montant conservé par le praticien
- calcul par encaissement
- calcul par période (mois, trimestre, année)

---

## Gestion des paiements
- enregistrement d'un paiement de rétrocession
- saisie de la date du paiement
- saisie du montant payé
- suivi du reste à payer
- gestion d'un statut :
  - à payer
  - partiellement payé
  - payé

---

## Tableau de bord
- total des encaissements
- total des rétrocessions dues
- total des rétrocessions payées
- reste à payer
- vue synthétique par période

---

## Historique et suivi
- historique des encaissements
- historique des rétrocessions
- historique des paiements
- recherche par période
- filtrage par praticien ou cabinet

---

## Exports
- export CSV
- export PDF (optionnel dans un second temps)
- impression des relevés

---

## Fonctionnalités complémentaires possibles
- notifications de paiement
- génération automatique de relevés mensuels
- gestion multi-cabinets
- ajout de justificatifs
- statistiques avancées
- journal des modifications

---

## Structure fonctionnelle simple envisagée

Dans une première version en PHP classique avec fichiers séparés par fonctionnalité, l'application peut être organisée ainsi :

- `index.php`
- `login.php`
- `register.php`
- `logout.php`
- `dashboard.php`
- `profile.php`
- `cabinet.php`
- `users.php`
- `encaissements.php`
- `add_encaissement.php`
- `retrocessions.php`
- `paiements.php`
- `reports.php`
- `config.php`

D'autres fichiers peuvent être ajoutés progressivement selon l'évolution du projet.

---

## MVP conseillé

Pour une première version simple et fonctionnelle, les priorités sont :

1. Authentification
2. Gestion des utilisateurs
3. Gestion des cabinets
4. Déclaration des encaissements
5. Calcul automatique des rétrocessions
6. Enregistrement des paiements
7. Tableau de bord simple
8. Historique par mois