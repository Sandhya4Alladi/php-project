<?php
$key = $data;
$vttKey = $key . ".vtt";
$video_id = $id;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StreamBox</title>
    <link rel="icon" type="image/png" href="/public/images/play.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Protest+Strike&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="/public/css/player.css" rel="stylesheet" />
    <link href="/public/css/video-player.css" rel="stylesheet" />

    <script>
      function disableRightClick() {
          var specificDiv = document.getElementById('videoPlayer');
          specificDiv.addEventListener('contextmenu', function(e) {
              e.preventDefault();
          });
      }
      document.addEventListener('DOMContentLoaded', disableRightClick);
  </script>
</head>

<body>
  <div id="page-content-wrapper">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
      <button class="navbar-brand p-2 pl-10 btn btn-primary" id="streambox-logo" >
        &nbsp;&nbsp;&nbsp;&nbsp;<img src="/public/images/homelogo.jpg" class="img-fluid" alt="logo" width="50">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>STREAMBOX</b>
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
              
          <li class="nav-item ml-3">
            <form class="form-inline" method="get" action="/users/find">
              <button style="padding-right: 10px;" style="border: none; outline: none;" class="btn btn-outline-light my-2 my-sm-0" type="submit"><i class="fas fa-user"></i>
            </form>
          </li>&nbsp;&nbsp;&nbsp;&nbsp;
          
        </ul>
      </div>
    </nav>
    </div>
 
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
      </div>
      <div class="modal-body">
        Do you want to continue from where you previously stopped?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary" id="confirmYes">Yes</button>
      </div>
    </div>
  </div>
</div>
 
    <div class="video-player">
        <div id="profile-logo">
        </div>
        <div class="videoPlayer">
          <video id="videoPlayer"  style="width:80%; border: none;" controls controlsList="nodownload" muted loop >
            <source id="videoSource"  type="video/mp4">
            <track id="vtt" label="English captions" kind="captions" srclang="en"  default>
          </video>
        </div>
 
        <div class="btns-class" style="display: flex;">
          <button style="font-size: 26px;" class="like-btn" onclick="toggleLike(this,'<?php echo $video_id ?>')">
            <i class="fas fa-thumbs-up"></i>
          </button>
            
          <button style="font-size: 26px;" class="dislike-btn" onclick="toggleDislike(this, '<?php echo $video_id ?>')">
            <i class="fas fa-thumbs-down"></i>
          </button>
              
          <button style="font-size: 26px;" onclick="skipBackward()" >
            <i class="fa fa-backward"></i>
          </button>
          <!-- <button onclick="togglePause()" >
            <i id="pauseIcon" class="fa fa-pause"></i>
          </button> -->
          <button style="font-size: 26px;" onclick="skipForward()" >
            <i class="fa fa-forward"></i>
          </button>          
            
          
            
          <button style="font-size: 26px;" class="clock-btn" onclick="toggleClock(this, '<?php echo $video_id ?>')">
              <i class="fas fa-clock"></i>
          </button>
    
          <button style="font-size: 26px;" class="share-btn" onclick="showMessageBox('<?php echo $key ?>')">
            <i class="fas fa-code"></i>
          </button>
    
          <!-- <a href="/embed?key=<%= key %>">
            <i class="fas fa-share"></i>      
          </a> -->
        </div>
    </div>
 
      <div id = "chatContent" style="display: none; padding-top: 5%;">
          <h4>Comment</h4><br>
          <div>
              <div style="display: inline-flex;">
                  <input style="margin-bottom: 50px;" type="text" id="com" name="comment" placeholder="Comment" maxlength="30" required>
                  <button class="btn btn-outline-primary btn1" style="color: black; margin-bottom: 50px;" onclick="comment('<%= id %>')" type="submit">Post&nbsp;&nbsp;<i class="fas fa-share"></i></button>
              </div>
          </div>
        </div>
        <div id="cmtContainer">
        </div>
        <div class="message-box-overlay" id="messageBoxOverlay">
          <div class="message-box">
            <div class="message-header">
              <h4>Embed Video &nbsp;&nbsp;<button id="copy"class="btn btn-secondary"onclick="copyText()">Copy</button></h4>
              <button class="close-btn" onclick="closeMessageBox()">
                <i style="font-size:24px" class="fa">&#xf00d;</i>
              </button>
            </div>
            <div class="message-body">
              <p id="embed-code"></p>
            </div>
          </div>
        </div>
        <br><br>
    <footer>
        
    </footer>
    <script>

    const id = "<?php echo $video_id ?>";
    document.getElementById("vtt").src = "/video/getvtt?data=<?php echo $vttKey ?>" ;
    document.getElementById("videoSource").src = 'https://d2fpvsof67xqc9.cloudfront.net/<?php echo $key ?>';
    // const id = `<%= id %>`;
    // console.log(id);
    // const custom = `<%= custom %>`;
    // console.log(typeof custom);
    // console.log(custom);
    // const logo_img = <%- JSON.stringify(logo) %>;
    // var decodedData = decodeURIComponent(custom.replace(/&#34;/g, '"'))
    // decodedData = JSON.parse(decodedData)
    // console.log(decodedData)
 
    // var playerColor = decodedData['playerColor'];
    
    // const logo=document.getElementById("profile-logo");
    // const imgElement = document.createElement("img");
    // imgElement.src = `data:image/png;base64,` + logo_img;
    // logo.appendChild(imgElement);
 
    // const theme = decodedData['theme'];
    // var background_theme;
    // switch(theme){
    //   case 'default-theme': background_theme = '../images/default-theme.jpg'
    //                         break;
    //   case 'theme1':        background_theme = '../images/theme1.jpeg'
    //                         break;
    //   case 'theme2':        background_theme = '../images/theme2.jpeg'
    //                         break;
    //   case 'theme3':        background_theme = '../images/theme3.jpeg'
    //                         break;
    //   case 'theme4':        background_theme = '../images/theme4.jpeg'
    //                         break;
    //   default      :        background_theme = '../images/default-theme.jpg'
    // }
 
    // document.documentElement.style.setProperty('--background-image-url', `url(${background_theme})`);
  </script>
    <script src="/public/js/player.js"></script>
    <script src="/public/js/player-toggles.js"></script>
    <script src="/public/js/logo.js"></script>
    <script src="/public/js/video-player.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>