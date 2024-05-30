<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StreamBox</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <?php
        $baseUrl = Yii::app()->baseUrl;
        $cs = Yii::app()->getClientScript();
        // $cs->registerScriptFile($baseUrl . '/js/yourscript.js');
        $cs->registerCssFile($baseUrl . '/css/style.css');
    ?>
    <style>
        body,
        html {
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
                <div class="rounded bg-white shadow p-5">
                    <form method = "post">
                        <h3 class="text-dark fw-bolder fs-4 mb-2">Sign In</h3><br>
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control" id="floatingInput" placeholder="name@example.com" required>
                            <label for="floatingInput"><i class="fa fa-envelope"></i>&nbsp; &nbsp;Email address</label>
                        </div>
                        <div class="form-floating">
                            <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                            <label for="floatingPassword"><i class="fa fa-lock"></i>&nbsp; &nbsp;Password</label>
                        </div>
                        <button type="submit" id="login" name = "login" class="btn btn-primary submit_btn w-100 my-4">Login</button>
                    </form>
                    <form method="get" action="/auth/forgotpassword">
                        <button class="btn btn-light"> Forgot Password? </button>
                    </form>
                    <br>
                    <div>
                        <form class="rounded bg-white" method="post">
                            <div class="fw-normal text-muted mb-3">
                                New User?&nbsp;&nbsp;&nbsp;<button class="btn btn-outline-primary">Sign Up</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>