<?php

return [
    'time' => [
        'label' => [
            'hour_break' => ':hours Stunden',
            'early_check_in' => 'Vorzeitiger Check In',
            'early_check_out' => 'Vorzeitiger Check Out',
            'late_check_in' => 'Verspäteter Check In',
            'late_check_out' => 'Verspäteter Check Out',
            'wt_prevent_check_in' => 'Checkin vor Arbeitszeitbeginn verhindern',
            'wt_prevent_check_in_minutes' => 'Gleitzeit vor Arbeitszeitbeginn',
            'worktime_auto_logout_users' => 'Nutzer nach Ende der Arbeitszeit automatisch Ausloggen',
            'worktime_auto_logout_after_minutes' => 'Gleitzeit nach Arbeitszeitende',
            'target_auto_logout_users' => 'Nutzer nach erbringen der Sollzeit automatisch Ausloggen',
            'target_auto_logout_after_minutes' => 'Gleitzeit nach erbringen der Sollzeit',
            'overtime_cap_enabled' => 'Überstunden Deckeln',
            'overtime_cap_minutes' => 'Ungedeckelte Überstunden',
        ],
        'hint' => [
            'in_minutes' => '(In Minuten)',
        ],
        'helper' => [
            'hour_break' => 'Die Pausenzeit die einem bei :hours Stunden Arbeitszeit zusteht',
            'early_check_in' => 'Ab wievielen Minuten vor Beginn der Arbeitszeit ein Check In als "Vorzeitig" markiert wird',
            'late_check_in' => 'Ab wievielen Minuten nach Beginn der Arbeitszeit ein Check In als "Verspätet" markiert wird',
            'early_check_out' => 'Ab wievielen Minuten vor Ende der Arbeitszeit ein Check Out als "Vorzeitig" markiert wird',
            'late_check_out' => 'Ab wievielen Minuten nach Ende der Arbeitszeit ein Check out als "Verspätet" markiert wird',
            'wt_prevent_check_in' => 'Verhindert das Nutzer sich vor Beginn der im Kalender eingetragenen Arbeitszeiten einchecken.',
            'wt_prevent_check_in_minutes' => 'Erlaubt Check In wenn die Zeit im Oben angegebenen rahmen befindlich ist',
            'worktime_auto_logout_users' => 'Nutzer nach Ende der Arbeitszeit automatisch Ausloggen',
            'worktime_auto_logout_after_minutes' => 'Gleitzeit nach Arbeitszeitende',
            'target_auto_logout_users' => 'Nutzer nach erbringen der Sollzeit automatisch Ausloggen',
            'target_auto_logout_after_minutes' => 'Gleitzeit nach erbringen der Sollzeit',
            'overtime_cap_enabled' => 'Wenn aktiv, werden nur die Ungedeckelten Überstunden ohne genehmigung angerechnet',
            'overtime_cap_minutes' => 'Die anzahl an Minuten, die ohne Genehmigung angerechnet wird',
        ],
    ],
    'work_types' => [
        'label' => [
            'time_constraint' => 'Zeiterfassung',
        ],
        'helper' => [
            'time_constraint' => 'Ob die Zeiterfassung Berechnungen anhand der im Kalender eingetragenen Arbeitszeiten oder der beim Nutzer festgelegten Sollstunden ausführt.',
        ],
    ],
];
