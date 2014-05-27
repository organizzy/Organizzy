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