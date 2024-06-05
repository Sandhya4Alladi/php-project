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
            <div class="col-sm-8 offset-sm-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4 text-center" id="password-div">
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                    <div id="myToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-body" id="toast-body">
                        </div>
                    </div>
                </div>
                <br><br>
               <div class="logo">
                    <img src="<?php echo Yii::app()->baseUrl . '/assets/images/play.png';?>" class="img-fluid" alt="logo">
                    <h3>StreamBox</h3>
                </div><br><br>
                <form class="rounded bg-white shadow p-5"  method="post">
                    <h3 class="text-dark fw-bolder fs-4 mb-2">Reset Password</h3><br>
                    <div class="form-floating">
                        <input type="password" class="form-control" id="rest" name="password" pattern="(?=.*\d)(?=.*[a-zA-Z])(?=.*[\W_]).{6,}" placeholder="Password" required>
                        <label for="rest"><i class="fa fa-lock"></i>&nbsp; &nbsp;New Password</label><br>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" id="restpas" name="confirm_password" placeholder="Password" required>
                        <label for="restpas"><i class="fa fa-lock"></i>&nbsp; &nbsp;Confirm Password</label>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary submit_btn w-100 my-4" id="reset">Reset</button>
                    </div>
                    
                </form>
                
            </div>
        </div>
    </section>
    <script>
          var verifyButton = document.getElementById("reset");
            verifyButton.addEventListener('click', function(e){
            e.preventDefault();
                var password = document.getElementsByName("password")[0].value;
                var confirm_password = document.getElementsByName("confirm_password")[0].value;
                let url = '<?php echo Yii::app()->createUrl('/auth/resetpw'); ?>';
                fetch(url, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({password:password, confirm_password:confirm_password})
                })
                .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            }).then((data) => {
 
                    if(data.status === 'error'){    
                    showToastMsg(data.message);
                    }else{
                        showToastMsg(data.message);
                        window.location.href = '<?php echo Yii::app()->createUrl('/auth/login'); ?>';
        }
        }).catch(error => {
                console.error('Error:', error);
                });
        });
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
