<?php
    require_once __DIR__ . '/vendor/autoload.php';
    session_start();

    $client = new Google_Client();
    $client->setApplicationName('YT AutoUpload');
    $client->setScopes([
        'https://www.googleapis.com/auth/youtube.readonly',
        'https://www.googleapis.com/auth/youtube.upload',
    ]);
    $client->setAuthConfig('client_secrets.json');
    $client->setAccessType('offline');
    
    if (!isset($_SESSION['access_token'])) {
        // $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
        $redirect_uri = 'http://thevirtualcoding.com/yt_auto/oauth2callback.php';
        // $redirect_uri = 'https://twinsa.net/yt_auto/oauth2callback.php';
        header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
    }
    $client->setAccessToken($_SESSION['access_token']);
    $service = new Google_Service_YouTube($client);

    $nVideo = $_POST['video'];
    $newName = $_POST['title'];
    $desc = $_POST['description'];
    $cat = $_POST['category'];
    $tag = $_POST['tags'];

    $video = new Google_Service_YouTube_Video();
    $videoSnippet = new Google_Service_YouTube_VideoSnippet();

    $videoSnippet->setCategoryId($cat);
    $videoSnippet->setDescription($desc);
    $videoSnippet->setTitle($newName);
    $videoSnippet->setTags(explode(',',$tag));
    $video->setSnippet($videoSnippet);

    $videoStatus = new Google_Service_YouTube_VideoStatus();
    $videoStatus->setPrivacyStatus('public');
    $video->setStatus($videoStatus);
    set_time_limit(0);
    $response = $service->videos->insert(
        'snippet,status',
        $video,
        array(
        'data' => file_get_contents($nVideo),
        'mimeType' => 'application/octet-stream',
        'uploadType' => 'multipart'
        )
    );
    unlink($nVideo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Page</title>
    <style>
        .btn{
            background-color: #449d44;
            color:#fff;
            padding:8px 12px;
            display:block;
            max-width: 300px;
            text-align: center;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <?php
        echo '<a class="btn" href="https://www.youtube.com/watch?v='.$response->id.'">View Video</a>';
    ?>
</body>
</html>
</html>
