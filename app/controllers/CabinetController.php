<?php
declare(strict_types=1);

/**
 * Cabinet management controller.
 */
class CabinetController
{
    private CabinetRepository $cabinets;
    private AuditService $audit;

    public function __construct(CabinetRepository $cabinets, AuditService $audit)
    {
        $this->cabinets = $cabinets;
        $this->audit = $audit;
    }

    public function index(): array
    {
        requireAuth();
        return ['cabinets' => $this->cabinets->findAll()];
    }

    public function show(int $id): array
    {
        requireAuth();
        return ['cabinet' => $this->cabinets->findById($id)];
    }

    public function create(): void
    {
        requireAuth();
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }
        if (!isHostingPractitioner() && !isAdmin()) {
            redirectWithMessage('/dashboard', 'error', 'Unauthorized.');
        }

        $data = [
            'user_id' => (int)(user()['id'] ?? 0),
            'name' => sanitizeString($_POST['name'] ?? ''),
            'address' => sanitizeString($_POST['address'] ?? ''),
        ];

        if (!validateRequired($data['name'])) {
            flash('error', 'Cabinet name required.');
            redirectBack();
        }

        $created = $this->cabinets->create($data);
        if ($created && isset($created['id'])) {
            $this->audit->logCreate((int)user()['id'], 'cabinet', (int)$created['id'], $created);
            flash('success', 'Cabinet created.');
            redirect('/cabinets');
        }
        flash('error', 'Unable to create cabinet.');
        redirectBack();
    }

    public function update(int $id): void
    {
        requireAuth();
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }
        if (!isHostingPractitioner() && !isAdmin()) {
            redirectWithMessage('/dashboard', 'error', 'Unauthorized.');
        }

        $data = [
            'name' => sanitizeString($_POST['name'] ?? ''),
            'address' => sanitizeString($_POST['address'] ?? ''),
        ];

        $old = $this->cabinets->findById($id) ?? [];
        if ($this->cabinets->update($id, $data)) {
            $this->audit->logUpdate((int)user()['id'], 'cabinet', $id, $old, $data);
            flash('success', 'Cabinet updated.');
            redirect('/cabinets/' . $id);
        }
        flash('error', 'Update failed.');
        redirectBack();
    }

    public function delete(int $id): void
    {
        requireAuth();
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }
        if (!isHostingPractitioner() && !isAdmin()) {
            redirectWithMessage('/dashboard', 'error', 'Unauthorized.');
        }

        $old = $this->cabinets->findById($id) ?? [];
        if ($this->cabinets->delete($id)) {
            $this->audit->logDelete((int)user()['id'], 'cabinet', $id, $old);
            flash('success', 'Cabinet deleted.');
            redirect('/cabinets');
        }
        flash('error', 'Unable to delete cabinet.');
        redirectBack();
    }
}
