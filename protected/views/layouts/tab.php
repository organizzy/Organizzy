<?php
/* @var $this Controller */
/* @var string $content */

$menu = [
    array('icon' => 'home', 'id' => 'activity', 'link' => '/activity/index', 'label' => Yii::t('organizzy', 'Home')),
    array('icon' => 'group', 'id' => 'organization', 'link' => '/organization/index', 'label' => Yii::t('organizzy', 'Org')),
    array('icon' => 'clock-o', 'id' => 'event', 'link' => '/event/index', 'label' => Yii::t('organizzy', 'Event')),
    array('icon' => 'tasks', 'id' => 'task', 'link' => '/task/index', 'label' => Yii::t('organizzy', 'Task')),
    array('icon' => 'ellipsis-v', 'id' => '-', 'link' => '#', 'label' => Yii::t('organizzy', 'More')),
];

?>
<?php $this->beginContent('//layouts/main'); ?>
    <nav class="bar bar-tab">
        <?php foreach($menu as $i => $item) : ?>
            <a class="tab-item <?php if ($item['id'] == $this->getId()) echo 'active' ?>"
               id="tab-item-<?php echo $i ?>"
               href="<?php echo $item['link']{0} == '#' ? $item['link'] : $this->createUrl('/' . $item['link']) ?>">

                <span class="icon fa fa-<?php echo $item['icon'] ?>"></span>
                <!-- span class="tab-label"><?php echo $item['label'] ?></span -->
            </a>
        <?php endforeach ?>
    </nav>
<?php
$menu = $this->menu;
if ($menu) $menu[] = 'User';
$menu[] = array('label' => O::t('organizzy', 'Profile'),
                'url' => CHtml::normalizeUrl(['/user/view', 'id' => O::app()->user->id]) . '#return=' . urlencode(O::app()->request->url));
$menu[] = array('label' => O::t('organizzy', 'Log Out'), 'url' => array('/user/logout'));
$this->renderPartial('//layouts/_menu', array('menu' => $menu));
?>

    <div class="content" id="content-<?php echo $this->getPageId() ?>">

        <?php echo $content ?>
    </div><!-- #content -->

<?php $this->endContent(); ?>