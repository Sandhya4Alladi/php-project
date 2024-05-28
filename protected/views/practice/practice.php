    <?php
    $this->pageTitle = Yii::app()->name . ' -Practice';
    $this->breadcrumbs = array(
        'Practice',
    );
    ?>
    <h1>Practice View</h1>
    <div class="form-info">
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'register', //for ajax register button
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
        ));
        ?>

        <div>
            <button id="jquery">
                Jquery
            </button><br><br>
            

        </div>
        <div>
        <p>I'm practing the jquery</p>
        </div>

        <?php $this->endWidget(); ?>
    </div>

  


