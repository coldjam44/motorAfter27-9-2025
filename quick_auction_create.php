<?php
// public/quick_auction_create.php
header('Content-Type: text/html; charset=utf-8');

function randomImageUrl($w = 600, $h = 400) {
    return "https://picsum.photos/{$w}/{$h}?random=" . rand(1, 10000);
}

$apiUrl = 'https://motors.azsystems.tech/api/auctions/create';
$jwtToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL21vdG9ycy5henN5c3RlbXMudGVjaC9hcGkvbG9naW4iLCJpYXQiOjE3NTk4ODEzNDcsImV4cCI6MTc5MTQxNzM0NywibmJmIjoxNzU5ODgxMzQ3LCJqdGkiOiJHcWpBeTJSZHA0WjBNR0VTIiwic3ViIjoiMTAxIiwicHJ2IjoiYTk3NWI1MGRkOTE0NTRhM2Y1YzNhNTU1ZTZiZjdmM2U3OTY2ZTdjNCJ9.7CM-RtoreB9f3P-1W5GOXqzjC9Dxw2rVNtV1ISwg2wk';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'category_id' => 1,
        'country_id' => 13,
        'city_id' => 8,
        'title' => 'Mercedes-Benz E-Class 2022 - Premium Luxury Sedan',
        'description' => 'Stunning Mercedes-Benz E-Class 2022 in pristine condition. This luxury sedan features leather interior, panoramic sunroof, advanced safety systems, and premium sound system. Perfect for business executives or luxury car enthusiasts. Full service history available.',
        'price' => 2000,
        'phone_number' => '01516843223',
        'kilometer' => 125323,
        'address' => 'New Administrative Capital - Diplomatic Quarter',
        'car_model' => 662,
        'car_options' => '365,366,369,378,382,386,387,396,399,410',
        'starting_price' => 1750000,
        'bid_increment' => 25000,
        'auction_duration_days' => 10,
    ];
    for ($i = 0; $i <= 13; $i++) {
        $fieldIds = [1,41,52,53,54,55,56,57,58,59,60,61,62,63];
        $valueIds = [251,11818,322,329,335,343,360,413,423,431,444,450,457,460];
        $fields["fields[$i][category_field_id]"] = $fieldIds[$i];
        $fields["fields[$i][category_field_value_id]"] = $valueIds[$i];
    }
    // Download a random image to temp file
    $imgUrl = randomImageUrl();
    $imgData = file_get_contents($imgUrl);
    $tmpFile = tempnam(sys_get_temp_dir(), 'img');
    file_put_contents($tmpFile, $imgData);
    $fields['main_image'] = new CURLFile($tmpFile, 'image/jpeg', 'main.jpg');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Authorization: Bearer ' . $jwtToken
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    @unlink($tmpFile);
    echo "<pre>";
    if ($err) {
        echo "cURL Error: $err\n";
    }
    echo "HTTP $httpCode\n";
    echo htmlspecialchars($response);
    echo "</pre>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quick Auction Creator</title>
</head>
<body>
    <h1>Create Random Auction</h1>
    <form method="post">
        <button type="submit">Create Auction Now</button>
    </form>
</body>
</html>
