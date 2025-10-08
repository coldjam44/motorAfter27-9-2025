<?php
// اختبار الملف الموحد الجديد مع صورة أخرى

// تعيين البيانات كأنها جاءت من POST
$_POST['convert_to_webp'] = '1';
$_POST['image_id'] = '260';
$_POST['ad_id'] = '260';
$_POST['image_type'] = 'main';
$_POST['current_path'] = 'ads/1750178719_5ccec7fd-7f2c-47b6-9cee-363dd399866f.jpeg';
$_POST['found_file'] = '1750178719_5ccec7fd-7f2c-47b6-9cee-363dd399866f.jpeg';

echo "=== اختبار الملف الموحد مع صورة JPEG ===\n";
echo "الملف: {$_POST['found_file']}\n";
echo "نوع الصورة: {$_POST['image_type']}\n";
echo "ID الإعلان: {$_POST['ad_id']}\n";
echo "=====================================\n\n";

// تضمين ملف التحويل الموحد
ob_start();
include 'convert_to_webp_unified.php';
$output = ob_get_clean();

// طباعة النتيجة (مبسطة)
if (strpos($output, '✅ تمت العملية بنجاح!') !== false) {
    echo "✅ نجح التحويل!\n";
    
    // استخراج معلومات التوفير
    if (preg_match('/💾 توفير في المساحة: ([\d.]+)%/', $output, $matches)) {
        echo "💾 توفير: {$matches[1]}%\n";
    }
    
    if (preg_match('/📊 حجم ملف WebP: ([\d.]+) KB/', $output, $matches)) {
        echo "📊 حجم WebP: {$matches[1]} KB\n";
    }
} else {
    echo "❌ فشل التحويل!\n";
    echo $output;
}
?>