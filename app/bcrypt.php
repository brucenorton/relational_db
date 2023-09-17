<?php
// a quick test to verify a password
//create an encrypted password here: 
// https://www.devglan.com/online-tools/bcrypt-hash-generator
$hash = '$2a$04$lHRmTSExsly7YoklX.0j6eEEPJzDPDBp2Rac8idEsYz2uBMY/fr1S';

if (password_verify('hvrtHunti9', $hash)) {
    echo 'Password is valid!';
} else {
    echo 'Invalid password.';
}

?>