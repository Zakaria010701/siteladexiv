SET FOREIGN_KEY_CHECKS = 0;

##################################################
# USERS                                          #
##################################################

# Delete duplicate providers
DELETE FROM providers WHERE id NOT IN (SELECT MIN(id) FROM providers GROUP BY user_id);

# Migrate Active Users
INSERT INTO ladex_iv.users (
    id,
    name,
    email, email_verified_at,
    password,
    created_at, updated_at, deleted_at,
    current_branch_id,
    firstname, lastname,
    street, postcode, location,
    phone_number, birthday,
    user_work_type_id,
    is_provider, show_in_frontend
)
SELECT DISTINCT
    u.id,
    u.name,
    u.email, u.email_verified_at,
    u.password,
#'$2y$12$Xtf94pnTy75wDSGSTDK7qebrBALgJ8n7oVwCXs46ODspkSwxlTdPW',
    u.created_at, u.updated_at, u.deleted_at,
    u.current_branch_id,
    u.firstname, u.lastname,
    u.street, u.zip_code, u.location,
    u.phone_number, u.birthday,
    1,
    IF(IFNULL(p.deactivated, 1) = 1, 0, 1) , IFNULL(p.show_in_frontend, 0)
FROM ladex_iii_migrate.users AS u
LEFT JOIN ladex_iii_migrate.providers AS p ON u.id = p.user_id
WHERE u.is_active = 1;

# Migrate Deactive Users
INSERT INTO ladex_iv.users (
    id,
    name,
    email, email_verified_at,
    password,
    created_at, updated_at, deleted_at,
    current_branch_id,
    firstname, lastname,
    street, postcode, location,
    phone_number, birthday,
    user_work_type_id,
    is_provider, show_in_frontend
)
SELECT DISTINCT
    u.id,
    u.name,
    u.email, u.email_verified_at,
    u.password,
#'$2y$12$Xtf94pnTy75wDSGSTDK7qebrBALgJ8n7oVwCXs46ODspkSwxlTdPW',
    u.created_at, u.updated_at, now(),
    u.current_branch_id,
    u.firstname, u.lastname,
    u.street, u.zip_code, u.location,
    u.phone_number, u.birthday,
    1,
    IF(IFNULL(p.deactivated, 1) = 1, 0, 1) , IFNULL(p.show_in_frontend, 0)
FROM ladex_iii_migrate.users AS u
LEFT JOIN ladex_iii_migrate.providers AS p ON u.id = p.user_id
WHERE u.is_active = 0;

# Create Role Super-Admin
INSERT INTO ladex_iv.roles (id, name, guard_name, created_at, updated_at)
VALUES (1, 'super-admin', 'web', now(), now());

# Asign Role Super-Admin
INSERT INTO ladex_iv.model_has_roles (role_id, model_type, model_id)
VALUES (1, "App\\Models\\User", 1), (1, "App\\Models\\User", 19);

# Migrate User belongs to current_branch_id
DELETE FROM ladex_iii_migrate.branch_user WHERE user_id NOT IN (SELECT id FROM ladex_iv.users) OR branch_id NOT IN (1,2,3);

INSERT INTO ladex_iv.branch_user (created_at, updated_at, branch_id, user_id)
SELECT now(), now(), branch_id, user_id
FROM ladex_iii_migrate.branch_user;

# Migrate User provides Category
DELETE FROM ladex_iii_migrate.category_provider WHERE category_id NOT IN (SELECT id FROM ladex_iv.categories);

INSERT INTO ladex_iv.category_user (created_at, updated_at, category_id, user_id)
SELECT now(), now(), category_id, user_id
FROM ladex_iii_migrate.category_provider
JOIN ladex_iii_migrate.providers ON category_provider.provider_id = providers.id;

DELETE FROM ladex_iii_migrate.category_consultation_provider WHERE category_id NOT IN (SELECT id FROM ladex_iv.categories);

INSERT INTO ladex_iv.consultation_category_user (created_at, updated_at, category_id, user_id)
SELECT now(), now(), category_id, user_id
FROM ladex_iii_migrate.category_consultation_provider
JOIN ladex_iii_migrate.providers ON category_consultation_provider.provider_id = providers.id;

# Migrate User provides Service
DELETE FROM ladex_iii_migrate.provider_service WHERE service_id NOT IN (SELECT id FROM ladex_iv.services);

INSERT INTO ladex_iv.service_user (created_at, updated_at, service_id, user_id)
SELECT now(), now(), service_id, user_id
FROM ladex_iii_migrate.provider_service
JOIN ladex_iii_migrate.providers ON provider_service.provider_id = providers.id;


##################################################
# CUSTOMERS                                      #
##################################################

# Migrate customers
INSERT INTO ladex_iv.customers (id, created_at, updated_at, deleted_at, gender, firstname, lastname, birthday, email, phone_number, options, meta)
SELECT
    id,
    created_at,
    updated_at,
    deleted_at,
    gender,
    firstname,
    lastname,
    birthday,
    email,
    phone_number,
    '[]',
    JSON_OBJECT(
        'salutation', salutation,
        'birthday', birthday,
        'profession', profession,
        'aware_of_laderma', aware_of_laderma,
        'discount', discount,
        'type', type,
        'credits', credits,
        'iban', iban,
        'data_checked', data_checked,
        'stripe_id', stripe_id,
        'pm_type', pm_type,
        'pm_last_four', pm_last_four,
        'trial_ends_at', trial_ends_at,
        'discount_expiry_date', discount_expiry_date,
        'customer_has_special_risks', customer_has_special_risks,
        'customer_special_risks', customer_special_risks,
        'customer_takes_medication', customer_takes_medication,
        'customer_medication', customer_medication,
        'is_transgender', is_transgender,
        'prefered_pronouns', prefered_pronouns,
        'transgender_notes', transgender_notes
    )
FROM ladex_iii_migrate.customers
WHERE is_active = 1;

INSERT INTO ladex_iv.customers (id, created_at, updated_at, deleted_at, gender, firstname, lastname, birthday, email, phone_number, options, meta)
SELECT
    id,
    created_at,
    updated_at,
    IFNULL(deleted_at, now()),
    gender,
    firstname,
    lastname,
    birthday,
    email,
    phone_number,
    '[]',
    JSON_OBJECT(
        'salutation', salutation,
        'birthday', birthday,
        'profession', profession,
        'aware_of_laderma', aware_of_laderma,
        'discount', discount,
        'type', type,
        'credits', credits,
        'iban', iban,
        'data_checked', data_checked,
        'stripe_id', stripe_id,
        'pm_type', pm_type,
        'pm_last_four', pm_last_four,
        'trial_ends_at', trial_ends_at,
        'discount_expiry_date', discount_expiry_date,
        'customer_has_special_risks', customer_has_special_risks,
        'customer_special_risks', customer_special_risks,
        'customer_takes_medication', customer_takes_medication,
        'customer_medication', customer_medication,
        'is_transgender', is_transgender,
        'prefered_pronouns', prefered_pronouns,
        'transgender_notes', transgender_notes
    )
FROM ladex_iii_migrate.customers
WHERE is_active = 0;

# Migrate Customer Gender
UPDATE ladex_iv.customers SET gender = 'non-binary' WHERE gender = 'other';

UPDATE ladex_iv.customers SET title = 'Frau' WHERE gender = 'female';
UPDATE ladex_iv.customers SET title = 'Herr' WHERE gender = 'male';

# Migrate customer options
UPDATE ladex_iv.customers
JOIN ladex_iii_migrate.customers
ON ladex_iii_migrate.customers.id = ladex_iv.customers.id
SET options = JSON_ARRAY_APPEND(options,'$', 'no_notifications')
WHERE ladex_iii_migrate.customers.no_notifications = TRUE;

UPDATE ladex_iv.customers
JOIN ladex_iii_migrate.customers
ON ladex_iii_migrate.customers.id = ladex_iv.customers.id
SET options = JSON_ARRAY_APPEND(options,'$', 'no_newsletters')
WHERE ladex_iii_migrate.customers.no_newsletters = TRUE;

UPDATE ladex_iv.customers
JOIN ladex_iii_migrate.customers
ON ladex_iii_migrate.customers.id = ladex_iv.customers.id
SET options = JSON_ARRAY_APPEND(options,'$', 'no_emails')
WHERE ladex_iii_migrate.customers.no_emails = TRUE;

UPDATE ladex_iv.customers
JOIN ladex_iii_migrate.customers
ON ladex_iii_migrate.customers.id = ladex_iv.customers.id
SET options = JSON_ARRAY_APPEND(options,'$', 'no_sms')
WHERE ladex_iii_migrate.customers.no_sms = TRUE;

UPDATE ladex_iv.customers
JOIN ladex_iii_migrate.customers
ON ladex_iii_migrate.customers.id = ladex_iv.customers.id
SET options = JSON_ARRAY_APPEND(options,'$', 'no_phone_calls')
WHERE ladex_iii_migrate.customers.no_phone_calls = TRUE;

UPDATE ladex_iv.customers
JOIN ladex_iii_migrate.customers
ON ladex_iii_migrate.customers.id = ladex_iv.customers.id
SET options = JSON_ARRAY_APPEND(options,'$', 'allows_picture_usage')
WHERE ladex_iii_migrate.customers.allows_image_usage = TRUE;

UPDATE ladex_iv.customers
JOIN ladex_iii_migrate.customers
ON ladex_iii_migrate.customers.id = ladex_iv.customers.id
SET options = JSON_ARRAY_APPEND(options,'$', 'no_further_appointments')
WHERE ladex_iii_migrate.customers.no_further_appointments = TRUE;

UPDATE ladex_iv.customers
JOIN ladex_iii_migrate.customers
ON ladex_iii_migrate.customers.id = ladex_iv.customers.id
SET options = JSON_ARRAY_APPEND(options,'$', 'is_vip')
WHERE ladex_iii_migrate.customers.is_vip = TRUE;

UPDATE ladex_iv.customers
JOIN ladex_iii_migrate.customers
ON ladex_iii_migrate.customers.id = ladex_iv.customers.id
SET options = JSON_ARRAY_APPEND(options,'$', 'is_difficult')
WHERE ladex_iii_migrate.customers.is_difficult = TRUE;

# Migrate additional emails
INSERT INTO email_addresses (created_at, updated_at, addressable_type, addressable_id, email)
SELECT
    now(),
    now(),
    "App\\Models\\Customer",
    id,
    email_alt
FROM ladex_iii_migrate.customers
WHERE email_alt IS NOT NULL;

# Migrate additional phone_number
INSERT INTO phone_numbers (created_at, updated_at, callable_type, callable_id, phone_number)
SELECT
    now(),
    now(),
    "App\\Models\\Customer",
    id,
    mobile_number
FROM ladex_iii_migrate.customers
WHERE mobile_number IS NOT NULL;

# Migrate address

INSERT INTO addresses (created_at, updated_at, addressable_type, addressable_id, location, postcode, address)
SELECT
    now(), now(),
    "App\\Models\\Customer", id,
    IFNULL(city, ''), IFNULL(zipcode, ''), IFNULL(address, '')
FROM ladex_iii_migrate.customers
WHERE city IS NOT NULL
OR zipcode IS NOT NULL
OR address IS NOT NULL;

INSERT INTO ladex_iv.customer_user (created_at, updated_at, customer_id, user_id)
SELECT
    now(), now(),
    c.id,
    u.id
FROM ladex_iii_migrate.customers AS c
JOIN ladex_iii_migrate.providers AS p ON c.prefered_provider_id = p.id
JOIN ladex_iv.users AS u ON p.user_id = u.id
WHERE c.prefered_provider_id IS NOT NULL;

##################################################
# APPOINTMENTS                                   #
##################################################
TRUNCATE TABLE ladex_iii_migrate.appointment_todos;

DELETE FROM ladex_iii_migrate.appointments WHERE customer_id NOT IN (SELECT id FROM ladex_iv.customers) OR category_id NOT IN (SELECT id FROM ladex_iv.categories);

UPDATE ladex_iii_migrate.appointments SET resource_id = 3 WHERE resource_id = 8;
UPDATE ladex_iii_migrate.appointments SET resource_id = 7 WHERE resource_id = 9;
UPDATE ladex_iii_migrate.appointments SET resource_id = 4 WHERE resource_id = 10;
UPDATE ladex_iii_migrate.appointments SET resource_id = 1 WHERE resource_id = 11;
UPDATE ladex_iii_migrate.appointments SET resource_id = null WHERE resource_id = 12;
UPDATE ladex_iii_migrate.appointments SET resource_id = null WHERE resource_id = 13;
UPDATE ladex_iii_migrate.appointments SET resource_id = null WHERE resource_id = 14;

INSERT INTO ladex_iv.appointments (
    id,
    created_at, updated_at, deleted_at,
    approved_at, canceled_at, done_at, reminder_sent_at,
    branch_id, room_id, customer_id, user_id, category_id,
    type, status,
    start, end,
    done_by_id,
    next_appointment_in, next_appointment_step, next_appointment_date, next_appointment_reminder_sent_at,
    cancel_reason, description,
    check_in_at, check_out_at, controlled_at, confirmed_at,
    treatment_type_id,
    meta
)
SELECT
    a.id,
    a.created_at, a.updated_at, a.deleted_at,
    approved_at, cancelled_at, is_done, reminder_sent_at,
    branch_id, room_id, customer_id, p.user_id, category_id,
    availability_type_id, 'pending',
    start, end,
    pb.user_id,
    next_appointment_in, next_appointment_step, next_appointment_date, next_appointment_reminder_sent,
    NULL, details,
    check_in, check_out, is_controlled, is_confirmed,
    a.resource_id,
    JSON_OBJECT(
        'is_hidden', a.is_hidden,
        'needs_control', a.needs_control,
        'moved_for_organisation_reasons', a.moved_for_organisation_reasons,
        'google_event_id', a.google_event_id,
        'has_service_detail', a.has_service_detail,
        'is_exceptional', a.is_exceptional,
        'serial_number', a.serial_number,
        'is_selected', a.is_selected,
        'clarify_discount_with_id', a.clarify_discount_with_id,
        'discount_needs_further_clarification', a.discount_needs_further_clarification,
        'prefered_provider_id', a.prefered_provider_id,
        'is_paid', a.is_paid,
        'is_payment_canceled', a.is_payment_canceled,
        'is_discount_checked', a.is_discount_checked,
        'has_discount', a.has_discount,
        'has_payment', a.has_payment,
        'complain', a.complain,
        'is_complain_done', a.is_complain_done,
        'complaint_note', a.complaint_note,
        'send_invoice', a.send_invoice,
        'reminder_sent_at', a.reminder_sent_at,
        'cannot_be_done', a.cannot_be_done,
        'recurrence_type', a.recurrence_type,
        'recurrence_end_date', a.recurrence_end_date,
        'customer_not_appeared', a.customer_not_appeared,
        'same_day_cancellation', a.same_day_cancellation,
        'customer_sick', a.customer_sick,
        'online_entry', a.online_entry,
        'extra_provided_by_id', a.extra_provided_by_id
    )
FROM ladex_iii_migrate.appointments AS a
JOIN ladex_iii_migrate.providers AS p ON p.id = a.provider_id
LEFT JOIN ladex_iii_migrate.providers AS pb ON pb.id = a.provided_by_id;

# Migrate Roomblocks
INSERT INTO ladex_iv.appointments (
    created_at, updated_at, deleted_at,
    approved_at, canceled_at, done_at, reminder_sent_at,
    branch_id, room_id, customer_id, user_id, category_id,
    type, status,
    start, end,
    done_by_id,
    next_appointment_in, next_appointment_step, next_appointment_date, next_appointment_reminder_sent_at,
    cancel_reason,
    description,
    check_in_at, check_out_at, controlled_at, confirmed_at,
    treatment_type_id,
    meta
)
SELECT
    r.created_at, r.updated_at, null,
    null, null, null, null,
    ro.branch_id, r.room_id, null, r.user_id, null,
    'room-block', 'approved',
    r.start_at, r.end_at,
    null,
    null, null, null, null,
    null,
    r.notes,
    null, null, null, null,
    null,
    JSON_OBJECT(
        'recurrence_end_date', r.recurrence_end_date,
        'recurrence_type', r.recurrence_type,
        'todo_id', r.todo_id,
        'parent_id', r.parent_id
    )
FROM ladex_iii_migrate.room_blocks AS r
JOIN ladex_iv.rooms AS ro ON r.room_id = ro.id;

# Update appointment type
UPDATE ladex_iv.appointments SET type = 'treatment' WHERE type = '4';
UPDATE ladex_iv.appointments SET type = 'consultation' WHERE type = '3';
UPDATE ladex_iv.appointments SET type = 'treatment-consultation' WHERE type = '5';
UPDATE ladex_iv.appointments SET type = 'debriefing' WHERE type = '10';
UPDATE ladex_iv.appointments SET type = 'follow-up' WHERE type = '6';
UPDATE ladex_iv.appointments SET type = 'room-block' WHERE type = '1';
UPDATE ladex_iv.appointments SET type = 'reservation' WHERE type = '2';

# Update appointment status

UPDATE ladex_iv.appointments SET status = 'approved' WHERE approved_at IS NOT NULL;
UPDATE ladex_iv.appointments SET status = 'done' WHERE done_at IS NOT NULL;
UPDATE ladex_iv.appointments SET status = 'canceled' WHERE canceled_at IS NOT NULL;
UPDATE ladex_iv.appointments SET status = 'approved' WHERE status = 'pending' AND start < now();

# Update appointment cancel_reason
UPDATE appointments SET canceled_at = IFNULL(canceled_at, now()), cancel_reason = 'same-day-cancellation' WHERE JSON_VALUE(meta, '$.same_day_cancellation') = 1;
UPDATE appointments SET canceled_at = IFNULL(canceled_at, now()), cancel_reason = 'customer-not-appeared' WHERE JSON_VALUE(meta, '$.customer_not_appeared') = 1;
UPDATE appointments SET canceled_at = IFNULL(canceled_at, now()), cancel_reason = 'customer-sick' WHERE JSON_VALUE(meta, '$.customer_sick') = 1;

# Migrate Consultation Details
INSERT INTO ladex_iv.appointment_consultations (
    created_at, updated_at,
    appointment_id, customer_id,
    status,
    informed_about_risks, has_special_risks, special_risks,
    takes_medicine, medicine,
    individual_responsibility_signed,
    informed_about_consultation_fee
)
SELECT
    a.created_at, a.updated_at,
    a.id, a.customer_id,
    IFNULL(a.consultation_status, 'success'),
    a.customer_informed_risks, c.customer_has_special_risks, c.customer_special_risks,
    c.customer_takes_medication, c.customer_medication,
    a.individual_responsibility_signed,
    a.customer_informed_consultation_fee
FROM ladex_iii_migrate.appointments AS a
JOIN ladex_iii_migrate.customers AS c ON a.customer_id = c.id
JOIN ladex_iv.appointments AS la ON a.id = la.id
WHERE availability_type_id = 3
OR availability_type_id = 5;

UPDATE ladex_iv.appointment_consultations SET status = 'success' WHERE status = 'Erfolg mit Termin';
UPDATE ladex_iv.appointment_consultations SET status = 'will_call' WHERE status = 'will_call_himself';
UPDATE ladex_iv.appointment_consultations SET status = 'will_call' WHERE status = 'Erfolg o. Termin (meldet sich)';
UPDATE ladex_iv.appointment_consultations SET status = 'considering' WHERE status = 'will_consider';
UPDATE ladex_iv.appointment_consultations SET status = 'considering' WHERE status = 'Überlegt sich';
UPDATE ladex_iv.appointment_consultations SET status = 'needs_recall' WHERE status = 'needs_recall_from_us';
UPDATE ladex_iv.appointment_consultations SET status = 'needs_recall' WHERE status = 'Klärung und Rückruf von uns';
UPDATE ladex_iv.appointment_consultations SET status = 'price_to_high' WHERE status = 'Preis zu hoch';
UPDATE ladex_iv.appointment_consultations SET status = 'failure' WHERE status = 'no_success';
UPDATE ladex_iv.appointment_consultations SET status = 'failure' WHERE status = 'Kein Erfolg';
UPDATE ladex_iv.appointment_consultations SET status = 'treatment_impossible' WHERE status = 'treatment_not_possible';
UPDATE ladex_iv.appointment_consultations SET status = 'treatment_impossible' WHERE status = 'Behandlung n. möglich';

# Migrate Appointment Items
DELETE FROM ladex_iii_migrate.appointment_service WHERE appointment_id NOT IN (SELECT id FROM ladex_iv.appointments);
DELETE FROM ladex_iii_migrate.appointment_service WHERE service_id NOT IN (SELECT id FROM ladex_iv.services);

INSERT INTO ladex_iv.appointment_items (
    created_at, updated_at,
    appointment_id, purchasable_type, purchasable_id,
    type, description, note,
    unit_price, quantity, used, purchased, discount_total, sub_total
)
SELECT DISTINCT
    now(), now(),
    appointment_id, "App\\Models\\Service", service_id,
    'service', services.name, null,
    services.price, 1, 0, 0, 0, services.price
FROM ladex_iii_migrate.appointment_service
JOIN ladex_iv.services ON services.id = appointment_service.service_id;

# Migrate Appointment Services

INSERT INTO ladex_iv.appointment_service_details (created_at, updated_at, appointment_id, service_id, is_completed,
                                                  use_credit, spot_size, energy, li_count)
SELECT DISTINCT
    now(),
    now(),
    appointment_id,
    service_id,
    (CASE appointments.is_done WHEN NULL THEN 0 ELSE 1 END),
    0,
    spot_size,
    energy,
    li_count
FROM ladex_iii_migrate.appointment_service
JOIN ladex_iii_migrate.appointments ON appointment_service.appointment_id = appointments.id;

# Migrate Appointment Details

CREATE TEMPORARY TABLE IF NOT EXISTS ladex_iii_migrate.hair_type_migrate (
    id int,
    name varchar(255)
);

INSERT INTO ladex_iii_migrate.hair_type_migrate (id, name)
VALUES (null, null), (1, 'thick'), (2, 'normal'), (3, 'skinny');

CREATE TEMPORARY TABLE IF NOT EXISTS ladex_iii_migrate.pigment_type_migrate (
    id int,
    name varchar(255)
);

INSERT INTO ladex_iii_migrate.pigment_type_migrate (id, name)
VALUES (null, null), (1, 'none'), (2, 'pale'), (3, 'medium'), (4, 'strong');

DELETE FROM ladex_iii_migrate.appointment_details WHERE appointment_id NOT IN (SELECT id FROM ladex_iv.appointments);

INSERT INTO ladex_iv.appointment_details (
    id,
    created_at, updated_at,
    appointment_id,
    hair_type, pigment_type, skin_type, satisfaction,
    energy, li_count, spot_size, wave_length, milliseconds,
    meta
)
SELECT
    ad.id,
    created_at, updated_at,
    appointment_id,
    ht.name, pt.name, skin_type_id, satisfaction_type_id,
    energy, li_count, spot_size, wave_length, milliseconds,
    JSON_OBJECT(
        'order_id', order_id,
        'order_id_two', order_id_two,
        'order_id_three', order_id_three,
        'order_id_four', order_id_four,
        'comment', comment,
        'no_payment_reason', no_payment_reason
    )
FROM ladex_iii_migrate.appointment_details AS ad
LEFT JOIN ladex_iii_migrate.hair_type_migrate AS ht ON ad.hair_type_id = ht.id
LEFT JOIN ladex_iii_migrate.pigment_type_migrate AS pt ON ad.pigment_type_id = pt.id
GROUP BY appointment_id;

# Migrate Appointment notes

DELETE FROM ladex_iii_migrate.appointment_notes WHERE customer_id IS NOT NULL AND customer_id NOT IN (SELECT id FROM ladex_iv.customers);

INSERT INTO ladex_iv.notes (
    created_at,
    updated_at,
    notable_type,
    notable_id,
    user_id,
    customer_id,
    content,
    is_important
)
SELECT
    an.created_at,
    an.updated_at,
    "App\\Models\\Appointment",
    an.appointment_id,
    null,
    an.customer_id,
    an.text,
    IFNULL(an.is_important, 0)
FROM ladex_iii_migrate.appointment_notes AS an;


##################################################
# PACKAGES                                       #
##################################################

INSERT INTO ladex_iv.service_packages (
    id,
    created_at,
    updated_at,
    deleted_at,
    category_id,
    name,
    short_code,
    description,
    gender,
    customer_id,
    discount_percentage,
    discount,
    price,
    meta
)
SELECT
    sp.id,
    sp.created_at,
    sp.updated_at,
    sp.deleted_at,
    s.category_id,
    sp.name,
    sp.short_code,
    sp.description,
    sp.gender,
    sp.customer_id,
    NULL,
    NULL,
    sp.price,
    JSON_OBJECT(
        'duration', sp.duration,
        'duration2', sp.duration2,
        'duration3', sp.duration3,
        'stripe_payment_id', sp.stripe_payment_id,
        'discount', sp.discount,
        'default_price', sp.default_price,
        'disabled', sp.disabled
    )
FROM ladex_iii_migrate.service_packages AS sp
JOIN ladex_iii_migrate.service_service_package AS ssp ON ssp.service_package_id = sp.id
JOIN ladex_iv.services AS s ON ssp.service_id = s.id
-- WHERE sp.customer_id IS NOT NULL
GROUP BY sp.id;

INSERT INTO ladex_iv.service_service_package (created_at, updated_at, service_id, service_package_id)
SELECT
    sp.created_at,
    sp.updated_at,
    ssp.service_id,
    sp.id
FROM ladex_iv.service_packages AS sp
JOIN ladex_iii_migrate.service_service_package AS ssp ON ssp.service_package_id = sp.id;
-- WHERE sp.customer_id IS NOT NULL;

# Migrate Appointment Service Packages
DELETE FROM ladex_iii_migrate.appointment_service_package WHERE appointment_id NOT IN (SELECT id FROM ladex_iv.appointments);
DELETE FROM ladex_iii_migrate.appointment_service_package WHERE service_package_id NOT IN (SELECT id FROM ladex_iv.service_packages);

INSERT INTO ladex_iv.appointment_service_package (created_at, updated_at, appointment_id, service_package_id)
SELECT
    now(),
    now(),
    asp.appointment_id,
    asp.service_package_id
FROM ladex_iii_migrate.appointment_service_package AS asp;


# Migrate Package Discount Balance
CREATE TEMPORARY TABLE IF NOT EXISTS ladex_iii_migrate.spm (
    service_package_id int,
    name varchar(255),
    price decimal(8, 2),
    normal_price decimal(8, 2),
    discount decimal(8, 2)
);

INSERT INTO ladex_iii_migrate.spm (service_package_id, name, price, normal_price, discount)
SELECT
    sp.id,
    sp.name,
    sp.price,
    SUM(s.price),
    SUM(s.price) - sp.price
FROM ladex_iii_migrate.service_packages AS sp
JOIN ladex_iii_migrate.service_service_package AS ssp ON ssp.service_package_id = sp.id
JOIN ladex_iv.services AS s ON ssp.service_id = s.id
GROUP BY sp.id;

INSERT INTO ladex_iv.discounts (created_at, updated_at, discountable_type, discountable_id, source_type, source_id, type, description, percentage, amount)
SELECT DISTINCT
    now(),
    now(),
    "App\\Models\\Appointment",
    ap.appointment_id,
    "App\\Models\\ServicePackage",
    ap.service_package_id,
    'package',
    spm.name,
    null,
    spm.discount
FROM ladex_iii_migrate.appointment_service_package AS ap
JOIN ladex_iii_migrate.spm AS spm ON ap.service_package_id = spm.service_package_id;

##################################################
# PAYMENTS                                       #
##################################################
DELETE FROM ladex_iii_migrate.payments WHERE customer_id NOT IN (SELECT id FROM ladex_iv.customers);
DELETE
FROM ladex_iii_migrate.payments
WHERE price > 999999.99;

# Migrate EC Card payments
INSERT INTO ladex_iv.payments (
    id,
    created_at, updated_at, deleted_at,
    customer_id,
    payable_type, payable_id,
    reference_type, reference_id,
    type, amount, note,
    pays_at_next_appointment,
    meta
)
SELECT
    p.id,
    p.created_at, p.updated_at, p.deleted_at,
    p.customer_id,
    "App\\Models\\Appointment", p.appointment_id,
    null, null,
    'card', p.price, p.comment,
    p.pays_at_next_appointment,
    JSON_OBJECT(
        'is_paid', p.is_paid,
        'is_partial', p.is_partial,
        'selected_services', p.selected_services,
        'pays_at_next_appointment', p.pays_at_next_appointment,
        'stripe_payment_id', p.stripe_payment_id
    )
FROM ladex_iii_migrate.payments AS p
WHERE p.payment_type_id = 1;

# Migrate Cash payments
INSERT INTO ladex_iv.payments (
    id,
    created_at, updated_at, deleted_at,
    customer_id,
    payable_type, payable_id,
    reference_type, reference_id,
    type, amount, note,
    pays_at_next_appointment,
    meta
)
SELECT
    p.id,
    p.created_at, p.updated_at, p.deleted_at,
    p.customer_id,
    "App\\Models\\Appointment", p.appointment_id,
    null, null,
    'cash', p.price, p.comment,
    p.pays_at_next_appointment,
    JSON_OBJECT(
        'is_paid', p.is_paid,
        'is_partial', p.is_partial,
        'selected_services', p.selected_services,
        'pays_at_next_appointment', p.pays_at_next_appointment,
        'stripe_payment_id', p.stripe_payment_id
    )
FROM ladex_iii_migrate.payments AS p
WHERE p.payment_type_id = 2;

# Migrate Debit payments
INSERT INTO ladex_iv.payments (
    id,
    created_at, updated_at, deleted_at,
    customer_id,
    payable_type, payable_id,
    reference_type, reference_id,
    type, amount, note,
    pays_at_next_appointment,
    meta
)
SELECT
    p.id,
    p.created_at, p.updated_at, p.deleted_at,
    p.customer_id,
    "App\\Models\\Appointment", p.appointment_id,
    null, null,
    'debit', p.price, p.comment,
    p.pays_at_next_appointment,
    JSON_OBJECT(
        'is_paid', p.is_paid,
        'is_partial', p.is_partial,
        'selected_services', p.selected_services,
        'pays_at_next_appointment', p.pays_at_next_appointment,
        'stripe_payment_id', p.stripe_payment_id
    )
FROM ladex_iii_migrate.payments AS p
WHERE p.payment_type_id = 3;

# Migrate Invoice payments
INSERT INTO ladex_iv.payments (
    id,
    created_at, updated_at, deleted_at,
    customer_id,
    payable_type, payable_id,
    reference_type, reference_id,
    type, amount, note,
    pays_at_next_appointment,
    meta
)
SELECT
    p.id,
    p.created_at, p.updated_at, p.deleted_at,
    p.customer_id,
    "App\\Models\\Appointment", p.appointment_id,
    "App\\Models\\Invoice", p.invoice_id,
    'invoice', p.price, p.comment,
    p.pays_at_next_appointment,
    JSON_OBJECT(
        'is_paid', p.is_paid,
        'is_partial', p.is_partial,
        'selected_services', p.selected_services,
        'pays_at_next_appointment', p.pays_at_next_appointment,
        'stripe_payment_id', p.stripe_payment_id
    )
FROM ladex_iii_migrate.payments AS p
WHERE p.payment_type_id = 5;

# Migrate Contract payments
INSERT INTO ladex_iv.payments (
    id,
    created_at, updated_at, deleted_at,
    customer_id,
    payable_type, payable_id,
    reference_type, reference_id,
    type, amount, note,
    pays_at_next_appointment,
    meta
)
SELECT
    p.id,
    p.created_at, p.updated_at, p.deleted_at,
    p.customer_id,
    "App\\Models\\Appointment", p.appointment_id,
    "App\\Models\\Contract", p.contract_id,
    'contract', p.price, p.comment,
    p.pays_at_next_appointment,
    JSON_OBJECT(
        'is_paid', p.is_paid,
        'is_partial', p.is_partial,
        'selected_services', p.selected_services,
        'pays_at_next_appointment', p.pays_at_next_appointment,
        'stripe_payment_id', p.stripe_payment_id
    )
FROM ladex_iii_migrate.payments AS p
WHERE p.payment_type_id = 6;

# Migrate Goodwill payments
INSERT INTO ladex_iv.payments (
    id,
    created_at, updated_at, deleted_at,
    customer_id,
    payable_type, payable_id,
    reference_type, reference_id,
    type, amount, note,
    pays_at_next_appointment,
    meta
)
SELECT
    p.id,
    p.created_at, p.updated_at, p.deleted_at,
    p.customer_id,
    "App\\Models\\Appointment", p.appointment_id,
    null, null,
    'goodwill', p.price, p.comment,
    p.pays_at_next_appointment,
    JSON_OBJECT(
        'is_paid', p.is_paid,
        'is_partial', p.is_partial,
        'selected_services', p.selected_services,
        'pays_at_next_appointment', p.pays_at_next_appointment,
        'stripe_payment_id', p.stripe_payment_id
    )
FROM ladex_iii_migrate.payments AS p
WHERE p.payment_type_id = 7;

# Migrate Transaction payments
INSERT INTO ladex_iv.payments (
    id,
    created_at, updated_at, deleted_at,
    customer_id,
    payable_type, payable_id,
    reference_type, reference_id,
    type, amount, note,
    pays_at_next_appointment,
    meta
)
SELECT
    p.id,
    p.created_at, p.updated_at, p.deleted_at,
    p.customer_id,
    "App\\Models\\Appointment", p.appointment_id,
    null, null,
    'transaction', p.price, p.comment,
    p.pays_at_next_appointment,
    JSON_OBJECT(
        'is_paid', p.is_paid,
        'is_partial', p.is_partial,
        'selected_services', p.selected_services,
        'pays_at_next_appointment', p.pays_at_next_appointment,
        'stripe_payment_id', p.stripe_payment_id
    )
FROM ladex_iii_migrate.payments AS p
WHERE p.payment_type_id = 8;

# Migrate Credit payments
INSERT INTO ladex_iv.payments (
    id,
    created_at, updated_at, deleted_at,
    customer_id,
    payable_type, payable_id,
    reference_type, reference_id,
    type, amount, note,
    pays_at_next_appointment,
    meta
)
SELECT
    p.id,
    p.created_at, p.updated_at, p.deleted_at,
    p.customer_id,
    "App\\Models\\Appointment", p.appointment_id,
    "App\\Models\\CustomerCredit",
    p.credit_id,
    'credit', p.price, p.comment,
    p.pays_at_next_appointment,
    JSON_OBJECT(
        'is_paid', p.is_paid,
        'is_partial', p.is_partial,
        'selected_services', p.selected_services,
        'pays_at_next_appointment', p.pays_at_next_appointment,
        'stripe_payment_id', p.stripe_payment_id
    )
FROM ladex_iii_migrate.payments AS p
WHERE p.payment_type_id = 10;

# Migrate Split payments
INSERT INTO ladex_iv.payments (
    id,
    created_at, updated_at, deleted_at,
    customer_id,
    payable_type, payable_id,
    reference_type, reference_id,
    type, amount, note,
    pays_at_next_appointment,
    meta
)
SELECT
    p.id,
    p.created_at, p.updated_at, p.deleted_at,
    p.customer_id,
    "App\\Models\\Appointment", p.appointment_id,
    null, null,
    'split', p.price, p.comment,
    p.pays_at_next_appointment,
    JSON_OBJECT(
        'is_paid', p.is_paid,
        'is_partial', p.is_partial,
        'selected_services', p.selected_services,
        'pays_at_next_appointment', p.pays_at_next_appointment,
        'stripe_payment_id', p.stripe_payment_id
    )
FROM ladex_iii_migrate.payments AS p
WHERE p.payment_type_id = 11;

# Migrate Proforma payments
INSERT INTO ladex_iv.payments (
    id,
    created_at, updated_at, deleted_at,
    customer_id,
    payable_type, payable_id,
    reference_type, reference_id,
    type, amount, note,
    pays_at_next_appointment,
    meta
)
SELECT
    p.id,
    p.created_at, p.updated_at, p.deleted_at,
    p.customer_id,
    "App\\Models\\Appointment", p.appointment_id,
    null, null,
    'proforma', p.price, p.comment,
    p.pays_at_next_appointment,
    JSON_OBJECT(
        'is_paid', p.is_paid,
        'is_partial', p.is_partial,
        'selected_services', p.selected_services,
        'pays_at_next_appointment', p.pays_at_next_appointment,
        'stripe_payment_id', p.stripe_payment_id
    )
FROM ladex_iii_migrate.payments AS p
WHERE p.payment_type_id = 12;

# Migrate price change payments
INSERT INTO ladex_iv.payments (
    id,
    created_at, updated_at, deleted_at,
    customer_id,
    payable_type, payable_id,
    reference_type, reference_id,
    type, amount, note,
    pays_at_next_appointment,
    meta
)
SELECT
    p.id,
    p.created_at, p.updated_at, p.deleted_at,
    p.customer_id,
    "App\\Models\\Appointment", p.appointment_id,
    null, null,
    'price-change', p.price, p.comment,
    p.pays_at_next_appointment,
    JSON_OBJECT(
        'is_paid', p.is_paid,
        'is_partial', p.is_partial,
        'selected_services', p.selected_services,
        'pays_at_next_appointment', p.pays_at_next_appointment,
        'stripe_payment_id', p.stripe_payment_id
    )
FROM ladex_iii_migrate.payments AS p
WHERE p.payment_type_id = 13;

# Migrate PayPal payments
INSERT INTO ladex_iv.payments (
    id,
    created_at, updated_at, deleted_at,
    customer_id,
    payable_type, payable_id,
    reference_type, reference_id,
    type, amount, note,
    pays_at_next_appointment,
    meta
)
SELECT
    p.id,
    p.created_at, p.updated_at, p.deleted_at,
    p.customer_id,
    "App\\Models\\Appointment", p.appointment_id,
    null, null,
    'pay-pal', p.price, p.comment,
    p.pays_at_next_appointment,
    JSON_OBJECT(
        'is_paid', p.is_paid,
        'is_partial', p.is_partial,
        'selected_services', p.selected_services,
        'pays_at_next_appointment', p.pays_at_next_appointment,
        'stripe_payment_id', p.stripe_payment_id
    )
FROM ladex_iii_migrate.payments AS p
WHERE p.payment_type_id = 14;

##################################################
# CONTRACTS                                      #
##################################################

INSERT INTO ladex_iv.contracts (id, created_at, updated_at, deleted_at, customer_id, appointment_id, payment_id, user_id, date, type, description, sub_total, price, treatment_count, meta)
SELECT
    contracts.id,
    contracts.created_at,
    contracts.updated_at,
    contracts.deleted_at,
    customer_id,
    appointment_id,
    payment_id,
    user_id,
    date,
    type,
    description,
    price,
    price,
    treatment_count,
    JSON_OBJECT('treatments_done', treatments_done, 'old_treatments_done', old_treatments_done, 'is_active', is_active, 'payment_type_id', payment_type_id, 'invoice_id', invoice_id)
FROM ladex_iii_migrate.contracts
JOIN ladex_iii_migrate.providers ON provider_id = providers.id;

# Migrate contract services
DELETE FROM ladex_iii_migrate.contract_service WHERE contract_id NOT IN (SELECT id FROM ladex_iv.contracts);

INSERT INTO ladex_iv.contract_services (created_at, updated_at, contract_id, service_id, price, meta)
SELECT
    contract_service.created_at,
    contract_service.updated_at,
    contract_id,
    service_id,
    services.price,
    JSON_OBJECT('treatment_count', treatment_count, 'treatments_done', treatments_done, 'old_treatments_done', old_treatments_done, 'converted_at', converted_at)
FROM ladex_iii_migrate.contract_service
JOIN ladex_iv.services ON service_id = services.id;

# Migrate Unused Service Credits
CREATE TEMPORARY TABLE IF NOT EXISTS ladex_iii_migrate.numbers (
    number int
);

TRUNCATE TABLE ladex_iii_migrate.numbers;
INSERT INTO ladex_iii_migrate.numbers (number)
VALUES (1), (2), (3), (4), (5), (6), (7), (8), (9), (10), (11), (12);


INSERT INTO ladex_iv.service_credits (created_at, updated_at, customer_id, service_id, contract_id, contract_service_id, source_type, source_id,
                                      usage_type, usage_id, price, used_at)
SELECT
    cs.created_at,
    cs.updated_at,
    c.customer_id,
    cs.service_id,
    c.id,
    cs.id,
    "App\\Models\\Appointment",
    c.appointment_id,
    NULL,
    NULL,
    cs.price,
    NULL
FROM ladex_iv.contract_services AS cs
JOIN ladex_iv.contracts AS c ON c.id = cs.contract_id
JOIN ladex_iii_migrate.contract_service AS ocs ON ocs.contract_id = c.id
JOIN ladex_iii_migrate.numbers AS n ON n.number <= (ocs.treatment_count - ocs.treatments_done)
WHERE c.appointment_id IS NOT NULL
AND ocs.service_id = cs.service_id;

INSERT INTO ladex_iv.service_credits (created_at, updated_at, customer_id, service_id, contract_id, contract_service_id, source_type, source_id,
                                      usage_type, usage_id, price, used_at)
SELECT
    cs.created_at,
    cs.updated_at,
    c.customer_id,
    cs.service_id,
    c.id,
    cs.id,
    NULL,
    NULL,
    NULL,
    NULL,
    cs.price,
    NULL
FROM ladex_iv.contract_services AS cs
JOIN ladex_iv.contracts AS c ON c.id = cs.contract_id
JOIN ladex_iii_migrate.contract_service AS ocs ON ocs.contract_id = c.id
JOIN ladex_iii_migrate.numbers AS n ON n.number <= (ocs.treatment_count - ocs.treatments_done)
WHERE c.appointment_id IS NULL
AND ocs.service_id = cs.service_id;

# Migrate Used Service Credits
INSERT INTO ladex_iv.service_credits (created_at, updated_at, customer_id, service_id, contract_id, contract_service_id, source_type, source_id,
                                      usage_type, usage_id, price, used_at)
SELECT
    cs.created_at,
    cs.updated_at,
    c.customer_id,
    cs.service_id,
    c.id,
    cs.id,
    "App\\Models\\Appointment",
    c.appointment_id,
    "App\\Models\\Appointment",
    p.appointment_id,
    cs.price,
    p.created_at
FROM ladex_iv.contract_services AS cs
JOIN ladex_iv.contracts AS c ON c.id = cs.contract_id
JOIN ladex_iii_migrate.contract_service AS ocs ON ocs.contract_id = c.id
JOIN ladex_iii_migrate.payments AS p ON p.contract_id = c.id
WHERE p.payment_type_id = 6
AND p.deleted_at IS NULL
AND ocs.service_id = cs.service_id
AND ocs.service_id IN (SELECT service_id FROM ladex_iii_migrate.payment_service WHERE payment_id = p.id)
AND c.appointment_id IS NOT NULL;

INSERT INTO ladex_iv.service_credits (created_at, updated_at, customer_id, service_id, contract_id, contract_service_id, source_type, source_id,
                                      usage_type, usage_id, price, used_at)
SELECT
    cs.created_at,
    cs.updated_at,
    c.customer_id,
    cs.service_id,
    c.id,
    cs.id,
    NULL,
    NULL,
    "App\\Models\\Appointment",
    p.appointment_id,
    cs.price,
    p.created_at
FROM ladex_iv.contract_services AS cs
JOIN ladex_iv.contracts AS c ON c.id = cs.contract_id
JOIN ladex_iii_migrate.contract_service AS ocs ON ocs.contract_id = c.id
JOIN ladex_iii_migrate.payments AS p ON p.contract_id = c.id
WHERE c.appointment_id IS NULL
AND ocs.service_id = cs.service_id
AND cs.service_id IN (SELECT service_id FROM ladex_iii_migrate.payment_service WHERE payment_id = p.id)
AND p.payment_type_id = 6
AND p.deleted_at IS NULL;

# Update Use Credit In Appointment details
UPDATE
    ladex_iv.appointment_service_details AS asd,
    ladex_iv.service_credits AS sc
SET
    asd.use_credit = 1
WHERE asd.appointment_id = sc.usage_id
AND asd.service_id = sc.service_id;


# Update Quantity In Appointment Items
UPDATE
    ladex_iv.appointment_items AS ai,
    ladex_iv.appointment_service_details AS asd
SET
    ai.purchased = 1,
    ai.used = 1
WHERE asd.appointment_id = ai.appointment_id
AND asd.service_id = ai.purchasable_id
AND ai.purchasable_type = "App\\Models\\Service"
AND asd.use_credit = 1;

# Add Contracts to Appointment Items
INSERT INTO ladex_iv.appointment_items (created_at, updated_at, appointment_id, purchasable_type, purchasable_id, type, description, unit_price,
                                        quantity, used, purchased, discount_total, sub_total)
SELECT c.created_at,
       c.updated_at,
       c.appointment_id,
       "App\\Models\\Contract",
       c.id,
       "contract",
       CONCAT("Vertrag ", UPPER(c.type), " ", DATE_FORMAT(c.date, "%d.%m.%Y")),
       c.price,
       1,
       0,
       0,
       0,
       c.price
FROM ladex_iv.contracts AS c
WHERE c.appointment_id IS NOT NULL
  AND c.appointment_id IN (SELECT id FROM ladex_iv.appointments);

##################################################
# CREDITS                                        #
##################################################

# Migrate Contract Credits
INSERT INTO ladex_iv.customer_credits (id, created_at, updated_at, deleted_at, customer_id, source_type, source_id, amount, description, spent_at,
    meta)
SELECT id,
       created_at,
       updated_at,
       null,
       customer_id,
       'App\\Models\\Contract',
       contract_id,
       amount,
       description,
       spent_at,
       JSON_OBJECT('used_amount', used_amount, 'payment_type_id', payment_type_id, 'payment_id', payment_id)
FROM ladex_iii_migrate.credits
WHERE contract_id IS NOT NULL;

# Migrate Appointment Credits
INSERT INTO ladex_iv.customer_credits (id, created_at, updated_at, deleted_at, customer_id, source_type, source_id, amount, description, spent_at,
     meta)
SELECT id,
       created_at,
       updated_at,
       null,
       customer_id,
       'App\\Models\\Appointment',
       appointment_id,
       amount,
       description,
       spent_at,
       JSON_OBJECT('used_amount', used_amount, 'payment_type_id', payment_type_id, 'payment_id', payment_id)
FROM ladex_iii_migrate.credits
WHERE appointment_id IS NOT NULL
  AND contract_id IS NULL;

# Migrate remaining Credits
INSERT INTO ladex_iv.customer_credits (id, created_at, updated_at, deleted_at, customer_id, source_type, source_id, amount, description, spent_at,
                                       meta)
SELECT id,
       created_at,
       updated_at,
       null,
       customer_id,
       NULL,
       NULL,
       amount,
       description,
       spent_at,
       JSON_OBJECT('used_amount', used_amount, 'payment_type_id', payment_type_id, 'payment_id', payment_id)
FROM ladex_iii_migrate.credits
WHERE appointment_id IS NULL
  AND contract_id IS NULL;

UPDATE ladex_iv.customer_credits AS cc
SET cc.spent_at = now()
WHERE cc.spent_at IS NULL
  AND cc.amount <= (SELECT SUM(amount) FROM payments AS p WHERE p.reference_id = cc.id AND p.type = 'credit');

##################################################
# ORDER                                          #
##################################################

INSERT INTO ladex_iv.discounts (created_at, updated_at, discountable_type, discountable_id, type, description, percentage, amount, meta)
SELECT DISTINCT
    ad.created_at,
    ad.updated_at,
    "App\\Models\\Appointment",
    ad.appointment_id,
    'custom',
    IFNULL(ad.description, "Migriert von appointment_discounts"),
    ad.percentage,
    ad.discount,
    JSON_OBJECT("appointment_discounts_id", ad.id)
FROM ladex_iii_migrate.appointment_discounts AS ad;

INSERT INTO ladex_iv.discounts (created_at, updated_at, discountable_type, discountable_id, type, description, percentage, amount, meta)
SELECT DISTINCT
    now(),
    now(),
    "App\\Models\\Appointment",
    a.id,
    'custom',
    "Legacy Preisangleich",
    0,
    (ai.sub_total-IFNULL(d.amount, 0) - p.amount),
    JSON_OBJECT("legacy", 1)
FROM ladex_iii_migrate.appointments AS a
LEFT JOIN (
    SELECT appointment_id, sum(sub_total) AS 'sub_total'
    FROM ladex_iv.appointment_items
    GROUP BY appointment_id
) AS ai ON a.id = ai.appointment_id
LEFT JOIN (
    SELECT discountable_id, sum(amount) AS 'amount'
    FROM ladex_iv.discounts
    WHERE discountable_type = "App\\Models\\Appointment"
    GROUP BY discountable_id
) AS d ON d.discountable_id = a.id
LEFT JOIN (
    SELECT payable_id, sum(amount) AS 'amount'
    FROM ladex_iv.payments
    WHERE payable_type = "App\\Models\\Appointment"
    GROUP BY payable_id
) AS p ON p.payable_id = a.id
WHERE is_paid IS NOT NULL
AND ai.sub_total IS NOT NULL
AND p.amount IS NOT NULL
AND ai.sub_total-IFNULL(d.amount, 0) > p.amount;


TRUNCATE TABLE ladex_iv.appointment_orders;
INSERT INTO ladex_iv.appointment_orders (created_at, updated_at, appointment_id, status, base_total, discount_total, net_total, tax_total, gross_total, paid_total)
SELECT
    a.created_at,
    a.updated_at,
    a.id,
    'open',
    IFNULL(ai.sub_total, 0),
    IFNULL(d.amount, 0),
    IFNULL(ai.sub_total, 0)-IFNULL(d.amount, 0),
    0,
    IFNULL(ai.sub_total, 0)-IFNULL(d.amount, 0),
    IFNULL(p.amount, 0)
FROM ladex_iv.appointments AS a
LEFT JOIN (
    SELECT appointment_id, sum(sub_total) AS 'sub_total'
    FROM ladex_iv.appointment_items
    WHERE quantity > 0
    GROUP BY appointment_id
) AS ai ON a.id = ai.appointment_id
LEFT JOIN (
    SELECT discountable_id, sum(amount) AS 'amount'
    FROM ladex_iv.discounts
    WHERE discountable_type = "App\\Models\\Appointment"
    GROUP BY discountable_id
) AS d ON d.discountable_id = a.id
LEFT JOIN (
    SELECT payable_id, sum(amount) AS 'amount'
    FROM ladex_iv.payments
    WHERE payable_type = "App\\Models\\Appointment"
    GROUP BY payable_id
) AS p ON p.payable_id = a.id;

UPDATE ladex_iv.appointment_orders SET status = 'paid' WHERE gross_total <= paid_total;

##################################################
# Invoice                                        #
##################################################

INSERT INTO ladex_iv.invoices (
    id,
    created_at, updated_at, deleted_at,
    recipient_type, recipient_id,
    source_type, source_id,
    type,
    status,
    series, sequence, invoice_number,
    invoice_date, due_date,
    base_total, discount_total, net_total, tax_total, gross_total, paid_total,
    header, footer, note,
    meta
)
SELECT
    i.id,
    i.created_at, i.updated_at, i.deleted_at,
    "App\\Models\\Customer", i.customer_id,
    IF(i.appointment_id IS NOT NULL,"App\\Models\\Appointment",NULL), IFNULL(i.appointment_id, NULL),
    'invoice',
    IF(i.is_canceled, 'canceled', IF(i.is_debt_collection, 'debt_collection', IF(i.is_paid, 'paid', IF(i.is_warned, 'reminder', 'open')))),
    i.series, i.sequence, CONCAT("RE-", LPAD(i.sequence, 5, 0)),
    i.date, i.due_date,
    SUM(ii.price_per_unit * ii.quantity), SUM(ii.discount), SUM(ii.sub_total - ii.tax), SUM(ii.tax), SUM(ii.sub_total), i.paid_amount,
    i.header, i.footer, i.note,
    JSON_OBJECT(
        'account_id', i.account_id,
        'address', i.address,
        'is_paid', i.is_paid,
        'is_canceled', i.is_canceled,
        'is_warned', i.is_warned,
        'is_debt_collection', i.is_debt_collection,
        'is_offer', i.is_offer,
        'is_sent', i.is_sent,
        'is_printed', i.is_printed,
        'is_automatic', i.is_automatic
    )
FROM ladex_iii_migrate.invoices AS i
JOIN ladex_iii_migrate.invoice_items AS ii ON i.id = ii.invoice_id
WHERE customer_id IS NOT NULL
AND i.series = "RE"
GROUP BY i.id;

INSERT INTO ladex_iv.invoices (
    id,
    created_at, updated_at, deleted_at,
    recipient_type, recipient_id,
    source_type, source_id,
    type,
    status,
    series, sequence, invoice_number,
    invoice_date, due_date,
    base_total, discount_total, net_total, tax_total, gross_total, paid_total,
    header, footer, note,
    meta
)
SELECT
    i.id,
    i.created_at, i.updated_at, i.deleted_at,
    "App\\Models\\Customer", i.customer_id,
    IF(i.appointment_id IS NOT NULL,"App\\Models\\Appointment",NULL), IFNULL(i.appointment_id, NULL),
    'proforma',
    IF(i.is_canceled, 'canceled', IF(i.is_debt_collection, 'debt_collection', IF(i.is_paid, 'paid', IF(i.is_warned, 'reminder', 'open')))),
    i.series, i.sequence, CONCAT("PF-", LPAD(i.sequence, 5, 0)),
    i.date, i.due_date,
    SUM(ii.price_per_unit * ii.quantity), SUM(ii.discount), SUM(ii.sub_total - ii.tax), SUM(ii.tax), SUM(ii.sub_total), i.paid_amount,
    i.header, i.footer, i.note,
    JSON_OBJECT(
            'account_id', i.account_id,
            'address', i.address,
            'is_paid', i.is_paid,
            'is_canceled', i.is_canceled,
            'is_warned', i.is_warned,
            'is_debt_collection', i.is_debt_collection,
            'is_offer', i.is_offer,
            'is_sent', i.is_sent,
            'is_printed', i.is_printed,
            'is_automatic', i.is_automatic
    )
FROM ladex_iii_migrate.invoices AS i
         JOIN ladex_iii_migrate.invoice_items AS ii ON i.id = ii.invoice_id
WHERE customer_id IS NOT NULL
  AND i.series = "PF"
GROUP BY i.id;

DELETE FROM ladex_iii_migrate.invoice_items WHERE invoice_id NOT IN (SELECT id FROM ladex_iv.invoices);

INSERT INTO ladex_iv.invoice_items (
    id,
    created_at, updated_at, deleted_at,
    invoice_id,
    invoicable_type, invoicable_id,
    title, description,
    quantity, unit, unit_price,
    tax_percentage, tax,
    sub_total,
    meta
)
SELECT
    ii.id,
    ii.created_at, ii.updated_at, ii.deleted_at,
    ii.invoice_id,
    null, null,
    ii.title, ii.description,
    ii.quantity, ii.units, ii.price_per_unit,
    ii.tax_percentage, ii.tax,
    ii.sub_total,
    JSON_OBJECT(
            'discount_percentage', ii.discount_percentage,
            'discount', ii.discount,
            'order_id', ii.order_id
    )
FROM ladex_iii_migrate.invoice_items AS ii;

##################################################
# Vouchers                                       #
##################################################

INSERT INTO ladex_iv.vouchers (
    id,
    created_at, updated_at, deleted_at,
    customer_id, purchaser_id,
    voucher_nr, amount,
    description,
    meta
)
SELECT
    v.id,
    v.created_at, v.updated_at, null,
    v.customer_id, null,
    v.voucher_nr, v.amount,
    v.description,
    JSON_OBJECT(
        'status', v.status,
        'payment_type_id', v.payment_type_id,
        'is_paid', v.is_paid,
        'used_amount', v.used_amount
    )
FROM ladex_iii_migrate.vouchers AS v
WHERE v.voucher_nr IS NOT NULL;

INSERT INTO ladex_iv.customer_credits (
    created_at, updated_at, deleted_at,
    customer_id,
    source_type, source_id,
    amount, description, spent_at,
    meta
)
SELECT
    now(), now(), null,
    customer_id,
    'App\\Models\\Voucher',
    v.id,
    v.amount,
    v.description,
    null,
    JSON_OBJECT('used_amount', v.used_amount, 'payment_type_id', v.payment_type_id, 'is_paid', v.is_paid)
FROM ladex_iii_migrate.vouchers AS v
WHERE v.customer_id IS NOT NULL;

# Migrate Voucher payments
INSERT INTO ladex_iv.payments (
    id,
    created_at, updated_at, deleted_at,
    customer_id,
    payable_type, payable_id,
    reference_type, reference_id,
    type, amount, note,
    meta
)
SELECT
    p.id,
    p.created_at, p.updated_at, p.deleted_at,
    p.customer_id,
    "App\\Models\\Appointment", p.appointment_id,
    "App\\Models\\CustomerCredit", cc.id,
    'credit', p.price, p.comment,
    JSON_OBJECT(
        'is_paid', p.is_paid,
        'is_partial', p.is_partial,
        'selected_services', p.selected_services,
        'pays_at_next_appointment', p.pays_at_next_appointment,
        'stripe_payment_id', p.stripe_payment_id
    )
FROM ladex_iii_migrate.payments AS p
JOIN ladex_iv.customer_credits AS cc ON p.voucher_id = cc.source_id
WHERE p.payment_type_id = 9
AND cc.source_type = 'App\\Models\\Voucher';

##################################################
# Availabilities                                 #
##################################################

# Migrate Availabilities
INSERT INTO ladex_iv.availabilities (
	id,
	created_at, updated_at, deleted_at,
	planable_type, planable_id,
	availability_type_id,
	title,
	start_date, end_date,
	meta
)
SELECT
	tp.id,
	tp.created_at, tp.updated_at, u.deleted_at,
	"App\\Models\\User", u.id,
	3,
	IFNULL(u.firstname, u.name),
	tp.start_date, tp.end_date,
	JSON_OBJECT(
		'monday_hours', tp.monday_hours,
		'tuesday_hours', tp.tuesday_hours,
		'wednesday_hours', tp.wednesday_hours,
		'thursday_hours', tp.thursday_hours,
		'friday_hours', tp.friday_hours,
		'saturday_hours', tp.saturday_hours,
		'sunday_hours', tp.sunday_hours,
		'wage_type', tp.wage_type,
        'vacation_days', tp.vacation_days,
        'carry_vacation_days', tp.carry_vacation_days,
        'start_overtime_minutes', tp.start_overtime_minutes,
        'count_from', tp.count_from
    )
FROM ladex_iii_migrate.time_plans AS tp
JOIN ladex_iv.users AS u ON tp.user_id = u.id;

UPDATE ladex_iv.availabilities AS a
JOIN ladex_iv.availability_types t on t.id = a.availability_type_id
SET
    a.color = t.color,
    a.is_hidden = t.is_hidden,
    a.is_all_day = t.is_all_day,
    a.is_background = t.is_background,
    a.is_background_inverted = t.is_background_inverted
WHERE 1;

# Migrate Monday Shifts
INSERT INTO ladex_iv.availability_shifts (
	created_at, updated_at,
	availability_id, room_id,
	start, target_minutes,
	weekday,
	repeat_step, repeat_every,
	start_date
)
SELECT
	tp.created_at, tp.updated_at,
	tp.id, null,
	null, tp.monday_hours*60,
	1,
	'weeks', 1,
	tp.start_date
FROM ladex_iii_migrate.time_plans AS tp;

# Migrate Tuesday Shifts
INSERT INTO ladex_iv.availability_shifts (
	created_at, updated_at,
	availability_id, room_id,
	start, target_minutes,
	weekday,
	repeat_step, repeat_every,
	start_date
)
SELECT
	tp.created_at, tp.updated_at,
	tp.id, null,
	null, tp.tuesday_hours*60,
	2,
	'weeks', 1,
	tp.start_date
FROM ladex_iii_migrate.time_plans AS tp;

# Migrate Wednesday Shifts
INSERT INTO ladex_iv.availability_shifts (
	created_at, updated_at,
	availability_id, room_id,
	start, target_minutes,
	weekday,
	repeat_step, repeat_every,
	start_date
)
SELECT
	tp.created_at, tp.updated_at,
	tp.id, null,
	null, tp.wednesday_hours*60,
	3,
	'weeks', 1,
	tp.start_date
FROM ladex_iii_migrate.time_plans AS tp;

# Migrate Thursday Shifts
INSERT INTO ladex_iv.availability_shifts (
	created_at, updated_at,
	availability_id, room_id,
	start, target_minutes,
	weekday,
	repeat_step, repeat_every,
	start_date
)
SELECT
	tp.created_at, tp.updated_at,
	tp.id, null,
	null, tp.thursday_hours*60,
	4,
	'weeks', 1,
	tp.start_date
FROM ladex_iii_migrate.time_plans AS tp;

# Migrate Friday Shifts
INSERT INTO ladex_iv.availability_shifts (
	created_at, updated_at,
	availability_id, room_id,
	start, target_minutes,
	weekday,
	repeat_step, repeat_every,
	start_date
)
SELECT
	tp.created_at, tp.updated_at,
	tp.id, null,
	null, tp.friday_hours*60,
	5,
	'weeks', 1,
	tp.start_date
FROM ladex_iii_migrate.time_plans AS tp;

# Migrate Saturday Shifts
INSERT INTO ladex_iv.availability_shifts (
	created_at, updated_at,
	availability_id, room_id,
	start, target_minutes,
	weekday,
	repeat_step, repeat_every,
	start_date
)
SELECT
	tp.created_at, tp.updated_at,
	tp.id, null,
	null, tp.saturday_hours*60,
	6,
	'weeks', 1,
	tp.start_date
FROM ladex_iii_migrate.time_plans AS tp;

# Migrate Provider Exceptions
INSERT INTO ladex_iv.availability_exceptions (
    created_at, updated_at,
    availability_id, room_id, availability_type_id,
    date, start, target_minutes
)
SELECT DISTINCT
    wt.created_at, wt.updated_at,
    a.id, wt.room_id, 1,
    DATE_FORMAT(wt.start_time, "%Y-%m-%d"), DATE_FORMAT(wt.start_time, "%H:%i"), TIMESTAMPDIFF(MINUTE, wt.start_time, wt.end_time)
FROM ladex_iii_migrate.work_times AS wt
JOIN ladex_iii_migrate.providers AS p ON p.id = wt.provider_id
JOIN ladex_iv.availabilities AS a ON p.user_id = a.planable_id
WHERE wt.provider_id IS NOT NULL
AND a.planable_type = "App\\Models\\User"
AND a.start_date <= wt.start_time
AND wt.deleted_at IS NULL
AND (
    a.end_date IS NULL OR a.end_date >= wt.end_time
);

# Migrate User Exceptions
INSERT INTO ladex_iv.availability_exceptions (
    created_at, updated_at,
    availability_id, room_id, availability_type_id,
    date, start, target_minutes
)
SELECT DISTINCT
    wt.created_at, wt.updated_at,
    a.id, wt.room_id, 2,
    DATE_FORMAT(wt.start_time, "%Y-%m-%d"), DATE_FORMAT(wt.start_time, "%H:%i"), TIMESTAMPDIFF(MINUTE, wt.start_time, wt.end_time)
FROM ladex_iii_migrate.work_times AS wt
JOIN ladex_iv.availabilities AS a ON wt.user_id = a.planable_id
WHERE wt.provider_id IS NULL
AND wt.user_id IS NOT NULL
AND a.planable_type = "App\\Models\\User"
AND a.start_date <= wt.start_time
AND wt.deleted_at IS NULL
AND (
    a.end_date IS NULL OR a.end_date >= wt.end_time
);

##################################################
# Leaves                                         #
##################################################
DELETE FROM ladex_iii_migrate.leaves WHERE user_id NOT IN (SELECT id FROM ladex_iv.users);

INSERT INTO ladex_iv.leaves (
    id,
    created_at, updated_at, deleted_at,
    user_id,
    leave_type,
    `from`,
    till,
    total_leave_days,
    processed_by_id,
    approved_at,
    denied_at,
    user_note,
    admin_note,
    meta
)
SELECT
	l.id,
    l.created_at, l.updated_at, l.deleted_at,
    l.user_id,
    IFNULL(l.leave_type_id, 'vacation'),
    l.leave_from,
    l.leave_till,
    l.total_leave_days,
    l.processor_id,
    l.approved_at,
    l.denied_at,
    l.user_comment,
    l.admin_comment,
    JSON_OBJECT('confirmation_user_id', l.confirmation_user_id, 'has_sick_certification', l.has_sick_certification, 'leave_type_id', l.leave_type_id)
FROM ladex_iii_migrate.leaves AS l;

UPDATE ladex_iv.leaves SET leave_type = 'vacation' WHERE leave_type = '1';
UPDATE ladex_iv.leaves SET leave_type = 'sick-leave' WHERE leave_type = '4';
UPDATE ladex_iv.leaves SET leave_type = 'school' WHERE leave_type = '5' OR leave_type = '6' OR leave_type = '7';
UPDATE ladex_iv.leaves SET leave_type = 'unpaid-leave' WHERE leave_type = '3';
UPDATE ladex_iv.leaves SET leave_type = 'overtime-reduction' WHERE leave_type = '2';
UPDATE ladex_iv.leaves SET leave_type = 'holliday' WHERE leave_type = '8';

##################################################
# TimeReport                                     #
##################################################
INSERT INTO ladex_iv.time_reports (
    id,
	created_at, updated_at,
	user_id, time_report_overview_id,
	date,
	target_minutes,
	time_in, time_in_status, real_time_in,
	time_out, time_out_status, real_time_out,
	total_minutes, real_total_minutes,
    break_minutes, actual_minutes, manual_minutes,
	overtime_minutes, uncapped_overtime_minutes, is_overtime_capped,
	leave_type,
	edited_at, edited_by_id,
	controlled_at, controlled_by_id,
	note,
	meta
)
SELECT
    tr.id,
    tr.created_at, tr.updated_at,
    tr.user_id, tro.id,
    tr.date,
    tr.target_minutes,
    MIN(tre.time_in), IFNULL(tre.checkin_status, 'ok'), MIN(tre.real_time_in),
    MAX(tre.time_out), IFNULL(tre.checkout_status, 'ok'), MAX(tre.real_time_out),
    IFNULL(tr.total_minutes, 0), IFNULL(TIMESTAMPDIFF(MINUTE, MIN(tre.real_time_in), MAX(tre.real_time_out)), 0),
    IFNULL(tr.break_minutes, 0), IFNULL(tr.actual_minutes, 0), IFNULL(tr.manual_minutes, 0) + IFNULL(tr.school_minutes, 0),
    IFNULL(tr.overtime_minutes, 0), IFNULL(tr.uncapped_overtime_minutes, 0), tr.overtime_cap_enabled,
    tr.leave_type_id,
    tr.edited_at, tr.edited_by,
    tr.checked_at, tr.checked_by,
    tr.`comment`,
    JSON_OBJECT(
    	'is_holiday', tr.is_holiday,
    	'is_sick', tr.is_sick,
    	'is_vacation', tr.is_vacation,
    	'is_overtime_leave', tr.is_overtime_leave,
    	'is_unpaid_leave', tr.is_unpaid_leave,
    	'total_overtime_minutes', tr.total_overtime_minutes,
    	'total_vacation_days', tr.total_vacation_days,
    	'overtime_controlled_at', tr.overtime_controlled_at,
    	'overtime_controlled_by', tr.overtime_controlled_by,
    	'manual_minutes', tr.manual_minutes,
    	'school_minutes', tr.school_minutes,
    	'no_break', tre.no_break,
    	'autotime_out', tre.autotime_out,
    	'autotime_out_override', tre.autotime_out_override
    )
FROM ladex_iii_migrate.time_reports AS tr
JOIN ladex_iii_migrate.time_report_overviews AS tro ON (
	tr.user_id = tro.user_id
	AND MONTH(tr.date) = MONTH(tro.date)
	AND YEAR(tr.date) = YEAR(tro.date)
)
LEFT JOIN ladex_iii_migrate.time_records AS tre ON (tr.user_id = tre.user_id AND tr.date = DATE(tre.time_in))
GROUP BY tr.id;

UPDATE ladex_iv.time_reports SET leave_type = 'vacation' WHERE leave_type = '1';
UPDATE ladex_iv.time_reports SET leave_type = 'sick-leave' WHERE leave_type = '4';
UPDATE ladex_iv.time_reports SET leave_type = 'school' WHERE leave_type = '5' OR leave_type = '6' OR leave_type = '7';
UPDATE ladex_iv.time_reports SET leave_type = 'unpaid-leave' WHERE leave_type = '3';
UPDATE ladex_iv.time_reports SET leave_type = 'overtime-reduction' WHERE leave_type = '2';
UPDATE ladex_iv.time_reports SET leave_type = 'holliday' WHERE leave_type = '8';

INSERT INTO ladex_iv.time_report_overviews (
    id,
    created_at, updated_at,
    user_id, previous_id,
    date,
    target_minutes,
    total_minutes, real_total_minutes,
    actual_minutes,
    overtime_minutes, uncapped_overtime_minutes, carry_overtime_minutes, manual_overtime_minutes,
    leave_days, sick_days, vacation_days, carry_vacation_days, manual_vacation_days,
    edited_at, edited_by_id,
	controlled_at, controlled_by_id,
	note,
	meta
)
SELECT
    tro.id,
    tro.created_at, tro.updated_at,
    tro.user_id, null,
    tro.date,
    IFNULL(tro.target_minutes, 0),
    IFNULL(tro.total_minutes, 0), SUM(tr.real_total_minutes),
    IFNULL(tro.actual_minutes, 0),
    IFNULL(tro.overtime_minutes, 0), 0, IFNULL(tro.overtime_minutes_carry, 0), IFNULL(tro.overtime_minutes_manual, 0),
    0, IFNULL(tro.sick_days, 0), IFNULL(tro.vacation_days, 0), IFNULL(tro.vacation_days_carry, 0), IFNULL(tro.vacation_days_manual, 0),
    tro.edited_at, tro.edited_by,
    null, null,
    tro.comment,
    JSON_OBJECT(
    	'sum_target_minutes', SUM(tr.target_minutes),
    	'sum_total_minutes', SUM(tr.total_minutes),
    	'sum_actual_minutes', SUM(tr.actual_minutes),
    	'sum_overtime_minutes', SUM(tr.overtime_minutes)
    )
FROM ladex_iii_migrate.time_report_overviews AS tro
JOIN ladex_iv.time_reports AS tr ON tro.id = tr.time_report_overview_id
GROUP BY tro.id;

UPDATE ladex_iv.time_report_overviews AS tro SET previous_id = (
	SELECT id
	FROM ladex_iv.time_report_overviews AS pre
	WHERE pre.`date` < tro.`date`
	AND pre.user_id = tro.user_id
	ORDER BY pre.`date` DESC
	LIMIT 1);

UPDATE ladex_iv.time_report_overviews AS tro SET leave_days = (
	SELECT COUNT(*)
	FROM ladex_iv.time_reports AS tr
	WHERE tr.time_report_overview_id = tro.id
	AND leave_type IS NOT NULL);

##################################################
# Payrolls                                       #
##################################################

INSERT INTO ladex_iv.payrolls
SELECT
    p.id,
    p.created_at, p.updated_at, p.deleted_at,
    p.user_id, tr.id, pr.id,
    IF(pr.date IS NOT NULL, DATE_ADD(pr.date, INTERVAL 1 DAY), DATE_ADD(LAST_DAY(DATE_ADD(p.date, INTERVAL -1 MONTH)), INTERVAL 1 DAY)),
    p.date, p.minutes,
    p.hourly_wage, p.payment, p.extra_payment,
    p.prev_balance, p.payout, p.current_balance,
    null
FROM ladex_iii_migrate.payrolls AS p
JOIN ladex_iii_migrate.time_reports AS tr ON p.user_id = tr.user_id
LEFT JOIN ladex_iii_migrate.payrolls AS pr ON pr.id = (
    SELECT p1.id
    FROM ladex_iii_migrate.payrolls AS p1
    WHERE p1.user_id = p.user_id
    AND p1.date < p.date
    ORDER BY p1.date DESC
    LIMIT 1
)
WHERE p.date = tr.date;

##################################################
# Verifications                                  #
##################################################

INSERT INTO ladex_iv.verifications (created_at, updated_at, verifiable_type, verifiable_id, user_id, status, note)
SELECT
    c.controlled_at,
    c.controlled_at,
    "App\\Models\\Contract",
    c.id,
    c.controlled_by,
    "pass",
    null
FROM ladex_iii_migrate.contracts AS c
WHERE controlled_at IS NOT NULL;

INSERT INTO ladex_iv.verifications (created_at, updated_at, verifiable_type, verifiable_id, user_id, status, note)
SELECT
    c.data_checked,
    c.data_checked,
    "App\\Models\\Customer",
    c.id,
    1,
    "pass",
    "Migriert von LadexIII"
FROM ladex_iii_migrate.customers AS c
WHERE data_checked IS NOT NULL;

##################################################
# Media                                          #
##################################################

INSERT INTO ladex_iv.media (
    created_at, updated_at,
    model_type, model_id,
    uuid,
    collection_name, name, file_name, mime_type,
    disk, size,
    manipulations, custom_properties, order_column, generated_conversions, responsive_images
)
SELECT
    m.created_at, m.updated_at,
    m.model_type, m.model_id,
    m.uuid,
    m.collection_name, m.name, m.file_name, m.mime_type,
    m.disk, m.size,
    m.manipulations, m.custom_properties, m.order_column, m.generated_conversions, m.responsive_images
FROM ladex_iii_migrate.media AS m;

##################################################
# Accounts                                       #
##################################################

INSERT INTO ladex_iv.accounts (
    id,
    created_at, updated_at,
    customer_id,
    name,
    iban,
    meta
)
SELECT
    a.id,
    a.created_at, a.updated_at,
    a.customer_id,
    a.name,
    a.iban,
    JSON_OBJECT(
    	'vendor_id', a.vendor_id,
        'debtor_id', a.debtor_id,
        'migrated_at', now()
    )
FROM ladex_iii_migrate.accounts AS a
WHERE a.iban IS NOT NULL;

##################################################
# Banks                                          #
##################################################

INSERT INTO ladex_iv.banks (
    id,
    created_at, updated_at,
    name,
    iban,
    meta
)
SELECT
    b.id,
    b.created_at, b.updated_at,
    b.name,
    b.iban,
    JSON_OBJECT(
    	'is_cashbox', b.is_cashbox,
        'is_default', b.is_default,
        'migrated_at', now()
    )
FROM ladex_iii_migrate.banks AS b
WHERE b.iban IS NOT NULL;

##################################################
# Transactions                                   #
##################################################

DELETE FROM ladex_iii_migrate.transactions WHERE account_id NOT IN (SELECT id FROM ladex_iv.accounts);

INSERT INTO ladex_iv.transactions (
	id,
	created_at, updated_at,
	account_id, bank_id,
	bookable_type, bookable_id,
	date, amount,
	description,
	type, status,
	hash,
	meta
)
SELECT
	t.id,
	t.created_at, t.updated_at,
	t.account_id, t.bank_id,
    null, null,
    t.date, t.amount,
    t.description,
    t.type, IF(t.is_booked IS NOT NULL, "booked", "open"),
    tm.hash,
    JSON_OBJECT(
        'payment_type_id', t.payment_type_id,
    	'voucher_id', t.voucher_id,
        'is_booked', t.is_booked,
        'migrated_at', now()
    )
FROM ladex_iii_migrate.transactions AS t
JOIN ladex_iii_migrate.transaction_metas AS tm ON tm.transaction_id = t.id
WHERE t.invoice_id IS NULL;

INSERT INTO ladex_iv.transactions (
	id,
	created_at, updated_at,
	account_id, bank_id,
	bookable_type, bookable_id,
	date, amount,
	description,
	type, status,
	hash,
	meta
)
SELECT
	t.id,
	t.created_at, t.updated_at,
	t.account_id, t.bank_id,
    "App\\Models\\Invoice", t.invoice_id,
    t.date, t.amount,
    t.description,
    t.type, IF(t.is_booked IS NOT NULL, "booked", "open"),
    tm.hash,
    JSON_OBJECT(
        'payment_type_id', t.payment_type_id,
    	'voucher_id', t.voucher_id,
        'is_booked', t.is_booked,
        'migrated_at', now()
    )
FROM ladex_iii_migrate.transactions AS t
JOIN ladex_iii_migrate.transaction_metas AS tm ON tm.transaction_id = t.id
WHERE t.invoice_id IS NOT NULL;

##################################################
# FinTs Credentials                              #
##################################################

INSERT INTO ladex_iv.fints_credentials (
	id,
	created_at, updated_at,
	bank_id,
    bank_name, bank_url,
    bank_port, bank_code,
    username, password,
    bank_2fa, bank_2fa_device,
    fints_account,
    from_date, to_date
)
SELECT
    fc.id,
    fc.created_at, fc.updated_at,
    fc.bank_id,
    fc.name, fc.url,
    fc.port, fc.bank_code,
    fc.username, null,
    fc.bank_2fa, fc.bank_2fa_device,
    fc.fints_account,
    fc.from_date, fc.to_date
FROM ladex_iii_migrate.fints_credentials AS fc;


# INSERT INTO model_has_roles (role_id, model_type, model_id) SELECT 2, "App\\Models\\User", id FROM users;

SET FOREIGN_KEY_CHECKS = 1;
