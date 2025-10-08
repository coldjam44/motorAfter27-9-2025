<?php
// Usage: php create_auction_with_images.php

use App\Models\Ad;
use App\Models\AuctionHandler;

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// ---- CONFIGURE THESE IDs FOR YOUR DB ----
$userId = 1; // Change to a valid user ID
$categoryId = 1; // Change to a valid category ID
$countryId = 1; // Change to a valid country ID
$cityId = 1; // Change to a valid city ID
//------------------------------------------

// 1. Create Ad with random main image
$ad = new Ad();
$ad->title = 'Test Auction '.rand(1000,9999);
$ad->description = 'Test description for auction.';
$ad->main_image = 'https://picsum.photos/600/400?random=' . rand(1,1000);
$ad->price = '10000.00';
$ad->user_id = $userId;
$ad->category_id = $categoryId;
$ad->country_id = $countryId;
$ad->city_id = $cityId;
$ad->save();

// 2. Add sub images
$ad->images()->createMany([
    ['image' => 'https://picsum.photos/200/200?random=' . rand(1001,2000)],
    ['image' => 'https://picsum.photos/200/200?random=' . rand(2001,3000)]
]);

// 3. Create Auction
$auction = new AuctionHandler();
$auction->ad_id = $ad->id;
$auction->starting_price = floatval('1000.00');
$auction->current_highest_bid = floatval('1000.00');
$auction->mini_bid_increment = floatval('100.00');
// Add any other required fields for AuctionHandler here
if (property_exists($auction, 'currency')) {
    $auction->currency = 'USD';
}
$auction->start_time = now();
$auction->end_time = now()->addDays(3);
$auction->status = 'pending';
$auction->save();

echo "Auction created with ID: {$auction->id}\n";

// 4. Approve (activate) the auction
$auction->status = 'active';
$auction->save();
echo "Auction approved (activated).\n";
