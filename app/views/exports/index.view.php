<?php $pageTitle='Exports'; include __DIR__ . '/../layouts/app.php'; ?>
<div class="grid two-cols">
    <div class="card">
        <h4>Exports CSV</h4>
        <form method="GET" action="/exports/receipts">
            <label>Du</label><input type="date" name="start">
            <label>Au</label><input type="date" name="end">
            <button class="btn btn-primary" type="submit">Receipts CSV</button>
        </form>
        <form method="GET" action="/exports/payments">
            <label>Du</label><input type="date" name="start">
            <label>Au</label><input type="date" name="end">
            <button class="btn btn-primary" type="submit">Payments CSV</button>
        </form>
        <form method="GET" action="/exports/retrocessions">
            <button class="btn btn-primary" type="submit">Retrocessions CSV</button>
        </form>
    </div>
    <div class="card">
        <h4>Exports PDF</h4>
        <form method="GET" action="/exports/statement">
            <label>Mois</label><input type="month" name="month">
            <button class="btn btn-primary" type="submit">Relevé mensuel PDF</button>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
