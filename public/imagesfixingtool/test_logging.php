<?php
// اختبار نظام الـ logging الجديد

// تعيين البيانات كأنها جاءت من POST
$_POST['convert_to_webp'] = '1';
$_POST['image_id'] = '261';
$_POST['ad_id'] = '261';
$_POST['image_type'] = 'main';
$_POST['current_path'] = 'ads/1750191997_2_1709304177_2_1709291119_6.jpg';
$_POST['found_file'] = '1750191997_2_1709304177_2_1709291119_6.jpg';

echo "=== اختبار نظام الـ Logging الجديد ===\n";
echo "الملف: {$_POST['found_file']}\n";
echo "نوع الصورة: {$_POST['image_type']}\n";
echo "ID الإعلان: {$_POST['ad_id']}\n";
echo "=====================================\n\n";

// تضمين ملف التحويل مع الـ logging
ob_start();
include 'convert_to_webp_unified.php';
$output = ob_get_clean();

// طباعة نتيجة مبسطة
if (strpos($output, '✅ تمت العملية بنجاح!') !== false) {
    echo "✅ نجح التحويل مع تسجيل كامل!\n";
} else {
    echo "❌ حدث خطأ في التحويل\n";
}

echo "\n📋 تحقق من ملف السجل في: logs/webp_conversion.log\n";
echo "📋 أو افتح: view_logs.php لعرض السجل\n";
?>