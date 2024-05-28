<!-- views/site/login.php -->


<?php if(Yii::app()->user->hasFlash('loginMessage')): ?>
    <div class="flash-success">
        <?php echo Yii::app()->user->getFlash('loginMessage'); ?>
    </div>
<?php endif; ?>

<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'login', //for ajax
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
    ));
     ?>

<div class="row">
    <?php echo CHtml::label('Email', 'email'); ?>
    <?php echo CHtml::textField('email'); ?>
</div>

<div class="row">
    <?php echo CHtml::label('Password', 'password'); ?>
    <?php echo CHtml::passwordField('password'); ?>
</div>

<div class="row submit">
    <?php echo CHtml::submitButton('Login'); ?>
</div>

<?php $this->endWidget();?>
</div>
