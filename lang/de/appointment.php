<?php

return [
    'type' => [
        'short' => [
            'treatment' => 'BH',
            'consultation' => 'BR',
            'treatment-consultation' => 'BB',
            'debriefing' => 'NBS',
            'follow-up' => 'NBH',
            'room-block' => 'RB',
            'reservation' => 'RR',
        ],
        'treatment' => 'Behandlung',
        'consultation' => 'Beratung',
        'treatment-consultation' => 'Beratung & Behandlung',
        'debriefing' => 'Nachbesprechung',
        'follow-up' => 'Nachbehandlung',
        'room-block' => 'Raumsperrung',
        'reservation' => 'Reservation',
    ],
    'movement_reason' => [
        'optimization' => 'Kalender Optimierung',
        'employee_absence' => 'Mitarbeiter ausfall',
        'customer_request' => 'Kundenanfrage',
        'other' => 'Andere',
    ],
    'delete_reason' => [
        'customer_request' => 'Kundenanfrage',
        'other' => 'Andere',
    ],
    'cancel_reason' => [
        'same-day-cancellation' => 'Am gleichen Tag abgesagt',
        'customer-sick' => 'Kunde krank',
        'customer-not-appeared' => 'Kunde nicht erschienen',
        'other' => 'Andere',
    ],
];
