<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StreamBox</title>
    <link rel="icon" type="image/png" href="/public/images/play.png">
    <link rel="stylesheet" href="/public/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
 
    <style>
        body, html {
            overflow: hidden;
        }
    </style>
</head>
<body>
    <section class="wrapper" style="padding-top: 150px; padding-bottom: 250px;">
        <div class="container">
            <div class="col-sm-8 offset-sm-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4 text-center">
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                    <div id="myToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-body" id="toast-body">
                        </div>
                    </div>
                </div><br><br>
            <div class="logo">
                    <img src="<?php echo Yii::app()->baseUrl . '/assets/images/play.png';?>" class="img-fluid" alt="logo">
                    <h3>StreamBox</h3>
                </div><br><br>
                <form class="rounded bg-white shadow p-5" method="post">
                    <h3 class="text-dark fw-bolder fs-4 mb-2">Forgot Password</h3><br>
                    <div style="color: grey;">Enter your mail to reset your password</div><br>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="floatingInput" name="email" placeholder="name@example.com" required>
                        <label for="floatingInput"><i class="fa fa-envelope"></i>&nbsp; &nbsp;Email address</label>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary submit_btn my-4" id = "otp">Submit</button>
                    </div>
                </form>
                
            </div>
        </div>
    </section>
 
<script>
   var verifyButton = document.getElementById("otp");
        verifyButton.addEventListener('click', function(e){
            e.preventDefault();
            var email = document.getElementsByName("email")[0].value;
            let url = '<?php echo Yii::app()->createUrl('/auth/mail'); ?>';
 
            fetch(url, {
                method: "POST",
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({email: email}),
            })
            .then(response => {
                if(response){
                    showToastMsg('Email sent successfully.');
                     window.location.href = '<?php echo Yii::app()->createUrl('/auth/verifyotp'); ?>';
                }})
                .catch(error => {
                    console.error('Error:', error);
                });
        })
        function showToastMsg(message) {
            const myToast = document.getElementById('myToast');
            const toastbody = document.getElementById('toast-body');
            toastbody.innerHTML = message;
            const toast = new bootstrap.Toast(myToast);
            toast.show();
        }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>