<?php
// محاكاة POST request لتحويل الصورة إلى WebP

// تعيين البيانات كأنها جاءت من POST
$_POST['convert_to_webp'] = '1';
$_POST['image_id'] = '271';
$_POST['ad_id'] = '271';
$_POST['image_type'] = 'main';
$_POST['current_path'] = 'ads/1751052061_ad-main-image';
$_POST['found_file'] = '1751052061_ad-main-image';

echo "=== بدء عملية تحويل الصورة إلى WebP ===\n";
echo "الملف: {$_POST['found_file']}\n";
echo "نوع الصورة: {$_POST['image_type']}\n";
echo "ID الإعلان: {$_POST['ad_id']}\n";
echo "=====================================\n\n";

// تضمين ملف التحويل
ob_start();
include 'convert_to_webp_existing.php';
$output = ob_get_clean();

// طباعة النتيجة
echo $output;
?>