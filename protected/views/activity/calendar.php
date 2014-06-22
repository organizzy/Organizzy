<?php
/**
 * @var ActivityController $this
 * @var ActivityCalendar $model
 */


$month = $model->month;
$year = $model->year;

$this->menu=[
    ['label'=> _t('List'), 'url'=>array('index', 'mode' => 'list')],
];

$prevMonth = $model->month - 1;
$prevYear = $model->year;
if ($prevMonth <= 0) {
    $prevMonth = 12;
    $prevYear -= 1;
}

$nextMonth = $model->month + 1;
$nextYear = $model->year;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear += 1;
}

?>
<div class="content-padded">
    <div style="float: right">
        <?php echo CHtml::link('Today',
            array('index'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php echo CHtml::link('<span class="icon icon-left"></span>',
        array('index', 'm' => $prevMonth, 'y' => $prevYear), ['class' => 'btn']) ?>
    <b><?php echo O::app()->dateFormatter->format('MMMM yyyy', mktime(0, 0, 0, $month, 1, $year)) ?></b>
    <?php echo CHtml::link('<span class="icon icon-right"></span>',
        array('index', 'm' => $nextMonth, 'y' => $nextYear), ['class' => 'btn']) ?>
</div>
<?php

$firstDay = $model->firstWeekDay;
$monthLen = $model->numDays;
$eventsPerDay = $model->getNumActivitiesPerDay();

echo '<table class="calendar"><tr>';
for ($i=0; $i<7; $i++) {
    echo '<th>', O::app()->locale->getWeekDayName($i, 'abbreviated'), '</th>';
}
echo '</tr><tr>';
for ($d = 0; $d < $firstDay; $d++) { echo '<td> </td>';}

for ($i = 1; $i <= $monthLen; $i++) {
    echo '<td>';
    if (isset($eventsPerDay[$i])) {
        $details = '<br>';
        if ($eventsPerDay[$i]['event'] > 0) {
            $details .= '<span class="fa fa-clock-o">' . $eventsPerDay[$i]['event'] . '</span> ';
        }
        if ($eventsPerDay[$i]['task'] > 0) {
            $details .= '<span class="fa fa-tasks">' . $eventsPerDay[$i]['task'] . '</span>';
        }
        echo CHtml::link($i . $details, array('day', 'date' => sprintf('%04d-%02d-%02d', $model->year, $model->month, $i)));
    } else {
        echo $i;
    }
    echo '</td>';

    $d++;
    if ($d >= 7) {
        echo '</tr><tr>';
        $d = 0;
    }
}

for (; $d < 7; $d++) {echo '<td> </td>';}
echo '</tr></table>';

