<?php
// ملف موحد لتحويل الصور إلى WebP - يدعم الصور مع وبدون امتداد

// إعداد نظام الـ Logging
function writeLog($message, $level = 'INFO') {
    $logFile = __DIR__ . '/logs/webp_conversion.log';
    $logDir = dirname($logFile);
    
    // إنشاء مجلد logs إذا لم يكن موجود
    if (!file_exists($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
    
    // محاولة كتابة السجل (بدون عرض أخطاء)
    @file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    
    // عرض الرسالة في الصفحة مباشرة
    $levelColors = [
        'INFO' => '#17a2b8',
        'SUCCESS' => '#28a745', 
        'WARNING' => '#ffc107',
        'ERROR' => '#dc3545'
    ];
    
    $color = $levelColors[$level] ?? '#6c757d';
    $icons = [
        'INFO' => 'ℹ️',
        'SUCCESS' => '✅',
        'WARNING' => '⚠️', 
        'ERROR' => '❌'
    ];
    $icon = $icons[$level] ?? '📝';
    
    echo "<p><strong>$icon [$level]</strong> $message</p>";
    
    // إجبار إرسال المحتوى للمتصفح
    if (ob_get_level()) {
        ob_flush();
    }
    flush();
}

// بداية العملية
writeLog("=== بدء عملية تحويل WebP جديدة ===");
writeLog("معلومات الطلب: " . json_encode($_POST));

// إعداد اتصال قاعدة البيانات
require_once 'dbconn.php';
writeLog("تم تحميل إعدادات قاعدة البيانات");

try {
    $pdo = new PDO("mysql:host={$dbConfig['host']}:{$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4", $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    writeLog("تم الاتصال بقاعدة البيانات بنجاح");
} catch (PDOException $e) {
    writeLog("فشل الاتصال بقاعدة البيانات: " . $e->getMessage(), 'ERROR');
    die("❌ خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}

// التحقق من وجود البيانات المطلوبة
writeLog("التحقق من صحة البيانات المرسلة");
if (!isset($_POST['convert_to_webp']) || !isset($_POST['image_id']) || !isset($_POST['ad_id']) || !isset($_POST['image_type']) || !isset($_POST['current_path']) || !isset($_POST['found_file'])) {
    writeLog("بيانات ناقصة في الطلب", 'ERROR');
    writeLog("البيانات المفقودة: " . implode(', ', array_diff(['convert_to_webp', 'image_id', 'ad_id', 'image_type', 'current_path', 'found_file'], array_keys($_POST))), 'ERROR');
    echo "❌ خطأ: بيانات ناقصة";
    echo "<br><a href='index.php'>العودة للصفحة الرئيسية</a>";
    exit;
}

// تنظيف وحماية المدخلات
writeLog("تنظيف وحماية المدخلات");
$imageId = intval($_POST['image_id']);
$adId = intval($_POST['ad_id']);
$imageType = htmlspecialchars($_POST['image_type'], ENT_QUOTES, 'UTF-8');
$currentPath = htmlspecialchars($_POST['current_path'], ENT_QUOTES, 'UTF-8');
$foundFile = basename($_POST['found_file']); // حماية من path traversal

writeLog("البيانات المنظفة - Ad ID: $adId, Image ID: $imageId, Type: $imageType, File: $foundFile");

echo "<!DOCTYPE html>";
echo "<html lang='ar' dir='rtl'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>تحويل إلى WebP - أداة إصلاح الصور</title>";

echo "</head>";
echo "<body>";

echo "<h2>🌟 تحويل الصورة إلى WebP</h2>";

// عرض أزرار التنقل في بداية الصفحة
echo "<p>";
echo "<a href='index.php'>⬅️ العودة للصفحة الرئيسية</a> | ";
echo "<a href='https://motorssooq.com/en/ad-details/" . htmlspecialchars($adId) . "' target='_blank'>👁️ عرض الإعلان</a>";
echo "</p>";
echo "<hr>";

// بداية العملية
writeLog("🚀 بدء عملية تحويل WebP جديدة");
writeLog("📥 استلام البيانات: الإعلان رقم $adId، نوع الصورة: $imageType، الملف: $foundFile");

echo "<h3>📋 تفاصيل المعالجة:</h3>";
echo "<p><strong>ID الإعلان:</strong> " . htmlspecialchars($adId) . "</p>";
echo "<p><strong>نوع الصورة:</strong> " . ($imageType == 'main' ? 'صورة رئيسية' : 'صورة فرعية') . "</p>";
echo "<p><strong>المسار الحالي في قاعدة البيانات:</strong> " . htmlspecialchars($currentPath) . "</p>";
echo "<p><strong>الملف الموجود:</strong> " . htmlspecialchars($foundFile) . "</p>";

writeLog("✅ تم عرض تفاصيل المعالجة بنجاح");

// تحديد مسارات الملفات
writeLog("تحديد مسارات الملفات");
$adsDirectory = '../ads/';
$sourceFile = $adsDirectory . $foundFile;
$pathInfo = pathinfo($foundFile);
$webpFileName = ($pathInfo['filename'] ?: $foundFile) . '.webp';
$webpFilePath = $adsDirectory . $webpFileName;
$webpDbPath = "ads/" . $webpFileName;

writeLog("مسارات الملفات - Source: $sourceFile, Target: $webpFilePath, DB Path: $webpDbPath");

writeLog("📂 تحديد مسارات الملفات");
echo "<h3>🔄 خطوات التحويل:</h3>";
echo "<p><strong>الملف الأصلي:</strong> " . htmlspecialchars(basename($sourceFile)) . "</p>";
echo "<p><strong>الملف الجديد:</strong> " . htmlspecialchars(basename($webpFilePath)) . "</p>";
echo "<p><strong>المسار الجديد في قاعدة البيانات:</strong> " . htmlspecialchars($webpDbPath) . "</p>";

writeLog("✅ تم تحديد جميع المسارات - مصدر: $sourceFile، هدف: $webpFilePath");

// التحقق من وجود الملف الأصلي
writeLog("🔍 التحقق من وجود الملف الأصلي: $sourceFile");
if (!file_exists($sourceFile)) {
    writeLog("❌ الملف الأصلي غير موجود: $sourceFile", 'ERROR');
    echo "";
    echo "<h5>❌ خطأ:</h5>";
    echo "<p>الملف الأصلي غير موجود: " . htmlspecialchars(basename($sourceFile)) . "</p>";
    echo "";
    echo "<a href='index.php'>العودة للصفحة الرئيسية</a>";
    echo "</body></html>";
    exit;
}
writeLog("✅ تم العثور على الملف الأصلي بنجاح");

// اكتشاف نوع الملف الفعلي باستخدام getimagesize
writeLog("🔬 بدء فحص وتحليل الصورة...");
$imageInfo = @getimagesize($sourceFile);
if ($imageInfo === false) {
    writeLog("❌ فشل في قراءة معلومات الصورة - الملف قد يكون تالف", 'ERROR');
    echo "";
    echo "<h5>❌ خطأ:</h5>";
    echo "<p>الملف ليس صورة صالحة أو تالف: " . htmlspecialchars(basename($sourceFile)) . "</p>";
    echo "";
    echo "<a href='index.php'>العودة للصفحة الرئيسية</a>";
    echo "</body></html>";
    exit;
}

$mimeType = $imageInfo['mime'];
$width = $imageInfo[0];
$height = $imageInfo[1];
writeLog("✅ تم فحص الصورة بنجاح - النوع: $mimeType، الأبعاد: ${width}x${height}");

// التحقق من أن الملف ليس WebP بالفعل
if ($imageInfo['mime'] === 'image/webp') {
    writeLog("الملف بصيغة WebP بالفعل - لا حاجة للتحويل", 'WARNING');
    echo "";
    echo "<h5>⚠️ تنبيه:</h5>";
    echo "<p>الملف بصيغة WebP بالفعل: " . htmlspecialchars($foundFile) . "</p>";
    echo "";
    echo "<a href='index.php'>العودة للصفحة الرئيسية</a>";
    echo "</body></html>";
    exit;
}

// التحقق من تحميل مكتبة GD
writeLog("التحقق من توفر مكتبة GD");
if (!extension_loaded('gd')) {
    writeLog("مكتبة GD غير متوفرة", 'ERROR');
    echo "";
    echo "<h5>❌ خطأ:</h5>";
    echo "<p>مكتبة GD غير متوفرة. مطلوبة لتحويل الصور إلى WebP.</p>";
    echo "";
    echo "<a href='index.php'>العودة للصفحة الرئيسية</a>";
    echo "</body></html>";
    exit;
}
writeLog("مكتبة GD متوفرة");

// التحقق من دعم WebP
writeLog("التحقق من دعم WebP");
if (!function_exists('imagewebp')) {
    writeLog("دالة imagewebp غير متوفرة", 'ERROR');
    echo "";
    echo "<h5>❌ خطأ:</h5>";
    echo "<p>تحويل WebP غير مدعوم في هذا الخادم.</p>";
    echo "";
    echo "<a href='index.php'>العودة للصفحة الرئيسية</a>";
    echo "</body></html>";
    exit;
}
writeLog("دعم WebP متوفر");

// التحقق من وجود ملف WebP مسبقاً
if (file_exists($webpFilePath)) {
    writeLog("ملف WebP موجود مسبقاً - سيتم استبداله: $webpFilePath", 'WARNING');
    echo "";
    echo "<h5>⚠️ تنبيه:</h5>";
    echo "<p>ملف WebP موجود بالفعل: " . htmlspecialchars($webpFileName) . "</p>";
    echo "<p>سيتم استبداله بنسخة جديدة.</p>";
    echo "";
}

echo "<h5>🔄 بدء عملية التحويل...</h5>";

$mimeType = $imageInfo['mime'];
echo "<p>📷 نوع الصورة الأصلية: " . htmlspecialchars($mimeType) . "</p>";
echo "<p>📐 أبعاد الصورة: {$imageInfo[0]} x {$imageInfo[1]} بكسل</p>";

$originalSize = filesize($sourceFile);
echo "<p>📁 حجم الملف الأصلي: " . number_format($originalSize / 1024, 2) . " KB</p>";

writeLog("بدء عملية التحويل - النوع: $mimeType, الحجم الأصلي: " . ($originalSize / 1024) . " KB");

// إنشاء الصورة من المصدر حسب النوع
// إنشاء المورد من الملف الأصلي بناءً على نوعه
writeLog("🔧 بدء إنشاء مورد الصورة من النوع: $mimeType");
$originalImage = null;

switch($mimeType) {
    case 'image/jpeg':
        writeLog("📷 تحميل صورة JPEG...");
        $originalImage = @imagecreatefromjpeg($sourceFile);
        break;
    case 'image/png':
        writeLog("🖼️ تحميل صورة PNG...");
        $originalImage = @imagecreatefrompng($sourceFile);
        break;
    case 'image/gif':
        writeLog("🎞️ تحميل صورة GIF...");
        $originalImage = @imagecreatefromgif($sourceFile);
        break;
    case 'image/bmp':
    case 'image/x-ms-bmp':
        writeLog("🖥️ تحميل صورة BMP...");
        $originalImage = @imagecreatefrombmp($sourceFile);
        break;
    default:
        writeLog("❌ نوع الصورة غير مدعوم: $mimeType", 'ERROR');
        echo "";
        echo "<h5>❌ خطأ:</h5>";
        echo "<p>نوع الصورة غير مدعوم: " . htmlspecialchars($mimeType) . "</p>";
        echo "<p>الأنواع المدعومة: JPEG, PNG, GIF, BMP</p>";
        echo "";
        echo "<a href='index.php'>العودة للصفحة الرئيسية</a>";
        echo "</body></html>";
        exit;
}

if ($originalImage === false || $originalImage === null) {
    writeLog("❌ فشل في إنشاء مورد الصورة من الملف", 'ERROR');
    echo "";
    echo "<h5>❌ خطأ:</h5>";
    echo "<p>فشل في تحميل الصورة. قد يكون الملف تالف.</p>";
    echo "";
    echo "<a href='index.php'>العودة للصفحة الرئيسية</a>";
    echo "</body></html>";
    exit;
}

writeLog("✅ تم تحميل مورد الصورة بنجاح في الذاكرة");
$image = null;
switch ($mimeType) {
    case 'image/jpeg':
        $image = imagecreatefromjpeg($sourceFile);
        writeLog("تم إنشاء مورد JPEG بنجاح");
        echo "<p>✅ تم تحميل صورة JPEG بنجاح</p>";
        break;
    case 'image/png':
        $image = imagecreatefrompng($sourceFile);
        writeLog("تم إنشاء مورد PNG بنجاح");
        echo "<p>✅ تم تحميل صورة PNG بنجاح</p>";
        break;
    case 'image/gif':
        $image = imagecreatefromgif($sourceFile);
        writeLog("تم إنشاء مورد GIF بنجاح");
        echo "<p>✅ تم تحميل صورة GIF بنجاح</p>";
        break;
    case 'image/bmp':
        if (function_exists('imagecreatefrombmp')) {
            $image = imagecreatefrombmp($sourceFile);
            writeLog("تم إنشاء مورد BMP بنجاح");
            echo "<p>✅ تم تحميل صورة BMP بنجاح</p>";
        } else {
            writeLog("دالة imagecreatefrombmp غير متوفرة", 'ERROR');
            echo "<p>❌ تحويل BMP غير مدعوم</p>";
        }
        break;
    default:
        writeLog("نوع صورة غير مدعوم: $mimeType", 'ERROR');
        echo "<p>❌ نوع الصورة غير مدعوم: " . htmlspecialchars($mimeType) . "</p>";
        echo "";
        echo "<a href='index.php'>العودة للصفحة الرئيسية</a>";
        echo "</body></html>";
        exit;
}

if ($image === false) {
    writeLog("فشل في إنشاء مورد الصورة من الملف", 'ERROR');
    echo "<p>❌ فشل في إنشاء الصورة من الملف الأصلي</p>";
    echo "";
    echo "<a href='index.php'>العودة للصفحة الرئيسية</a>";
    echo "</body></html>";
    exit;
}

// حفظ الصورة بصيغة WebP بجودة 85%
writeLog("🚀 بدء عملية التحويل إلى تنسيق WebP...");
writeLog("📊 جودة الضغط المستخدمة: 85%");
writeLog("💾 حفظ الملف في: " . basename($webpFilePath));

$webpCreated = imagewebp($image, $webpFilePath, 85);

// تحرير الذاكرة
writeLog("🧹 تحرير مورد الصورة من الذاكرة...");
imagedestroy($image);
writeLog("✅ تم تحرير الذاكرة بنجاح");

if (!$webpCreated) {
    writeLog("❌ فشل في إنشاء ملف WebP", 'ERROR');
    echo "<p>❌ فشل في تحويل الصورة إلى WebP</p>";
    echo "";
    echo "<a href='index.php'>العودة للصفحة الرئيسية</a>";
    echo "</body></html>";
    exit;
}

// التحقق من إنشاء الملف
writeLog("🔍 التحقق من إنشاء ملف WebP الجديد...");
if (!file_exists($webpFilePath)) {
    writeLog("❌ لم يتم إنشاء ملف WebP على الرغم من نجاح الدالة", 'ERROR');
    echo "<p>❌ لم يتم إنشاء ملف WebP</p>";
    echo "";
    echo "<a href='index.php'>العودة للصفحة الرئيسية</a>";
    echo "</body></html>";
    exit;
}

writeLog("✅ تم إنشاء ملف WebP بنجاح!");

// حساب الإحصائيات
writeLog("📈 حساب إحصائيات التحويل...");
$webpSize = filesize($webpFilePath);
$savings = (($originalSize - $webpSize) / $originalSize) * 100;

writeLog("📊 الحجم الأصلي: " . number_format($originalSize / 1024, 2) . " KB");
writeLog("📊 الحجم الجديد: " . number_format($webpSize / 1024, 2) . " KB");
writeLog("💰 التوفير: " . number_format($savings, 1) . "%", 'SUCCESS');

echo "<p>✅ تم تحويل الصورة إلى WebP بنجاح</p>";
echo "<p>📊 حجم ملف WebP: " . number_format($webpSize / 1024, 2) . " KB</p>";
echo "<p>💾 توفير في المساحة: " . number_format($savings, 1) . "%</p>";

// تحديث قاعدة البيانات
writeLog("بدء تحديث قاعدة البيانات - النوع: $imageType");
try {
    if ($imageType == 'main') {
        // تحديث الصورة الرئيسية
        writeLog("تحديث الصورة الرئيسية في جدول ads للإعلان: $adId");
        $sql = "UPDATE ads SET main_image = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$webpDbPath, $adId]);
        
        if ($result && $stmt->rowCount() > 0) {
            writeLog("✅ تم تحديث الصورة الرئيسية بنجاح - صفوف متأثرة: " . $stmt->rowCount(), 'SUCCESS');
            echo "";
            echo "<h5>✅ نجح التحديث!</h5>";
            echo "<p>تم تحديث مسار الصورة الرئيسية في قاعدة البيانات</p>";
            echo "<p><strong>📝 المسار الجديد:</strong> " . htmlspecialchars($webpDbPath) . "</p>";
            echo "";
        } else {
            writeLog("❌ فشل في تحديث الصورة الرئيسية - لا توجد صفوف متأثرة", 'ERROR');
            echo "";
            echo "<h5>⚠️ تحذير!</h5>";
            echo "<p>فشل في تحديث قاعدة البيانات للصورة الرئيسية</p>";
            echo "";
        }
    } else {
        // تحديث الصورة الفرعية
        writeLog("تحديث الصورة الفرعية في جدول ad_images للصورة: $imageId");
        $sql = "UPDATE ad_images SET image = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$webpDbPath, $imageId]);
        
        if ($result && $stmt->rowCount() > 0) {
            writeLog("✅ تم تحديث الصورة الفرعية بنجاح - صفوف متأثرة: " . $stmt->rowCount(), 'SUCCESS');
            echo "";
            echo "<h5>✅ نجح التحديث!</h5>";
            echo "<p>تم تحديث مسار الصورة الفرعية في قاعدة البيانات</p>";
            echo "<p><strong>📝 المسار الجديد:</strong> " . htmlspecialchars($webpDbPath) . "</p>";
            echo "";
        } else {
            writeLog("❌ فشل في تحديث الصورة الفرعية - لا توجد صفوف متأثرة", 'ERROR');
            echo "";
            echo "<h5>⚠️ تحذير!</h5>";
            echo "<p>فشل في تحديث قاعدة البيانات للصورة الفرعية</p>";
            echo "";
        }
    }
} catch (PDOException $e) {
    writeLog("❌ خطأ PDO في تحديث قاعدة البيانات: " . $e->getMessage(), 'ERROR');
    echo "";
    echo "<h5>❌ خطأ في قاعدة البيانات!</h5>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "";
}

writeLog("🏁 === انتهاء عملية تحويل WebP ===");
writeLog("📋 ملخص العملية - الملف: $foundFile → $webpFileName, التوفير: " . round($savings, 1) . "%", 'SUCCESS');


echo "<h4>🎉 تمت العملية بنجاح!</h4>";
echo "<h6>📁 الملف الأصلي</h6>";
echo "<p>" . htmlspecialchars(basename($foundFile)) . "</p>";
echo "<small>" . number_format($originalSize / 1024, 2) . " KB</small>";
echo "<h6>🆕 الملف الجديد</h6>";
echo "<p>" . htmlspecialchars($webpFileName) . "</p>";
echo "<small>" . number_format($webpSize / 1024, 2) . " KB</small>";
echo "<h6>💰 التوفير</h6>";
echo "<h5>" . number_format($savings, 1) . "%</h5>";
echo "<small>" . number_format(($originalSize - $webpSize) / 1024, 2) . " KB محفوظة</small>";
echo "<hr>";
echo "<p><strong>✅ تم تحديث قاعدة البيانات بنجاح</strong></p>";

echo "<a href='index.php'>العودة للصفحة الرئيسية</a> | ";
echo "<a href='https://motorssooq.com/en/ad-details/" . htmlspecialchars($adId) . "' target='_blank'>عرض الإعلان</a>";

echo "</body>";
echo "</html>";
?>