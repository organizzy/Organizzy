<?php
/* @var $this UserController */
/* @var $user User */

$this->layout = '//layouts/single';
$this->pageTitle = 'Profile';
if ($user->id == O::app()->user->id) {
    $this->menu = [
        ['label' => 'Edit Profile', 'url' => ['edit']],
        ['label' => 'Edit Account', 'url' => ['account']],
        ['label' => 'Change Photo', 'url' => '#', 'options' => ['onclick' => '$("#Photo_file").click()']]
    ] ;
}
//if (!isset($_GET['return']))
    $this->backUrl = $this->createUrl('/activity');

?>
<?php $this->renderPartial('//layouts/_profile_header',[
        'name' => $user->name, 'description' => $user->aboutMe, 'photo' => $user->photo,
        'photoUploadAction' => ($this->userId == $user->id ? ['uploadPhoto'] : false)
    ]) ?>
<div class="tab-view">
    <div class="selector">
        <a href="#user-view-info" class="active" id="user-view-info-selector">Info</a>
        <a href="#user-view-history" id="user-view-history-selector">History</a>
    </div>
    <div class="content-padded tab-page active" id="user-view-info">
        <?php if ($user->id == $this->userId && !$user->getActivated()) : ?>
            <strong>Your account has not been activated.</strong>
            <?php echo CHtml::link(O::t('organizzy', 'Activate now'), ['activate'], ['class' => 'btn btn-block btn-primary']) ?>
        <?php endif ?>

        <?php
        function showDetail($format, $value, $icon) {
            echo '<p class="profile-detail"><span class="fa fa-', $icon, '"></span> ';
            printf($format, '<b>' . $value . '</b>');
            echo '</p>';
        }

        if ($user->getProfile('birth_date')) {
            showDetail(O::t('organizzy', 'Born on %s'),
                O::app()->dateFormatter->formatDateTime($user->getProfile('birth_date'), 'full', false), 'gift');
        }

        if ($user->getProfile('city')) {
            showDetail(O::t('organizzy', 'Live in %s'), $user->getProfile('city'), 'map-marker');
        }

        if ($user->getProfile('phone')) {
            showDetail(O::t('organizzy', 'Phone number: %s'), $user->getProfile('phone'), 'phone');
        }

        ?>
    </div>
    <div id="user-view-history" class="tab-page">
        <?php
        $this->widget('OListView', [
                'models' => Organization::model()->onlyMine($user->id)
                        ->with(['role.department' => ['select' => 'name']])->findAll(),
                'descriptionAttr' => function($model) {
                        /** @var Organization $model */
                        $position = $model->role->position;
                        if ($model->role->department_id)
                            $position .= ' ' . $model->role->department->name;
                        return sprintf(
                            'As %s since %s',
                            '<b>' . CHtml::encode($position ) . '</b>',
                            O::app()->dateFormatter->formatDateTime($model->role->join_time, 'medium', false)
                        );
                    },
                'photoAttr' => function($model) {
                        /** @var Organization $model */
                        return $model->logo ?: O::app()->getDummyPhoto();
                    },
                'linkAttr' => function($model) {
                        /** @var Organization $model */
                        return ['/organization/view', 'id' => $model->id];
                    }
            ]
        );

        ?>
    </div>
</div>