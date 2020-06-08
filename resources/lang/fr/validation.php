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

    'accepted' => ':attribute doit être accepté.e.',
    'active_url' => ':attribute n\'est pas un URL valide.',
    'after' => ':attribute doit être une date après :date.',
    'after_or_equal' => ':attribute doit être une date après ou à la même date que :date.',
    'alpha' => ':attribute peut contenir uniquement des lettres.',
    'alpha_dash' => ':attribute peut contenir uniquement des lettres, des chiffres, des tirets et des underscores.',
    'alpha_num' => ':attribute peut contenir uniquement des lettres et des chiffres.',
    'array' => ':attribute doit être un tableau (array).',
    'before' => ':attribute doit être une date avant :date.',
    'before_or_equal' => ':attribute doit être une date avant ou à la même date que :date.',
    'between' => [
        'numeric' => ':attribute doit être entre :min et :max.',
        'file' => ':attribute doit être entre :min et :max kilo-octets.',
        'string' => ':attribute doit être entre :min et :max caractères.',
        'array' => ':attribute doit être entre :min et :max éléments.',
    ],
    'boolean' => 'Le champ :attribute doit être une valeur booléenne.',
    'confirmed' => 'La confirmation de :attribute ne correspond pas.',
    'date' => ':attribute n\'est pas une date valide.',
    'date_equals' => ':attribute doit être la même date que :date.',
    'date_format' => ':attribute ne correspond pas au format :format.',
    'different' => ':attribute doit être différent de :other.',
    'digits' => ':attribute doit être de :digits chiffres.',
    'digits_between' => ':attribute doit être entre :min et :max chiffres.',
    'dimensions' => ':attribute a de dimensions d\'image invalides.',
    'distinct' => 'Le champ :attribute a une valeur en double.',
    'email' => ':attribute doit être une adresse email valide.',
    'ends_with' => ':attribute doit se terminer avec une des valeurs suivantes :values.',
    'exists' => ':attribute choisie est invalide.',
    'file' => ':attribute doit être un fichier.',
    'filled' => 'Le champ :attribute doit avoit une valeur.',
    'gt' => [
        'numeric' => ':attribute doit être plus grand que :value.',
        'file' => ':attribute doit être plus grand que :value kilo-octets.',
        'string' => ':attribute doit être plus grand que :value caractères.',
        'array' => ':attribute doit avoir plus que :value éléments.',
    ],
    'gte' => [
        'numeric' => ':attribute doit être plus grand ou égal à :value.',
        'file' => ':attribute doit être plus grand ou égal à :value kilo-octets.',
        'string' => ':attribute doit être plus grand ou égal à :value caractères.',
        'array' => ':attribute doit avoir :value éléments ou plus.',
    ],
    'image' => ':attribute doit être une image.',
    'in' => ':attribute choisi.e est invalide.',
    'in_array' => 'The :attribute field does not exist in :other. Le champ :attribute n\'existe pas dans :other.',
    'integer' => ':attribute doit être un entier.',
    'ip' => ':attribute doit être une adresse IP valide.',
    'ipv4' => ':attribute doit être une adresse IPv4 valide.',
    'ipv6' => ':attribute doit être une adresse IPv6 valide.',
    'json' => ':attribute doit être une chaine de caractères JSON valide.',
    'lt' => [
        'numeric' => ':attribute doit être plus petit que :value.',
        'file' => ':attribute doit être plus petit que :value kilo-octets.',
        'string' => ':attribute doit être plus petit que :value caractères.',
        'array' => ':attribute doit avoir moins que :value éléments.',
    ],
    'lte' => [
        'numeric' => ':attribute doit être plus petit ou égal à :value.',
        'file' => ':attribute doit être plus petit ou égal à :value kilo-octets.',
        'string' => ':attribute doit être plus petit ou égal à :value caractères.',
        'array' => ':attribute ne doit pas avoir plus que :value éléments.',
    ],
    'max' => [
        'numeric' => ':attribute ne peut être plus grand que :max.',
        'file' => ':attribute ne peut être plus grand que :max kilo-octets.',
        'string' => ':attribute ne peut être plus grand que :max caractères.',
        'array' => ':attribute ne peut avoir plus que :max éléments.',
    ],
    'mimes' => ':attribute doit être un fichier de type :values.',
    'mimetypes' => ':attribute doit être un fichier de type :values.',
    'min' => [
        'numeric' => ':attribute doit être au moins :min.',
        'file' => ':attribute doit être au moins de :min. kilo-octets.',
        'string' => ':attribute doit être au moins de :min. caractères.',
        'array' => ':attribute doit avoir au moins moins :min éléments.',
    ],
    'not_in' => ':attribute choisi.e est invalide.',
    'not_regex' => 'Le format :attribute est invalide.',
    'numeric' => ':attribute doit être un chiffre.',
    'password' => 'Le mot de passe est incorrecte.',
    'present' => 'Le champ :attribute doit être présent.',
    'regex' => 'Le format :attribute est invalide.',
    'required' => 'Le champ :attribute est requis.',
    'required_if' => 'Le champ :attribute est requis quand :other est :value.',
    'required_unless' => 'Le champ :attribute est requis à moins que :other est dans :values.',
    'required_with' => 'Le champ :attribute est requis quand :values est présent.',
    'required_with_all' => 'Le champ :attribute est requis quand :values sont présent.e.s.',
    'required_without' => 'Le champ :attribute est requis quand :values n\'est pas présent.',
    'required_without_all' => 'Le champ :attribute est requis quand aucune des :values ne sont présent.e.s.',
    'same' => ':attribute doit correspondre avec :other.',
    'size' => [
        'numeric' => ':attribute doit être :size.',
        'file' => ':attribute doit être :size kilo-octets.',
        'string' => ':attribute doit être de :size caractères.',
        'array' => ':attribute doit contenir :size éléments.',
    ],
    'starts_with' => ':attribute doit commencer avec une des valeurs suivantes :values.',
    'string' => ':attribute doit être une chaine de caractères.',
    'timezone' => ':attribute doit être une zone valide.',
    'unique' => ':attribute a déjà été pris.e.',
    'uploaded' => 'Échec du téléchargement de :attribute.',
    'url' => 'Le format de :attribute est invalide.',
    'uuid' => ':attribute doit être un UUID valide.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
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
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
