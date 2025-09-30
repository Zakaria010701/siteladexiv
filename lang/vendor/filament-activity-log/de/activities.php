<?php

return [
    'title' => 'Aktivitätsverlauf',

    'date_format' => 'j F, Y',
    'time_format' => 'H:i l',

    'filters' => [
        'date' => 'Datum',
        'causer' => 'Initiator',
        'subject_type' => 'Betreff',
        'subject_id' => 'Betreff ID',
        'event' => 'Aktion',
    ],
    'table' => [
        'field' => 'Feld',
        'old' => 'Alt',
        'new' => 'Neu',
        'Wert' => 'Wert',
        'no_records_yet' => 'Es sind noch keine Einträge vorhanden',
    ],
    'events' => [
        'created' => [
            'title' => 'Erstellt',
            'description' => 'Eintrag erstellt',
        ],
        'updated' => [
            'title' => 'Aktualisiert',
            'description' => 'Eintrag aktualisiert',
        ],
        'deleted' => [
            'title' => 'Gelöscht',
            'description' => 'Eintrag gelöscht',
        ],
        'restored' => [
            'title' => 'Wiederhergestellt',
            'description' => 'Eintrag wiederhergestellt',
        ],
        'attached' => [
            'title' => 'Angehängt',
            'description' => 'Eintrag angehängt',
        ],
        'detached' => [
            'title' => 'Abgehängt',
            'description' => 'Eintrag getrennt',
        ],
        'notification' => [
            'title' => 'Benachrichtigung',
            'description' => 'Benachrichtigung',
        ],
        'moved' => [
            'title' => 'Verschoben',
            'description' => 'Verschoben',
        ],
        'checked_in' => [
            'title' => 'Eingecheckt',
            'description' => 'Eingecheckt',
        ],
        'checked_out' => [
            'title' => 'Ausgecheckt',
            'description' => 'Ausgecheckt',
        ],
        'controlled' => [
            'title' => 'Kontrolliert',
            'description' => 'Kontrolliert',
        ],
        'confirmed' => [
            'title' => 'Bestätigt',
            'description' => 'Bestätigt',
        ],
        'pending' => [
            'title' => 'Ausstehend',
            'description' => 'Ausstehend',
        ],
        'canceled' => [
            'title' => 'Storniert',
            'description' => 'Storniert',
        ],
        'approved' => [
            'title' => 'Genehmigt',
            'description' => 'Genehmigt',
        ],
        'done' => [
            'title' => 'Fertig',
            'description' => 'Fertig',
        ],
        // Your custom events...
    ],
    'boolean' => [
        'true' => 'True',
        'false' => 'Falsch',
    ],
];
