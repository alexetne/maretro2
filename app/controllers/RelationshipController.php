<?php
declare(strict_types=1);

/**
 * Manages practitioner relationships.
 */
class RelationshipController
{
    private RelationshipRepository $relationships;
    private AuditService $audit;

    public function __construct(RelationshipRepository $relationships, AuditService $audit)
    {
        $this->relationships = $relationships;
        $this->audit = $audit;
    }

    public function index(): array
    {
        requireAuth();
        $cabinetId = (int)($_GET['cabinet_id'] ?? 0);
        if ($cabinetId > 0) {
            return ['relationships' => $this->relationships->findByCabinet($cabinetId)];
        }
        // Fallback: no generic list method, return empty
        return ['relationships' => []];
    }

    public function show(int $id): array
    {
        requireAuth();
        return ['relationship' => $this->relationships->findById($id)];
    }

    public function create(): void
    {
        requireAuth();
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        $data = [
            'cabinet_id' => (int)($_POST['cabinet_id'] ?? 0),
            'hosted_practitioner_id' => (int)($_POST['hosted_practitioner_id'] ?? 0),
            'hosting_practitioner_id' => (int)($_POST['hosting_practitioner_id'] ?? 0),
            'start_date' => sanitizeString($_POST['start_date'] ?? ''),
            'end_date' => sanitizeString($_POST['end_date'] ?? ''),
            'notes' => sanitizeString($_POST['notes'] ?? ''),
        ];

        if ($data['hosted_practitioner_id'] === $data['hosting_practitioner_id']) {
            flash('error', 'Hosted and hosting practitioners must differ.');
            redirectBack();
        }

        $created = $this->relationships->create($data);
        if ($created && isset($created['id'])) {
            $this->audit->logCreate((int)user()['id'], 'relationship', (int)$created['id'], $created);
            flash('success', 'Relationship created.');
            redirect('/relationships');
        }
        flash('error', 'Unable to create relationship.');
        redirectBack();
    }

    public function update(int $id): void
    {
        requireAuth();
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        $data = [
            'cabinet_id' => (int)($_POST['cabinet_id'] ?? 0),
            'hosted_practitioner_id' => (int)($_POST['hosted_practitioner_id'] ?? 0),
            'hosting_practitioner_id' => (int)($_POST['hosting_practitioner_id'] ?? 0),
            'start_date' => sanitizeString($_POST['start_date'] ?? ''),
            'end_date' => sanitizeString($_POST['end_date'] ?? ''),
            'notes' => sanitizeString($_POST['notes'] ?? ''),
        ];

        if ($data['hosted_practitioner_id'] === $data['hosting_practitioner_id']) {
            flash('error', 'Hosted and hosting practitioners must differ.');
            redirectBack();
        }

        $old = $this->relationships->findById($id) ?? [];
        if ($this->relationships->update($id, $data)) {
            $this->audit->logUpdate((int)user()['id'], 'relationship', $id, $old, $data);
            flash('success', 'Relationship updated.');
            redirect('/relationships/' . $id);
        }
        flash('error', 'Update failed.');
        redirectBack();
    }

    public function close(int $id): void
    {
        requireAuth();
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        if ($this->relationships->closeRelationship($id)) {
            $this->audit->logUpdate((int)user()['id'], 'relationship', $id, [], ['closed' => true]);
            flash('success', 'Relationship closed.');
            redirect('/relationships/' . $id);
        }
        flash('error', 'Unable to close relationship.');
        redirectBack();
    }
}
