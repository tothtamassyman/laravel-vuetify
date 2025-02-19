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

    'accepted' => 'A(z) :attribute el kell legyen fogadva.',
    'accepted_if' => 'A(z) :attribute mezőt el kell fogadni, ha az :other értéke :value.',
    'active_url' => 'A(z) :attribute nem érvényes url.',
    'after' => 'A(z) :attribute :date utáni dátum kell, hogy legyen.',
    'after_or_equal' => 'A(z) :attribute nem lehet korábbi dátum, mint :date.',
    'alpha' => 'A(z) :attribute kizárólag betűket tartalmazhat.',
    'alpha_dash' => 'A(z) :attribute kizárólag betűket, számokat és kötőjeleket tartalmazhat.',
    'alpha_num' => 'A(z) :attribute kizárólag betűket és számokat tartalmazhat.',
    'and_more' => '{1} és további 1 hiba|[2,*] és további :count hiba',
    'and_more_invalid_data' => 'A megadott adatok érvénytelenek.',
    'array' => 'A(z) :attribute egy tömb kell, hogy legyen.',
    'ascii' => 'A(z) :attribute mező csak egybájtos alfanumerikus karaktereket és szimbólumokat tartalmazhat.',
    'before' => 'A(z) :attribute :date előtti dátum kell, hogy legyen.',
    'before_or_equal' => 'A(z) :attribute nem lehet későbbi dátum, mint :date.',
    'between' => [
        'array' => 'A(z) :attribute :min - :max közötti elemet kell, hogy tartalmazzon.',
        'file' => 'A(z) :attribute mérete :min és :max kilobájt között kell, hogy legyen.',
        'numeric' => 'A(z) :attribute :min és :max közötti szám kell, hogy legyen.',
        'string' => 'A(z) :attribute hossza :min és :max karakter között kell, hogy legyen.',
    ],
    'boolean' => 'A(z) :attribute mező csak true vagy false értéket kaphat.',
    'can' => 'A(z) :attribute mező nem engedélyezett értéket tartalmaz.',
    'confirmed' => 'A(z) :attribute nem egyezik a megerősítéssel.',
    'contains' => 'A(z) :attribute mezőből hiányzik egy kötelező érték.',
    'current_password' => 'A jelszó helytelen.',
    'date' => 'A(z) :attribute nem érvényes dátum.',
    'date_equals' => 'A(z) :attribute mezőben a dátumnak egyenlőnek kell lennie a :date értékkel.',
    'date_format' => 'A(z) :attribute nem egyezik az alábbi dátum formátummal :format.',
    'decimal' => 'A(z) :attribute mezőben :decimal tizedesjegynek kell szerepelnie.',
    'declined' => 'A(z) :attribute mezőt el kell utasítani.',
    'declined_if' => 'A(z) :attribute mezőt el kell utasítani, ha az :other értéke :value.',
    'different' => 'A(z) :attribute és :other értékei különbözőek kell, hogy legyenek.',
    'digits' => 'A(z) :attribute :digits számjegyű kell, hogy legyen.',
    'digits_between' => 'A(z) :attribute értéke :min és :max közötti számjegy lehet.',
    'dimensions' => 'A(z) :attribute felbontása nem megfelelő.',
    'distinct' => 'A(z) :attribute értékének egyedinek kell lennie.',
    'doesnt_end_with' => 'A(z) :attribute mező nem végződhet a következők egyikével: :values.',
    'doesnt_start_with' => 'A(z) :attribute mező nem kezdődhet a következők egyikével: :values.',
    'email' => 'A(z) :attribute nem érvényes email formátum.',
    'ends_with' => 'A(z) :attribute mezőnek a következők valamelyikével kell végződnie: :values.',
    'enum' => 'A kiválasztott :attribute érvénytelen.',
    'exists' => 'A(z) :attribute már létezik.',
    'extensions' => 'A(z) :attribute mezőnek a következő kiterjesztések egyikével kell rendelkeznie: :values.',
    'file' => 'A(z) :attribute fájl kell, hogy legyen.',
    'filled' => 'A(z) :attribute megadása kötelező.',
    'gt' => [
        'array' => 'A(z) :attribute több, mint :value elemet kell, hogy tartalmazzon.',
        'file' => 'A(z) :attribute mérete nagyobb kell, hogy legyen, mint :value kilobájt.',
        'numeric' => 'A(z) :attribute nagyobb kell, hogy legyen, mint :value.',
        'string' => 'A(z) :attribute hosszabb kell, hogy legyen, mint :value karakter.',
    ],
    'gte' => [
        'array' => 'A(z) :attribute legalább :value elemet kell, hogy tartalmazzon.',
        'file' => 'A(z) :attribute mérete nem lehet kevesebb, mint :value kilobájt.',
        'numeric' => 'A(z) :attribute nagyobb vagy egyenlő kell, hogy legyen, mint :value.',
        'string' => 'A(z) :attribute hossza nem lehet kevesebb, mint :value karakter.',
    ],
    'hex_color' => 'A(z) :attribute mezőnek érvényes hexadecimális színnek kell lennie.',
    'image' => 'A(z) :attribute képfájl kell, hogy legyen.',
    'in' => 'A kiválasztott :attribute érvénytelen.',
    'in_array' => 'A(z) :attribute értéke nem található a(z) :other értékek között.',
    'integer' => 'A(z) :attribute értéke szám kell, hogy legyen.',
    'ip' => 'A(z) :attribute érvényes IP cím kell, hogy legyen.',
    'ipv4' => 'A(z) :attribute érvényes IPv4 cím kell, hogy legyen.',
    'ipv6' => 'A(z) :attribute érvényes IPv6 cím kell, hogy legyen.',
    'json' => 'A(z) :attribute érvényes JSON szöveg kell, hogy legyen.',
    'list' => 'A(z) :attribute mezőnek listának kell lennie.',
    'lowercase' => 'A(z) :attribute mezőnek kisbetűsnek kell lennie.',
    'lt' => [
        'array' => 'A(z) :attribute kevesebb, mint :value elemet kell, hogy tartalmazzon.',
        'file' => 'A(z) :attribute mérete kisebb kell, hogy legyen, mint :value kilobájt.',
        'numeric' => 'A(z) :attribute kisebb kell, hogy legyen, mint :value.',
        'string' => 'A(z) :attribute rövidebb kell, hogy legyen, mint :value karakter.',
    ],
    'lte' => [
        'array' => 'A(z) :attribute legfeljebb :value elemet kell, hogy tartalmazzon.',
        'file' => 'A(z) :attribute mérete nem lehet több, mint :value kilobájt.',
        'numeric' => 'A(z) :attribute kisebb vagy egyenlő kell, hogy legyen, mint :value.',
        'string' => 'A(z) :attribute hossza nem lehet több, mint :value karakter.',
    ],
    'mac_address' => 'Az :attribute mezőnek érvényes MAC-címnek kell lennie.',
    'max' => [
        'array' => 'A(z) :attribute legfeljebb :max elemet kell, hogy tartalmazzon.',
        'file' => 'A(z) :attribute mérete nem lehet több, mint :max kilobájt.',
        'numeric' => 'A(z) :attribute értéke nem lehet nagyobb, mint :max.',
        'string' => 'A(z) :attribute hossza nem lehet több, mint :max karakter.',
    ],
    'max_digits' => 'A(z) :attribute mező legfeljebb :max számjegyből állhat.',
    'mimes' => 'A(z) :attribute kizárólag az alábbi fájlformátumok egyike lehet: :values.',
    'mimetypes' => 'A(z) :attribute kizárólag az alábbi fájlformátumok egyike lehet: :values.',
    'min' => [
        'array' => 'A(z) :attribute legalább :min elemet kell, hogy tartalmazzon.',
        'file' => 'A(z) :attribute mérete nem lehet kevesebb, mint :min kilobájt.',
        'numeric' => 'A(z) :attribute értéke nem lehet kisebb, mint :min.',
        'string' => 'A(z) :attribute hossza nem lehet kevesebb, mint :min karakter.',
    ],
    'min_digits' => 'A(z) :attribute mezőnek legalább :min számjegyet kell tartalmaznia.',
    'missing' => 'A(z) :attribute mezőnek hiányoznia kell.',
    'missing_if' => 'A(z) :attribute mezőnek hiányoznia kell, amikor a(z) :other értéke :value.',
    'missing_unless' => 'A(z) :attribute mezőnek hiányoznia kell, kivéve, ha a(z) :other értéke :value.',
    'missing_with' => 'A(z) :attribute mezőnek hiányoznia kell, amikor a(z) :values jelen van.',
    'missing_with_all' => 'A(z) :attribute mezőnek hiányoznia kell, amikor a(z) :values jelen vannak.',
    'multiple_of' => 'A(z) :attribute mezőnek a(z) :value többszörösének kell lennie.',
    'not_in' => 'A(z) :attribute értéke érvénytelen.',
    'not_regex' => 'A(z) :attribute értéke érvénytelen.',
    'numeric' => 'A(z) :attribute szám kell, hogy legyen.',
    'password' => [
        'letters' => 'A(z) :attribute értékének tartalmaznia kell legalább egy betűt.',
        'mixed' => 'A(z) :attribute értékének tartalmaznia kell legalább egy kis- és egy nagybetűt.',
        'numbers' => 'A(z) :attribute értékének tartalmaznia kell legalább egy számot.',
        'symbols' => 'A(z) :attribute értékének tartalmaznia kell legalább egy speciális karaktert.',
        'uncompromised' => 'A megadott :attribute adatszivárgásban jelent meg. Kérjük, válasszon másik :attribute-t.',
        'history' => 'A legutóbbi :limit jelszavát nem használhatja fel újra.',
    ],
    'present' => 'A(z) :attribute mező nem található.',
    'present_if' => 'A(z) :attribute mezőnek jelen kell lennie, amikor a(z) :other értéke :value.',
    'present_unless' => 'A(z) :attribute mezőnek jelen kell lennie, kivéve ha a(z) :other értéke :value.',
    'present_with' => 'A(z) :attribute mezőnek jelen kell lennie, amikor a(z) :values jelen van.',
    'present_with_all' => 'A(z) :attribute mezőnek jelen kell lennie, amikor a(z) :values jelen vannak.',
    'prohibited' => 'A(z) :attribute mező tiltott.',
    'prohibited_if' => 'A(z) :attribute mező tiltott, amikor a(z) :other értéke :value.',
    'prohibited_unless' => 'A(z) :attribute mező tiltott, kivéve ha a(z) :other benne van a(z) :values-ban.',
    'prohibits' => 'A(z) :attribute mező megtiltja a(z) :other jelenlétét.',
    'regex' => 'A(z) :attribute formátuma érvénytelen.',
    'required' => 'A(z) :attribute megadása kötelező.',
    'required_array_keys' => 'A(z) :attribute mezőnek tartalmaznia kell bejegyzéseket a következőkhöz: :values.',
    'required_if' => 'A(z) :attribute megadása kötelező, ha a(z) :other értéke :value.',
    'required_if_accepted' => 'A(z) :attribute mező kitöltése kötelező, amikor a(z) :other elfogadott.',
    'required_if_declined' => 'Az :attribute mező kitöltése kötelező, ha az :other elutasított.',
    'required_unless' => 'A(z) :attribute megadása kötelező, ha a(z) :other értéke nem :values.',
    'required_with' => 'A(z) :attribute megadása kötelező, ha a(z) :values érték létezik.',
    'required_with_all' => 'A(z) :attribute megadása kötelező, ha a(z) :values értékek léteznek.',
    'required_without' => 'A(z) :attribute megadása kötelező, ha a(z) :values érték nem létezik.',
    'required_without_all' => 'A(z) :attribute megadása kötelező, ha egyik :values érték sem létezik.',
    'same' => 'A(z) :attribute és :other mezőknek egyezniük kell.',
    'size' => [
        'array' => 'A(z) :attribute :size elemet kell tartalmazzon.',
        'file' => 'A(z) :attribute mérete :size kilobájt kell, hogy legyen.',
        'numeric' => 'A(z) :attribute értéke :size kell, hogy legyen.',
        'string' => 'A(z) :attribute hossza :size karakter kell, hogy legyen.',
    ],
    'starts_with' => 'A(z) :attribute mezőnek a következők valamelyikével kell kezdődnie: :values.',
    'string' => 'A(z) :attribute szövegnek kell legyen.',
    'timezone' => 'A(z) :attribute nem létező időzona.',
    'unique' => 'A(z) :attribute már foglalt.',
    'uploaded' => 'A(z) :attribute feltöltése sikertelen.',
    'uppercase' => 'A(z) :attribute mezőnek nagybetűsnek kell lennie.',
    'url' => 'A(z) :attribute érvénytelen link.',
    'ulid' => 'A(z) :attribute mezőnek érvényes ULID-nak kell lennie.',
    'uuid' => 'A(z) :attribute mezőnek érvényes UUID-nak kell lennie.',

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
        'details.*.key' => [
            'unique' => 'A(z) :attribute kulcs már létezik ennél a felhasználónál.',
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

    'attributes' => [
        'email' => 'E-mail cím',
        'password' => 'Jelszó',
        'groups'=> [
            'name' => 'Név',
            'description' => 'Leírás',
            'users' => 'Felhasználók',
            'condition' => 'Keresési feltétel',
        ],
        'users' => [
            'default_group_id' => 'Alapértelmezett csoport'
        ],
        'roles'=> [
            'name' => 'Név',
            'description' => 'Leírás',
            'guard_name' => 'Őr neve',
            'permissions' => 'Jogosultság',
            'condition' => 'Keresési feltétel',
        ],
        'permissions'=> [
            'name' => 'Név',
            'description' => 'Leírás',
            'guard_name' => 'Őr neve',
            'fields' => [
                'field' => 'Mező',
            ],
            'conditions' => [
                'key' => 'Kulcs',
                'operator' => 'Operátor',
                'value' => 'Érték',
            ],
            'condition' => 'Keresési feltétel',
        ],
    ],

];
