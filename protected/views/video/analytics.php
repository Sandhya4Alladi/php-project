<?php 
$videoinfo = CJSON::encode($videoinfo);
$overall = CJSON::encode($overall);
$n = CJSON::encode($n);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StreamBox</title>
    <link rel="icon" type="image/png" href="../images/play.png">
    <!-- Include any CSS files -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Protest+Strike&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <style>
        .navbar-nav{
            margin-right: 40px;
        }

        body {
            font-family: 'Roboto', sans-serif;
        }

        .video-info {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .info-box {
            flex: 1;
            border: 1px solid #eee;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }

        .info-box:hover {
            background-color: #e6e6e6;
        }

        .info-box span {
            display: block;
            font-weight: bold;
            font-size: 18px;
        }

        .donutChartContainer {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 400px;
        }
    </style>
    <!-- Google tag (gtag.js) --> 
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-YTZJ577J6H"></script> 
</head>
<body>
    <div id="page-content-wrapper">
      <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <button class="navbar-brand p-2 btn btn-primary" id="streambox-logo" >
          <img src="/public/images/homelogo.jpg" alt="Your Logo" width="50">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>STREAMBOX</b>
        </button>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item mr-3">
              <form class="form-inline" method="get" action="/videos/search">
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
      </nav><br>
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
    <div class="container mt-4">
        <!-- Overall Report Section -->
        <div class="video-info overall-report">
            <h2 style="margin-bottom: 15px;">OVERALL REPORT</h2>
            <div class="info-row">
                <div class="info-box">
                    <span>Total Likes</span>

                    <span id="totalLikes"></span>
                </div>
                <div class="info-box">
                    <span>Total Dislikes</span>
                    <span id="totalDislikes"></span>
                </div>
                <div class="info-box">
                    <span>Total Views</span>
                    <span id="totalViews"></span>
                </div>
                <div class="info-box">
                    <span>Total Plays</span>
                    <span id="totalPlays"></span>
                </div>
                <div class="info-box">
                    <span>Play Rate</span>
                    <span id="playRate"></span>
                </div>
            </div>
            <div class="donutChartContainer">
                <canvas id="donutChart" width="400" height="400"></canvas>
            </div>
        </div>
        <!-- Video Analytics Section -->
        
        </div>        
        <script>
            const videodata = <?php echo $videoinfo; ?>;
            const overall = <?php echo $overall; ?>;
            const n = <?php echo $n; ?>;
        </script>
        <script src="/public/js/analytics.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="/public/js/confirm.js"></script>
        <script src="/public/js/logo.js"></script>
</body>
</html>