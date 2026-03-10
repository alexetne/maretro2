CREATE OR REPLACE VIEW retrocession_summary AS
SELECT
    r.id,
    r.receipt_id,
    r.base_amount,
    r.retrocession_amount,
    r.practitioner_kept_amount,
    COALESCE(SUM(p.amount),0) as total_paid,
    r.retrocession_amount - COALESCE(SUM(p.amount),0) as remaining
FROM retrocessions r
LEFT JOIN payments p ON p.retrocession_id = r.id
GROUP BY r.id, r.receipt_id, r.base_amount, r.retrocession_amount, r.practitioner_kept_amount;
