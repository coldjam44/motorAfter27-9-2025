<?php

echo "<style>
.success-box { background-color: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0; }
.error-box { background-color: #f8d7da; padding: 20px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px 0; }
.info-box { background-color: #d1ecf1; padding: 20px; border: 1px solid #bee5eb; border-radius: 5px; margin: 20px 0; }
.back-button { padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
.back-button:hover { background-color: #0056b3; }
</style>";

echo "<h1>🔧 تحديث امتداد الصورة</h1>";

// بيانات قاعدة البيانات
require_once 'dbconn.php';

// التحقق من وجود البيانات المطلوبة
if (!isset($_POST['image_id']) || !isset($_POST['ad_id']) || !isset($_POST['image_type']) || !isset($_POST['current_path']) || !isset($_POST['found_file'])) {
    echo "<div class='error-box'>";
    echo "<h3 style='color: #721c24;'>❌ خطأ في البيانات</h3>";
    echo "<p>البيانات المطلوبة للتحديث غير موجودة.</p>";
    echo "<a href='index.php' class='back-button'>العودة للصفحة الرئيسية</a>";
    echo "</div>";
    exit;
}

$imageId = intval($_POST['image_id']);
$adId = intval($_POST['ad_id']);
$imageType = $_POST['image_type']; // 'main' أو 'sub'
$currentPath = $_POST['current_path'];
$foundFile = $_POST['found_file'];

// عرض معلومات التحديث
echo "<div class='info-box'>";
echo "<h3 style='color: #0c5460;'>📋 معلومات التحديث</h3>";
echo "<p><strong>ID الإعلان:</strong> " . $adId . "</p>";
echo "<p><strong>نوع الصورة:</strong> " . ($imageType === 'main' ? 'رئيسية' : 'فرعية') . "</p>";
if ($imageType === 'sub') {
    echo "<p><strong>ID الصورة:</strong> " . $imageId . "</p>";
}
echo "<p><strong>المسار الحالي:</strong> " . htmlspecialchars($currentPath) . "</p>";
echo "<p><strong>المسار الجديد:</strong> ads/" . htmlspecialchars($foundFile) . "</p>";
echo "</div>";

try {
    // الاتصال بقاعدة البيانات
    $host = $dbConfig['host'];
    $port = $dbConfig['port'];
    $dbname = $dbConfig['database'];
    $username = $dbConfig['username'];
    $password = $dbConfig['password'];
    
    $dsn = "mysql:host=$host:$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    // التحقق من وجود الملف فعلياً
    $adsDirectory = '../ads/';
    $fullFilePath = $adsDirectory . $foundFile;
    
    if (!file_exists($fullFilePath)) {
        throw new Exception("الملف غير موجود على الخادم: " . $foundFile);
    }
    
    // التحقق من أن الملف صورة صالحة
    $imageInfo = @getimagesize($fullFilePath);
    if ($imageInfo === false) {
        throw new Exception("الملف ليس صورة صالحة أو تالف: " . $foundFile);
    }
    
    $newPath = 'ads/' . $foundFile;
    
    // تحديث قاعدة البيانات
    if ($imageType === 'main') {
        // التحقق من وجود الإعلان
        $checkStmt = $pdo->prepare("SELECT id, main_image FROM ads WHERE id = ?");
        $checkStmt->execute([$adId]);
        $existingAd = $checkStmt->fetch();
        
        if (!$existingAd) {
            throw new Exception("الإعلان غير موجود في قاعدة البيانات: ID " . $adId);
        }
        
        echo "<div class='info-box'>";
        echo "<h4 style='color: #0c5460;'>📄 بيانات الإعلان الحالية:</h4>";
        echo "<p><strong>الصورة الحالية:</strong> " . htmlspecialchars($existingAd['main_image']) . "</p>";
        echo "</div>";
        
        // تحديث الصورة الرئيسية
        $stmt = $pdo->prepare("UPDATE ads SET main_image = ? WHERE id = ?");
        $updateId = $adId;
        $tableInfo = "جدول ads";
        
    } else {
        // التحقق من وجود الصورة الفرعية
        $checkStmt = $pdo->prepare("SELECT id, ad_id, image FROM ad_images WHERE id = ?");
        $checkStmt->execute([$imageId]);
        $existingImage = $checkStmt->fetch();
        
        if (!$existingImage) {
            throw new Exception("الصورة الفرعية غير موجودة في قاعدة البيانات: ID " . $imageId);
        }
        
        if ($existingImage['ad_id'] != $adId) {
            throw new Exception("عدم تطابق في ID الإعلان. المتوقع: " . $adId . "، الموجود: " . $existingImage['ad_id']);
        }
        
        echo "<div class='info-box'>";
        echo "<h4 style='color: #0c5460;'>📄 بيانات الصورة الفرعية الحالية:</h4>";
        echo "<p><strong>الصورة الحالية:</strong> " . htmlspecialchars($existingImage['image']) . "</p>";
        echo "</div>";
        
        // تحديث الصورة الفرعية
        $stmt = $pdo->prepare("UPDATE ad_images SET image = ? WHERE id = ?");
        $updateId = $imageId;
        $tableInfo = "جدول ad_images";
    }
    
    // تنفيذ التحديث
    $result = $stmt->execute([$newPath, $updateId]);
    $affectedRows = $stmt->rowCount();
    
    if ($result && $affectedRows > 0) {
        echo "<div class='success-box'>";
        echo "<h3 style='color: #155724;'>✅ تم التحديث بنجاح!</h3>";
        echo "<p><strong>الجدول المحدث:</strong> " . $tableInfo . "</p>";
        echo "<p><strong>عدد الصفوف المتأثرة:</strong> " . $affectedRows . "</p>";
        echo "<p><strong>المسار الجديد:</strong> " . htmlspecialchars($newPath) . "</p>";
        
        // معلومات إضافية عن الصورة
        echo "<h4 style='color: #155724;'>📸 معلومات الصورة:</h4>";
        echo "<p><strong>الأبعاد:</strong> " . $imageInfo[0] . " × " . $imageInfo[1] . " بكسل</p>";
        echo "<p><strong>نوع الملف:</strong> " . $imageInfo['mime'] . "</p>";
        echo "<p><strong>حجم الملف:</strong> " . number_format(filesize($fullFilePath) / 1024, 2) . " كيلوبايت</p>";
        
        echo "<a href='index.php' class='back-button'>✅ العودة للصفحة الرئيسية</a>";
        echo "</div>";
        
    } else {
        throw new Exception("فشل في تحديث قاعدة البيانات. لم يتم تحديث أي صفوف.");
    }
    
} catch (Exception $e) {
    echo "<div class='error-box'>";
    echo "<h3 style='color: #721c24;'>❌ خطأ في التحديث</h3>";
    echo "<p><strong>رسالة الخطأ:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>الوقت:</strong> " . date('Y-m-d H:i:s') . "</p>";
    echo "<p><strong>ملاحظة:</strong> لم يتم إجراء أي تغييرات على قاعدة البيانات.</p>";
    echo "</div>";
    
    // عدم العودة للصفحة الرئيسية في حالة الخطأ كما طلب المستخدم
    echo "<h3>🔍 استكشاف الأخطاء</h3>";
    echo "<div class='info-box'>";
    echo "<h4 style='color: #0c5460;'>البيانات المستلمة:</h4>";
    echo "<ul>";
    echo "<li><strong>Image ID:</strong> " . $imageId . "</li>";
    echo "<li><strong>Ad ID:</strong> " . $adId . "</li>";
    echo "<li><strong>Image Type:</strong> " . htmlspecialchars($imageType) . "</li>";
    echo "<li><strong>Current Path:</strong> " . htmlspecialchars($currentPath) . "</li>";
    echo "<li><strong>Found File:</strong> " . htmlspecialchars($foundFile) . "</li>";
    echo "<li><strong>Full File Path:</strong> " . htmlspecialchars($fullFilePath ?? 'غير محدد') . "</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<a href='index.php' class='back-button' style='background-color: #6c757d;'>العودة للصفحة الرئيسية</a>";
}

?>