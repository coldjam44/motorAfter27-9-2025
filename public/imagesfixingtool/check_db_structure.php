<?php
require_once 'dbconn.php';

try {
    $pdo = new PDO("mysql:host={$dbConfig['host']}:{$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4", $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== بنية جدول ads ===\n";
    $stmt = $pdo->query('SHOW COLUMNS FROM ads');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($columns as $column) {
        echo sprintf("%-20s %-30s %-8s %-8s %-15s %s\n", 
            $column['Field'], 
            $column['Type'], 
            $column['Null'], 
            $column['Key'], 
            $column['Default'], 
            $column['Extra']
        );
    }
    
    echo "\n=== بنية جدول ad_images ===\n";
    $stmt = $pdo->query('SHOW COLUMNS FROM ad_images');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($columns as $column) {
        echo sprintf("%-20s %-30s %-8s %-8s %-15s %s\n", 
            $column['Field'], 
            $column['Type'], 
            $column['Null'], 
            $column['Key'], 
            $column['Default'], 
            $column['Extra']
        );
    }
    
    echo "\n=== إحصائيات ===\n";
    
    // إحصائيات جدول ads
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM ads');
    $totalAds = $stmt->fetch()['total'];
    echo "إجمالي الإعلانات: $totalAds\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM ads WHERE main_image IS NOT NULL AND main_image != ''");
    $adsWithMainImage = $stmt->fetch()['total'];
    echo "الإعلانات التي لها صورة رئيسية: $adsWithMainImage\n";
    
    // إحصائيات جدول ad_images
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM ad_images');
    $totalAdImages = $stmt->fetch()['total'];
    echo "إجمالي الصور الفرعية: $totalAdImages\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM ad_images WHERE image IS NOT NULL AND image != ''");
    $imagesWithPath = $stmt->fetch()['total'];
    echo "الصور الفرعية التي لها مسار: $imagesWithPath\n";
    
    echo "\n=== أنواع امتدادات الصور الرئيسية ===\n";
    $stmt = $pdo->query("
        SELECT 
            SUBSTRING_INDEX(main_image, '.', -1) as extension,
            COUNT(*) as count
        FROM ads 
        WHERE main_image IS NOT NULL AND main_image != '' AND main_image LIKE '%.%'
        GROUP BY extension
        ORDER BY count DESC
    ");
    $extensions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($extensions as $ext) {
        echo ".{$ext['extension']}: {$ext['count']} ملف\n";
    }
    
    echo "\n=== أنواع امتدادات الصور الفرعية ===\n";
    $stmt = $pdo->query("
        SELECT 
            SUBSTRING_INDEX(image, '.', -1) as extension,
            COUNT(*) as count
        FROM ad_images 
        WHERE image IS NOT NULL AND image != '' AND image LIKE '%.%'
        GROUP BY extension
        ORDER BY count DESC
    ");
    $extensions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($extensions as $ext) {
        echo ".{$ext['extension']}: {$ext['count']} ملف\n";
    }
    
    echo "\n=== الصور بدون امتداد ===\n";
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM ads 
        WHERE main_image IS NOT NULL 
        AND main_image != '' 
        AND main_image NOT LIKE '%.%'
    ");
    $mainNoExt = $stmt->fetch()['count'];
    echo "الصور الرئيسية بدون امتداد: $mainNoExt\n";
    
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM ad_images 
        WHERE image IS NOT NULL 
        AND image != '' 
        AND image NOT LIKE '%.%'
    ");
    $subNoExt = $stmt->fetch()['count'];
    echo "الصور الفرعية بدون امتداد: $subNoExt\n";
    
    echo "\n=== عينات من البيانات ===\n";
    echo "عينة من جدول ads:\n";
    $stmt = $pdo->query('SELECT id, main_image, created_at FROM ads WHERE main_image IS NOT NULL LIMIT 5');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row) {
        echo "ID: {$row['id']}, Main Image: {$row['main_image']}, Created: {$row['created_at']}\n";
    }
    
    echo "\nعينة من جدول ad_images:\n";
    $stmt = $pdo->query('SELECT id, ad_id, image FROM ad_images WHERE image IS NOT NULL LIMIT 5');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row) {
        echo "ID: {$row['id']}, Ad ID: {$row['ad_id']}, Image: {$row['image']}\n";
    }
    
} catch(Exception $e) {
    echo "خطأ: " . $e->getMessage() . "\n";
}
?>