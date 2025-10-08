<?php
// approve_all_approved_auctions.php
$apiUrlBase = 'https://motors.azsystems.tech/api/auctions/';
$jwtToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL21vdG9ycy5henN5c3RlbXMudGVjaC9hcGkvbG9naW4iLCJpYXQiOjE3NTk4ODEzNDcsImV4cCI6MTc5MTQxNzM0NywibmJmIjoxNzU5ODgxMzQ3LCJqdGkiOiJHcWpBeTJSZHA0WjBNR0VTIiwic3ViIjoiMTAxIiwicHJ2IjoiYTk3NWI1MGRkOTE0NTRhM2Y1YzNhNTU1ZTZiZjdmM2U3OTY2ZTdjNCJ9.7CM-RtoreB9f3P-1W5GOXqzjC9Dxw2rVNtV1ISwg2wk';

$ids = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20];

foreach ($ids as $id) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrlBase . $id . '/activate');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Authorization: Bearer ' . $jwtToken
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "Auction $id: HTTP $httpCode\n";
    echo $response . "\n";
    curl_close($ch);
}
