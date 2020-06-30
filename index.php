<?php
// ini_set('log_errors','On');
// ini_set('display_errors','Off');
// ini_set('error_reporting', E_ALL );
// define('WP_DEBUG', false);
// define('WP_DEBUG_LOG', true);
// define('WP_DEBUG_DISPLAY', false);

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    throw new Exception(sprintf('Please run "composer require google/apiclient:~2.0" in "%s"', __DIR__));
}
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



if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {

    // $client->authenticate($_SESSION['access_token']);
    $client->setAccessToken($_SESSION['access_token']);

    // Define service object for making API requests.
    $service = new Google_Service_YouTube($client);


    if (!isset($_POST['url'])) {
        echo '<strong style="color:red">NO urls found</strong>';
    } else {
        // $youtube_id = str_split(explode('?v=', $_POST['url'])[1], 11)[0];
        $youtube_id = $_POST['url'];


        // GET VIDEO DETAILS
        $queryParams = [
            'id' => $youtube_id
        ];
        $video = $service->videos->listVideos('snippet', $queryParams);
        // echo json_encode($video['items'][0]['snippet']);

        // GET STREAM DATA
        $youtube_video_info = explode('"streamingData":', urldecode(file_get_contents('https://www.youtube.com/get_video_info?html5=1&video_id=' . $youtube_id)));
        // echo json_encode($youtube_video_info[1]);
        $i_tags = json_decode(explode(',"playbackTracking"', $youtube_video_info[1])[0]);
        // echo json_encode($i_tags);
    }
}else{
    $redirect_uri = 'http://thevirtualcoding.com/yt_auto/oauth2callback.php';
    // $redirect_uri = 'https://twinsa.net/yt_auto/oauth2callback.php';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YT AutoLoad</title>
</head>

<body>
    <style>
        *{
            border-radius: 2px;
            box-sizing: border-box;
        }
        table{
            border-collapse: collapse;
            width: 100%;
            height: 100%;
        }
        td{
            padding: 5px 6px;
            border: 1px solid #000;
        }
        form{
            display: flex;
            max-width: 800px;
            margin: auto;
            flex-direction: column;
            justify-content: center;
        }
        form * {
            margin: 10px auto;
            font-size: 1.3rem;
        }
        .btn{
            background-color: #449d44;color:#fff;padding:8px 12px;display:block;
            max-width: 300px;
        }
    </style>
    <form action="./" method="post">
        <label for="url">Enter video ID</label>
        <input type="text" name="url">
        <button class="btn" type="submit">GET INFO</button>
    </form>
    <section style="display: flex;justify-content:center;max-width:800px;margin:auto;flex-wrap: wrap;">
        <?php
            if (isset($i_tags)) {
                // foreach ([$i_tags->adaptiveFormats[0]] as $itag) {
                foreach ($i_tags->adaptiveFormats as $itag) {
                    if(!empty($itag->contentLength)){
                        echo '<div style="padding: 2vw;background:#e3e3e3;box-sizing: border-box;">
                            <table>
                                <tr>
                                    <td>
                                        Title
                                    </td>
                                    <td>
                                        '. $video['items'][0]['snippet']['title'] .'
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        itag
                                    </td>
                                    <td>
                                        '. $itag->itag .'
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        mimeType
                                    </td>
                                    <td>
                                    '. $itag->mimeType .'
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        url
                                    </td>
                                    <td>
                                        <form action="./dlNup.php" method="post">
                                            <input hidden type="text" value="'.$itag->url.'" name="download">
                                            <input hidden type="text" value="'.$itag->contentLength.'" name="contentLength">
                                            <input hidden type="text" value="'.$youtube_id.'" name="id">
                                            <input hidden type="text" value="'.$video['items'][0]['snippet']['description'].'" name="description">
                                            <input hidden type="text" value="'.$video['items'][0]['snippet']['title'].'" name="title">
                                            <input hidden type="text" value="'.$video['items'][0]['snippet']['categoryId'].'" name="category">
                                            <input hidden type="text" value="'.implode(',', $video['items'][0]['snippet']['tags']).'" name="tags">
                                            <button class="btn" type="submit">'. $itag->qualityLabel .'</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        SIZE
                                    </td>
                                    <td>
                                        '. +$itag->contentLength / (1024*1024) .'MB
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Category
                                    </td>
                                    <td>
                                        '. $video['items'][0]['snippet']['categoryId'] .'
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Description
                                    </td>
                                    <td>
                                        '. $video['items'][0]['snippet']['description'] .'
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Tags
                                    </td>
                                    <td>
                                        '. implode(', ', $video['items'][0]['snippet']['tags']) .'
                                    </td>
                                </tr>
                            </table>
                        </div>';
                    }
                }
            }
        ?>
    </section>
</body>