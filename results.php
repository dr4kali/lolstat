
<!DOCTYPE html>
<html>
<head>
  <title>Centered Card</title>
  <link rel="stylesheet" type="text/css" href="stylers.css">
</head>
<body>
<?php include 'dbconf.php';
          $summonerName = $_GET['summonerName'];
           $sql = "SELECT profile, name, level, total_mastery FROM summoners WHERE name='$summonerName'";
           $result = $conn->query($sql);
           
           $msql = "SELECT championLevel, championId, championPoints FROM mastery WHERE name='$summonerName'";
           $mresult = $conn->query($msql);

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
              $query1 = "SELECT championId, championPoints, championLevel FROM mastery WHERE `Name` = '$summonerName'";
              $query2 = "SELECT championPoints, championLevel FROM mastery WHERE `Name` = '$summonerName'";
              $query3 = "SELECT championPoints, championLevel FROM mastery WHERE `Name` = '$summonerName'";

              // Execute the queries
              $result1 = mysqli_query($conn, $query1);
              $result2 = mysqli_query($conn, $query2);
              $result3 = mysqli_query($conn, $query3);

              // Check if th    e queries were successful
              if ($result1) {
              // Initial  ize variables
              $champion1 = null;
              $champion2 = null;
              $champion3 = null;
              $champion1P = null;
              $champion2P = null;
              $champion3P = null;
              $champion1L = null;
              $champion2L = null;
              $champion3L = null;

              // Fetch the rows from the result set
              while ($row = mysqli_fetch_assoc($result1)) {
              // Store the values from the columns into separate variables
              if ($champion1 === null) {
              $champion1 = $row['championId'];
              $champion1P = $row['championPoints'];
              $champion1L = $row['championLevel'];
              } elseif ($champion2 === null) {
              $champion2 = $row['championId'];
              $champion2P = $row['championPoints'];
              $champion2L = $row['championLevel'];
              } elseif ($champion3 === null) {
              $champion3 = $row['championId'];
              $champion3P = $row['championPoints'];
              $champion3L = $row['championLevel'];
          }
      }

      // Free the result set
      mysqli_free_result($result1);

      $sortedMastery = [$champion1P, $champion2P, $champion3P];
      sort($sortedMastery);
        // Determine which champion has the highest mastery
        if ($sortedMastery[2] === $champion1P) {
        $maxMasteryChampionId = $champion1;
        $maxMasteryChampionLevel = $champion1L;
        } elseif ($sortedMastery[2] === $champion2P) {
        $maxMasteryChampionId = $champion2;
        $maxMasteryChampionLevel = $champion2L;
        } elseif ($sortedMastery[2] === $champion3P) {
        $maxMasteryChampionId = $champion3;
        $maxMasteryChampionLevel = $champion3L;
      }

        // Determine which champion has the highest mastery
        if ($sortedMastery[0] === $champion1P) {
          $minMasteryChampionId = $champion1;
          $minMasteryChampionLevel = $champion1L;
          } elseif ($sortedMastery[0] === $champion2P) {
          $minMasteryChampionId = $champion2;
          $minMasteryChampionLevel = $champion2L;
          } elseif ($sortedMastery[0] === $champion3P) {
          $minMasteryChampionId = $champion3;
          $minMasteryChampionLevel = $champion3L;
        }

        if ($sortedMastery[1] === $champion1P) {
          $midMasteryChampionId = $champion1;
          $midMasteryChampionLevel = $champion1L;
          } elseif ($sortedMastery[1] === $champion2P) {
          $midMasteryChampionId = $champion2;
          $midMasteryChampionLevel = $champion2L;
          } elseif ($sortedMastery[1] === $champion3P) {
          $midMasteryChampionId = $champion3;
          $midMasteryChampionLevel = $champion3L;
        }


    } else {
        // Query execution failed
        echo "Error: " . mysqli_error($conn);
    }
        ?>
<div class="container">
  <div class="card">
    <?php
      // Fetch image URLs from the database
      // Display the image
      echo "<img src='$imageUrl' alt='Generic placeholder image' class='img-fluid'
      style='width: 180px; border-radius: 10px;'/>";
    ?>
        <h2><?php
        // Display the name
        echo "$summonerName";
        ?></h2>
    <h2>Level:                               
      <?php
        // Display the name
        echo "$level";
        ?>
    </h2>
    <h3>Total Mastery Level:<?php
        // Display the total mastery
        echo "$total_mastery";
        ?>
    </h3>
  </div>
  
  <div class="sub-card">
  <?php 
    $championId = $midMasteryChampionId;

    function getChampionName($championId) {
      $url = 'http://ddragon.leagueoflegends.com/cdn/13.13.1/data/en_US/champion.json';
    
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $response = curl_exec($ch);
    
      if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception('An error occurred: ' . $error);
      }
    
      $data = json_decode($response, true);
      $championData = array_values($data['data']);
      $champion = array_filter($championData, function($champ) use ($championId) {
        return $champ['key'] === strval($championId);
      });
    
      if (count($champion) > 0) {
        return reset($champion)['name'];
      } else {
        throw new Exception('Champion not found.');
      }
    }
    
    try {
      $name = getChampionName($championId);
      $name = str_replace(' ', '', $name);
    } catch (Exception $e) {
      echo 'Error: ' . $e->getMessage();
    }
    
    echo "<img src='http://ddragon.leagueoflegends.com/cdn/13.13.1/img/champion/$name.png' alt='Profile Image'>"
  ?>
 
    <h2>Mastery Points:<?php
        // Display the name
        echo "<p class='mb-0'>$sortedMastery[1]</p>";
        ?></h2>
    <h3>Mastery Level: <?php
        // Display the name
        echo "<p class='mb-0'>$midMasteryChampionLevel</p>";
        ?></h3>
  </div>
  
  <div class="sub-card">
  <?php 
    $championId = $maxMasteryChampionId;

    function getChampionName2($championId) {
      $url = 'http://ddragon.leagueoflegends.com/cdn/13.13.1/data/en_US/champion.json';
    
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $response = curl_exec($ch);
    
      if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception('An error occurred: ' . $error);
      }
    
      $data = json_decode($response, true);
      $championData = array_values($data['data']);
      $champion = array_filter($championData, function($champ) use ($championId) {
        return $champ['key'] === strval($championId);
      });
    
      if (count($champion) > 0) {
        return reset($champion)['name'];
      } else {
        throw new Exception('Champion not found.');
      }
    }
    
    try {
      $name = getChampionName2($championId);
      $name = str_replace(' ', '', $name);
    } catch (Exception $e) {
      echo 'Error: ' . $e->getMessage();
    }
    
    echo "<img src='http://ddragon.leagueoflegends.com/cdn/13.13.1/img/champion/$name.png' alt='Profile Image'>"
  ?>
 
    <h2>Mastery Points: <?php
        // Display the name
        echo "<p class='mb-0'>$sortedMastery[2]</p>";
        ?></h2>
    <h3>Mastery Level: <?php
        // Display the name
        echo "<p class='mb-0'>$maxMasteryChampionLevel</p>";
        ?></h3>
  </div>
  
  <div class="sub-card">
  <?php 
    $championId = $minMasteryChampionId;

    function getChampionName3($championId) {
      $url = 'http://ddragon.leagueoflegends.com/cdn/13.13.1/data/en_US/champion.json';
    
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $response = curl_exec($ch);
    
      if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception('An error occurred: ' . $error);
      }
    
      $data = json_decode($response, true);
      $championData = array_values($data['data']);
      $champion = array_filter($championData, function($champ) use ($championId) {
        return $champ['key'] === strval($championId);
      });
    
      if (count($champion) > 0) {
        return reset($champion)['name'];
      } else {
        throw new Exception('Champion not found.');
      }
    }
    
    try {
      $name = getChampionName3($championId);
      $name = str_replace(' ', '', $name);
    } catch (Exception $e) {
      echo 'Error: ' . $e->getMessage();
    }
    
    echo "<img src='http://ddragon.leagueoflegends.com/cdn/13.13.1/img/champion/$name.png' alt='Profile Image'>"
  ?>
    <h2>Mastery Points: <?php
        // Display the name
        echo "<p class='mb-0'>$sortedMastery[0]</p>";
        ?></h2>
    <h3>Mastery Level: <?php
        // Display the name
        echo "<p class='mb-0'>$minMasteryChampionLevel</p>";
        ?></h3>
  </div>
</div>
</body>
</html>
