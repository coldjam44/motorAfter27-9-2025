<?php

echo "<style>
.success-box { background-color: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0; }
.error-box { background-color: #f8d7da; padding: 20px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px 0; }
.info-box { background-color: #d1ecf1; padding: 20px; border: 1px solid #bee5eb; border-radius: 5px; margin: 20px 0; }
.warning-box { background-color: #fff3cd; padding: 20px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 20px 0; }
.back-button { padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
.back-button:hover { background-color: #0056b3; }
</style>";

echo "<h1>🔄 تحويل الصورة إلى WebP</h1>";

// بيانات قاعدة البيانات
require_once 'dbconn.php';

// التحقق من وجود البيانات المطلوبة
if (!isset($_POST['image_id']) || !isset($_POST['ad_id']) || !isset($_POST['image_type']) || !isset($_POST['current_path'])) {
    echo "<div class='error-box'>";
    echo "<h3 style='color: #721c24;'>❌ خطأ في البيانات</h3>";
    echo "<p>البيانات المطلوبة للتحويل غير موجودة.</p>";
    echo "<a href='index.php' class='back-button'>العودة للصفحة الرئيسية</a>";
    echo "</div>";
    exit;
}

$imageId = intval($_POST['image_id']);
$adId = intval($_POST['ad_id']);
$imageType = $_POST['image_type']; // 'main' أو 'sub'
$currentPath = $_POST['current_path'];

// استخراج اسم الملف والامتداد
$fileName = basename($currentPath);
$pathInfo = pathinfo($fileName);
$baseFileName = $pathInfo['filename'];
$currentExtension = strtolower($pathInfo['extension'] ?? '');

// التحقق من دعم التحويل
$supportedFormats = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
if (!in_array($currentExtension, $supportedFormats)) {
    echo "<div class='error-box'>";
    echo "<h3 style='color: #721c24;'>❌ تنسيق غير مدعوم</h3>";
    echo "<p>لا يمكن تحويل الملفات من نوع .$currentExtension إلى WebP.</p>";
    echo "<p><strong>التنسيقات المدعومة:</strong> " . implode(', ', $supportedFormats) . "</p>";
    echo "<a href='index.php' class='back-button'>العودة للصفحة الرئيسية</a>";
    echo "</div>";
    exit;
}

// إنشاء اسم الملف الجديد
$newFileName = $baseFileName . '.webp';
$newPath = 'ads/' . $newFileName;

// عرض معلومات التحويل
echo "<div class='info-box'>";
echo "<h3 style='color: #0c5460;'>📋 معلومات التحويل</h3>";
echo "<p><strong>ID الإعلان:</strong> " . $adId . "</p>";
echo "<p><strong>نوع الصورة:</strong> " . ($imageType === 'main' ? 'رئيسية' : 'فرعية') . "</p>";
if ($imageType === 'sub') {
    echo "<p><strong>ID الصورة:</strong> " . $imageId . "</p>";
}
echo "<p><strong>المسار الحالي:</strong> " . htmlspecialchars($currentPath) . "</p>";
echo "<p><strong>المسار الجديد:</strong> " . htmlspecialchars($newPath) . "</p>";
echo "<p><strong>التحويل:</strong> من .$currentExtension إلى .webp</p>";
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
    
    // مسارات الملفات
    $adsDirectory = '../ads/';
    $oldFilePath = $adsDirectory . $fileName;
    $newFilePath = $adsDirectory . $newFileName;
    
    // التحقق من وجود الملف الأصلي
    if (!file_exists($oldFilePath)) {
        throw new Exception("الملف الأصلي غير موجود: " . $fileName);
    }
    
    // التحقق من صحة الصورة الأصلية
    $imageInfo = @getimagesize($oldFilePath);
    if ($imageInfo === false) {
        throw new Exception("الملف ليس صورة صالحة: " . $fileName);
    }
    
    echo "<div class='info-box'>";
    echo "<h4 style='color: #0c5460;'>📸 معلومات الصورة الأصلية:</h4>";
    echo "<p><strong>الأبعاد:</strong> " . $imageInfo[0] . " × " . $imageInfo[1] . " بكسل</p>";
    echo "<p><strong>نوع الملف:</strong> " . $imageInfo['mime'] . "</p>";
    echo "<p><strong>حجم الملف الأصلي:</strong> " . number_format(filesize($oldFilePath) / 1024, 2) . " كيلوبايت</p>";
    echo "</div>";
    
    // التحقق من وجود ملف webp بنفس الاسم
    if (file_exists($newFilePath)) {
        echo "<div class='warning-box'>";
        echo "<h4 style='color: #856404;'>⚠️ تحذير</h4>";
        echo "<p>يوجد ملف WebP بنفس الاسم مسبقاً. سيتم استبداله.</p>";
        echo "<p><strong>حجم الملف الموجود:</strong> " . number_format(filesize($newFilePath) / 1024, 2) . " كيلوبايت</p>";
        echo "</div>";
    }
    
    // إنشاء الصورة من الملف الأصلي
    $sourceImage = null;
    switch ($imageInfo['mime']) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($oldFilePath);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($oldFilePath);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($oldFilePath);
            break;
        case 'image/bmp':
            $sourceImage = imagecreatefrombmp($oldFilePath);
            break;
        default:
            throw new Exception("تنسيق الصورة غير مدعوم: " . $imageInfo['mime']);
    }
    
    if (!$sourceImage) {
        throw new Exception("فشل في قراءة الصورة الأصلية");
    }
    
    // تحويل إلى WebP بجودة 85%
    $webpSuccess = imagewebp($sourceImage, $newFilePath, 85);
    
    // تحرير الذاكرة
    imagedestroy($sourceImage);
    
    if (!$webpSuccess) {
        throw new Exception("فشل في إنشاء ملف WebP");
    }
    
    // التحقق من إنشاء الملف الجديد
    if (!file_exists($newFilePath)) {
        throw new Exception("لم يتم إنشاء ملف WebP");
    }
    
    $newFileSize = filesize($newFilePath);
    $oldFileSize = filesize($oldFilePath);
    $compressionRatio = round((($oldFileSize - $newFileSize) / $oldFileSize) * 100, 2);
    
    echo "<div class='success-box'>";
    echo "<h4 style='color: #155724;'>✅ تم إنشاء ملف WebP بنجاح!</h4>";
    echo "<p><strong>حجم الملف الجديد:</strong> " . number_format($newFileSize / 1024, 2) . " كيلوبايت</p>";
    echo "<p><strong>نسبة الضغط:</strong> " . $compressionRatio . "% (توفير في المساحة)</p>";
    echo "</div>";
    
    // تحديث قاعدة البيانات
    if ($imageType === 'main') {
        // التحقق من وجود الإعلان
        $checkStmt = $pdo->prepare("SELECT id, main_image FROM ads WHERE id = ?");
        $checkStmt->execute([$adId]);
        $existingAd = $checkStmt->fetch();
        
        if (!$existingAd) {
            throw new Exception("الإعلان غير موجود في قاعدة البيانات: ID " . $adId);
        }
        
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
        echo "<h3 style='color: #155724;'>✅ تم تحديث قاعدة البيانات بنجاح!</h3>";
        echo "<p><strong>الجدول المحدث:</strong> " . $tableInfo . "</p>";
        echo "<p><strong>عدد الصفوف المتأثرة:</strong> " . $affectedRows . "</p>";
        echo "<p><strong>المسار الجديد:</strong> " . htmlspecialchars($newPath) . "</p>";
        
        echo "<div style='background-color: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px; margin-top: 15px;'>";
        echo "<h4 style='color: #495057;'>📋 ملخص العملية:</h4>";
        echo "<ul>";
        echo "<li>✅ تم إنشاء ملف WebP جديد</li>";
        echo "<li>✅ تم تحديث قاعدة البيانات</li>";
        echo "<li>ℹ️ الملف الأصلي ما زال موجود</li>";
        echo "<li>💾 توفير " . $compressionRatio . "% من المساحة</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<a href='index.php' class='back-button'>✅ العودة للصفحة الرئيسية</a>";
        echo "</div>";
        
    } else {
        throw new Exception("فشل في تحديث قاعدة البيانات. لم يتم تحديث أي صفوف.");
    }
    
} catch (Exception $e) {
    echo "<div class='error-box'>";
    echo "<h3 style='color: #721c24;'>❌ خطأ في التحويل</h3>";
    echo "<p><strong>رسالة الخطأ:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>الوقت:</strong> " . date('Y-m-d H:i:s') . "</p>";
    echo "<p><strong>ملاحظة:</strong> لم يتم إجراء أي تغييرات على قاعدة البيانات.</p>";
    echo "</div>";
    
    // عدم العودة للصفحة الرئيسية في حالة الخطأ
    echo "<h3>🔍 استكشاف الأخطاء</h3>";
    echo "<div class='info-box'>";
    echo "<h4 style='color: #0c5460;'>البيانات المستلمة:</h4>";
    echo "<ul>";
    echo "<li><strong>Image ID:</strong> " . $imageId . "</li>";
    echo "<li><strong>Ad ID:</strong> " . $adId . "</li>";
    echo "<li><strong>Image Type:</strong> " . htmlspecialchars($imageType) . "</li>";
    echo "<li><strong>Current Path:</strong> " . htmlspecialchars($currentPath) . "</li>";
    echo "<li><strong>File Name:</strong> " . htmlspecialchars($fileName ?? 'غير محدد') . "</li>";
    echo "<li><strong>Current Extension:</strong> " . htmlspecialchars($currentExtension ?? 'غير محدد') . "</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<a href='index.php' class='back-button' style='background-color: #6c757d;'>العودة للصفحة الرئيسية</a>";
}

?>