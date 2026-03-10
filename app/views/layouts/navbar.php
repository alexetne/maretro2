<nav class="navbar">
    <div class="navbar-brand"><a href="/">RetroHealth</a></div>
    <ul class="navbar-links">
        <?php if (isAuthenticated()): ?>
            <li><a href="/dashboard">Dashboard</a></li>
            <li><a href="/profile">Profil</a></li>
            <li><a href="/logout">Déconnexion</a></li>
        <?php else: ?>
            <li><a href="/login">Connexion</a></li>
            <li><a href="/register">Créer un compte</a></li>
        <?php endif; ?>
    </ul>
</nav>
