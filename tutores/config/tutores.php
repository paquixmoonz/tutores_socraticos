<?php

return [

    // Dominio de correo permitido para iniciar sesión. Regla dura del sistema.
    'allowed_domain' => env('TUTORES_ALLOWED_DOMAIN', 'catolica.edu.sv'),

    // Correo institucional que recibe rol admin al sembrar la base.
    'seed_admin_email' => env('TUTORES_ADMIN_EMAIL', 'admin@catolica.edu.sv'),

    // Límites de subida (KB) y extensiones aceptadas por tipo de documento.
    'uploads' => [
        'agent' => [
            'max_kb' => 10240,
            'extensions' => ['txt', 'md', 'json', 'pdf'],
            'mimes' => ['text/plain', 'text/markdown', 'application/json', 'application/pdf'],
        ],
        'study_plan' => [
            'max_kb' => 20480,
            'extensions' => ['pdf', 'docx'],
            'mimes' => [
                'application/pdf',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],
        ],
    ],
];
