<?php if (isAuthenticated()): ?>
<aside class="sidebar">
    <ul>
        <li><a href="/dashboard">Dashboard</a></li>
        <li><a href="/cabinets">Cabinets</a></li>
        <li><a href="/relationships">Relations</a></li>
        <li><a href="/rules">Règles</a></li>
        <li><a href="/receipts">Encaissements</a></li>
        <li><a href="/retrocessions">Rétrocessions</a></li>
        <li><a href="/payments">Paiements</a></li>
        <li><a href="/history">Historique</a></li>
        <li><a href="/exports">Exports</a></li>
        <?php if (isAdmin()): ?>
            <li><a href="/admin">Admin</a></li>
        <?php endif; ?>
    </ul>
</aside>
<?php endif; ?>
