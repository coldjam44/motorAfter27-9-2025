<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Userauth;
use App\Models\Ad;
use App\Models\AdImage;

$users = Userauth::whereNotNull('profile_image')->where('profile_image', '!=', '')->get();
foreach($users as $user) {
    $filename = basename($user->profile_image);
    $path = public_path('profile_images/' . $filename);
    if (!file_exists($path)) {
        $user->update(['profile_image' => null]);
        echo "Updated user {$user->id} profile_image to null\n";
    }
}

$ads = Ad::whereNotNull('main_image')->where('main_image', '!=', '')->get();
foreach($ads as $ad) {
    $path = public_path($ad->main_image);
    if (!file_exists($path)) {
        $ad->update(['main_image' => '']);
        echo "Updated ad {$ad->id} main_image to empty\n";
    }
}

$adImages = AdImage::whereNotNull('image')->where('image', '!=', '')->get();
foreach($adImages as $adImage) {
    $path = public_path($adImage->image);
    if (!file_exists($path)) {
        $adImage->delete();
        echo "Deleted ad_image {$adImage->id} with missing file\n";
    }
}

echo "Done\n";