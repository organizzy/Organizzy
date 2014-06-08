<?php
/** @var Controller $this */
/** @var string $content */

if (O::app()->isAjaxRequest || isset($_SERVER['HTTP_ORIGIN'])) {
    $data = array(
        'id' => $this->getPageId(),
        'url' => Yii::app()->request->url,
        'title' => $this->getPageTitle(),
        'backUrl' => O::app()->baseUrl . (is_array($this->backUrl) ? $this->createUrl($this->backUrl) : $this->backUrl),
    );
    if (O::app()->session->isStarted) {
        $data['sid'] = O::app()->session->sessionID;
    }

    if ($this->disableCache) {
        $data['disableCache'] = true;
    }

    echo '<!--page:', json_encode($data), '-->', 
        preg_replace(['/\s{2,}/s', '/> </s'], [' ', '><'], $content), 
        '<div id="loader" style="display: none"></div>';
    $this->renderPartial('//layouts/_flash');
    return;
}

$assetBase = O::app()->baseUrl;

function getVersionedUrl($fileName) {
    return O::app()->baseUrl . $fileName;
    $pos = strrpos($fileName, '.');
    return O::app()->baseUrl . substr($fileName, 0, $pos) . '-' . filemtime(O::app()->basePath . '/../web' . $fileName) . substr($fileName, $pos);
    //return O::app()->baseUrl . $fileName . '?m=' . filemtime(O::app()->basePath . '/../web' . $fileName);
}

?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo CHtml::encode($this->pageTitle) ?></title>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link href="<?php echo $assetBase ?>/css/lib.css" rel="stylesheet">
    <link href="<?php echo getVersionedUrl('/css/style.css') ?>" rel="stylesheet">

</head>
<body id="page-<?php echo $this->getPageId() ?>" class="page">
<?php echo $content ?>
<?php $this->renderPartial('//layouts/_flash') ?>
<div id="loader"></div>
<script>window.onload=function(){var d=document,h=d.getElementsByTagName('head')[0],i=function(src, onload) {
var j=d.createElement('script');j.src=src;if(onload)j.onload=onload;h.appendChild(j);
};i('<?php echo $assetBase ?>/js/lib<?php if (isset($_COOKIE['cordova']) && $_COOKIE['cordova'] != '0') echo '-mobile' ?>.js',
function(){i('<?php echo getVersionedUrl('/js/app.js') ?>')})};</script>
</body>
</html>
