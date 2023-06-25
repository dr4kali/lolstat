<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="css/bootstrap.css"/>
 </head>
  <body>
        <?php include 'dbconf.php';
          $summonerName = $_GET['summonerName'];
           $sql = "SELECT profile, name, level, total_mastery FROM summoners WHERE name='$summonerName'";
           $result = $conn->query($sql);
           
           // Check if there are any results
           if ($result->num_rows > 0) {
               // Loop through each row and display the data
               while ($row = $result->fetch_assoc()) {
                   $imageUrl = $row["profile"];
                   $name = $row["name"];
                   $level = $row["level"];
                   $total_mastery = $row["total_mastery"];
               }
              }
        ?>
        <section class="vh-100" style="background-color: #9de2ff;">
            <div class="container py-5 h-100">
              <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col col-md-9 col-lg-7 col-xl-5">
                  <div class="card" style="border-radius: 15px;">
                    <div class="card-body p-4">
                      <div class="d-flex text-black">
                        <div class="flex-shrink-0">
                          <?php
                            // Fetch image URLs from the database
                            // Display the image
                            echo "<img src='$imageUrl' alt='Generic placeholder image' class='img-fluid'
                            style='width: 180px; border-radius: 10px;'/>";
                            ?>
                        </div>
                        <div class="flex-grow-1 ms-3">
                        <?php 
                          // Display the name
                            echo "<h5 class='mb-1'>$name</h5>";
                        ?>
                          <p class="mb-2 pb-1" style="color: #2b2a2a;">Akali Lover</p>
                          <div class="d-flex justify-content-start rounded-3 p-2 mb-2"
                            style="background-color: #efefef;">
                            <div>
                              <p class="small text-muted mb-1">Level</p>
                              <?php
                              // Display the name
                              echo "<p class='mb-0'>$level</p>";
                              ?>
                            </div>
                            <div class="px-3">
                              <p class="small text-muted mb-1">Total Mastery</p>

                              <?php
                              // Display the total mastery
                              echo "<p class='mb-0'>$total_mastery</p>";
                              ?>
                            </div>
                            <div>
                              <p class="small text-muted mb-1">Rating</p>
                              <p class="mb-0">8.5</p>
                            </div>
                          </div>
                          <div class="d-flex pt-1">
                            <button type="button" class="btn btn-outline-primary me-1 flex-grow-1">Chat</button>
                            <button type="button" class="btn btn-primary flex-grow-1">Follow</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
          <script src="js/bootstrap.bundle.js"></script>
  </body>
</html>