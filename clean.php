<?php

$files = glob('database/migrations/*.php');
foreach ($files as $f) {
    if (! preg_match('/2014|2019|2025_10_12|sanctum|personal_access_tokens|permission_tables|medical_services|doctors|patients|appointments|cleanup/', $f)) {
        unlink($f);
    }
}
echo "Cleaned migrations.\n";
