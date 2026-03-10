Voici une **proposition complète d’arborescence** pour un site web répondant à ton besoin, avec **tous les fichiers de base**, les **fichiers fonctionnels**, et le **rôle/contenu attendu de chacun**.

Je pars sur une application **PHP + PostgreSQL + `.env`**, en architecture simple, propre et évolutive.

---

# Arborescence globale

```text
retrocession-app/
│
├── .env
├── .env.example
├── .gitignore
├── composer.json
├── README.md
│
├── public/
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── profile.php
│   ├── cabinets.php
│   ├── cabinet_form.php
│   ├── practitioners.php
│   ├── relationships.php
│   ├── rules.php
│   ├── receipts.php
│   ├── receipt_form.php
│   ├── retrocessions.php
│   ├── payments.php
│   ├── payment_form.php
│   ├── history.php
│   ├── exports.php
│   ├── admin.php
│   ├── forgot-password.php
│   ├── reset-password.php
│   ├── unauthorized.php
│   │
│   └── assets/
│       ├── css/
│       │   ├── style.css
│       │   ├── auth.css
│       │   ├── dashboard.css
│       │   ├── tables.css
│       │   └── forms.css
│       ├── js/
│       │   ├── app.js
│       │   ├── alerts.js
│       │   ├── receipts.js
│       │   ├── payments.js
│       │   ├── filters.js
│       │   └── dashboard.js
│       ├── img/
│       │   ├── logo.png
│       │   ├── favicon.ico
│       │   └── default-avatar.png
│       └── uploads/
│           └── justificatifs/
│
├── app/
│   ├── config/
│   │   ├── bootstrap.php
│   │   ├── config.php
│   │   ├── db.php
│   │   ├── mail.php
│   │   ├── session.php
│   │   ├── auth.php
│   │   ├── csrf.php
│   │   └── roles.php
│   │
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── UserController.php
│   │   ├── ProfileController.php
│   │   ├── CabinetController.php
│   │   ├── RelationshipController.php
│   │   ├── RetrocessionRuleController.php
│   │   ├── ReceiptController.php
│   │   ├── RetrocessionController.php
│   │   ├── PaymentController.php
│   │   ├── DashboardController.php
│   │   ├── ExportController.php
│   │   ├── AdminController.php
│   │   ├── PasswordResetController.php
│   │   └── AuditController.php
│   │
│   ├── models/
│   │   ├── User.php
│   │   ├── Cabinet.php
│   │   ├── CabinetUser.php
│   │   ├── PractitionerRelationship.php
│   │   ├── RetrocessionRule.php
│   │   ├── Receipt.php
│   │   ├── Retrocession.php
│   │   ├── Payment.php
│   │   ├── AuditLog.php
│   │   └── PasswordReset.php
│   │
│   ├── services/
│   │   ├── AuthService.php
│   │   ├── MailService.php
│   │   ├── RetrocessionCalculatorService.php
│   │   ├── DashboardService.php
│   │   ├── ExportCsvService.php
│   │   ├── ExportPdfService.php
│   │   ├── ReceiptService.php
│   │   ├── PaymentService.php
│   │   ├── RuleResolverService.php
│   │   ├── NotificationService.php
│   │   └── AuditService.php
│   │
│   ├── repositories/
│   │   ├── UserRepository.php
│   │   ├── CabinetRepository.php
│   │   ├── RelationshipRepository.php
│   │   ├── RuleRepository.php
│   │   ├── ReceiptRepository.php
│   │   ├── RetrocessionRepository.php
│   │   ├── PaymentRepository.php
│   │   └── AuditRepository.php
│   │
│   ├── helpers/
│   │   ├── redirect.php
│   │   ├── response.php
│   │   ├── validator.php
│   │   ├── sanitizer.php
│   │   ├── flash.php
│   │   ├── format.php
│   │   ├── upload.php
│   │   ├── pagination.php
│   │   └── date.php
│   │
│   ├── middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── GuestMiddleware.php
│   │   ├── AdminMiddleware.php
│   │   ├── OwnerMiddleware.php
│   │   └── CsrfMiddleware.php
│   │
│   └── views/
│       ├── layouts/
│       │   ├── header.php
│       │   ├── footer.php
│       │   ├── navbar.php
│       │   ├── sidebar.php
│       │   ├── topbar.php
│       │   ├── flash.php
│       │   └── app.php
│       │
│       ├── auth/
│       │   ├── login.view.php
│       │   ├── register.view.php
│       │   ├── forgot-password.view.php
│       │   └── reset-password.view.php
│       │
│       ├── dashboard/
│       │   └── index.view.php
│       │
│       ├── profile/
│       │   └── profile.view.php
│       │
│       ├── cabinets/
│       │   ├── index.view.php
│       │   ├── create.view.php
│       │   ├── edit.view.php
│       │   └── show.view.php
│       │
│       ├── relationships/
│       │   ├── index.view.php
│       │   ├── create.view.php
│       │   └── edit.view.php
│       │
│       ├── rules/
│       │   ├── index.view.php
│       │   ├── create.view.php
│       │   └── edit.view.php
│       │
│       ├── receipts/
│       │   ├── index.view.php
│       │   ├── create.view.php
│       │   ├── edit.view.php
│       │   └── show.view.php
│       │
│       ├── retrocessions/
│       │   ├── index.view.php
│       │   └── show.view.php
│       │
│       ├── payments/
│       │   ├── index.view.php
│       │   ├── create.view.php
│       │   └── show.view.php
│       │
│       ├── history/
│       │   └── index.view.php
│       │
│       ├── exports/
│       │   └── index.view.php
│       │
│       ├── admin/
│       │   ├── index.view.php
│       │   ├── users.view.php
│       │   ├── cabinets.view.php
│       │   └── logs.view.php
│       │
│       └── errors/
│           ├── 403.view.php
│           ├── 404.view.php
│           └── 500.view.php
│
├── database/
│   ├── schema.sql
│   ├── views.sql
│   ├── triggers.sql
│   ├── seed.sql
│   └── migrations/
│       ├── 001_create_users.sql
│       ├── 002_create_cabinets.sql
│       ├── 003_create_cabinet_users.sql
│       ├── 004_create_practitioner_relationships.sql
│       ├── 005_create_retrocession_rules.sql
│       ├── 006_create_receipts.sql
│       ├── 007_create_retrocessions.sql
│       ├── 008_create_payments.sql
│       ├── 009_create_password_resets.sql
│       └── 010_create_audit_logs.sql
│
├── routes/
│   ├── web.php
│   ├── auth.php
│   ├── admin.php
│   └── api.php
│
└── storage/
    ├── logs/
    │   ├── app.log
    │   ├── error.log
    │   └── mail.log
    ├── cache/
    └── exports/
        ├── csv/
        └── pdf/
```

---

# Fichiers racine

## `.env`

Contient toutes les variables sensibles et configurables.

Composition :

* `APP_NAME`
* `APP_ENV`
* `APP_URL`
* `APP_DEBUG`
* `APP_TIMEZONE`
* `DB_HOST`
* `DB_PORT`
* `DB_DATABASE`
* `DB_USERNAME`
* `DB_PASSWORD`
* `MAIL_HOST`
* `MAIL_PORT`
* `MAIL_USERNAME`
* `MAIL_PASSWORD`
* `MAIL_ENCRYPTION`
* `MAIL_FROM_ADDRESS`
* `MAIL_FROM_NAME`
* `SESSION_NAME`
* `SESSION_LIFETIME`
* `CSRF_SECRET`
* `EXPORT_PATH`
* `UPLOAD_PATH`

---

## `.env.example`

Version modèle sans données sensibles.

Composition :

* mêmes clés que `.env`
* valeurs d’exemple ou vides

---

## `.gitignore`

Ignore :

* `.env`
* `vendor/`
* `storage/logs/*`
* `storage/exports/*`
* `public/assets/uploads/*`

---

## `composer.json`

Déclare les dépendances PHP.

Composition :

* autoload PSR-4
* dépendances :

  * `vlucas/phpdotenv`
  * `phpmailer/phpmailer`
  * éventuellement `dompdf/dompdf`
* scripts éventuels de démarrage

---

## `README.md`

Documentation du projet.

Composition :

* présentation du projet
* prérequis
* installation
* configuration `.env`
* création BDD
* lancement local
* structure du projet
* rôles utilisateurs
* roadmap

---

# Dossier `public/`

## `public/index.php`

Point d’entrée principal.

Composition :

* charge `bootstrap.php`
* charge les routes
* redirige selon session :

  * vers `dashboard.php` si connecté
  * vers `login.php` sinon

---

## `public/login.php`

Page de connexion.

Composition :

* formulaire email / mot de passe
* case “se souvenir de moi” optionnelle
* lien mot de passe oublié
* appel `AuthController`

---

## `public/register.php`

Page d’inscription.

Composition :

* prénom
* nom
* email
* mot de passe
* confirmation
* rôle initial si autorisé
* validation des champs
* appel `AuthController`

---

## `public/logout.php`

Déconnexion.

Composition :

* destruction session
* redirection login

---

## `public/dashboard.php`

Page tableau de bord.

Composition :

* indicateurs principaux
* graphiques ou cartes de synthèse
* filtres période
* widgets par rôle

---

## `public/profile.php`

Gestion du profil utilisateur.

Composition :

* informations personnelles
* mot de passe
* téléphone
* profession
* identifiants pro éventuels

---

## `public/cabinets.php`

Liste des cabinets accessibles à l’utilisateur.

Composition :

* tableau des cabinets
* bouton création
* bouton modification
* détail cabinet

---

## `public/cabinet_form.php`

Création / modification de cabinet.

Composition :

* nom
* adresse
* coordonnées
* propriétaire
* statut

---

## `public/practitioners.php`

Liste des praticiens.

Composition :

* praticiens du cabinet
* rôle
* rattachement cabinet
* actions consultation / édition

---

## `public/relationships.php`

Gestion des associations hébergé / hébergeur.

Composition :

* liste des relations
* dates début / fin
* notes
* filtre par cabinet

---

## `public/rules.php`

Gestion des règles de rétrocession.

Composition :

* règle par relation
* type règle
* valeur
* période application
* historique

---

## `public/receipts.php`

Liste des encaissements.

Composition :

* tableau paginé
* filtres date / praticien / cabinet / type d’acte
* total encaissé
* actions modifier / supprimer / détail

---

## `public/receipt_form.php`

Ajout / modification d’encaissement.

Composition :

* date
* montant
* type d’acte
* praticien
* relation
* commentaire
* bouton enregistrer

---

## `public/retrocessions.php`

Vue des rétrocessions calculées.

Composition :

* liste des rétrocessions
* montant dû
* montant conservé
* statut
* paiements liés
* reste à payer

---

## `public/payments.php`

Liste des paiements.

Composition :

* tableau des paiements
* filtre période
* filtre statut
* lien vers rétrocession associée

---

## `public/payment_form.php`

Ajout d’un paiement.

Composition :

* rétrocession cible
* date paiement
* montant
* mode de paiement
* référence
* commentaire
* justificatif

---

## `public/history.php`

Historique global.

Composition :

* encaissements
* rétrocessions
* paiements
* événements métier
* filtres avancés

---

## `public/exports.php`

Exports des données.

Composition :

* export CSV encaissements
* export CSV paiements
* export relevé mensuel
* export PDF plus tard

---

## `public/admin.php`

Espace admin.

Composition :

* gestion utilisateurs
* supervision cabinets
* journaux
* corrections de données

---

## `public/forgot-password.php`

Demande de réinitialisation.

Composition :

* champ email
* envoi du mail
* message flash

---

## `public/reset-password.php`

Réinitialisation mot de passe.

Composition :

* token
* nouveau mot de passe
* confirmation
* validation sécurité

---

## `public/unauthorized.php`

Page 403 simplifiée.

Composition :

* message accès refusé
* lien retour tableau de bord

---

# `public/assets/css/`

## `style.css`

Style global.

Composition :

* reset léger
* typographie
* couleurs
* espacements
* boutons
* tableaux
* formulaires
* alertes
* cartes dashboard

---

## `auth.css`

Style des pages connexion / inscription.

Composition :

* layout centré
* carte auth
* champs
* liens secondaires

---

## `dashboard.css`

Style spécifique tableau de bord.

Composition :

* cards KPI
* graphiques
* grille responsive
* blocs synthèse

---

## `tables.css`

Style pour tableaux.

Composition :

* lignes
* entêtes
* tri
* hover
* pagination

---

## `forms.css`

Style formulaires métier.

Composition :

* labels
* champs
* erreurs
* sections
* boutons d’action

---

# `public/assets/js/`

## `app.js`

JS global.

Composition :

* initialisation UI
* fermeture alertes
* interactions communes

---

## `alerts.js`

Gestion des messages flash et confirmations.

Composition :

* confirmation suppression
* auto-hide alertes

---

## `receipts.js`

Logique front pour les encaissements.

Composition :

* contrôles montant/date
* prévisualisation éventuelle
* validation client

---

## `payments.js`

Logique front pour les paiements.

Composition :

* contrôle montant
* calcul indicatif du reste à payer
* validation du formulaire

---

## `filters.js`

Gestion des filtres.

Composition :

* filtres date
* praticien
* cabinet
* remise à zéro

---

## `dashboard.js`

Comportement du dashboard.

Composition :

* chargement widgets
* switch période
* graphiques simples

---

# `app/config/`

## `bootstrap.php`

Fichier de démarrage principal.

Composition :

* chargement autoload Composer
* chargement `.env`
* timezone
* erreurs PHP
* session
* helpers globaux
* connexion DB

---

## `config.php`

Centralise la lecture des variables de config.

Composition :

* tableau de configuration applicative
* accès simple aux paramètres `.env`

---

## `db.php`

Connexion base de données via `.env`.

Composition :

* lecture `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
* création PDO
* options :

  * exceptions activées
  * fetch mode assoc
  * utf8
* fonction ou classe singleton `getPDO()`

---

## `mail.php`

Configuration de l’envoi d’emails via `.env`.

Composition :

* lecture :

  * `MAIL_HOST`
  * `MAIL_PORT`
  * `MAIL_USERNAME`
  * `MAIL_PASSWORD`
  * `MAIL_ENCRYPTION`
  * `MAIL_FROM_ADDRESS`
  * `MAIL_FROM_NAME`
* création objet PHPMailer
* méthode de base d’envoi

---

## `session.php`

Gestion de session.

Composition :

* nom session
* durée
* sécurité cookie
* démarrage session
* régénération ID si besoin

---

## `auth.php`

Fonctions globales d’authentification.

Composition :

* `isAuthenticated()`
* `user()`
* `loginUser()`
* `logoutUser()`
* `requireAuth()`

---

## `csrf.php`

Gestion protection CSRF.

Composition :

* génération token
* stockage session
* validation token
* injection dans formulaires

---

## `roles.php`

Gestion des rôles et permissions.

Composition :

* constantes de rôles
* helpers :

  * `hasRole()`
  * `isAdmin()`
  * `isHostingPractitioner()`
  * `isHostedPractitioner()`

---

# `app/controllers/`

## `AuthController.php`

Gère l’inscription, connexion, déconnexion.

Composition :

* `showLogin()`
* `login()`
* `showRegister()`
* `register()`
* `logout()`

---

## `UserController.php`

Gestion des utilisateurs.

Composition :

* liste utilisateurs
* création
* mise à jour
* consultation
* désactivation

---

## `ProfileController.php`

Gestion du profil connecté.

Composition :

* affichage profil
* mise à jour infos
* changement mot de passe

---

## `CabinetController.php`

Gestion des cabinets.

Composition :

* liste
* création
* modification
* détail
* ajout praticiens

---

## `RelationshipController.php`

Gestion des associations hébergé / hébergeur.

Composition :

* création relation
* modification
* clôture relation
* listing par cabinet

---

## `RetrocessionRuleController.php`

Gestion des règles.

Composition :

* création règle
* modification
* historique
* activation / désactivation

---

## `ReceiptController.php`

Gestion des encaissements.

Composition :

* liste
* création
* édition
* suppression
* affichage détail

---

## `RetrocessionController.php`

Gestion des calculs et affichage des rétrocessions.

Composition :

* calcul d’une rétrocession
* recalcul
* liste
* détail
* synthèse période

---

## `PaymentController.php`

Gestion des paiements.

Composition :

* création paiement
* liste paiements
* détail
* mise à jour du statut rétrocession lié

---

## `DashboardController.php`

Alimente le tableau de bord.

Composition :

* KPI encaissements
* KPI rétrocessions
* KPI paiements
* données par période
* widgets par rôle

---

## `ExportController.php`

Gère les exports.

Composition :

* export CSV encaissements
* export CSV paiements
* relevés périodiques
* futur export PDF

---

## `AdminController.php`

Fonctions admin.

Composition :

* tableau de bord admin
* gestion utilisateurs
* gestion cabinets
* consultation des logs

---

## `PasswordResetController.php`

Gestion mot de passe oublié.

Composition :

* demande reset
* génération token
* envoi mail
* vérification token
* mise à jour mot de passe

---

## `AuditController.php`

Gestion du journal d’actions.

Composition :

* enregistrement des actions critiques
* consultation filtrée admin

---

# `app/models/`

## `User.php`

Représente un utilisateur.

Composition :

* propriétés du user
* hydratation
* méthodes simples d’accès

---

## `Cabinet.php`

Représente un cabinet.

Composition :

* propriétés cabinet
* infos de contact
* propriétaire

---

## `CabinetUser.php`

Liaison utilisateur-cabinet.

Composition :

* rôle dans le cabinet
* dates rattachement

---

## `PractitionerRelationship.php`

Représente la relation hébergé / hébergeur.

Composition :

* IDs utilisateurs
* dates
* notes

---

## `RetrocessionRule.php`

Représente une règle.

Composition :

* type
* valeur
* période
* ciblage éventuel type d’acte

---

## `Receipt.php`

Représente un encaissement.

Composition :

* date
* montant
* type acte
* commentaire

---

## `Retrocession.php`

Représente une rétrocession calculée.

Composition :

* base
* montant rétrocession
* montant conservé
* statut

---

## `Payment.php`

Représente un paiement.

Composition :

* date
* montant
* méthode
* référence

---

## `AuditLog.php`

Représente une trace d’action.

Composition :

* utilisateur
* action
* entité
* anciennes / nouvelles valeurs

---

## `PasswordReset.php`

Représente une demande de réinitialisation.

Composition :

* email
* token
* expiration
* consommation du token

---

# `app/services/`

## `AuthService.php`

Logique métier auth.

Composition :

* vérification identifiants
* hash / verify password
* création session
* contrôle compte actif

---

## `MailService.php`

Service d’envoi de mails.

Composition :

* méthode d’envoi générique
* mail bienvenue
* mail reset password
* notification paiement
* notification relevé mensuel

---

## `RetrocessionCalculatorService.php`

Cœur métier du calcul.

Composition :

* récupération règle applicable
* calcul pourcentage
* calcul montant fixe
* calcul montant praticien
* gestion arrondi
* génération / mise à jour rétrocession

---

## `DashboardService.php`

Prépare les données de synthèse.

Composition :

* totals période
* totaux par praticien
* restes à payer
* données chart

---

## `ExportCsvService.php`

Création des fichiers CSV.

Composition :

* export encaissements
* export paiements
* export rétrocessions
* entêtes colonnes
* streaming ou génération fichier

---

## `ExportPdfService.php`

Préparation future PDF.

Composition :

* relevé mensuel
* état des paiements
* détail période

---

## `ReceiptService.php`

Métier autour des encaissements.

Composition :

* validation logique
* création encaissement
* recalcul rétrocession associée

---

## `PaymentService.php`

Métier autour des paiements.

Composition :

* validation montant
* ajout paiement
* recalcul solde
* changement statut rétrocession

---

## `RuleResolverService.php`

Trouve la bonne règle pour une date et une relation.

Composition :

* sélection par période
* sélection par type acte
* priorité des règles

---

## `NotificationService.php`

Notifications métier.

Composition :

* notification paiement saisi
* notification solde dû
* notification relevé généré

---

## `AuditService.php`

Centralise les logs métiers.

Composition :

* `logCreate()`
* `logUpdate()`
* `logDelete()`
* `logLogin()`

---

# `app/repositories/`

Chaque repository contient :

* requêtes SQL ciblées
* recherche par ID
* liste filtrée
* création
* modification
* suppression logique ou physique

## Fichiers

* `UserRepository.php`
* `CabinetRepository.php`
* `RelationshipRepository.php`
* `RuleRepository.php`
* `ReceiptRepository.php`
* `RetrocessionRepository.php`
* `PaymentRepository.php`
* `AuditRepository.php`

---

# `app/helpers/`

## `redirect.php`

Composition :

* redirection simple
* redirection avec message flash

## `response.php`

Composition :

* réponse JSON si besoin
* code HTTP
* helpers succès / erreur

## `validator.php`

Composition :

* validation email
* champs requis
* nombres positifs
* dates
* tailles mini/maxi

## `sanitizer.php`

Composition :

* trim
* nettoyage chaînes
* cast nombre/date

## `flash.php`

Composition :

* flash success
* flash error
* flash warning
* lecture / suppression

## `format.php`

Composition :

* format euro
* format date FR
* format statut

## `upload.php`

Composition :

* envoi justificatifs
* contrôle extension
* contrôle taille
* nommage sécurisé

## `pagination.php`

Composition :

* calcul offset
* limite
* nb pages

## `date.php`

Composition :

* helpers périodes
* début/fin mois
* trimestre
* année

---

# `app/middleware/`

## `AuthMiddleware.php`

Contrôle utilisateur connecté.

## `GuestMiddleware.php`

Empêche accès pages auth si déjà connecté.

## `AdminMiddleware.php`

Réserve certaines pages aux admins.

## `OwnerMiddleware.php`

Réserve certaines actions à l’hébergeur / manager / owner.

## `CsrfMiddleware.php`

Vérifie les tokens CSRF sur formulaires POST.

---

# `app/views/layouts/`

## `header.php`

Composition :

* balises HTML début
* `<head>`
* meta charset
* viewport
* titre page
* chargement CSS
* favicon

---

## `footer.php`

Composition :

* fermeture conteneurs
* pied de page
* copyright
* scripts JS globaux
* fermeture `body` / `html`

---

## `navbar.php`

Composition :

* logo
* liens principaux
* profil utilisateur
* bouton déconnexion

---

## `sidebar.php`

Composition :

* menu latéral
* dashboard
* cabinets
* relations
* règles
* encaissements
* rétrocessions
* paiements
* historique
* admin si rôle admin

---

## `topbar.php`

Composition :

* titre de page
* breadcrumb
* raccourcis actions rapides

---

## `flash.php`

Composition :

* affichage des messages de session :

  * succès
  * erreur
  * avertissement

---

## `app.php`

Composition :

* layout global
* inclusion header/navbar/sidebar/topbar/footer
* zone contenu principal

---

# `app/views/auth/`

## `login.view.php`

Composition :

* formulaire login
* email
* mot de passe
* lien reset
* bouton connexion

## `register.view.php`

Composition :

* formulaire inscription
* identité
* email
* mot de passe
* confirmation

## `forgot-password.view.php`

Composition :

* champ email
* bouton envoi

## `reset-password.view.php`

Composition :

* champ mot de passe
* confirmation
* token caché

---

# `app/views/dashboard/`

## `index.view.php`

Composition :

* cartes KPI
* résumés financiers
* filtres date
* graphiques
* liste dernières actions

---

# `app/views/profile/`

## `profile.view.php`

Composition :

* données utilisateur
* édition profil
* changement mot de passe

---

# `app/views/cabinets/`

## `index.view.php`

Composition :

* tableau cabinets
* bouton créer

## `create.view.php`

Composition :

* formulaire cabinet

## `edit.view.php`

Composition :

* formulaire édition cabinet

## `show.view.php`

Composition :

* détail cabinet
* praticiens rattachés
* relations liées

---

# `app/views/relationships/`

## `index.view.php`

Composition :

* tableau des associations

## `create.view.php`

Composition :

* formulaire association hébergé / hébergeur

## `edit.view.php`

Composition :

* mise à jour association

---

# `app/views/rules/`

## `index.view.php`

Composition :

* liste règles
* historique

## `create.view.php`

Composition :

* type règle
* valeur
* période
* type acte éventuel

## `edit.view.php`

Composition :

* mise à jour règle

---

# `app/views/receipts/`

## `index.view.php`

Composition :

* tableau encaissements
* filtres
* total
* actions

## `create.view.php`

Composition :

* formulaire ajout

## `edit.view.php`

Composition :

* formulaire édition

## `show.view.php`

Composition :

* détail encaissement
* rétrocession liée

---

# `app/views/retrocessions/`

## `index.view.php`

Composition :

* tableau rétrocessions
* montant dû
* montant payé
* reste
* statut

## `show.view.php`

Composition :

* détail rétrocession
* encaissement source
* règle appliquée
* paiements associés

---

# `app/views/payments/`

## `index.view.php`

Composition :

* liste paiements
* filtres

## `create.view.php`

Composition :

* formulaire paiement
* upload justificatif optionnel

## `show.view.php`

Composition :

* détail paiement

---

# `app/views/history/`

## `index.view.php`

Composition :

* historique fusionné
* filtres période / utilisateur / cabinet / type événement

---

# `app/views/exports/`

## `index.view.php`

Composition :

* boutons export
* choix plage dates
* type export

---

# `app/views/admin/`

## `index.view.php`

Composition :

* KPI admin
* raccourcis gestion

## `users.view.php`

Composition :

* liste utilisateurs
* activation / désactivation
* rôles

## `cabinets.view.php`

Composition :

* liste cabinets
* supervision

## `logs.view.php`

Composition :

* journal d’audit
* filtres action / date / utilisateur

---

# `app/views/errors/`

## `403.view.php`

Composition :

* accès refusé

## `404.view.php`

Composition :

* page introuvable

## `500.view.php`

Composition :

* erreur serveur

---

# `database/`

## `schema.sql`

Composition :

* création des tables principales :

  * users
  * cabinets
  * cabinet_users
  * practitioner_relationships
  * retrocession_rules
  * receipts
  * retrocessions
  * payments
  * password_resets
  * audit_logs

---

## `views.sql`

Composition :

* vues SQL utiles :

  * vue résumé rétrocessions
  * vue reste à payer
  * vue synthèse mensuelle

---

## `triggers.sql`

Composition :

* triggers `updated_at`
* trigger journalisation
* trigger recalcul statut paiement si besoin

---

## `seed.sql`

Composition :

* admin par défaut
* cabinet de démo
* utilisateurs de test
* règles exemple
* encaissements exemple
* paiements exemple

---

## `database/migrations/*.sql`

Composition :

* une migration par table
* versionnement propre de la base

---

# `routes/`

## `web.php`

Composition :

* routes principales GET/POST de l’application

## `auth.php`

Composition :

* routes login/register/logout/reset

## `admin.php`

Composition :

* routes admin protégées

## `api.php`

Composition :

* endpoints AJAX éventuels :

  * calcul rétrocession
  * filtres dynamiques
  * widgets dashboard

---

# `storage/`

## `storage/logs/app.log`

Composition :

* logs applicatifs généraux

## `storage/logs/error.log`

Composition :

* erreurs PHP / SQL / métier

## `storage/logs/mail.log`

Composition :

* suivi envois mails

## `storage/cache/`

Composition :

* fichiers temporaires / cache léger

## `storage/exports/csv/`

Composition :

* exports CSV générés

## `storage/exports/pdf/`

Composition :

* futurs exports PDF

---

# Fichiers indispensables en plus pour répondre à tous les besoins métier

Il en manque quelques-uns si on veut couvrir proprement tout le besoin.

## `database/migrations/009_create_password_resets.sql`

Composition :

* table reset mot de passe
* email
* token
* expiration
* date utilisation

---

## `app/models/PasswordReset.php`

Composition :

* gestion du token de reset

---

## `app/services/RuleResolverService.php`

Composition :

* récupération de la bonne règle selon :

  * relation
  * date encaissement
  * type acte

C’est indispensable pour que le calcul soit correct.

---

## `app/services/RetrocessionCalculatorService.php`

Composition :

* calcul pourcentage
* calcul fixe
* création ou MAJ rétrocession
* calcul du montant conservé

C’est le cœur du projet.

---

## `app/services/PaymentService.php`

Composition :

* somme des paiements
* calcul reste à payer
* mise à jour statut :

  * `to_pay`
  * `partially_paid`
  * `paid`

---

## `app/services/AuditService.php`

Composition :

* journalisation création / modification / suppression / connexion

Très utile si admin et correction de données.

---

# Version MVP minimale

Si tu veux aller au plus direct, le **minimum de fichiers réellement nécessaires** serait :

```text
public/
  index.php
  login.php
  register.php
  logout.php
  dashboard.php
  receipts.php
  retrocessions.php
  payments.php
  profile.php

app/config/
  bootstrap.php
  db.php
  mail.php
  auth.php
  session.php
  csrf.php

app/controllers/
  AuthController.php
  ReceiptController.php
  RetrocessionController.php
  PaymentController.php
  DashboardController.php
  ProfileController.php

app/services/
  AuthService.php
  RetrocessionCalculatorService.php
  PaymentService.php
  MailService.php

app/views/layouts/
  header.php
  footer.php
  navbar.php
  flash.php

database/
  schema.sql
  seed.sql
```

Mais pour un projet propre et vendable, l’arborescence complète plus haut est la bonne.

Si tu veux, je peux maintenant te donner le **contenu exact de chaque fichier essentiel**, en commençant par `db.php`, `mail.php`, `bootstrap.php`, `header.php`, `footer.php`, puis les contrôleurs principaux.
