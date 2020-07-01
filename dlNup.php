<?php
    $downloadURL = $_POST['download'];
    $id = $_POST['id'];
    $newName = $_POST['title'];
    $desc = $_POST['description'];
    $cat = $_POST['category'];
    $tag = $_POST['tags'];
    $length = $_POST['contentLength'];
    // exit();
    if (!empty($downloadURL) && !filter_var($downloadURL, FILTER_VALIDATE_URL) === false) {
        $downloadURL = urldecode($downloadURL);
        $newfname = __DIR__ . '/videos/'. $id .'.mp4';
        $file_exists = file_exists($newfname);
        if (!empty($downloadURL) && !$file_exists) {

            set_time_limit(0);
            // $file = file_get_contents($downloadURL);
            // file_put_contents($newfname, $file);
            $file = fopen(urldecode($downloadURL), "rb");
            if ($file) {
                $newf = fopen($newfname, "wb");
                if ($newf) {
                    while (!feof($file)) {
                        fwrite($newf, fread($file, 8 * 1024 ), 8 * 1024 );
                    }
                }
            }
            if ($file) {
                fclose($file);
            }
            if ($newf) {
                fclose($newf);
            }
            echo 'success - '.$newfname;

        } else {
            echo "This video aleady exists";
        }
    } else {
        echo "Please provide valid YouTube URL.";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Page</title>
    <style>
        .btn{
            background-color: #449d44;color:#fff;padding:8px 12px;display:block;
            max-width: 300px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php
        echo '<form action="./Upload.php" method="post">
                <input hidden type="text" value="'. $newfname .'" name="video">
                <input hidden type="text" value="'. $desc .'" name="description">
                <input hidden type="text" value="'. $newName .'" name="title">
                <input hidden type="text" value="'. $cat .'" name="category">
                <input hidden type="text" value="'. $tag .'" name="tags">
                <button class="btn" type="submit">Upload</button>
            </form>';
    ?>
</body>
</html>
</html>
