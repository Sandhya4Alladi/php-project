<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StreamBox</title>
    <link rel="icon" type="image/png" href="/public/images/play.png">
    <link rel="stylesheet" href="/public/css/uploadvideostyle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Protest+Strike&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="/public/css/style.css" rel="stylesheet"?>
    <style>
        body, html {
            overflow: hidden;
        }
        .navbar-nav{
            margin-right: 40px;
        }
        .button-container {
            display: flex;
            /* justify-content: space-between; */
            margin-left: 45%; /* Adjust the margin as needed */
            width: 50%; /* Adjust the width of the container */
        }
        .upload-button {
            width: 15%; /* Adjust the width of the buttons */
        }
        
      progress {
         width: 300px;
         height: 25px;
         border: 2px solid gray;
      }
      progress::-webkit-progress-bar {
         background-color: green;
      }
      progress::-webkit-progress-value {
         background-color: red;
      }
      </style>
</head>
<body>
    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
          <button class="navbar-brand p-2 pl-10 btn btn-primary" id="streambox-logo" >
            &nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo Yii::app()->baseUrl . '/assets/images/homelogo.jpg';?>" class="img-fluid" alt="logo" width="50">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>STREAMBOX</b>
          </button>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav ml-auto">
              <li class="nav-item mr-3">
                <form class="form-inline" method="get" action="/videos/search" >
                  <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" id="search" name="search">
                </form>
                </li>&nbsp;&nbsp;&nbsp;&nbsp;
                 
                
              <!-- <li class="nav-item ml-3">
                <form class="form-inline">
                  <button style="border: none; outline: none;" class="btn btn-outline-light my-2 my-sm-0" type="submit"><i class="fas fa-bell"></i>
                </form>
              </li>&nbsp;&nbsp;&nbsp;&nbsp; -->
              <li class="nav-item ml-3">
                <form class="form-inline" method="get" action="/users/find">
                  <button style="padding-right: 10px;" style="border: none; outline: none;" class="btn btn-outline-light my-2 my-sm-0" type="submit"><i class="fas fa-user"></i>
                </form>
              </li>&nbsp;&nbsp;&nbsp;&nbsp;
              <li class="nav-item ml-3">
              <form>
                <button class="btn btn-primary" type="button" id="confirmButton"> Logout </button>
              </form>
            </li>
            </ul>
          </div>
        </nav>
      </div>
      <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="confirmationModalLabel">Confirm Logut</h5>
              <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button> -->
            </div>
            <div class="modal-body">
              Are you sure you want to logout?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary" id="confirmActionButton">Confirm</button>
            </div>
          </div>
        </div>
      </div>
    <section class="wrapper" style="padding-top: 100px; padding-bottom: 30px;">
        <div class="container">
            <div class="row justify-content-center" >
                <div class="col-lg-8">
                    <form class="rounded bg-white shadow p-5" action='/video/addvideo' method="post" width="100%" enctype="multipart/form-data">
                        <h3 class="text-dark fw-bolder fs-4 mb-2 text-center">Upload Video</h3>
                        <div class="mb-3">
                            <label for="title" class="form-label"><h6>Title</h6></label>
                            <input type="text" class="form-control" id="title" name="title" required>
                            <label for="desc" class="form-label"><h6>Description</h6></label>
                            <input type="text" class="form-control" id="desc" name="desc" required>
                            <label for="videoFile" class="form-label"><h6>Choose a video file</h6></label>
                            <input type="file" class="form-control" id="videoFile" name="videoFile" accept=".mp4" required><br>
                            <label for="imageFile" class="form-label"><h6>Choose a image as video thumbnail</h6></label>
                            <input type="file" class="form-control" id="imageFile" name="imageFile" accept="image/*" required>
                        </div>
                                <div class="main-title">
                                    <h6>Add Tags for the video</h6>
                                </div>
                                <div class="row category-checkbox">
                                  <!-- Checkbox columns -->
                                      <div class="custom-control custom-checkbox mb-3">
                                          <input type="checkbox" class="custom-control-input" id="customCheck1" name="tags" value="product-trainings">
                                          <label class="custom-control-label" for="customCheck1" >Product Trainings</label>
                                      </div>
                                      <div class="custom-control custom-checkbox mb-3">
                                          <input type="checkbox" class="custom-control-input" id="customCheck2" name="tags" value="process-trainings">
                                          <label class="custom-control-label" for="customCheck2">Process Trainings</label>
                                      </div>
                                      <div class="custom-control custom-checkbox mb-3">
                                        <input type="checkbox" class="custom-control-input" id="customCheck3" name="tags" value="hr-induction">
                                        <label class="custom-control-label" for="customCheck3" >HR Induction</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mb-3">
                                        <input type="checkbox" class="custom-control-input" id="customCheck4" name="tags" value="infosec-compliance">
                                        <label class="custom-control-label" for="customCheck4" >Infosec & Compliance</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mb-3">
                                      <input type="checkbox" class="custom-control-input" id="customCheck5" name="tags" value="soft-skills">
                                      <label class="custom-control-label" for="customCheck5" >Soft Skills</label>
                                 </div>
                                 <div class="custom-control custom-checkbox mb-3">
                                  <input type="checkbox" class="custom-control-input" id="customCheck6" name="tags" value="webinars">
                                  <label class="custom-control-label" for="customCheck6" >Webinars</label>
                             </div>
                             <div class="custom-control custom-checkbox mb-3">
                              <input type="checkbox" class="custom-control-input" id="customCheck7" name="tags" value="events">
                              <label class="custom-control-label" for="customCheck7" >Events</label>
                         </div>
                             </div>
                            </div>
                        </div>
                     </div>
                            <div class="button-container">
                                <button type="submit" class="btn btn-primary submit_btn upload-button mt-4" id="upload">Upload</button>&nbsp;&nbsp;&nbsp;
                                <button type="button" id="cancel" class="btn btn-secondary submit_btn upload-button mt-4"> Cancel</button>
                            </div>                   
                    </form>
                </div>
            </div>
        </div>
    </section>
    <script>
      const cancel = document.getElementById('cancel');
      cancel.addEventListener('click', function(event){
          window.location.href='/video/home'
      });
    </script>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
   <script src="/public/js/confirm.js"></script>
   <script src="/public/js/logo.js"></script>
</body>
</html>
 