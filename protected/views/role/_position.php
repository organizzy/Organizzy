<?php
/* @var $this Controller */
/* @var $model Role */

?>
<?php echo  CHtml::activeTextField($model,'position',array('maxlength'=>16,'style'=>'display:none','id' => 'position-text')); ?>
<select id="position-select" name="<?php $name = 'position'; echo CHtml::resolveName($model, $name) ?>"
        onchange="if(this.value==0){$('#position-text').show();$(this).remove();}">
    <?php
    foreach ($model->getMostUsedPositions() as $v) {
        echo '<option value="', $v, '">', $v, '</option>';
    }
    ?>
    <option value="0"><?php _p('Other ...') ?></option>
</select>