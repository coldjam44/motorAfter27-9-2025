<?php
// ملف عرض سجل العمليات لتحويل WebP

echo "<!DOCTYPE html>";
echo "<html lang='ar' dir='rtl'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>سجل عمليات تحويل WebP</title>";
echo "<style>";
echo "body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; padding: 20px; }";
echo ".container { max-width: 1200px; margin: 0 auto; background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }";
echo ".log-entry { margin: 5px 0; padding: 8px; border-radius: 4px; font-family: monospace; font-size: 12px; }";
echo ".log-INFO { background-color: #e7f3ff; border-left: 4px solid #0066cc; }";
echo ".log-SUCCESS { background-color: #e8f5e8; border-left: 4px solid #28a745; }";
echo ".log-WARNING { background-color: #fff3cd; border-left: 4px solid #ffc107; }";
echo ".log-ERROR { background-color: #f8d7da; border-left: 4px solid #dc3545; }";
echo ".controls { margin-bottom: 20px; text-align: center; }";
echo ".btn { padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }";
echo ".btn-primary { background-color: #007bff; color: white; }";
echo ".btn-success { background-color: #28a745; color: white; }";
echo ".btn-warning { background-color: #ffc107; color: black; }";
echo ".btn-danger { background-color: #dc3545; color: white; }";
echo ".stats { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }";
echo ".filter-controls { background-color: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 20px; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>📋 سجل عمليات تحويل WebP</h1>";

$logFile = __DIR__ . '/logs/webp_conversion.log';

// أزرار التحكم
echo "<div class='controls'>";
echo "<a href='index.php' class='btn btn-primary'>🏠 العودة للصفحة الرئيسية</a>";
echo "<a href='?action=clear' class='btn btn-danger' onclick='return confirm(\"هل أنت متأكد من حذف السجل؟\")'>🗑️ حذف السجل</a>";
echo "<a href='?action=download' class='btn btn-success'>💾 تحميل السجل</a>";
echo "<a href='?refresh=1' class='btn btn-warning'>🔄 تحديث</a>";
echo "</div>";

// معالجة الإجراءات
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'clear':
            if (file_exists($logFile)) {
                file_put_contents($logFile, '');
                echo "<div class='log-entry log-SUCCESS'>✅ تم حذف السجل بنجاح!</div>";
            }
            break;
        case 'download':
            if (file_exists($logFile)) {
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="webp_conversion_log_' . date('Y-m-d_H-i-s') . '.log"');
                header('Content-Length: ' . filesize($logFile));
                readfile($logFile);
                exit;
            }
            break;
    }
}

// التحقق من وجود ملف السجل
if (!file_exists($logFile)) {
    echo "<div class='log-entry log-WARNING'>⚠️ لا يوجد ملف سجل حتى الآن. قم بتشغيل عملية تحويل لإنشاء السجل.</div>";
    echo "</div></body></html>";
    exit;
}

// قراءة السجل
$logContent = file_get_contents($logFile);
if (empty($logContent)) {
    echo "<div class='log-entry log-WARNING'>⚠️ السجل فارغ.</div>";
    echo "</div></body></html>";
    exit;
}

// تحليل السجل
$lines = explode("\n", trim($logContent));
$totalLines = count($lines);
$infoCount = 0;
$successCount = 0;
$warningCount = 0;
$errorCount = 0;

foreach ($lines as $line) {
    if (strpos($line, '[INFO]') !== false) $infoCount++;
    elseif (strpos($line, '[SUCCESS]') !== false) $successCount++;
    elseif (strpos($line, '[WARNING]') !== false) $warningCount++;
    elseif (strpos($line, '[ERROR]') !== false) $errorCount++;
}

// إحصائيات
echo "<div class='stats'>";
echo "<h3>📊 إحصائيات السجل</h3>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;'>";
echo "<div><strong>📝 إجمالي الرسائل:</strong> $totalLines</div>";
echo "<div><strong>ℹ️ معلومات:</strong> $infoCount</div>";
echo "<div><strong>✅ نجاح:</strong> $successCount</div>";
echo "<div><strong>⚠️ تحذيرات:</strong> $warningCount</div>";
echo "<div><strong>❌ أخطاء:</strong> $errorCount</div>";
echo "</div>";
echo "<p><strong>📅 آخر تحديث:</strong> " . date('Y-m-d H:i:s', filemtime($logFile)) . "</p>";
echo "</div>";

// فلاتر
echo "<div class='filter-controls'>";
echo "<h4>🔍 فلترة السجل:</h4>";
echo "<div>";
echo "<label><input type='checkbox' id='showInfo' checked> ℹ️ معلومات</label> ";
echo "<label><input type='checkbox' id='showSuccess' checked> ✅ نجاح</label> ";
echo "<label><input type='checkbox' id='showWarning' checked> ⚠️ تحذيرات</label> ";
echo "<label><input type='checkbox' id='showError' checked> ❌ أخطاء</label>";
echo "</div>";
echo "</div>";

// عرض السجل
echo "<div id='logContainer'>";
echo "<h3>📋 محتوى السجل</h3>";

// عكس ترتيب الأسطر لعرض الأحدث أولاً
$lines = array_reverse($lines);

foreach ($lines as $line) {
    if (empty(trim($line))) continue;
    
    $level = 'INFO';
    if (strpos($line, '[SUCCESS]') !== false) $level = 'SUCCESS';
    elseif (strpos($line, '[WARNING]') !== false) $level = 'WARNING';
    elseif (strpos($line, '[ERROR]') !== false) $level = 'ERROR';
    
    // تنسيق الرسالة
    $formattedLine = htmlspecialchars($line);
    
    // إضافة ألوان للطوابع الزمنية
    $formattedLine = preg_replace('/(\[[\d\-\s:]+\])/', '<strong style="color: #666;">$1</strong>', $formattedLine);
    
    // إضافة ألوان للمستويات
    $formattedLine = str_replace('[INFO]', '<span style="color: #0066cc; font-weight: bold;">[INFO]</span>', $formattedLine);
    $formattedLine = str_replace('[SUCCESS]', '<span style="color: #28a745; font-weight: bold;">[SUCCESS]</span>', $formattedLine);
    $formattedLine = str_replace('[WARNING]', '<span style="color: #ffc107; font-weight: bold;">[WARNING]</span>', $formattedLine);
    $formattedLine = str_replace('[ERROR]', '<span style="color: #dc3545; font-weight: bold;">[ERROR]</span>', $formattedLine);
    
    echo "<div class='log-entry log-$level' data-level='$level'>$formattedLine</div>";
}

echo "</div>";

// JavaScript للفلترة
echo "<script>";
echo "function updateLogDisplay() {";
echo "    const showInfo = document.getElementById('showInfo').checked;";
echo "    const showSuccess = document.getElementById('showSuccess').checked;";
echo "    const showWarning = document.getElementById('showWarning').checked;";
echo "    const showError = document.getElementById('showError').checked;";
echo "    ";
echo "    const entries = document.querySelectorAll('.log-entry');";
echo "    entries.forEach(entry => {";
echo "        const level = entry.getAttribute('data-level');";
echo "        let show = false;";
echo "        if (level === 'INFO' && showInfo) show = true;";
echo "        if (level === 'SUCCESS' && showSuccess) show = true;";
echo "        if (level === 'WARNING' && showWarning) show = true;";
echo "        if (level === 'ERROR' && showError) show = true;";
echo "        entry.style.display = show ? 'block' : 'none';";
echo "    });";
echo "}";
echo "";
echo "document.getElementById('showInfo').addEventListener('change', updateLogDisplay);";
echo "document.getElementById('showSuccess').addEventListener('change', updateLogDisplay);";
echo "document.getElementById('showWarning').addEventListener('change', updateLogDisplay);";
echo "document.getElementById('showError').addEventListener('change', updateLogDisplay);";
echo "</script>";

echo "</div>";
echo "</body>";
echo "</html>";
?>