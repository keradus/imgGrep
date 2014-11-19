<?php

\Ker\Res::set ( array (
    "validate_empty_string_error" => "Pole nie może być puste",
    "validate_empty_radio_error" => "Należy dokonać wyboru jednej możliwości",
    "validate_min_string_error" => function ($_rule) {
        return "Minimalna ilość znaków: $_rule";
    },
    "validate_max_string_error" => function ($_rule) {
        return "Maksymalna ilość znaków: $_rule";
    },
    "validate_min_selection_error" => function ($_rule) {
//        if ($_rule === 1) {
//            return "Należy dokonać wyboru jednej możliwości";
//        }
        return "Minimalna ilość zaznaczeń : $_rule";
    },
    "validate_max_selection_error" => function ($_rule) {
//        if ($_rule === 1) {
//            return "Należy dokonać wyboru jednej możliwości";
//        }
        return "Maksymalna ilość zaznaczeń : $_rule";
    },
    "validate_password_error_length" => function ($_len) {
        return "Minimalna długość hasła: $_len";
    },
    "validate_password_error_noUppercase" => "Hasło musi zawierać wielką literę.",
    "validate_password_error_noLowercase" => "Hasło musi zawierać małą literę.",
    "validate_password_error_noSpecial" => function ($_special) {
        return "Hasło musi zawierać znak specjalny ($_special).";
    },
    "validate_password_error_noNumber" => "Hasło musi zawierać liczbę.",
) );
