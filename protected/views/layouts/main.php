<?php
/** @var Controller $this */
/** @var string $content */

$data = array(
    'id' => $this->getPageId(),
    'url' => Yii::app()->request->url,
    'title' => $this->getPageTitle(),
    'backUrl' => (is_array($this->backUrl) ? $this->createUrl($this->backUrl) : $this->backUrl),
);
if (O::app()->session->isStarted) {
    $data['sid'] = O::app()->session->sessionID;
}

if ($this->disableCache) {
    $data['disableCache'] = true;
}
echo '<!--page:', json_encode($data), '-->',  $content;

?>
<div id="loader" style="display: none; /*noinspection CssUnknownTarget*/background-image: url(images/ajax_loader.gif)"></div>

<?php
$flashes = O::app()->user->getFlashes();
if (count($flashes)) :
    ?>
    <div id="flash-container">
        <?php
        foreach($flashes as $key => $message) {
            switch($key) {
                case 'info':
                    $icon = 'info-circle';
                    break;

                case 'success':
                    $icon = 'check-circle';
                    break;

                case 'warning':
                    $icon = 'exclamation-triangle';
                    break;

                case 'error':
                    $icon = 'exclamation-circle';
                    break;

                default:
                    $icon = null;
            }

            echo '<div class="flash flash-' . $key . '">';
            if ($icon) {
                echo '<span class="fa fa-', $icon, '"></span> ';
            }
            echo $message . "</div>\n";
        }
        ?>
    </div>

<?php endif ?>