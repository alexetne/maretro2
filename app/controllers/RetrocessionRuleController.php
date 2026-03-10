<?php
declare(strict_types=1);

/**
 * Controller for retrocession rules.
 */
class RetrocessionRuleController
{
    private RuleRepository $rules;
    private AuditService $audit;

    public function __construct(RuleRepository $rules, AuditService $audit)
    {
        $this->rules = $rules;
        $this->audit = $audit;
    }

    public function index(): array
    {
        requireAuth();
        return ['rules' => $this->rules->findByRelationship((int)($_GET['relationship_id'] ?? 0))];
    }

    public function show(int $id): array
    {
        requireAuth();
        return ['rule' => $this->rules->findById($id)];
    }

    public function create(): void
    {
        requireAuth();
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        $data = $this->sanitizeInput();
        if (!$this->validateRule($data)) {
            redirectBack();
        }

        $created = $this->rules->create($data);
        if ($created && isset($created['id'])) {
            $this->audit->logCreate((int)user()['id'], 'retrocession_rule', (int)$created['id'], $created);
            flash('success', 'Rule created.');
            redirect('/rules');
        }
        flash('error', 'Unable to create rule.');
        redirectBack();
    }

    public function update(int $id): void
    {
        requireAuth();
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        $data = $this->sanitizeInput();
        if (!$this->validateRule($data)) {
            redirectBack();
        }

        $old = $this->rules->findById($id) ?? [];
        if ($this->rules->update($id, $data)) {
            $this->audit->logUpdate((int)user()['id'], 'retrocession_rule', $id, $old, $data);
            flash('success', 'Rule updated.');
            redirect('/rules/' . $id);
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

        $old = $this->rules->findById($id) ?? [];
        // Soft delete not implemented; skip actual deletion if repository lacks it
        flash('error', 'Delete not implemented.');
        $this->audit->logDelete((int)user()['id'], 'retrocession_rule', $id, $old);
        redirectBack();
    }

    private function sanitizeInput(): array
    {
        return [
            'relationship_id' => (int)($_POST['relationship_id'] ?? 0),
            'rule_type' => sanitizeString($_POST['rule_type'] ?? ''),
            'value' => (float)($_POST['value'] ?? 0),
            'applies_from' => sanitizeString($_POST['applies_from'] ?? ''),
            'applies_to' => sanitizeString($_POST['applies_to'] ?? ''),
        ];
    }

    private function validateRule(array $data): bool
    {
        if (!in_array($data['rule_type'], ['percentage', 'fixed_amount'], true)) {
            flash('error', 'Invalid rule type.');
            return false;
        }
        if ($data['value'] <= 0) {
            flash('error', 'Rule value must be positive.');
            return false;
        }
        if (!validateRequired($data['applies_from'])) {
            flash('error', 'applies_from is required.');
            return false;
        }
        return true;
    }
}
