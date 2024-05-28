<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Register';
$this->breadcrumbs=array(
	'Register',
);
?>

<h1>Register</h1>

<p>Please fill out the following form with your login credentials:</p>

<link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.dataTables.css" />
  
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>


<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'register', //for ajax register button
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); 

?>
<div>
	<button id="find">Find</button>
</div><br>
<div id="resultContainer"></div><br>
<div>
    <button id="findAll">Find All</button>
</div><br>
<div id="detailsContainer"></div><br>
<div>
    <button id="findByAttribute">FindByAttribute</button>
</div><br>

<?php foreach ($imageUrls as $imageUrl): ?>
    <img src="<?= $imageUrl ?>" alt="Image">
<?php endforeach; ?>


<?php $this->endWidget(); ?>	
</div><!-- form -->	
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    
    $(document).ready(function(){
    
        $('#find').click(function(e){
            e.preventDefault();
            // var data = $(this);
            var url = "<?php echo Yii::app()->createUrl('sample/add') ?>";
            $.ajax({
                type : 'GET',
                url,
                data : {queryType:'find'},
                dataType:"json",
                success: function(response) {
                console.log('response...', response.data);
                var htmlContent = '<table class="result-table">';
                $.each(response.data, function(key, value) {
                    htmlContent += '<tr>';
                    htmlContent += '<th>' + key + '</th>';
                    htmlContent += '<td>' + value + '</td>';
                    htmlContent += '</tr>';
                });
                htmlContent += '</table>';
                $('#resultContainer').html(htmlContent);
}

            })
        })

        $('#findAll').click(function(e){
            e.preventDefault();
            // var data = $(this);
            var url = "<?php echo Yii::app()->createUrl('sample/add') ?>";
            $.ajax({
                type : 'GET',
                url,
                data : {queryType:'findAll'},
                dataType:"json",
                success: function(response) {
                console.log('response...', response.data);
                var html = '<table class="details-table">';
                html += '<thead>';
                html += '<tr>';
                html += '<th>Username</th>';
                html += '<th>Email</th>';
                html += '<th>Gender</th>';
                html += '<th>Apartment</th>';
                html += '<th>Landmarks</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';
                response.data.forEach(function(obj, index) {
                    html += '<tr>';
                    html += '<td>' + obj.username + '</td>';
                    html += '<td>' + obj.email + '</td>';
                    html += '<td>' + obj.gender + '</td>';
                    html += '<td>' + obj.address.apartment + '</td>';
                    var landmarks = Array.isArray(obj.address.landmark) ? obj.address.landmark.join(', ') : obj.address.landmark;
                    html += '<td>' + landmarks + '</td>';
                    html += '</tr>';
                });
                html += '</tbody>';
                html += '</table>';
             $('#detailsContainer').html(html);
}
            })
        })

        $('#findByAttribute').click(function(e){
            e.preventDefault();
            // var data = $(this);
            var url = "<?php echo Yii::app()->createUrl('sample/add') ?>";
            $.ajax({
                type : 'GET',
                url,
                data : {queryType:'findByAttribute'},
                dataType:"json",
                success : function(response){
                    console.log('response...', response.data);
                }
            })
        })
        
    })
</script>





