<?php
/* @var $this Controller */
/* @var $model ManageMemberForm */
/* @var $form CActiveForm */

$this->layoutSingle();
$this->pageTitle = 'Manage Member';

?>

<?php if (count($model->roles) == 0) : ?>
    <div class="content-padded">
        <p class="error">There is no other member to be managed.</p>
    </div>
<?php else : ?>
    <?php $form = $this->beginWidget('CActiveForm') ?>
    <div class="card"><?php $this->widget('OListView', [
            'models' => $model->roles,

            'titleAttr' => function($model) {
                    /** @var Role $model */
                    return $model->user->name;
                },

            'descriptionAttr' => function($model) {
                    /** @var Role $model */
                    return $model->position . ($model->isAdmin ? '*' : '');
                },

            'photoAttr' => function($model) {
                    /** @var Role $model */
                    return $model->user->photo ?: O::app()->baseUrl . '/images/dummy_person.gif';
                },

            'checkBox' => function($model) {
                    /** @var Role $model */
                    //return CHtml::checkBox('ManageMemberForm[users_id][]', false, ['value' => $model->user_id]);
                    return [
                        'name' => 'ManageMemberForm[users_id][]',
                        'checked' => false,
                        'value' => $model->user_id,
                    ];
                },

        ]
    );
    ?></div>
    <div class="content-padded">
        <?php if ($model->department_id): ?>
            <div class="row">
                <?php echo $form->dropDownList($model, 'action', $model->getPossibleActions(),[
                        'empty' => [0 => O::t('organizzy', '-- select action --')],
                        'onchange' => '$("#row-submit-button")[this.value==0?"hide":"show"]()',
                    ]) ?>
            </div>
            <div class="row" id="row-submit-button" style="display: none">
                <?php echo CHtml::submitButton(O::t('organizzy', 'Submit'), ['class' => 'btn btn-block btn-primary']); ?>
            </div>
        <?php else: ?>
            <?php echo $form->hiddenField($model, 'action', ['value' => ManageMemberForm::ACTION_KICK]) ?>
            <div class="row">
                <?php echo CHtml::submitButton(O::t('organizzy', 'Kick!'), ['class' => 'btn btn-block btn-primary']); ?>
            </div>
        <?php endif ?>
    </div>
    <?php $this->endWidget(); ?>
<?php endif; ?>
