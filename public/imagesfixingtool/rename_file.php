<?php

echo "<style>
.success-box { background-color: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0; }
.error-box { background-color: #f8d7da; padding: 20px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px 0; }
.info-box { background-color: #d1ecf1; padding: 20px; border: 1px solid #bee5eb; border-radius: 5px; margin: 20px 0; }
.warning-box { background-color: #fff3cd; padding: 20px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 20px 0; }
.back-button { padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
.back-button:hover { background-color: #0056b3; }
</style>";

echo "<h1>📁 إعادة تسمية الملف على الخادم</h1>";

// التحقق من وجود البيانات المطلوبة
if (!isset($_POST['old_filename']) || !isset($_POST['new_filename'])) {
    echo "<div class='error-box'>";
    echo "<h3 style='color: #721c24;'>❌ خطأ في البيانات</h3>";
    echo "<p>البيانات المطلوبة لإعادة التسمية غير موجودة.</p>";
    echo "<a href='index.php' class='back-button'>العودة للصفحة الرئيسية</a>";
    echo "</div>";
    exit;
}

$oldFilename = trim($_POST['old_filename']);
$newFilename = trim($_POST['new_filename']);

// التحقق من صحة أسماء الملفات
if (empty($oldFilename) || empty($newFilename)) {
    echo "<div class='error-box'>";
    echo "<h3 style='color: #721c24;'>❌ خطأ في أسماء الملفات</h3>";
    echo "<p>أسماء الملفات لا يمكن أن تكون فارغة.</p>";
    echo "<a href='index.php' class='back-button'>العودة للصفحة الرئيسية</a>";
    echo "</div>";
    exit;
}

// تنظيف أسماء الملفات
$oldFilename = basename($oldFilename); // أمان - منع path traversal
$newFilename = basename($newFilename);

// مسار مجلد الصور
$adsDirectory = '../ads/';
$oldFilePath = $adsDirectory . $oldFilename;
$newFilePath = $adsDirectory . $newFilename;

// عرض معلومات العملية
echo "<div class='info-box'>";
echo "<h3 style='color: #0c5460;'>📋 معلومات إعادة التسمية</h3>";
echo "<p><strong>الاسم القديم:</strong> " . htmlspecialchars($oldFilename) . "</p>";
echo "<p><strong>الاسم الجديد:</strong> " . htmlspecialchars($newFilename) . "</p>";
echo "<p><strong>المسار القديم:</strong> " . htmlspecialchars($oldFilePath) . "</p>";
echo "<p><strong>المسار الجديد:</strong> " . htmlspecialchars($newFilePath) . "</p>";
echo "</div>";

try {
    // التحقق من وجود الملف القديم
    if (!file_exists($oldFilePath)) {
        throw new Exception("الملف القديم غير موجود: " . $oldFilename);
    }
    
    // التحقق من أن الملف القديم هو صورة صالحة
    $imageInfo = @getimagesize($oldFilePath);
    if ($imageInfo === false) {
        throw new Exception("الملف المحدد ليس صورة صالحة: " . $oldFilename);
    }
    
    // عرض معلومات الملف القديم
    echo "<div class='info-box'>";
    echo "<h4 style='color: #0c5460;'>📸 معلومات الملف القديم:</h4>";
    echo "<p><strong>الأبعاد:</strong> " . $imageInfo[0] . " × " . $imageInfo[1] . " بكسل</p>";
    echo "<p><strong>نوع الملف:</strong> " . $imageInfo['mime'] . "</p>";
    echo "<p><strong>حجم الملف:</strong> " . number_format(filesize($oldFilePath) / 1024, 2) . " كيلوبايت</p>";
    echo "</div>";
    
    // التحقق من وجود ملف بالاسم الجديد
    if (file_exists($newFilePath)) {
        echo "<div class='warning-box'>";
        echo "<h4 style='color: #856404;'>⚠️ تحذير - ملف موجود بالاسم الجديد</h4>";
        echo "<p>يوجد ملف بالاسم الجديد مسبقاً: " . htmlspecialchars($newFilename) . "</p>";
        
        $existingImageInfo = @getimagesize($newFilePath);
        if ($existingImageInfo !== false) {
            echo "<p><strong>معلومات الملف الموجود:</strong></p>";
            echo "<ul>";
            echo "<li>الأبعاد: " . $existingImageInfo[0] . " × " . $existingImageInfo[1] . " بكسل</li>";
            echo "<li>النوع: " . $existingImageInfo['mime'] . "</li>";
            echo "<li>الحجم: " . number_format(filesize($newFilePath) / 1024, 2) . " كيلوبايت</li>";
            echo "</ul>";
        }
        
        throw new Exception("لا يمكن إعادة التسمية لأن ملفاً بالاسم الجديد موجود مسبقاً. يرجى اختيار اسم مختلف أو حذف الملف الموجود أولاً.");
    }
    
    // تنفيذ إعادة التسمية
    $renameResult = rename($oldFilePath, $newFilePath);
    
    if (!$renameResult) {
        throw new Exception("فشل في إعادة تسمية الملف. تحقق من صلاحيات الملفات.");
    }
    
    // التحقق من نجاح العملية
    if (!file_exists($newFilePath)) {
        throw new Exception("فشل في إعادة التسمية - الملف الجديد غير موجود");
    }
    
    if (file_exists($oldFilePath)) {
        throw new Exception("فشل في إعادة التسمية - الملف القديم ما زال موجود");
    }
    
    // نجحت العملية
    echo "<div class='success-box'>";
    echo "<h3 style='color: #155724;'>✅ تمت إعادة تسمية الملف بنجاح!</h3>";
    echo "<p><strong>الاسم القديم:</strong> " . htmlspecialchars($oldFilename) . " ❌</p>";
    echo "<p><strong>الاسم الجديد:</strong> " . htmlspecialchars($newFilename) . " ✅</p>";
    
    // التحقق من معلومات الملف الجديد
    $newImageInfo = @getimagesize($newFilePath);
    if ($newImageInfo !== false) {
        echo "<div style='background-color: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px; margin-top: 15px;'>";
        echo "<h4 style='color: #495057;'>📋 تأكيد نجاح العملية:</h4>";
        echo "<ul>";
        echo "<li>✅ الملف موجود بالاسم الجديد</li>";
        echo "<li>✅ الأبعاد سليمة: " . $newImageInfo[0] . " × " . $newImageInfo[1] . " بكسل</li>";
        echo "<li>✅ نوع الملف سليم: " . $newImageInfo['mime'] . "</li>";
        echo "<li>✅ حجم الملف: " . number_format(filesize($newFilePath) / 1024, 2) . " كيلوبايت</li>";
        echo "<li>❌ الملف القديم تم حذفه من الخادم</li>";
        echo "</ul>";
        echo "</div>";
    }
    
    echo "<div style='background-color: #e8f4f8; padding: 15px; border: 1px solid #bee5eb; border-radius: 5px; margin-top: 15px;'>";
    echo "<h4 style='color: #0c5460;'>📝 خطوات مهمة بعد إعادة التسمية:</h4>";
    echo "<ol>";
    echo "<li><strong>تحديث قاعدة البيانات:</strong> تأكد من تحديث مسارات الصور في الجداول</li>";
    echo "<li><strong>فحص الروابط:</strong> تحقق من أن الصور تظهر بشكل صحيح في الموقع</li>";
    echo "<li><strong>النسخ الاحتياطية:</strong> تأكد من أن النسخ الاحتياطية تتضمن الأسماء الجديدة</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<a href='index.php' class='back-button'>✅ العودة للصفحة الرئيسية</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error-box'>";
    echo "<h3 style='color: #721c24;'>❌ خطأ في إعادة التسمية</h3>";
    echo "<p><strong>رسالة الخطأ:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>الوقت:</strong> " . date('Y-m-d H:i:s') . "</p>";
    echo "</div>";
    
    // معلومات إضافية للاستكشاف
    echo "<h3>🔍 معلومات إضافية للاستكشاف</h3>";
    echo "<div class='info-box'>";
    echo "<h4 style='color: #0c5460;'>حالة الملفات:</h4>";
    echo "<ul>";
    echo "<li><strong>الملف القديم موجود:</strong> " . (file_exists($oldFilePath) ? "نعم ✅" : "لا ❌") . "</li>";
    echo "<li><strong>الملف الجديد موجود:</strong> " . (file_exists($newFilePath) ? "نعم ✅" : "لا ❌") . "</li>";
    echo "<li><strong>صلاحية القراءة للقديم:</strong> " . (is_readable($oldFilePath) ? "نعم ✅" : "لا ❌") . "</li>";
    echo "<li><strong>صلاحية الكتابة للمجلد:</strong> " . (is_writable($adsDirectory) ? "نعم ✅" : "لا ❌") . "</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<a href='index.php' class='back-button' style='background-color: #6c757d;'>العودة للصفحة الرئيسية</a>";
}

?>