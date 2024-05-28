<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'upload-form',
    'enableAjaxValidation' => false,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
));
?>
 
<div class="row">
    <label for="file">Choose File</label>
    <input type="file" name="file" id="file">
</div>

 
<div class="row buttons">
    <?php echo CHtml::submitButton('Upload'); ?>
</div>
 
<?php $this->endWidget(); ?>
