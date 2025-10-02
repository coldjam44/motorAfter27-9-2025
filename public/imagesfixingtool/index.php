<?php

// معالجة رقم الصفحة من URL
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 100; // عدد الصور في كل صفحة
$offset = ($page - 1) * $perPage;

echo "<style>
.fix-button {
    padding: 5px 10px;
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 11px;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
}
.fix-button:hover {
    background: linear-gradient(135deg, #218838, #1c7a6b);
}
.convert-button {
    padding: 5px 10px;
    background: linear-gradient(135deg, #6f42c1, #e83e8c);
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 11px;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
}
.convert-button:hover {
    background: linear-gradient(135deg, #5a32a3, #dc2d75);
}
.rename-button {
    padding: 4px 8px;
    background: linear-gradient(135deg, #fd7e14, #e83e8c);
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 10px;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
}
.rename-button:hover {
    background: linear-gradient(135deg, #e8650e, #dc2d75);
}
.rename-input {
    width: 100px;
    padding: 2px 4px;
    font-size: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
}
.status-existing { color: #dc3545; font-size: 11px; }
.status-matched { color: #28a745; font-size: 11px; }
.status-none { color: #6c757d; font-size: 11px; }
</style>";

echo "Hello World<br>";
echo "اول الاشكاليات اللبتعالجها الصفحة هي الصور الي ماعندها امتداد ملف <br>";
echo "Current Path: " . __DIR__ . "<br>";
echo "Full Path: " . realpath(__DIR__) . "<br>";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "<br>";

echo "<hr>";

// بيانات قاعدة البيانات المنسوخة من ملف .env
$dbConfig = [
    'host' => 'localhost',
    'port' => '3306',
    'database' => 'motorsss',
    'username' => 'motorsss',
    'password' => '4JJnTEgH3Qppl0qQojBY'
];

// حساب الصور بدون امتداد ملف
echo "<h2 style='color: red;'>📊 إحصائيات الصور بدون امتداد ملف</h2>";

try {
    // الاتصال بقاعدة البيانات لحساب الصور
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
    
    // البحث عن الصور بدون امتداد ملف من جدول ads
    $stmt = $pdo->prepare("
        SELECT id, main_image 
        FROM ads 
        WHERE main_image IS NOT NULL 
        AND main_image != '' 
        AND main_image NOT LIKE '%.jpg' 
        AND main_image NOT LIKE '%.jpeg' 
        AND main_image NOT LIKE '%.png' 
        AND main_image NOT LIKE '%.gif' 
        AND main_image NOT LIKE '%.webp' 
        AND main_image NOT LIKE '%.bmp'
        ORDER BY id ASC
    ");
    $stmt->execute();
    $mainImagesWithoutExtension = $stmt->fetchAll();
    
    // البحث عن الصور الفرعية بدون امتداد ملف من جدول ad_images
    $stmt3 = $pdo->prepare("
        SELECT ai.id as image_id, ai.ad_id, ai.image 
        FROM ad_images ai
        WHERE ai.image IS NOT NULL 
        AND ai.image != '' 
        AND ai.image NOT LIKE '%.jpg' 
        AND ai.image NOT LIKE '%.jpeg' 
        AND ai.image NOT LIKE '%.png' 
        AND ai.image NOT LIKE '%.gif' 
        AND ai.image NOT LIKE '%.webp' 
        AND ai.image NOT LIKE '%.bmp'
        ORDER BY ai.ad_id ASC, ai.id ASC
    ");
    $stmt3->execute();
    $subImagesWithoutExtension = $stmt3->fetchAll();
    
    // دمج الصور الرئيسية والفرعية
    $imagesWithoutExtension = [];
    
    // إضافة الصور الرئيسية
    foreach ($mainImagesWithoutExtension as $image) {
        $imagesWithoutExtension[] = [
            'id' => $image['id'],
            'image_id' => null,
            'main_image' => $image['main_image'],
            'type' => 'رئيسية'
        ];
    }
    
    // إضافة الصور الفرعية
    foreach ($subImagesWithoutExtension as $image) {
        $imagesWithoutExtension[] = [
            'id' => $image['ad_id'],
            'image_id' => $image['image_id'],
            'main_image' => $image['image'],
            'type' => 'فرعية'
        ];
    }
    
    // عدد الصور بدون امتداد
    $countWithoutExtension = count($imagesWithoutExtension);
    
    // إجمالي الصور في الجداول
    $stmt2 = $pdo->prepare("SELECT COUNT(*) as total FROM ads WHERE main_image IS NOT NULL AND main_image != ''");
    $stmt2->execute();
    $totalMainImages = $stmt2->fetch()['total'];
    
    $stmt4 = $pdo->prepare("SELECT COUNT(*) as total FROM ad_images WHERE image IS NOT NULL AND image != ''");
    $stmt4->execute();
    $totalSubImages = $stmt4->fetch()['total'];
    
    $totalImages = $totalMainImages + $totalSubImages;
    
    // عد الصور الرئيسية والفرعية بدون امتداد
    $mainImagesCount = count($mainImagesWithoutExtension);
    $subImagesCount = count($subImagesWithoutExtension);
    
    echo "<div style='background-color: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #856404;'>📈 ملخص الإحصائيات:</h3>";
    echo "<p><strong>إجمالي الصور في النظام:</strong> " . number_format($totalImages) . " صورة</p>";
    echo "<p><strong>- الصور الرئيسية:</strong> " . number_format($totalMainImages) . " صورة</p>";
    echo "<p><strong>- الصور الفرعية:</strong> " . number_format($totalSubImages) . " صورة</p>";
    echo "<hr style='margin: 10px 0;'>";
    echo "<p><strong>الصور بدون امتداد ملف:</strong> " . number_format($countWithoutExtension) . " صورة</p>";
    echo "<p><strong>- الصور الرئيسية بدون امتداد:</strong> " . number_format($mainImagesCount) . " صورة</p>";
    echo "<p><strong>- الصور الفرعية بدون امتداد:</strong> " . number_format($subImagesCount) . " صورة</p>";
    echo "<p><strong>النسبة المئوية:</strong> " . ($totalImages > 0 ? round(($countWithoutExtension / $totalImages) * 100, 2) : 0) . "%</p>";
    echo "</div>";
    
    if ($countWithoutExtension > 0) {
        echo "<div style='background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 20px;'>";
        echo "<h3 style='color: #721c24;'>🚨 قائمة الصور بدون امتداد ملف:</h3>";
        echo "<p style='color: #721c24; margin-bottom: 15px;'><strong>تعليمات:</strong> استخدم زر 'إضافة الامتداد' لتحديث الصور التي تحتاج امتداد ملف. الزر متاح فقط للصور الموجودة على الخادم.</p>";
        
        // عرض عدد الصور القابلة للإصلاح (سيتم حسابه في نهاية الحلقة)
        echo "<div style='background-color: #fff3cd; padding: 10px; margin-bottom: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>";
        echo "<p style='color: #856404; margin: 0;'><strong>📊 ملخص سريع:</strong> من أصل " . number_format($countWithoutExtension) . " صورة بدون امتداد، يمكن إصلاح <span style='font-weight: bold; color: #28a745;'>[سيتم حسابه]</span> صورة تلقائياً.</p>";
        echo "</div>";
        
        echo "<div style='max-height: 600px; overflow-y: auto; background-color: white; padding: 10px; border: 1px solid #ddd;'>";
        echo "<table border='1' cellpadding='8' cellspacing='0' style='width: 100%; border-collapse: collapse; font-size: 12px;'>";
        echo "<thead style='background-color: #dc3545; color: white;'>";
        echo "<tr><th>ID الإعلان</th><th>نوع الصورة</th><th>ID الصورة</th><th>مسار الصورة</th><th>نوع الملف</th><th>هل الصورة موجودة؟</th><th>هل الاسم متطابق؟</th><th>إضافة الامتداد</th></tr>";
        echo "</thead>";
        echo "<tbody>";
        
        $adsDirectory = '../ads/'; // مسار مجلد الصور
        $fixableImagesCount = 0; // عدد الصور القابلة للإصلاح
        
        foreach ($imagesWithoutExtension as $index => $image) {
            $rowColor = ($index % 2 == 0) ? '#f9f9f9' : '#ffffff';
            
            // تحديد نوع الصورة ولونها
            $imageTypeLabel = $image['type'];
            $imageTypeLabelColor = ($image['type'] === 'رئيسية') ? '#007bff' : '#28a745';
            
            // استخراج اسم الملف من مسار الصورة
            $imagePath = $image['main_image']; // مثل: ads/1750426513_ad-main-image
            $fileName = basename($imagePath); // استخراج: 1750426513_ad-main-image
            
            // البحث عن الملف في مجلد ads واكتشاف نوع الصورة
            $fileExists = false;
            $foundFiles = [];
            $exactMatch = false;
            $imageType = "غير محدد";
            $imageTypeColor = "#6c757d";
            
            if (is_dir($adsDirectory)) {
                $files = scandir($adsDirectory);
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..') continue;
                    
                    // مقارنة اسم الملف (بدون امتداد أو مع امتداد)
                    $fileBaseName = pathinfo($file, PATHINFO_FILENAME);
                    if ($fileBaseName === $fileName || $file === $fileName) {
                        $fileExists = true;
                        $foundFiles[] = $file;
                        
                        // التحقق من التطابق التام
                        if ($file === $fileName) {
                            $exactMatch = true;
                        }
                        
                        // اكتشاف نوع الصورة الفعلي
                        $fullFilePath = $adsDirectory . $file;
                        if (file_exists($fullFilePath)) {
                            $imageInfo = @getimagesize($fullFilePath);
                            if ($imageInfo !== false) {
                                $mimeType = $imageInfo['mime'];
                                switch ($mimeType) {
                                    case 'image/jpeg':
                                        $imageType = "JPEG";
                                        $imageTypeColor = "#28a745";
                                        break;
                                    case 'image/png':
                                        $imageType = "PNG";
                                        $imageTypeColor = "#17a2b8";
                                        break;
                                    case 'image/webp':
                                        $imageType = "WebP";
                                        $imageTypeColor = "#6f42c1";
                                        break;
                                    case 'image/gif':
                                        $imageType = "GIF";
                                        $imageTypeColor = "#fd7e14";
                                        break;
                                    case 'image/bmp':
                                        $imageType = "BMP";
                                        $imageTypeColor = "#20c997";
                                        break;
                                    default:
                                        $imageType = "نوع آخر";
                                        $imageTypeColor = "#6c757d";
                                }
                                $imageType .= "<br><small style='color: #666;'>" . $imageInfo[0] . "x" . $imageInfo[1] . "</small>";
                            } else {
                                $imageType = "⚠️ ملف تالف";
                                $imageTypeColor = "#dc3545";
                            }
                        }
                        break; // أول ملف موجود
                    }
                }
            }
            
            // حساب الصور القابلة للإصلاح
            if ($fileExists && !$exactMatch && !empty($foundFiles)) {
                $fixableImagesCount++;
            }
            
            $statusIcon = $fileExists ? "✅" : "❌";
            $statusColor = $fileExists ? "#28a745" : "#dc3545";
            $statusText = $fileExists ? "موجود" : "مفقود";
            
            if ($fileExists && !empty($foundFiles)) {
                $statusText .= " (" . implode(', ', $foundFiles) . ")";
            }
            
            // حالة التطابق
            $matchIcon = "";
            $matchColor = "";
            $matchText = "";
            
            if (!$fileExists) {
                $matchIcon = "❌";
                $matchColor = "#dc3545";
                $matchText = "غير موجود";
                $imageType = "❌ غير موجود";
                $imageTypeColor = "#dc3545";
            } elseif ($exactMatch) {
                $matchIcon = "✅";
                $matchColor = "#28a745";
                $matchText = "مطابق تماماً";
            } else {
                $matchIcon = "⚠️";
                $matchColor = "#ffc107";
                $matchText = "مختلف (يحتاج امتداد)";
                $matchText .= "<br><small>الموجود: " . implode(', ', $foundFiles) . "</small>";
            }
            
            echo "<tr style='background-color: $rowColor;'>";
            echo "<td style='text-align: center; font-weight: bold;'><a href='https://motorssooq.com/en/ad-details/" . htmlspecialchars($image['id']) . "' target='_blank' style='color: #007bff; text-decoration: none;'>" . htmlspecialchars($image['id']) . "</a></td>";
            echo "<td style='text-align: center; color: $imageTypeLabelColor; font-weight: bold;'>" . $imageTypeLabel . "</td>";
            echo "<td style='text-align: center; font-weight: bold;'>" . ($image['image_id'] ? $image['image_id'] : '-') . "</td>";
            echo "<td>" . htmlspecialchars($image['main_image']) . "</td>";
            echo "<td style='text-align: center; color: $imageTypeColor; font-weight: bold;'>";
            echo $imageType;
            echo "</td>";
            echo "<td style='text-align: center; color: $statusColor; font-weight: bold;'>";
            echo $statusIcon . " " . $statusText;
            echo "</td>";
            echo "<td style='text-align: center; color: $matchColor; font-weight: bold;'>";
            echo $matchIcon . " " . $matchText;
            echo "</td>";
            
            // عمود زر إضافة الامتداد
            echo "<td style='text-align: center;'>";
            if ($fileExists && !$exactMatch && !empty($foundFiles)) {
                // يمكن إضافة الامتداد
                $foundFile = $foundFiles[0]; // أول ملف موجود
                $imageTypeValue = ($image['type'] === 'رئيسية') ? 'main' : 'sub';
                $imageIdValue = ($image['type'] === 'رئيسية') ? $image['id'] : $image['image_id'];
                
                $updateUrl = "update_extension.php";
                $params = http_build_query([
                    'image_id' => $imageIdValue,
                    'ad_id' => $image['id'],
                    'image_type' => $imageTypeValue,
                    'current_path' => $imagePath,
                    'found_file' => $foundFile
                ]);
                
                echo "<form method='POST' action='update_extension.php' style='margin: 0; display: inline-block;'>";
                echo "<input type='hidden' name='image_id' value='" . $imageIdValue . "'>";
                echo "<input type='hidden' name='ad_id' value='" . $image['id'] . "'>";
                echo "<input type='hidden' name='image_type' value='" . $imageTypeValue . "'>";
                echo "<input type='hidden' name='current_path' value='" . htmlspecialchars($imagePath) . "'>";
                echo "<input type='hidden' name='found_file' value='" . htmlspecialchars($foundFile) . "'>";
                echo "<button type='submit' name='add_extension' class='fix-button'>";
                echo "🔧 إضافة الامتداد";
                echo "</button>";
                echo "</form>";
                
                echo "<br>";
                
                echo "<form method='POST' action='convert_to_webp_unified.php' style='margin: 0; display: inline-block;'>";
                echo "<input type='hidden' name='image_id' value='" . $imageIdValue . "'>";
                echo "<input type='hidden' name='ad_id' value='" . $image['id'] . "'>";
                echo "<input type='hidden' name='image_type' value='" . $imageTypeValue . "'>";
                echo "<input type='hidden' name='current_path' value='" . htmlspecialchars($imagePath) . "'>";
                echo "<input type='hidden' name='found_file' value='" . htmlspecialchars($foundFile) . "'>";
                echo "<button type='submit' name='convert_to_webp' class='webp-button' style='background-color: #28a745; color: white; border: none; padding: 5px 10px; border-radius: 3px; font-size: 11px; cursor: pointer;'>";
                echo "🌟 تحويل إلى WebP";
                echo "</button>";
                echo "</form>";
                
                echo "<br><small style='color: #666; font-weight: bold;'>سيتم تحديث إلى:<br>" . htmlspecialchars($foundFile) . "</small>";
            } elseif (!$fileExists) {
                echo "<span class='status-existing'>❌ ملف غير موجود</span>";
            } elseif ($exactMatch) {
                // للصور المطابقة تماماً - إضافة زر تحويل إلى WebP
                $imageTypeValue = ($image['type'] === 'رئيسية') ? 'main' : 'sub';
                $imageIdValue = ($image['type'] === 'رئيسية') ? $image['id'] : $image['image_id'];
                $foundFile = $foundFiles[0]; // الملف الموجود
                
                echo "<span class='status-matched'>✅ مطابق</span><br>";
                
                // التحقق من أن الملف ليس WebP بالفعل
                $fileExtension = strtolower(pathinfo($foundFile, PATHINFO_EXTENSION));
                if ($fileExtension !== 'webp') {
                    echo "<form method='POST' action='convert_to_webp_unified.php' style='margin: 5px 0; display: inline-block;'>";
                    echo "<input type='hidden' name='image_id' value='" . $imageIdValue . "'>";
                    echo "<input type='hidden' name='ad_id' value='" . $image['id'] . "'>";
                    echo "<input type='hidden' name='image_type' value='" . $imageTypeValue . "'>";
                    echo "<input type='hidden' name='current_path' value='" . htmlspecialchars($imagePath) . "'>";
                    echo "<input type='hidden' name='found_file' value='" . htmlspecialchars($foundFile) . "'>";
                    echo "<button type='submit' name='convert_to_webp' class='webp-button' style='background-color: #28a745; color: white; border: none; padding: 4px 8px; border-radius: 3px; font-size: 10px; cursor: pointer;'>";
                    echo "🌟 تحويل إلى WebP";
                    echo "</button>";
                    echo "</form>";
                } else {
                    echo "<small style='color: #6f42c1; font-weight: bold;'>📄 WebP بالفعل</small>";
                }
            } else {
                echo "<span class='status-none'>-</span>";
            }
            echo "</td>";
            echo "</tr>";
        }
        
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        
        // عرض عدد الصور القابلة للإصلاح
        echo "<div style='background-color: #e8f5e8; padding: 15px; border: 1px solid #4CAF50; border-radius: 5px; margin-top: 15px;'>";
        echo "<h4 style='color: #155724;'>📊 النتيجة النهائية:</h4>";
        echo "<p><strong>عدد الصور القابلة للإصلاح:</strong> <span style='font-weight: bold; color: " . ($fixableImagesCount > 0 ? '#28a745' : '#dc3545') . ";'>" . number_format($fixableImagesCount) . "</span> من أصل " . number_format($countWithoutExtension) . " صورة</p>";
        if ($fixableImagesCount > 0) {
            echo "<p style='color: #155724;'>✅ يمكنك إصلاح هذه الصور باستخدام الأزرار أعلاه</p>";
        } else {
            echo "<p style='color: #721c24;'>⚠️ لا توجد صور قابلة للإصلاح التلقائي</p>";
        }
        echo "</div>";
        
        echo "</div>";
    } else {
        echo "<div style='background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px;'>";
        echo "<p style='color: #155724; font-weight: bold;'>✅ ممتاز! جميع الصور لها امتداد ملف صحيح.</p>";
        echo "</div>";
    }
    
    // جدول الصور التي لها امتداد صحيح
    echo "<div style='background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #155724;'>✅ قائمة الصور التي لها امتداد ملف صحيح:</h3>";
    echo "<p style='color: #155724; margin-bottom: 15px;'><strong>تعليمات:</strong></p>";
    echo "<ul style='color: #155724; margin-bottom: 15px;'>";
    echo "<li>استخدم زر '🔧 إصلاح الامتداد' للصور التي لها امتداد خاطئ في قاعدة البيانات</li>";
    echo "<li>استخدم زر '✏️ تعديل' لتغيير اسم الصورة في قاعدة البيانات</li>";
    echo "<li>استخدم زر '🔄 تحويل إلى WebP' لتحسين أداء الموقع وتوفير مساحة التخزين</li>";
    echo "</ul>";
    
    // فورم إعادة تسمية الملفات على الخادم
    echo "<div style='background-color: #fff3cd; padding: 15px; border: 2px solid #ffeaa7; border-radius: 5px; margin-bottom: 20px;'>";
    echo "<h4 style='color: #856404; margin-top: 0;'>📁 إعادة تسمية الملفات على الخادم</h4>";
    echo "<p style='color: #856404; margin-bottom: 15px;'>استخدم هذا الفورم لإعادة تسمية الملفات الفعلية على الخادم (وليس في قاعدة البيانات).</p>";
    
    echo "<form method='POST' action='rename_file.php' style='display: flex; align-items: center; gap: 10px; flex-wrap: wrap;'>";
    echo "<div style='display: flex; flex-direction: column;'>";
    echo "<label for='old_filename' style='color: #856404; font-weight: bold; font-size: 12px;'>الاسم القديم للملف:</label>";
    echo "<input type='text' id='old_filename' name='old_filename' placeholder='مثال: old_image.jpg' style='padding: 8px; border: 1px solid #ffeaa7; border-radius: 4px; width: 200px;' required>";
    echo "</div>";
    
    echo "<div style='display: flex; flex-direction: column;'>";
    echo "<label for='new_filename' style='color: #856404; font-weight: bold; font-size: 12px;'>الاسم الجديد للملف:</label>";
    echo "<input type='text' id='new_filename' name='new_filename' placeholder='مثال: new_image.webp' style='padding: 8px; border: 1px solid #ffeaa7; border-radius: 4px; width: 200px;' required>";
    echo "</div>";
    
    echo "<div style='display: flex; flex-direction: column; justify-content: end;'>";
    echo "<button type='submit' style='padding: 8px 15px; background: linear-gradient(135deg, #fd7e14, #ffc107); color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;'>";
    echo "📁 إعادة تسمية الملف";
    echo "</button>";
    echo "</div>";
    echo "</form>";
    
    echo "<div style='background-color: #e8f4f8; padding: 10px; border-radius: 4px; margin-top: 15px;'>";
    echo "<h5 style='color: #0c5460; margin: 0 0 5px 0;'>💡 ملاحظات مهمة:</h5>";
    echo "<ul style='color: #0c5460; margin: 5px 0; font-size: 12px;'>";
    echo "<li><strong>هذا يغير اسم الملف الفعلي</strong> على الخادم في مجلد /ads/</li>";
    echo "<li><strong>بعد إعادة التسمية</strong> ستحتاج لتحديث قاعدة البيانات يدوياً</li>";
    echo "<li><strong>تأكد من عدم وجود ملف</strong> بالاسم الجديد لتجنب التضارب</li>";
    echo "<li><strong>أمثلة صحيحة:</strong> image.jpg, photo_2024.webp, car_1234.png</li>";
    echo "</ul>";
    echo "</div>";
    echo "</div>";
    
    // البحث عن الصور التي لها امتداد من جدول ads
    $stmt = $pdo->prepare("
        SELECT id, main_image, created_at 
        FROM ads 
        WHERE main_image IS NOT NULL 
        AND main_image != '' 
        AND (main_image LIKE '%.jpg' 
        OR main_image LIKE '%.jpeg' 
        OR main_image LIKE '%.png' 
        OR main_image LIKE '%.gif' 
        OR main_image LIKE '%.webp' 
        OR main_image LIKE '%.bmp')
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $mainImagesWithExtension = $stmt->fetchAll();
    
    // البحث عن الصور الفرعية التي لها امتداد من جدول ad_images
    $stmt5 = $pdo->prepare("
        SELECT ai.id as image_id, ai.ad_id, ai.image, a.created_at 
        FROM ad_images ai
        LEFT JOIN ads a ON ai.ad_id = a.id
        WHERE ai.image IS NOT NULL 
        AND ai.image != '' 
        AND (ai.image LIKE '%.jpg' 
        OR ai.image LIKE '%.jpeg' 
        OR ai.image LIKE '%.png' 
        OR ai.image LIKE '%.gif' 
        OR ai.image LIKE '%.webp' 
        OR ai.image LIKE '%.bmp')
        ORDER BY a.created_at DESC, ai.id DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt5->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt5->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt5->execute();
    $subImagesWithExtension = $stmt5->fetchAll();
    
    // دمج الصور الرئيسية والفرعية التي لها امتداد
    $imagesWithExtension = [];
    
    // إضافة الصور الرئيسية
    foreach ($mainImagesWithExtension as $image) {
        $imagesWithExtension[] = [
            'id' => $image['id'],
            'image_id' => null,
            'main_image' => $image['main_image'],
            'type' => 'رئيسية',
            'created_at' => $image['created_at']
        ];
    }
    
    // إضافة الصور الفرعية
    foreach ($subImagesWithExtension as $image) {
        $imagesWithExtension[] = [
            'id' => $image['ad_id'],
            'image_id' => $image['image_id'],
            'main_image' => $image['image'],
            'type' => 'فرعية',
            'created_at' => $image['created_at']
        ];
    }
    
    // ترتيب المصفوفة المدمجة: الصور القابلة للتحويل أولاً، ثم حسب التاريخ
    usort($imagesWithExtension, function($a, $b) {
        // تحديد امتداد الملف لكل صورة
        $extensionA = strtolower(pathinfo($a['main_image'], PATHINFO_EXTENSION));
        $extensionB = strtolower(pathinfo($b['main_image'], PATHINFO_EXTENSION));
        
        // الصور القابلة للتحويل (غير WebP) لها أولوية
        $isConvertibleA = in_array($extensionA, ['jpg', 'jpeg', 'png', 'gif', 'bmp']) && $extensionA !== 'webp';
        $isConvertibleB = in_array($extensionB, ['jpg', 'jpeg', 'png', 'gif', 'bmp']) && $extensionB !== 'webp';
        
        // إذا كان أحدهما قابل للتحويل والآخر لا، فالقابل للتحويل يأتي أولاً
        if ($isConvertibleA && !$isConvertibleB) {
            return -1; // A يأتي أولاً
        } elseif (!$isConvertibleA && $isConvertibleB) {
            return 1;  // B يأتي أولاً
        } else {
            // كلاهما قابل للتحويل أو كلاهما غير قابل، رتب حسب التاريخ
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        }
    });
    
    $countWithExtension = count($imagesWithExtension);
    
    // حساب إجمالي الصور التي لها امتداد
    $stmt6 = $pdo->prepare("SELECT COUNT(*) as total FROM ads WHERE main_image IS NOT NULL AND main_image != '' AND (main_image LIKE '%.jpg' OR main_image LIKE '%.jpeg' OR main_image LIKE '%.png' OR main_image LIKE '%.gif' OR main_image LIKE '%.webp' OR main_image LIKE '%.bmp')");
    $stmt6->execute();
    $totalMainImagesWithExtension = $stmt6->fetch()['total'];
    
    $stmt7 = $pdo->prepare("SELECT COUNT(*) as total FROM ad_images WHERE image IS NOT NULL AND image != '' AND (image LIKE '%.jpg' OR image LIKE '%.jpeg' OR image LIKE '%.png' OR image LIKE '%.gif' OR image LIKE '%.webp' OR image LIKE '%.bmp')");
    $stmt7->execute();
    $totalSubImagesWithExtension = $stmt7->fetch()['total'];
    
    $totalImagesWithExtension = $totalMainImagesWithExtension + $totalSubImagesWithExtension;
    
    // حساب معلومات pagination
    $totalImages = $totalImagesWithExtension;
    $totalPages = ceil($totalImages / $perPage);
    $currentPage = $page;
    
    // حساب عدد الصور WebP الموجودة
    $stmt8 = $pdo->prepare("SELECT COUNT(*) as total FROM ads WHERE main_image IS NOT NULL AND main_image != '' AND main_image LIKE '%.webp'");
    $stmt8->execute();
    $totalMainWebP = $stmt8->fetch()['total'];
    
    $stmt9 = $pdo->prepare("SELECT COUNT(*) as total FROM ad_images WHERE image IS NOT NULL AND image != '' AND image LIKE '%.webp'");
    $stmt9->execute();
    $totalSubWebP = $stmt9->fetch()['total'];
    
    $totalWebP = $totalMainWebP + $totalSubWebP;
    $webpPercentage = $totalImagesWithExtension > 0 ? round(($totalWebP / $totalImagesWithExtension) * 100, 2) : 0;
    
    echo "<div style='background-color: #f8f9fa; padding: 10px; margin-bottom: 15px; border: 1px solid #dee2e6; border-radius: 5px;'>";
    echo "<p style='color: #495057; margin: 0;'><strong>📊 إحصائيات الصور الصحيحة:</strong></p>";
    echo "<p style='margin: 5px 0;'><strong>إجمالي الصور الصحيحة:</strong> " . number_format($totalImagesWithExtension) . " صورة</p>";
    echo "<p style='margin: 5px 0;'><strong>- الصور الرئيسية:</strong> " . number_format($totalMainImagesWithExtension) . " صورة</p>";
    echo "<p style='margin: 5px 0;'><strong>- الصور الفرعية:</strong> " . number_format($totalSubImagesWithExtension) . " صورة</p>";
    echo "<hr style='margin: 8px 0; border-color: #dee2e6;'>";
    echo "<p style='margin: 5px 0; color: #6f42c1;'><strong>🔄 صور WebP:</strong> " . number_format($totalWebP) . " صورة (" . $webpPercentage . "% من الإجمالي)</p>";
    echo "<hr style='margin: 8px 0; border-color: #dee2e6;'>";
    echo "<p style='margin: 5px 0; color: #007bff;'><strong>📄 الصفحة الحالية:</strong> " . $currentPage . " من " . $totalPages . "</p>";
    echo "<p style='margin: 5px 0; color: #6c757d;'><em>عرض " . number_format($countWithExtension) . " صورة من إجمالي " . number_format($totalImages) . "</em></p>";
    echo "</div>";
    
    if ($countWithExtension > 0) {
        // إضافة روابط pagination قبل الجدول
        if ($totalPages > 1) {
            echo "<div style='background-color: #e9ecef; padding: 15px; margin-bottom: 15px; border-radius: 5px; text-align: center;'>";
            echo "<h4 style='margin: 0 0 10px 0; color: #495057;'>📄 التنقل بين الصفحات</h4>";
            
            // زر الصفحة السابقة
            if ($currentPage > 1) {
                echo "<a href='?page=" . ($currentPage - 1) . "' style='background: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin: 0 5px;'>⬅️ السابقة</a>";
            }
            
            // أرقام الصفحات
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);
            
            for ($i = $startPage; $i <= $endPage; $i++) {
                if ($i == $currentPage) {
                    echo "<span style='background: #28a745; color: white; padding: 8px 12px; border-radius: 4px; margin: 0 2px; font-weight: bold;'>$i</span>";
                } else {
                    echo "<a href='?page=$i' style='background: #6c757d; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; margin: 0 2px;'>$i</a>";
                }
            }
            
            // زر الصفحة التالية
            if ($currentPage < $totalPages) {
                echo "<a href='?page=" . ($currentPage + 1) . "' style='background: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin: 0 5px;'>التالية ➡️</a>";
            }
            
            echo "<br><small style='color: #6c757d; margin-top: 10px; display: block;'>الانتقال السريع: ";
            echo "<a href='?page=1' style='color: #007bff;'>الأولى</a> | ";
            echo "<a href='?page=$totalPages' style='color: #007bff;'>الأخيرة</a>";
            echo "</small>";
            echo "</div>";
        }
        
        echo "<div style='max-height: 600px; overflow-y: auto; background-color: white; padding: 10px; border: 1px solid #ddd;'>";
        echo "<table border='1' cellpadding='8' cellspacing='0' style='width: 100%; border-collapse: collapse; font-size: 12px;'>";
        echo "<thead style='background-color: #28a745; color: white;'>";
        echo "<tr><th>#</th><th>ID الإعلان<br><small>تاريخ الإنشاء</small></th><th>نوع الصورة</th><th>ID الصورة</th><th>مسار الصورة</th><th>نوع الملف</th><th>هل الصورة موجودة؟</th><th>معلومات إضافية</th><th>إصلاح الامتداد</th><th>تعديل الاسم</th><th>تحويل إلى WebP</th></tr>";
        echo "</thead>";
        echo "<tbody>";
        
        $adsDirectory = '../ads/'; // مسار مجلد الصور
        $wrongExtensionCount = 0; // عدد الصور ذات الامتداد الخاطئ
        $hasAlternativesCount = 0; // عدد الصور التي لها بدائل متاحة
        
        foreach ($imagesWithExtension as $index => $image) {
            $rowColor = ($index % 2 == 0) ? '#f9f9f9' : '#ffffff';
            
            // تحديد نوع الصورة ولونها
            $imageTypeLabel = $image['type'];
            $imageTypeLabelColor = ($image['type'] === 'رئيسية') ? '#007bff' : '#28a745';
            
            // استخراج اسم الملف من مسار الصورة
            $imagePath = $image['main_image'];
            
            // إزالة البادئة ads/ إذا كانت موجودة
            $fileName = basename($imagePath);
            if (strpos($imagePath, 'ads/') === 0) {
                $fileName = substr($imagePath, 4); // إزالة "ads/"
            }
            
            // التأكد من أن اسم الملف لا يحتوي على مسارات إضافية
            $fileName = basename($fileName);
            
            // البحث عن الملف في مجلد ads واكتشاف نوع الصورة
            $fileExists = false;
            $imageType = "غير محدد";
            $imageTypeColor = "#6c757d";
            $additionalInfo = "";
            $wrongExtension = false;
            $correctFileName = "";
            $alternativeFiles = []; // للملفات البديلة
            
            if (is_dir($adsDirectory)) {
                $fullFilePath = $adsDirectory . $fileName;
                if (file_exists($fullFilePath)) {
                    $fileExists = true;
                    
                    // اكتشاف نوع الصورة الفعلي
                    $imageInfo = @getimagesize($fullFilePath);
                    if ($imageInfo !== false) {
                        $mimeType = $imageInfo['mime'];
                        switch ($mimeType) {
                            case 'image/jpeg':
                                $imageType = "JPEG";
                                $imageTypeColor = "#28a745";
                                break;
                            case 'image/png':
                                $imageType = "PNG";
                                $imageTypeColor = "#17a2b8";
                                break;
                            case 'image/webp':
                                $imageType = "WebP";
                                $imageTypeColor = "#6f42c1";
                                break;
                            case 'image/gif':
                                $imageType = "GIF";
                                $imageTypeColor = "#fd7e14";
                                break;
                            case 'image/bmp':
                                $imageType = "BMP";
                                $imageTypeColor = "#20c997";
                                break;
                            default:
                                $imageType = "نوع آخر";
                                $imageTypeColor = "#6c757d";
                        }
                        $imageType .= "<br><small style='color: #666;'>" . $imageInfo[0] . "x" . $imageInfo[1] . "</small>";
                        
                        // معلومات إضافية
                        $fileSizeKB = number_format(filesize($fullFilePath) / 1024, 2);
                        $additionalInfo = "الحجم: " . $fileSizeKB . " كيلوبايت";
                    } else {
                        $imageType = "⚠️ ملف تالف";
                        $imageTypeColor = "#dc3545";
                        $additionalInfo = "الملف غير قابل للقراءة";
                    }
                } else {
                    // الملف غير موجود بالامتداد المذكور، دعنا نبحث عن ملف بنفس الاسم وامتداد مختلف
                    
                    // إزالة البادئة ads/ إذا كانت موجودة لضمان البحث الصحيح
                    $cleanFileName = $fileName;
                    if (strpos($imagePath, 'ads/') === 0) {
                        $cleanFileName = substr($imagePath, 4);
                        $cleanFileName = basename($cleanFileName);
                    }
                    
                    $baseNameWithoutExt = pathinfo($cleanFileName, PATHINFO_FILENAME);
                    $originalExtension = strtolower(pathinfo($cleanFileName, PATHINFO_EXTENSION));
                    $files = scandir($adsDirectory);
                    
                    // البحث عن جميع الملفات البديلة
                    foreach ($files as $file) {
                        if ($file === '.' || $file === '..') continue;
                        
                        $fileBaseName = pathinfo($file, PATHINFO_FILENAME);
                        $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        
                        if ($fileBaseName === $baseNameWithoutExt && $file !== $cleanFileName) {
                            // وجدنا ملف بنفس الاسم لكن امتداد مختلف
                            $alternativeFiles[] = $file;
                            
                            if (!$wrongExtension) { // أول ملف بديل نجده
                                $wrongExtension = true;
                                $wrongExtensionCount++; // عدّ الصور ذات الامتداد الخاطئ
                                $correctFileName = $file;
                                $correctFilePath = $adsDirectory . $file;
                                
                                // اكتشاف نوع الصورة الصحيح
                                $imageInfo = @getimagesize($correctFilePath);
                                if ($imageInfo !== false) {
                                    $mimeType = $imageInfo['mime'];
                                    switch ($mimeType) {
                                        case 'image/jpeg':
                                            $imageType = "JPEG (امتداد خاطئ)";
                                            $imageTypeColor = "#ffc107";
                                            break;
                                        case 'image/png':
                                            $imageType = "PNG (امتداد خاطئ)";
                                            $imageTypeColor = "#ffc107";
                                            break;
                                        case 'image/webp':
                                            $imageType = "WebP (امتداد خاطئ)";
                                            $imageTypeColor = "#ffc107";
                                            break;
                                        case 'image/gif':
                                            $imageType = "GIF (امتداد خاطئ)";
                                            $imageTypeColor = "#ffc107";
                                            break;
                                        case 'image/bmp':
                                            $imageType = "BMP (امتداد خاطئ)";
                                            $imageTypeColor = "#ffc107";
                                            break;
                                    }
                                    $imageType .= "<br><small style='color: #666;'>" . $imageInfo[0] . "x" . $imageInfo[1] . "</small>";
                                    
                                    $fileSizeKB = number_format(filesize($correctFilePath) / 1024, 2);
                                    $additionalInfo = "الحجم: " . $fileSizeKB . " كيلوبايت<br><small style='color: #dc3545;'>الملف الصحيح: " . $correctFileName . "</small>";
                                }
                            }
                        }
                    }
                    
                    if (!$wrongExtension) {
                        // لم نجد أي ملف بديل، لكن دعنا نبحث عن ملفات مشابهة بدون امتداد أو بامتدادات أخرى
                        $imageType = "❌ غير موجود";
                        $imageTypeColor = "#dc3545";
                        
                        // البحث المتقدم عن ملفات مشابهة
                        $searchExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'];
                        $foundAlternatives = [];
                        
                        // البحث الأساسي - نفس الاسم امتدادات مختلفة
                        foreach ($searchExtensions as $ext) {
                            if ($ext === $originalExtension) continue; // تجاهل الامتداد الأصلي
                            
                            $searchFile = $baseNameWithoutExt . '.' . $ext;
                            if (file_exists($adsDirectory . $searchFile)) {
                                $foundAlternatives[] = $searchFile;
                            }
                        }
                        
                        // البحث عن ملف بدون امتداد
                        if (file_exists($adsDirectory . $baseNameWithoutExt)) {
                            $foundAlternatives[] = $baseNameWithoutExt . ' (بدون امتداد)';
                        }
                        
                        // البحث المتقدم - ملفات تحتوي على جزء من الاسم
                        if (empty($foundAlternatives) && strlen($baseNameWithoutExt) > 10) {
                            // إذا لم نجد ملفات بنفس الاسم والاسم طويل، ابحث عن ملفات مشابهة
                            $files = scandir($adsDirectory);
                            $partialMatches = [];
                            
                            // استخراج أجزاء من اسم الملف للبحث
                            $searchParts = [];
                            
                            // البحث عن الأرقام في اسم الملف (مثل timestamp)
                            if (preg_match('/(\d{10,})/', $baseNameWithoutExt, $matches)) {
                                $searchParts[] = $matches[1]; // timestamp
                            }
                            
                            // البحث عن أجزاء النص (إزالة الأرقام والرموز)
                            $textPart = preg_replace('/[^a-zA-Z\s]/', '', $baseNameWithoutExt);
                            $textPart = trim($textPart);
                            if (strlen($textPart) > 5) {
                                $searchParts[] = $textPart;
                            }
                            
                            // البحث في الملفات
                            foreach ($files as $file) {
                                if ($file === '.' || $file === '..' || $file === $fileName) continue;
                                
                                $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                if (in_array($fileExt, $searchExtensions)) {
                                    $fileBase = pathinfo($file, PATHINFO_FILENAME);
                                    
                                    // البحث عن تطابق جزئي
                                    foreach ($searchParts as $part) {
                                        if (strlen($part) > 5 && stripos($fileBase, $part) !== false) {
                                            $partialMatches[] = $file . ' (مشابه: ' . $part . ')';
                                            break; // تجنب التكرار
                                        }
                                    }
                                }
                            }
                            
                            if (!empty($partialMatches)) {
                                $foundAlternatives = array_merge($foundAlternatives, array_slice($partialMatches, 0, 5)); // أول 5 نتائج
                            }
                        }
                        
                        if (!empty($foundAlternatives)) {
                            $hasAlternativesCount++; // عدّ الصور التي لها بدائل
                            $additionalInfo = "النوع الحالي ." . $originalExtension . " غير متوفر<br>";
                            $additionalInfo .= "<strong style='color: #28a745;'>لكن متوفر:</strong><br>";
                            $additionalInfo .= "<small style='color: #007bff;'>" . implode('<br>', $foundAlternatives) . "</small>";
                        } else {
                            $additionalInfo = "الملف مفقود من الخادم";
                        }
                    }
                }
            }
            
            $statusIcon = $fileExists ? "✅" : ($wrongExtension ? "⚠️" : "❌");
            $statusColor = $fileExists ? "#28a745" : ($wrongExtension ? "#ffc107" : "#dc3545");
            $statusText = $fileExists ? "موجود" : ($wrongExtension ? "امتداد خاطئ" : "مفقود");
            
            echo "<tr style='background-color: $rowColor;'>";
            echo "<td style='text-align: center; font-weight: bold; color: #495057; background-color: #f8f9fa;'>" . ($index + 1) . "</td>";
            
            // عمود ID الإعلان مع التاريخ
            echo "<td style='text-align: center; font-weight: bold;'>";
            echo "<a href='https://motorssooq.com/en/ad-details/" . htmlspecialchars($image['id']) . "' target='_blank' style='color: #007bff; text-decoration: none;'>" . htmlspecialchars($image['id']) . "</a>";
            
            // عرض التاريخ
            if (!empty($image['created_at'])) {
                $createdDate = date('Y-m-d', strtotime($image['created_at']));
                $createdTime = date('H:i', strtotime($image['created_at']));
                $timeAgo = '';
                
                // حساب المدة الزمنية
                $timestamp = strtotime($image['created_at']);
                $now = time();
                $diff = $now - $timestamp;
                
                if ($diff < 60) {
                    $timeAgo = 'الآن';
                } elseif ($diff < 3600) {
                    $minutes = floor($diff / 60);
                    $timeAgo = $minutes . ' دقيقة';
                } elseif ($diff < 86400) {
                    $hours = floor($diff / 3600);
                    $timeAgo = $hours . ' ساعة';
                } elseif ($diff < 2592000) {
                    $days = floor($diff / 86400);
                    $timeAgo = $days . ' يوم';
                } elseif ($diff < 31536000) {
                    $months = floor($diff / 2592000);
                    $timeAgo = $months . ' شهر';
                } else {
                    $years = floor($diff / 31536000);
                    $timeAgo = $years . ' سنة';
                }
                
                echo "<br><small style='color: #6c757d; font-weight: normal;'>" . $createdDate . "</small>";
                echo "<br><small style='color: #28a745; font-weight: normal;'>منذ " . $timeAgo . "</small>";
            }
            echo "</td>";
            
            echo "<td style='text-align: center; color: $imageTypeLabelColor; font-weight: bold;'>" . $imageTypeLabel . "</td>";
            echo "<td style='text-align: center; font-weight: bold;'>" . ($image['image_id'] ? $image['image_id'] : '-') . "</td>";
            echo "<td>" . htmlspecialchars($image['main_image']) . "</td>";
            echo "<td style='text-align: center; color: $imageTypeColor; font-weight: bold;'>";
            echo $imageType;
            echo "</td>";
            echo "<td style='text-align: center; color: $statusColor; font-weight: bold;'>";
            echo $statusIcon . " " . $statusText;
            echo "</td>";
            echo "<td style='text-align: center; color: #666; font-size: 11px;'>";
            echo $additionalInfo;
            echo "</td>";
            
            // عمود زر إصلاح الامتداد الخاطئ
            echo "<td style='text-align: center;'>";
            if ($wrongExtension && !empty($correctFileName)) {
                // يمكن إصلاح الامتداد - ملف واحد بديل
                $imageIdValue = ($image['type'] === 'رئيسية') ? $image['id'] : $image['image_id'];
                $imageTypeValue = ($image['type'] === 'رئيسية') ? 'main' : 'sub';
                
                echo "<form method='POST' action='update_extension.php' style='margin: 0; display: inline-block;'>";
                echo "<input type='hidden' name='image_id' value='" . $imageIdValue . "'>";
                echo "<input type='hidden' name='ad_id' value='" . $image['id'] . "'>";
                echo "<input type='hidden' name='image_type' value='" . $imageTypeValue . "'>";
                echo "<input type='hidden' name='current_path' value='" . htmlspecialchars($imagePath) . "'>";
                echo "<input type='hidden' name='found_file' value='" . htmlspecialchars($correctFileName) . "'>";
                echo "<button type='submit' name='add_extension' class='fix-button'>";
                echo "🔧 إصلاح الامتداد";
                echo "</button>";
                echo "</form>";
                echo "<br><small style='color: #666; font-weight: bold;'>سيتم تحديث إلى:<br>" . htmlspecialchars($correctFileName) . "</small>";
                
                // إذا كان هناك ملفات بديلة أخرى، اعرضها
                if (count($alternativeFiles) > 1) {
                    echo "<br><small style='color: #17a2b8;'>بدائل أخرى: " . implode(', ', array_slice($alternativeFiles, 1)) . "</small>";
                }
                
            } elseif (!empty($foundAlternatives) && count($foundAlternatives) > 0) {
                // لا يوجد ملف بنفس الاسم، لكن توجد بدائل مشابهة
                echo "<span style='color: #17a2b8; font-size: 11px; font-weight: bold;'>🔍 بدائل متاحة</span>";
                echo "<br><small style='color: #666;'>يتطلب تدخل يدوي</small>";
                
            } elseif ($fileExists) {
                echo "<span style='color: #28a745; font-size: 11px; font-weight: bold;'>✅ صحيح</span>";
            } else {
                echo "<span style='color: #dc3545; font-size: 11px;'>❌ غير متاح</span>";
            }
            echo "</td>";
            
            // عمود تعديل الاسم
            echo "<td style='text-align: center;'>";
            
            $imageIdValue = ($image['type'] === 'رئيسية') ? $image['id'] : $image['image_id'];
            $imageTypeValue = ($image['type'] === 'رئيسية') ? 'main' : 'sub';
            $currentFileName = basename($imagePath);
            $currentFileNameNoExt = pathinfo($currentFileName, PATHINFO_FILENAME);
            
            echo "<form method='POST' action='rename_image.php' style='margin: 0; display: inline-block;'>";
            echo "<input type='hidden' name='image_id' value='" . $imageIdValue . "'>";
            echo "<input type='hidden' name='ad_id' value='" . $image['id'] . "'>";
            echo "<input type='hidden' name='image_type' value='" . $imageTypeValue . "'>";
            echo "<input type='hidden' name='current_path' value='" . htmlspecialchars($imagePath) . "'>";
            echo "<input type='text' name='new_name' class='rename-input' placeholder='اسم جديد' value='" . htmlspecialchars($currentFileNameNoExt) . "' required>";
            echo "<br>";
            echo "<button type='submit' name='rename_image' class='rename-button' style='margin-top: 2px;'>";
            echo "✏️ تعديل";
            echo "</button>";
            echo "</form>";
            echo "<small style='color: #666; font-size: 9px;'>سيصبح: ads/الاسم_الجديد</small>";
            
            echo "</td>";
            
            // عمود زر التحويل إلى WebP
            echo "<td style='text-align: center;'>";
            if ($fileExists && $imageType !== "❌ غير موجود" && $imageType !== "⚠️ ملف تالف") {
                // التحقق من نوع الملف الحالي
                $currentExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $supportedFormats = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
                
                if (in_array($currentExtension, $supportedFormats) && $currentExtension !== 'webp') {
                    // يمكن التحويل
                    $imageIdValue = ($image['type'] === 'رئيسية') ? $image['id'] : $image['image_id'];
                    $imageTypeValue = ($image['type'] === 'رئيسية') ? 'main' : 'sub';
                    
                    echo "<form method='POST' action='convert_to_webp_unified.php' style='margin: 0; display: inline-block;'>";
                    echo "<input type='hidden' name='image_id' value='" . $imageIdValue . "'>";
                    echo "<input type='hidden' name='ad_id' value='" . $image['id'] . "'>";
                    echo "<input type='hidden' name='image_type' value='" . $imageTypeValue . "'>";
                    echo "<input type='hidden' name='current_path' value='" . htmlspecialchars($imagePath) . "'>";
                    echo "<input type='hidden' name='found_file' value='" . htmlspecialchars($fileName) . "'>";
                    echo "<button type='submit' name='convert_to_webp' class='convert-button'>";
                    echo "🔄 تحويل إلى WebP";
                    echo "</button>";
                    echo "</form>";
                    echo "<br><small style='color: #666; font-weight: bold;'>من ." . $currentExtension . " إلى .webp</small>";
                } elseif ($currentExtension === 'webp') {
                    echo "<span style='color: #6f42c1; font-size: 11px; font-weight: bold;'>✅ WebP بالفعل</span>";
                } else {
                    echo "<span style='color: #6c757d; font-size: 11px;'>غير مدعوم</span>";
                }
            } else {
                echo "<span style='color: #dc3545; font-size: 11px;'>❌ غير متاح</span>";
            }
            echo "</td>";
            echo "</tr>";
        }
        
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        
        // عرض إحصائية الامتدادات الخاطئة
        if ($wrongExtensionCount > 0) {
            echo "<div style='background-color: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px; margin-top: 15px;'>";
            echo "<h4 style='color: #856404;'>⚠️ تحذير - امتدادات خاطئة:</h4>";
            echo "<p style='color: #856404; margin: 5px 0;'><strong>عدد الصور ذات الامتداد الخاطئ:</strong> <span style='font-weight: bold; color: #dc3545;'>" . number_format($wrongExtensionCount) . "</span> صورة</p>";
            echo "<p style='color: #856404; margin: 5px 0;'>هذه الصور موجودة على الخادم لكن بامتداد مختلف عن المذكور في قاعدة البيانات.</p>";
            echo "<p style='color: #856404; margin: 5px 0;'>استخدم زر '🔧 إصلاح الامتداد' لتحديث قاعدة البيانات.</p>";
            echo "</div>";
        }
        
        // عرض إحصائية البدائل المتاحة
        if ($hasAlternativesCount > 0) {
            echo "<div style='background-color: #d1ecf1; padding: 15px; border: 1px solid #bee5eb; border-radius: 5px; margin-top: 15px;'>";
            echo "<h4 style='color: #0c5460;'>🔍 ملفات لها بدائل متاحة:</h4>";
            echo "<p style='color: #0c5460; margin: 5px 0;'><strong>عدد الصور التي لها بدائل:</strong> <span style='font-weight: bold; color: #17a2b8;'>" . number_format($hasAlternativesCount) . "</span> صورة</p>";
            echo "<p style='color: #0c5460; margin: 5px 0;'>هذه الصور غير موجودة بالامتداد المطلوب، لكن تتوفر بدائل:</p>";
            echo "<ul style='color: #0c5460; margin: 5px 0 5px 20px;'>";
            echo "<li><strong>بدائل مطابقة:</strong> نفس اسم الملف لكن امتداد مختلف</li>";
            echo "<li><strong>بدائل مشابهة:</strong> ملفات تحتوي على أجزاء من اسم الملف الأصلي</li>";
            echo "</ul>";
            echo "<p style='color: #0c5460; margin: 5px 0;'>راجع عمود 'معلومات إضافية' لمعرفة البدائل المتاحة.</p>";
            echo "</div>";
        }
        
        // إضافة روابط pagination بعد الجدول
        if ($totalPages > 1) {
            echo "<div style='background-color: #f8f9fa; padding: 15px; margin-top: 20px; border-radius: 5px; text-align: center;'>";
            echo "<h4 style='margin: 0 0 10px 0; color: #495057;'>📄 التنقل بين الصفحات</h4>";
            
            // زر الصفحة السابقة
            if ($currentPage > 1) {
                echo "<a href='?page=" . ($currentPage - 1) . "' style='background: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin: 0 5px;'>⬅️ السابقة</a>";
            }
            
            // أرقام الصفحات
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);
            
            for ($i = $startPage; $i <= $endPage; $i++) {
                if ($i == $currentPage) {
                    echo "<span style='background: #28a745; color: white; padding: 8px 12px; border-radius: 4px; margin: 0 2px; font-weight: bold;'>$i</span>";
                } else {
                    echo "<a href='?page=$i' style='background: #6c757d; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; margin: 0 2px;'>$i</a>";
                }
            }
            
            // زر الصفحة التالية
            if ($currentPage < $totalPages) {
                echo "<a href='?page=" . ($currentPage + 1) . "' style='background: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin: 0 5px;'>التالية ➡️</a>";
            }
            
            echo "<br><small style='color: #6c757d; margin-top: 10px; display: block;'>";
            echo "الصفحة $currentPage من $totalPages - إجمالي " . number_format($totalImages) . " صورة<br>";
            echo "الانتقال السريع: ";
            echo "<a href='?page=1' style='color: #007bff;'>الأولى</a> | ";
            echo "<a href='?page=$totalPages' style='color: #007bff;'>الأخيرة</a>";
            echo "</small>";
            echo "</div>";
        }
        
    } else {
        echo "<p style='color: #6c757d; font-style: italic;'>لا توجد صور صحيحة للعرض حالياً.</p>";
    }
    
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 20px;'>";
    echo "<p style='color: #721c24;'><strong>❌ خطأ في حساب الصور:</strong><br>";
    echo htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<hr>";

// معالجة الاستعلام عن الإعلان
if (isset($_POST['search_ad']) && !empty($_POST['ad_id'])) {
    $adId = intval($_POST['ad_id']);
    
    echo "<h3>نتائج البحث عن إعلان رقم: " . $adId . "</h3>";
    
    try {
        // استخدام بيانات قاعدة البيانات المحفوظة مباشرة
        $host = $dbConfig['host'];
        $port = $dbConfig['port'];
        $dbname = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];
        
        if (empty($dbname)) {
            throw new Exception("معلومات قاعدة البيانات غير موجودة");
        }
        
        // الاتصال بقاعدة البيانات
        $dsn = "mysql:host=$host:$port;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        // البحث في جدول ads
        $stmt = $pdo->prepare("SELECT * FROM ads WHERE id = ?");
        $stmt->execute([$adId]);
        $ad = $stmt->fetch();
        
        if ($ad) {
            echo "<div style='background-color: #e8f5e8; padding: 15px; border: 1px solid #4CAF50; border-radius: 5px;'>";
            echo "<h4 style='color: green;'>تم العثور على الإعلان!</h4>";
            echo "<table border='1' cellpadding='10' cellspacing='0' style='width: 100%; border-collapse: collapse;'>";
            
            foreach ($ad as $column => $value) {
                echo "<tr>";
                echo "<td style='background-color: #f2f2f2; font-weight: bold;'>" . htmlspecialchars($column) . "</td>";
                echo "<td>" . htmlspecialchars($value) . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
            echo "</div>";
        } else {
            echo "<div style='background-color: #ffebee; padding: 15px; border: 1px solid #f44336; border-radius: 5px;'>";
            echo "<p style='color: red; font-weight: bold;'>لم يتم العثور على إعلان برقم: " . $adId . "</p>";
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div style='background-color: #ffebee; padding: 15px; border: 1px solid #f44336; border-radius: 5px;'>";
        echo "<p style='color: red;'><strong>خطأ في الاتصال بقاعدة البيانات:</strong><br>";
        echo htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
    
    echo "<hr>";
}

// معالجة البحث
if (isset($_POST['search']) && !empty($_POST['filename'])) {
    $searchFilename = $_POST['filename'];
    $currentPath = __DIR__;
    $parentPath = dirname(__DIR__);
    
    echo "<h3>نتائج البحث عن: " . htmlspecialchars($searchFilename) . "</h3>";
    
    function searchFiles($dir, $filename) {
        $results = [];
        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir),
                RecursiveIteratorIterator::SELF_FIRST
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $basename = $file->getBasename();
                    if (stripos($basename, $filename) !== false) {
                        $results[] = $file->getPathname();
                    }
                }
            }
        } catch (Exception $e) {
            // تجاهل الأخطاء في المجلدات غير المتاحة
        }
        
        return $results;
    }
    
    // المرحلة الأولى: البحث في المسار الحالي
    echo "<h4 style='color: blue;'>المرحلة الأولى: البحث في المسار الحالي</h4>";
    echo "<p><strong>المسار:</strong> " . htmlspecialchars($currentPath) . "</p>";
    
    $currentResults = searchFiles($currentPath, $searchFilename);
    
    if (!empty($currentResults)) {
        echo "<ul style='background-color: #e8f5e8; padding: 10px;'>";
        foreach ($currentResults as $file) {
            echo "<li>" . htmlspecialchars($file) . "</li>";
        }
        echo "</ul>";
        echo "<p style='color: green;'>تم العثور على " . count($currentResults) . " ملف(ات) في المسار الحالي</p>";
    } else {
        echo "<p style='color: orange;'>لم يتم العثور على أي ملفات في المسار الحالي</p>";
    }
    
    echo "<hr>";
    
    // المرحلة الثانية: البحث في المسار الأب
    echo "<h4 style='color: purple;'>المرحلة الثانية: البحث في المسار الأب</h4>";
    echo "<p><strong>المسار:</strong> " . htmlspecialchars($parentPath) . "</p>";
    
    $parentResults = searchFiles($parentPath, $searchFilename);
    
    if (!empty($parentResults)) {
        echo "<ul style='background-color: #f0e8ff; padding: 10px;'>";
        foreach ($parentResults as $file) {
            echo "<li>" . htmlspecialchars($file) . "</li>";
        }
        echo "</ul>";
        echo "<p style='color: green;'>تم العثور على " . count($parentResults) . " ملف(ات) في المسار الأب</p>";
    } else {
        echo "<p style='color: orange;'>لم يتم العثور على أي ملفات في المسار الأب</p>";
    }
    
    // ملخص النتائج
    $totalResults = count($currentResults) + count($parentResults);
    echo "<hr>";
    echo "<h4 style='color: red;'>ملخص النتائج:</h4>";
    echo "<p><strong>إجمالي الملفات الموجودة:</strong> " . $totalResults . " ملف</p>";
    echo "<p><strong>في المسار الحالي:</strong> " . count($currentResults) . " ملف</p>";
    echo "<p><strong>في المسار الأب:</strong> " . count($parentResults) . " ملف</p>";
    
    if ($totalResults == 0) {
        echo "<p style='color: red; font-weight: bold;'>لم يتم العثور على أي ملفات تحتوي على: " . htmlspecialchars($searchFilename) . "</p>";
    }
    
    echo "<hr>";
}

?>

<h2>الاستعلام باستخدام رقم الإعلان</h2>
<form method="POST" action="">
    <label for="ad_id">رقم الإعلان (ID):</label><br>
    <input type="number" id="ad_id" name="ad_id" placeholder="أدخل رقم الإعلان" style="width: 300px; padding: 8px; margin: 5px 0;" value="<?php echo isset($_POST['ad_id']) ? htmlspecialchars($_POST['ad_id']) : ''; ?>" required><br><br>
    
    <input type="submit" name="search_ad" value="البحث عن الإعلان" style="padding: 10px 20px; background-color: #28a745; color: white; border: none; cursor: pointer; border-radius: 3px;">
</form>

<p><small>ملاحظة: سيتم البحث في جدول ads باستخدام رقم الإعلان</small></p>

<hr>

<h2>البحث في الملفات</h2>
<form method="POST" action="">
    <label for="filename">اسم الملف للبحث عنه:</label><br>
    <input type="text" id="filename" name="filename" placeholder="اكتب اسم الملف أو جزء منه" style="width: 300px; padding: 5px;" value="<?php echo isset($_POST['filename']) ? htmlspecialchars($_POST['filename']) : ''; ?>"><br><br>
    
    <input type="submit" name="search" value="بحث" style="padding: 10px 20px; background-color: #007cba; color: white; border: none; cursor: pointer;">
</form>

<p><small>ملاحظة: البحث سيتم في المجلد الحالي وجميع المجلدات الفرعية</small></p>