<?php
// 1. إعداد المجلد الذي سيتم حفظ الملفات المرفوعة داخله
// تأكد من وجود هذا المجلد في نفس المسار أو قم بإنشائه بـ mkdir uploads
$target_dir = "uploads/"; 

$message = "";

// 2. التحقق مما إذا كان المستخدم قد ضغط على زر الرفع وتم إرسال ملف بالفعل
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fileToUpload'])) {
    
    // الحصول على المسار الكامل المستهدف للملف
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    
    // التحقق من إنشاء مجلد الحفظ، وإذا لم يكن موجوداً يتم إنشاؤه تلقائياً
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // 3. عملية نقل الملف من المجلد المؤقت في السيرفر إلى المجلد الدائم
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $message = "<div style='color: green; font-weight: bold;'>تم رفع الملف بنجاح! المسار: " . htmlspecialchars($target_file) . "</div>";
    } else {
        $message = "<div style='color: red; font-weight: bold;'>عذراً، حدث خطأ أثناء رفع الملف. تأكد من صلاحيات المجلد.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مركز رفع ملفات محلي بسيط</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 50px;
            text-align: center;
        }
        .upload-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            display: inline-block;
            min-width: 300px;
        }
        input[type="file"] {
            margin: 20px 0;
            display: block;
            width: 100%;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .message-box {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="upload-container">
    <h2>مركز رفع الملفات للمختبر المحلي</h2>
    
    <form action="" method="post" enctype="multipart/form-data">
        <label>اختر الملف المراد رفعه:</label>
        <input type="file" name="fileToUpload" id="fileToUpload" required>
        <input type="submit" value="رفع الملف الآن" name="submit">
    </form>

    <div class="message-box">
        <?php echo $message; ?>
    </div>
</div>

</body>
</html>