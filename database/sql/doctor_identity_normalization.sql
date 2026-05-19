BEGIN TRANSACTION;

UPDATE admin_appointments SET doctor_id = 3 WHERE doctor_id IN (7,11,15,27);
UPDATE departments SET doctor_id = 3 WHERE doctor_id IN (7,11,15,27);
UPDATE reports SET doctor_id = 3 WHERE doctor_id IN (7,11,15,27);
DELETE FROM doctors WHERE id IN (7,11,15,27);

UPDATE departments SET doctor_id = 4 WHERE doctor_id IN (8,12,16,20,24);
UPDATE reports SET doctor_id = 4 WHERE doctor_id IN (8,12,16,20,24);
DELETE FROM doctors WHERE id IN (8,12,16,20,24);

UPDATE appointments SET doctor_id = 2 WHERE doctor_id IN (22,26,6,10,14);
UPDATE departments SET doctor_id = 2 WHERE doctor_id IN (22,26,6,10,14);
UPDATE reports SET doctor_id = 2 WHERE doctor_id IN (22,26,6,10,14);
DELETE FROM doctors WHERE id IN (22,26,6,10,14);

UPDATE appointments SET doctor_id = 1 WHERE doctor_id IN (9,13,17,21,25);
UPDATE departments SET doctor_id = 1 WHERE doctor_id IN (9,13,17,21,25);
UPDATE reports SET doctor_id = 1 WHERE doctor_id IN (9,13,17,21,25);
DELETE FROM doctors WHERE id IN (9,13,17,21,25);

COMMIT;
