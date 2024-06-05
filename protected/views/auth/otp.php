<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StreamBox</title>
    <link rel="icon" type="image/png" href="/public/images/play.png">
    <link rel="stylesheet" href="/public/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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
                </div>
                <div class="logo">
                    <img src="<?php echo Yii::app()->baseUrl . '/assets/images/play.png';?>" class="img-fluid" alt="logo">
                    <h3>StreamBox</h3>
                </div><br><br>
                <form class="rounded bg-white shadow p-5">
                    <h3 class="text-dark fw-bolder fs-4 mb-2">Two step verification</h3>
                    <div style="color: grey;">Enter the verification code we sent to your email</div><br>
                    <div class="otp_input text-start mb-2">
                        <label for="digit">Type your 6 digit security code
                        </label>
                        <div class="d-flex align-items-center justify-content-between mt-2" id="otp-div">
                            <input type="text" name="d1" maxlength="1" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control border border-primary otpInput" placeholder="" autofocus>
                            <input type="text" name="d2" maxlength="1" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control border border-primary otpInput" placeholder="" >
                            <input type="text" name="d3" maxlength="1" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control border border-primary otpInput" placeholder="" >
                            <input type="text" name="d4" maxlength="1" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control border border-primary otpInput" placeholder="" >
                            <input type="text" name="d5" maxlength="1" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control border border-primary otpInput" placeholder="" >
                            <input type="text" name="d6" maxlength="1" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control border border-primary otpInput" placeholder="" >
                        </div>
                    </div>
                    <div>
                    <button type="button" class="btn btn-primary submit_btn my-4" id="otp-submit">Submit</button>
                    </div>
                </form>
                
            </div>
        </div>
    </section>
    <script>
          var verifyButton = document.getElementById("otp-submit");
            verifyButton.addEventListener('click', function(e){
            e.preventDefault();
            const otpdiv = document.getElementById('otp-div');
                var d1=document.getElementsByName("d1")[0].value;
                var d2=document.getElementsByName("d2")[0].value;
                var d3=document.getElementsByName("d3")[0].value;
                var d4=document.getElementsByName("d4")[0].value;
                var d5=document.getElementsByName("d5")[0].value;
                var d6=document.getElementsByName("d6")[0].value;
                let url = '<?php echo Yii::app()->createUrl('/auth/verifyotp'); ?>';
                console.log(url);
                fetch(url, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({d1:d1, d2:d2, d3:d3, d4:d4, d5:d5, d6:d6})
                })
                .then(response => {
                if(response){
                    var otpdiv = document.getElementById("otp-div");
                    otpdiv.style.display = "none";
                    showToastMsg('OTP verified successfully.')
                    window.location.href = '<?php echo Yii::app()->createUrl('/auth/resetpw'); ?>';
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
    <!-- <script src="../js/otp.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>