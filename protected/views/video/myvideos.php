<?php 
$jsonData = CJSON::encode($data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StreamBox</title>
    <link rel="icon" type="image/png" href="../images/play.png">
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Protest+Strike&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/public/css/home.css">
    <!-- <link rel="stylesheet" href="../css/video-player.css"> -->
</head>
<style>
  .sidebar {
  width: 250px;
}
.list-group-item {
  border: none;
  margin-top: 8px;
  margin-bottom: 8px;
  border: none;
  outline: none;
}
.navbar-nav{
margin-right: 100px;
}

.main-body {
display: flex;
flex-direction: row;
height: calc(100vh - 114px);

}
.main-body .main {
padding: 15px;
width: 100%;
}

.main {
overflow: auto;
}

#videos {
display:  flex;
flex-wrap:  wrap;
gap: 15px;
height: calc(100vh - 114px);
cursor: pointer;
}

.card {
position: relative;
height: 300px;
flex: 0 0 calc(33.333% - 15px);
border: 1px solid #ccc;
-webkit-box-shadow: 0px 0px 38px -16px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 38px -16px rgba(0,0,0,0.75);
box-shadow: 0px 0px 38px -16px rgba(0,0,0,0.75);
}

.card button {
text-align: center;
font-size: medium;
font-family: "Protest Strike", sans-serif;
font-weight: 150;
font-style: normal;
}

.card img {
width: 100%;
height: 100%;
overflow: hidden;
}

.overlay {
display: none;
position: absolute;
top: 0;
left: 0;
width: 100%;
height: 100%;
flex-direction: column;
justify-content: center;
align-items: center;
background: rgba(0, 0, 0, 0);
transition: all 0.5s ease;
}

.material-symbols-outlined {
font-size: 3rem;
font-variation-settings:
'FILL' 0,
'wght' 700,
'GRAD' 0,
'opsz' 48
}

.explore-tags{
display: none;
}
</style>
<body>
<div id="page-content-wrapper">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <button class="navbar-brand p-2 btn btn-primary" id="streambox-logo" >
    <img src="/public/images/homelogo.jpg" class="img-fluid" alt="logo" width="50">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>STREAMBOX</b>
    </button>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item mr-3">
          <form class="form-inline" method="get" action="/video/search">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" id="search" name="search">
            <button class="btn btn-outline-light my-2 my-sm-0" type="submit" id="searchBtn"><i class="fas fa-search"></i></button>
          </form>
        </li>
            
          
        <!-- <li class="nav-item ml-3">
          <form class="form-inline">
            <button style="border: none; outline: none;" class="btn btn-outline-light my-2 my-sm-0" type="submit"><i class="fas fa-bell"></i>
          </form>
        </li> -->

        <li class="nav-item ml-3">
          <form class="form-inline" method="get" action="/user/profile">
            <button style="border: none; outline: none;" class="btn btn-outline-light my-2 my-sm-0" type="submit"><i class="fas fa-user"></i>
          </form>
        </li>
        <li class="nav-item ml-3"></li>
        <form>
          <button class="btn btn-primary" type="submit" id="confirmButton"> Logout </button>
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
<!-- <div>
  <iframe width="1000" height="800" src="http://localhost:5000/embed?key=660ce4da05280bee862e6f1f-4ec29457-4064-4b95-9f0a-f81ac38e74fa-inflexion.mp4" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen ></iframe>
</div> -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmationModalLabel">Delete Account</h5>
        </div>
        <div class="modal-body">
          Are you sure you want to delete the video?
          <p style="color: red;"><b>Your video will be permenantly deleted</b></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="confirmDeleteButton">Delete</button>
        </div>
      </div>
    </div>
  </div>
<div class="main-body">
<div class="d-flex" id="wrapper">
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar-wrapper">

      <div class="list-group list-group-flush ">
        <form method="get" action="/video/home">
          <button class="list-group-item list-group-item-action" style="border: none; outline: none;">
              <i class="fas fa-home"></i> &nbsp;&nbsp;Home
          </button>
        </form>
        <div class="explore">
            <button class="list-group-item list-group-item-action" style="border: none; outline: none;">
                <i class="fas fa-compass"></i> &nbsp;&nbsp;Explore
            </button>
            <div class="explore-tags">
                  <form method="get" action="/video/tags">
                      <input type="hidden" name="tag" value="product-trainings">
                      <button type="submit" class="list-group-item list-group-item-action" style="border: none; outline: none;">
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>&#x2022;</b>&nbsp;&nbsp;Product Training
                      </button>
                  </form>
                  <form method="get" action="/video/tags">
                      <input type="hidden" name="tag" value="process-trainings">
                      <button type="submit" class="list-group-item list-group-item-action" style="border: none; outline: none;">
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>&#x2022;</b>&nbsp;&nbsp;Process Training
                      </button>
                  </form>
                  <form method="get" action="/video/tags">
                      <input type="hidden" name="tag" value="hr-induction">
                      <button type="submit" class="list-group-item list-group-item-action" style="border: none; outline: none;">
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>&#x2022;</b>&nbsp;&nbsp;HR Induction
                      </button>
                  </form>
                  <form method="get" action="/video/tags">
                      <input type="hidden" name="tag" value="infosec-compliance">
                      <button type="submit" class="list-group-item list-group-item-action" style="border: none; outline: none;">
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>&#x2022;</b>&nbsp;&nbsp;Infosec Compliance
                      </button>
                  </form>
                  <form method="get" action="/video/tags">
                      <input type="hidden" name="tag" value="soft-skills">
                      <button type="submit" class="list-group-item list-group-item-action" style="border: none; outline: none;">
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>&#x2022;</b>&nbsp;&nbsp;Soft Skills
                      </button>
                  </form>
                  <form method="get" action="/video/tags">
                      <input type="hidden" name="tag" value="webinars">
                      <button type="submit" class="list-group-item list-group-item-action" style="border: none; outline: none;">
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>&#x2022;</b>&nbsp;&nbsp;Webinars
                      </button>
                  </form>
                  <form method="get" action="/video/tags">
                      <input type="hidden" name="tag" value="events">
                      <button type="submit" class="list-group-item list-group-item-action" style="border: none; outline: none;">
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>&#x2022;</b>&nbsp;&nbsp;Events
                      </button>
                  </form>
            </div>
          </div>
        <form action="/video/trends" method="get">
          <button class="list-group-item list-group-item-action" style="border: none; outline: none;">
              <i class="fas fa-fire"></i> &nbsp;&nbsp;Trending
          </button>
        </form>
        <form action="/video/uploadvideo" method="get">
          <button class="list-group-item list-group-item-action" style="border: none; outline: none;">
            <i class="fas fa-upload"></i> &nbsp;&nbsp; Upload a Video
          </button>
        </form>
         <form method="get" action="/video/myvideos">
          <button class="list-group-item list-group-item-action" style="border: none; outline: none;">
            <i class="fa fa-video-camera"></i> &nbsp;&nbsp;My videos
          </button>
        </form>
        <form method="get" action="/video/analytics">
          <button class="list-group-item list-group-item-action" style="border: none; outline: none;">
            <i class="fa fa-chart-bar"></i> &nbsp;&nbsp;Analytics
          </button>
        </form>
        <form method="get" action="/video/likedVideos">
          <button class="list-group-item list-group-item-action" style="border: none; outline: none;">
              <i class="fas fa-heart"></i> &nbsp;&nbsp;Liked Videos
          </button>
        </form>
        <form method="get" action="/video/watchLater">
          <button class="list-group-item list-group-item-action" style="border: none; outline: none;">
              <i class="fas fa-clock"></i> &nbsp;&nbsp;Watch Later
          </button>
        </form>
      </div>
  </div>
</div>
<div class="main">
  <div id="videos">
</div>
<!-- <div>
<iframe width="1000" height="800" src="http://localhost:5000/embed?key=660ce2f8b0a3fb0e5b2572dd-32f2639b-237f-478d-9e99-be6e1d555033-hr.mp4" frameborder='0'></iframe>
</div> -->
<!-- <footer>
</footer> -->
<script src="/public/js/myVideos.js"></script>
    <script>

    const allVideos = <?php echo $jsonData; ?>;
    //console.log(allVideos);
    display(allVideos);

    </script>
    <script>
      document.querySelector('.explore').addEventListener('click', function() {
          const tags = document.querySelector('.explore-tags'); // Select the explore-tags element
          if (tags.style.display === 'none' || tags.style.display === '') {
              tags.style.display = 'block';
          } else {
              tags.style.display = 'none';
          }
      });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="/public/js/confirm.js"></script>
    <script src="/public/js/logo.js"></script>
</body>
</html>