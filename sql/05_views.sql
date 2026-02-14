USE retro_podo;

DROP VIEW IF EXISTS v_ca_by_actor;
DROP VIEW IF EXISTS v_ca_by_payment_method;
DROP VIEW IF EXISTS v_activity_by_day;

CREATE VIEW v_ca_by_actor AS
SELECT
  cs.cabinet_id,
  cs.actor_id,
  SUM(cs.price_cents) AS total_cents,
  COUNT(*) AS sessions
FROM care_sessions cs
GROUP BY cs.cabinet_id, cs.actor_id;

CREATE VIEW v_ca_by_payment_method AS
SELECT
  p.cabinet_id,
  p.payment_method_id,
  SUM(p.amount_cents) AS total_cents,
  COUNT(*) AS payments
FROM payments p
WHERE p.status = 'paid'
GROUP BY p.cabinet_id, p.payment_method_id;

CREATE VIEW v_activity_by_day AS
SELECT
  cabinet_id,
  DATE(session_date) AS day,
  COUNT(*) AS sessions,
  SUM(price_cents) AS total_cents
FROM care_sessions
GROUP BY cabinet_id, day;
