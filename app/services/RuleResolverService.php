<?php
declare(strict_types=1);

/**
 * Resolves applicable retrocession rule for a relationship.
 */
class RuleResolverService
{
    private RuleRepository $rules;

    public function __construct(RuleRepository $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Resolve active rule for date (and optional act type).
     *
     * @param int $relationshipId
     * @param string $receiptDate
     * @param string|null $actType
     * @return array|null
     */
    public function resolveActiveRule(int $relationshipId, string $receiptDate, ?string $actType = null): ?array
    {
        $rule = $this->rules->findActiveRule($relationshipId, $receiptDate);
        if ($rule === null) {
            return null;
        }

        // If act type specific logic exists, filter accordingly.
        if ($actType !== null && isset($rule['act_type']) && $rule['act_type'] !== $actType) {
            return null;
        }

        return $rule;
    }
}
