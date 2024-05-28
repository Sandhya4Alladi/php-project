<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle = Yii::app()->name . ' - Register';
$this->breadcrumbs = array(
	'Register',
);
?>

<h1>Register</h1>

<p>Please fill out the following form with your login credentials:</p>



<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'register', //for ajax register button
		'enableClientValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
		),
	));

	?>
	<?php echo $form->errorSummary($model); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<div class="row">
		<?php echo $form->labelEx($model, 'username'); ?>
		<?php echo $form->textField($model, 'username'); ?>
		<?php echo $form->error($model, 'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'password'); ?>
		<?php echo $form->passwordField($model, 'password'); ?>
		<?php echo $form->error($model, 'password'); ?>

	</div>

	<div class="numbers">
		<div class="number">
				<?php echo $form->labelEx($model['number'], 'number'); ?>
				<?php echo $form->textField($model['number'], 'number[]'); ?>
				<?php echo $form->error($model['number'], 'number'); ?>
		</div>
	</div>
	<button id="add2">Add</button>

	<div class="row">
		<?php echo $form->labelEx($model, 'email'); ?>
		<?php echo $form->textField($model, 'email'); ?>
		<?php echo $form->error($model, 'email'); ?>
	</div>

	<div class="addresses">
		<div class="address">
			<div class="row">
				<?php echo $form->labelEx($model, 'apartment'); ?>
				<?php echo $form->textField($model, 'address[0][apartment]'); ?>
				<?php echo $form->error($model, 'address[0][apartment]'); ?>
			</div>
			<div class="row">
				<?php echo $form->labelEx($model, 'landmark'); ?>
				<?php echo $form->textField($model, 'address[0][landmark]'); ?>
				<?php echo $form->error($model, 'address[0][landmark]'); ?>
			</div>
		</div>
	</div>

	<button id="add">Add</button>

	<div class="row">
		<?php echo $form->labelEx($model, 'gender'); ?>
		<?php echo $form->textField($model, 'gender'); ?>
		<?php echo $form->error($model, 'gender'); ?>
	</div>

	<div class="row rememberMe">
		<?php echo $form->checkBox($model, 'rememberMe'); ?>
		<?php echo $form->label($model, 'rememberMe'); ?>
		<?php echo $form->error($model, 'rememberMe'); ?>
		<p class="hint">
			Hint: You may login with <kbd>demo</kbd>/<kbd>demo</kbd> or <kbd>admin</kbd>/<kbd>admin</kbd>.
		</p>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('register'); ?>
	</div>

	<?php $this->endWidget(); ?>
</div><!-- form -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
	$(document).ready(function() {
		let ind = 1;
		$('#add').click(function(e) {
			e.preventDefault();

			var $original = $('.address:first');
			var $cloned = $original.clone();

			$cloned.find('input[type=text]').each(function() {
				$(this).attr('name', $(this).attr('name').replace('0', ind));
				$(this).attr('id', $(this).attr('id').replace('0', `${ind}`));
				$(this).val('');
			})
			$cloned.find('.errorMessage').each(function() {
				$(this).attr('id', $(this).attr('id').replace('0', `${ind}`));
				$(this).val('');

			})

			ind += 1;

			$('.addresses').append($cloned);
		})

		$('#add2').click(function(e) {
			e.preventDefault();

			var $original = $('.number:first');
			var $cloned = $original.clone();

			$cloned.find('input[type=text]').each(function() {
				$(this).attr('name', $(this).attr('name').replace('0', ind));
				$(this).attr('id', $(this).attr('id').replace('0', ind));
				$(this).val('');
			})
			$cloned.find('.errorMessage').each(function() {
				$(this).attr('id', $(this).attr('id').replace('0', ind));
				$(this).val('');

			})

			ind += 1;

			$('.numbers').append($cloned);
		})

		// 

		$('#register').submit(function(e) {
			// alert("hi");
			e.preventDefault();
			$('.errorMessage').hide();
			// var formData = $(this); //current context - jquery	
			var formData = new FormData($(this)[0]);
			// console.log("formData..", formData.serialize());
			var url = "<?php echo Yii::app()->createUrl('sample/register') ?>";
			$.ajax({
				
				// type: formData.attr('method'), //$_POST
				type : 'POST',
				// url : formData.attr('action'), //specify url
				url,
				// data: formData.serialize(),
				data : formData,
				processData:false,
				contentType:false,
				success: function(response) {
					// console.log('checking');
					console.log(response);
					console.log('data...', response.status);
					if (response.status == 0) {
						console.log('err...', response.error);
						var errors = response.error;
						console.log(errors);
						$.each(errors, function(attribute, errorMessages) {
							var errorMessage = errorMessages[0];
							if (attribute == 'address[]') {
								$.each(errorMessages, (ind, msg) => {
									$.each(msg, (attr, em) => {
										console.log(attr, em);
										$('#Register_address_' + ind + '_' + attr + '_em_').html(em).show();
									})
								})
							} else 
								$('#Register_' + attribute + '_em_').html(errorMessage).show();

						})
						return;
					} else {
						// console.log(response.status);
						console.log("validation Successful");
					}
				},
				error: function(xhr, status, error) {
					console.error(error);
				}
			});
		});
	});
</script>