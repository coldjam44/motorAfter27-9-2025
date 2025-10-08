<?php
// تحويل الصور بدون امتداد إلى WebP وتحديث قاعدة البيانات

// إعداد اتصال قاعدة البيانات
require_once 'dbconn.php';

try {
    $pdo = new PDO("mysql:host={$dbConfig['host']}:{$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4", $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}

// التحقق من وجود البيانات المطلوبة
if (!isset($_POST['convert_to_webp']) || !isset($_POST['image_id']) || !isset($_POST['ad_id']) || !isset($_POST['image_type']) || !isset($_POST['current_path']) || !isset($_POST['found_file'])) {
    echo "❌ خطأ: بيانات ناقصة";
    echo "<br><a href='index.php'>العودة للصفحة الرئيسية</a>";
    exit;
}

$imageId = $_POST['image_id'];
$adId = $_POST['ad_id'];
$imageType = $_POST['image_type'];
$currentPath = $_POST['current_path'];
$foundFile = $_POST['found_file'];

echo "<!DOCTYPE html>";
echo "<html lang='ar' dir='rtl'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>تحويل إلى WebP - أداة إصلاح الصور</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<style>";
echo "body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; padding: 20px; }";
echo ".container { max-width: 800px; margin: 0 auto; background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }";
echo ".success { color: #28a745; font-weight: bold; }";
echo ".error { color: #dc3545; font-weight: bold; }";
echo ".info { color: #17a2b8; font-weight: bold; }";
echo ".warning { color: #ffc107; font-weight: bold; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h2 class='text-center mb-4'>🌟 تحويل الصورة إلى WebP</h2>";

echo "<div class='alert alert-info'>";
echo "<h5>📋 تفاصيل المعالجة:</h5>";
echo "<p><strong>ID الإعلان:</strong> $adId</p>";
echo "<p><strong>نوع الصورة:</strong> " . ($imageType == 'main' ? 'صورة رئيسية' : 'صورة فرعية') . "</p>";
echo "<p><strong>المسار الحالي:</strong> $currentPath</p>";
echo "<p><strong>الملف الموجود:</strong> $foundFile</p>";
echo "</div>";

// تحديد مسارات الملفات
$sourceFile = "/home/smallwombat98/htdocs/motors.azsystems.tech/public/public/ads/" . $foundFile;
$pathInfo = pathinfo($foundFile);
$webpFileName = $pathInfo['filename'] . '.webp';
$webpFilePath = "/home/smallwombat98/htdocs/motors.azsystems.tech/public/public/ads/" . $webpFileName;
$webpDbPath = "ads/" . $webpFileName;

echo "<div class='alert alert-secondary'>";
echo "<h5>🔄 خطوات التحويل:</h5>";
echo "<p><strong>الملف الأصلي:</strong> $sourceFile</p>";
echo "<p><strong>الملف الجديد:</strong> $webpFilePath</p>";
echo "<p><strong>المسار في قاعدة البيانات:</strong> $webpDbPath</p>";
echo "</div>";

// التحقق من وجود الملف الأصلي
if (!file_exists($sourceFile)) {
    echo "<div class='alert alert-danger'>";
    echo "<h5>❌ خطأ:</h5>";
    echo "<p>الملف الأصلي غير موجود: $sourceFile</p>";
    echo "</div>";
    echo "<a href='index.php' class='btn btn-secondary'>العودة للصفحة الرئيسية</a>";
    echo "</div></body></html>";
    exit;
}

// التحقق من تحميل مكتبة GD
if (!extension_loaded('gd')) {
    echo "<div class='alert alert-danger'>";
    echo "<h5>❌ خطأ:</h5>";
    echo "<p>مكتبة GD غير متوفرة. مطلوبة لتحويل الصور إلى WebP.</p>";
    echo "</div>";
    echo "<a href='index.php' class='btn btn-secondary'>العودة للصفحة الرئيسية</a>";
    echo "</div></body></html>";
    exit;
}

// التحقق من دعم WebP
if (!function_exists('imagewebp')) {
    echo "<div class='alert alert-danger'>";
    echo "<h5>❌ خطأ:</h5>";
    echo "<p>تحويل WebP غير مدعوم في هذا الخادم.</p>";
    echo "</div>";
    echo "<a href='index.php' class='btn btn-secondary'>العودة للصفحة الرئيسية</a>";
    echo "</div></body></html>";
    exit;
}

echo "<div class='alert alert-info'>";
echo "<h5>🔄 بدء عملية التحويل...</h5>";

// تحديد نوع الصورة الأصلية وإنشاء resource
$imageInfo = getimagesize($sourceFile);
if ($imageInfo === false) {
    echo "<p class='error'>❌ فشل في قراءة معلومات الصورة</p>";
    echo "</div>";
    echo "<a href='index.php' class='btn btn-secondary'>العودة للصفحة الرئيسية</a>";
    echo "</div></body></html>";
    exit;
}

$image = null;
$mimeType = $imageInfo['mime'];

echo "<p class='info'>📷 نوع الصورة الأصلية: $mimeType</p>";
echo "<p class='info'>📐 أبعاد الصورة: {$imageInfo[0]} x {$imageInfo[1]} بكسل</p>";

// إنشاء الصورة من المصدر حسب النوع
switch ($mimeType) {
    case 'image/jpeg':
        $image = imagecreatefromjpeg($sourceFile);
        echo "<p class='success'>✅ تم تحميل صورة JPEG بنجاح</p>";
        break;
    case 'image/png':
        $image = imagecreatefrompng($sourceFile);
        echo "<p class='success'>✅ تم تحميل صورة PNG بنجاح</p>";
        break;
    case 'image/gif':
        $image = imagecreatefromgif($sourceFile);
        echo "<p class='success'>✅ تم تحميل صورة GIF بنجاح</p>";
        break;
    case 'image/bmp':
        if (function_exists('imagecreatefrombmp')) {
            $image = imagecreatefrombmp($sourceFile);
            echo "<p class='success'>✅ تم تحميل صورة BMP بنجاح</p>";
        } else {
            echo "<p class='error'>❌ تحويل BMP غير مدعوم</p>";
        }
        break;
    case 'image/webp':
        echo "<p class='warning'>⚠️ الصورة بصيغة WebP بالفعل</p>";
        echo "<p class='info'>سيتم فقط تحديث مسار قاعدة البيانات</p>";
        $image = imagecreatefromwebp($sourceFile);
        break;
    default:
        echo "<p class='error'>❌ نوع الصورة غير مدعوم: $mimeType</p>";
        echo "</div>";
        echo "<a href='index.php' class='btn btn-secondary'>العودة للصفحة الرئيسية</a>";
        echo "</div></body></html>";
        exit;
}

if ($image === false) {
    echo "<p class='error'>❌ فشل في إنشاء الصورة من الملف الأصلي</p>";
    echo "</div>";
    echo "<a href='index.php' class='btn btn-secondary'>العودة للصفحة الرئيسية</a>";
    echo "</div></body></html>";
    exit;
}

// حفظ الصورة بصيغة WebP
$webpCreated = imagewebp($image, $webpFilePath, 85); // جودة 85%

if ($webpCreated) {
    echo "<p class='success'>✅ تم تحويل الصورة إلى WebP بنجاح</p>";
    
    // تحرير الذاكرة
    imagedestroy($image);
    
    // التحقق من إنشاء الملف
    if (file_exists($webpFilePath)) {
        $originalSize = filesize($sourceFile);
        $webpSize = filesize($webpFilePath);
        $savings = (($originalSize - $webpSize) / $originalSize) * 100;
        
        echo "<p class='info'>📊 حجم الملف الأصلي: " . number_format($originalSize / 1024, 2) . " KB</p>";
        echo "<p class='info'>📊 حجم ملف WebP: " . number_format($webpSize / 1024, 2) . " KB</p>";
        echo "<p class='success'>💾 توفير في المساحة: " . number_format($savings, 1) . "%</p>";
        
        // تحديث قاعدة البيانات
        try {
            if ($imageType == 'main') {
                // تحديث الصورة الرئيسية
                $sql = "UPDATE ads SET main_image = :new_path WHERE id = :ad_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':new_path', $webpDbPath);
                $stmt->bindParam(':ad_id', $adId);
                
                if ($stmt->execute()) {
                    echo "<p class='success'>✅ تم تحديث مسار الصورة الرئيسية في قاعدة البيانات</p>";
                    echo "<p class='info'>📝 المسار الجديد: $webpDbPath</p>";
                } else {
                    echo "<p class='error'>❌ فشل في تحديث قاعدة البيانات للصورة الرئيسية</p>";
                }
            } else {
                // تحديث الصورة الفرعية
                $sql = "UPDATE ad_images SET image_path = :new_path WHERE id = :image_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':new_path', $webpDbPath);
                $stmt->bindParam(':image_id', $imageId);
                
                if ($stmt->execute()) {
                    echo "<p class='success'>✅ تم تحديث مسار الصورة الفرعية في قاعدة البيانات</p>";
                    echo "<p class='info'>📝 المسار الجديد: $webpDbPath</p>";
                } else {
                    echo "<p class='error'>❌ فشل في تحديث قاعدة البيانات للصورة الفرعية</p>";
                }
            }
            
        } catch (PDOException $e) {
            echo "<p class='error'>❌ خطأ في قاعدة البيانات: " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<p class='error'>❌ فشل في إنشاء ملف WebP</p>";
    }
    
} else {
    echo "<p class='error'>❌ فشل في تحويل الصورة إلى WebP</p>";
    imagedestroy($image);
}

echo "</div>";

echo "<div class='alert alert-success text-center'>";
echo "<h5>✅ تمت العملية بنجاح!</h5>";
echo "<p>تم تحويل الصورة إلى WebP وتحديث قاعدة البيانات</p>";
echo "</div>";

echo "<div class='text-center'>";
echo "<a href='index.php' class='btn btn-primary me-2'>العودة للصفحة الرئيسية</a>";
echo "<a href='https://motorssooq.com/en/ad-details/$adId' target='_blank' class='btn btn-secondary'>عرض الإعلان</a>";
echo "</div>";

echo "</div>";
echo "</body>";
echo "</html>";
?>