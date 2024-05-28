<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm */
 
$this->pageTitle = Yii::app()->name . ' - Logout';
?>
 
<h1>Logout</h1>
 
<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'logout-form',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
    )); ?>
 
    <div class="row">
        <?php echo "You are Successfully logged in"; ?>
    </div>
 
    <div class="row buttons">
        <?php echo CHtml::submitButton('Logout', array('name' => 'logout')); ?>
    </div>
 
    <?php $this->endWidget(); ?>
</div><!-- form -->