<?php
/* @var $this UserController */
/* @var $user User */

$this->layout = '//layouts/single';
$this->pageTitle = _t('Profile');
if ($user->id == O::app()->user->id) {
    $this->menu = [
        ['label' => _t('Edit Profile'), 'url' => ['edit']],
        ['label' => _t('Edit Account'), 'url' => ['account']],
        ['label' => _t('Change Photo'), 'url' => '#', 'options' => ['onclick' => '$("#Photo_file").click()']]
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
        <a href="#user-view-info" class="active" id="user-view-info-selector"><?php _p('Info') ?></a>
        <a href="#user-view-history" id="user-view-history-selector"><?php _p('History') ?></a>
    </div>
    <div class="content-padded tab-page active" id="user-view-info">
        <?php if ($user->id == $this->userId && !$user->getActivated()) : ?>
            <strong><?php _p('Your account has not been activated'); ?></strong>
            <?php echo CHtml::link(_t('Activate now'), ['activate'], ['class' => 'btn btn-block btn-primary']) ?>
        <?php endif ?>

        <?php
        function showDetail($format, $value, $icon) {
            echo '<p class="profile-detail"><span class="fa fa-', $icon, '"></span> ';
            printf($format, '<b>' . $value . '</b>');
            echo '</p>';
        }

        if ($value = $user->getProfile('birth_date')) {
            showDetail(_t('Born on %s'), O::app()->dateFormatter->formatDateTime($value, 'full', false), 'gift');
        }

        if ($value = $user->getProfile('city')) {
            showDetail(_t('Live in %s'), $value, 'map-marker');
        }

        if ($value = $user->getProfile('phone')) {
            showDetail(_t('Phone number: %s'), $value, 'phone');
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
                        return _t( 'As {position} since {joint-time}',
                            [
                                '{position}' => '<b>' . CHtml::encode($position ) . '</b>',
                                '{joint-time}' => O::app()->dateFormatter->formatDateTime($model->role->join_time, 'medium', false)
                            ]
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