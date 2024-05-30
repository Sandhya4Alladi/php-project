<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StreamBox</title>
    <link rel="icon" type="image/png" href="../images/play.png">
    <link rel="stylesheet" href="../css/style.css">
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
               <div class="logo">
                    <img src="<?php echo Yii::app()->baseUrl . '/assets/images/play.png';?>" class="img-fluid" alt="logo">
                    <h3>StreamBox</h3>
                </div><br><br>
                <form class="rounded bg-white shadow p-5" action='/auth/resetpw' method="post">
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
                      <button type="submit" class="btn btn-primary submit_btn w-100 my-4">Reset</button>
                    </div>
                    
                </form>
                
            </div>
        </div>
    </section>
</body>
</html>