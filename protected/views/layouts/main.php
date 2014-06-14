<?php
/** @var Controller $this */
/** @var string $content */

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
    //preg_replace(['/\s{2,}/s'], [' '], $content),
    $content,
    '<div id="loader" style="display: none"></div>';
$this->renderPartial('//layouts/_flash');
return;

