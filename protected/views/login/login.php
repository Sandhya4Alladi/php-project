<?php
    $this->pageTitle = Yii::app()->name . ' - Login';
    $this->breadcrumbs = array(
        'Login',
    );
 ?>

 <h1>Login Form</h1>

 <div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'login', //for ajax
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
    ));
     ?>

    <div class="login-form">
        <div class="row">
            <?php echo $form->labelEx($model, 'email'); ?>
            <?php echo $form->textField($model, 'email'); ?>
            <?php echo $form->error($model, 'email'); ?>
        </div>
        <div class="row">
            <?php echo $form->labelEx($model, 'password'); ?>
            <?php echo $form->passwordField($model, 'password'); ?>
            <?php echo $form->error($model, 'password'); ?>
        </div>
        <div class= "row rememberMe">
            <?php echo $form->checkBox($model, 'rememberMe'); ?>
            <?php echo $form->label($model, 'rememberMe'); ?>
            <?php echo $form->error($model, 'rememberMe'); ?>
        </div>
    </div>

     <?php echo $form->errorSummary($model); ?>
     <div>
        <?php echo CHtml::submitButton('login'); ?>
    </div>
    <?php $this->endWidget();?>
 </div>