<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'El campo :attribute debe ser aceptado.',
    'active_url' => 'El campo :attribute no es una URL válida.',
    'after' => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal' => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
    'alpha' => 'El campo :attribute solo puede contener letras.',
    'alpha_dash' => 'El campo :attribute solo puede contener letras, números, guiones y guiones bajos.',
    'alpha_num' => 'El campo :attribute solo puede contener letras y números.',
    'array' => 'El campo :attribute debe ser un arreglo.',
    'before' => 'El campo :attribute debe ser una fecha anterior a :date.',
    'before_or_equal' => 'El campo :attribute debe ser una fecha anterior o igual a :date.',
    'between' => [
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'file' => 'El campo :attribute debe tener entre :min y :max kilobytes.',
        'string' => 'El campo :attribute debe tener entre :min y :max caracteres.',
        'array' => 'El campo :attribute debe tener entre :min y :max elementos.',
    ],
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'date' => 'El campo :attribute no es una fecha válida.',
    'date_equals' => 'El campo :attribute debe ser una fecha igual a :date.',
    'date_format' => 'El campo :attribute no coincide con el formato :format.',
    'different' => 'El campo :attribute y :other deben ser diferentes.',
    'digits' => 'El campo :attribute debe tener :digits dígitos.',
    'digits_between' => 'El campo :attribute debe tener entre :min y :max dígitos.',
    'dimensions' => 'El campo :attribute tiene dimensiones de imagen inválidas.',
    'distinct' => 'El campo :attribute tiene un valor duplicado.',
    'email' => 'El campo :attribute debe ser una dirección de correo válida.',
    'ends_with' => 'El campo :attribute debe terminar con uno de los siguientes valores: :values',
    'exists' => 'El valor seleccionado para :attribute es inválido.',
    'file' => 'El campo :attribute debe ser un archivo.',
    'filled' => 'El campo :attribute debe tener un valor.',
    'gt' => [
        'numeric' => 'El campo :attribute debe ser mayor que :value.',
        'file' => 'El campo :attribute debe ser mayor que :value kilobytes.',
        'string' => 'El campo :attribute debe tener más de :value caracteres.',
        'array' => 'El campo :attribute debe tener más de :value elementos.',
    ],
    'gte' => [
        'numeric' => 'El campo :attribute debe ser mayor o igual que :value.',
        'file' => 'El campo :attribute debe ser mayor o igual que :value kilobytes.',
        'string' => 'El campo :attribute debe tener :value caracteres o más.',
        'array' => 'El campo :attribute debe tener :value elementos o más.',
    ],
    'image' => 'El campo :attribute debe ser una imagen.',
    'in' => 'El valor seleccionado para :attribute es inválido.',
    'in_array' => 'El campo :attribute no existe en :other.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'ip' => 'El campo :attribute debe ser una dirección IP válida.',
    'ipv4' => 'El campo :attribute debe ser una dirección IPv4 válida.',
    'ipv6' => 'El campo :attribute debe ser una dirección IPv6 válida.',
    'json' => 'El campo :attribute debe ser una cadena JSON válida.',
    'lt' => [
        'numeric' => 'El campo :attribute debe ser menor que :value.',
        'file' => 'El campo :attribute debe ser menor que :value kilobytes.',
        'string' => 'El campo :attribute debe tener menos de :value caracteres.',
        'array' => 'El campo :attribute debe tener menos de :value elementos.',
    ],
    'lte' => [
        'numeric' => 'El campo :attribute debe ser menor o igual que :value.',
        'file' => 'El campo :attribute debe ser menor o igual que :value kilobytes.',
        'string' => 'El campo :attribute debe tener :value caracteres o menos.',
        'array' => 'El campo :attribute no debe tener más de :value elementos.',
    ],
    'max' => [
        'numeric' => 'El campo :attribute no puede ser mayor que :max.',
        'file' => 'El campo :attribute no puede ser mayor que :max kilobytes.',
        'string' => 'El campo :attribute no puede tener más de :max caracteres.',
        'array' => 'El campo :attribute no puede tener más de :max elementos.',
    ],
    'mimes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'mimetypes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'min' => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'file' => 'El campo :attribute debe tener al menos :min kilobytes.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
    ],
    'not_in' => 'El valor seleccionado para :attribute es inválido.',
    'not_regex' => 'El formato del campo :attribute es inválido.',
    'numeric' => 'El campo :attribute debe ser un número.',
    'present' => 'El campo :attribute debe estar presente.',
    'regex' => 'El formato del campo :attribute es inválido.',
    'required' => 'El campo :attribute es obligatorio.',
    'required_if' => 'El campo :attribute es obligatorio cuando :other es :value.',
    'required_unless' => 'El campo :attribute es obligatorio a menos que :other esté en :values.',
    'required_with' => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_with_all' => 'El campo :attribute es obligatorio cuando :values están presentes.',
    'required_without' => 'El campo :attribute es obligatorio cuando :values no está presente.',
    'required_without_all' => 'El campo :attribute es obligatorio cuando ninguno de :values está presente.',
    'same' => 'El campo :attribute y :other deben coincidir.',
    'size' => [
        'numeric' => 'El campo :attribute debe ser :size.',
        'file' => 'El campo :attribute debe tener :size kilobytes.',
        'string' => 'El campo :attribute debe tener :size caracteres.',
        'array' => 'El campo :attribute debe contener :size elementos.',
    ],
    'starts_with' => 'El campo :attribute debe comenzar con uno de los siguientes valores: :values',
    'string' => 'El campo :attribute debe ser una cadena de texto.',
    'timezone' => 'El campo :attribute debe ser una zona horaria válida.',
    'unique' => 'El valor de :attribute ya ha sido tomado.',
    'uploaded' => 'El campo :attribute no pudo cargarse.',
    'url' => 'El formato del campo :attribute es inválido.',
    'uuid' => 'El campo :attribute debe ser un UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Aquí puede especificar mensajes de validación personalizados para atributos usando
    | la convención "attribute.rule" para nombrar las líneas. Esto permite
    | especificar un mensaje personalizado para una regla de atributo dada.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas se usan para reemplazar los marcadores de posición
    | con nombres más amigables como "Dirección de correo electrónico" en lugar
    | de "email". Esto ayuda a que los mensajes sean más claros.
    |
    */

    'must_be' => 'El campo :name debe estar en :other',

    'attributes' => [
        'core' => [
            'access' => [
                'permissions' => [
                    'associated_roles' => 'Roles Asociados',
                    'dependencies' => 'Dependencias',
                    'display_name' => 'Nombre para mostrar',
                    'group' => 'Grupo',
                    'group_sort' => 'Orden del grupo',

                    'groups' => [
                        'name' => 'Nombre del grupo',
                    ],

                    'name' => 'Nombre',
                    'first_name' => 'Nombre',
                    'last_name' => 'Apellido',
                    'system' => 'Sistema',
                ],

                'roles' => [
                    'associated_permissions' => 'Permisos Asociados',
                    'name' => 'Nombre',
                    'sort' => 'Orden',
                ],

                'users' => [
                    'active' => 'Activo',
                    'associated_roles' => 'Roles Asociados',
                    'confirmed' => 'Confirmado',
                    'email' => 'Correo electrónico',
                    'name' => 'Nombre',
                    'last_name' => 'Apellido',
                    'first_name' => 'Nombre',
                    'other_permissions' => 'Otros permisos',
                    'password' => 'Contraseña',
                    'password_confirmation' => 'Confirmación de contraseña',
                    'send_confirmation_email' => 'Enviar correo de confirmación',
                    'timezone' => 'Zona horaria',
                    'language' => 'Idioma',
                ],
            ],
        ],

        'frontend' => [
            'avatar' => 'Ubicación del avatar',
            'email' => 'Correo electrónico',
            'first_name' => 'Nombre',
            'last_name' => 'Apellido',
            'name' => 'Nombre completo',
            'password' => 'Contraseña',
            'password_confirmation' => 'Confirmación de contraseña',
            'phone' => 'Teléfono',
            'message' => 'Mensaje',
            'new_password' => 'Nueva contraseña',
            'new_password_confirmation' => 'Confirmación de nueva contraseña',
            'old_password' => 'Contraseña anterior',
            'timezone' => 'Zona horaria',
            'language' => 'Idioma',
        ],
    ],
];
