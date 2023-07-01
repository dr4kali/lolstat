<?php

include "dbconf.php";

// Get the summoner name from the form input
$summonerName = $_POST['summoner_name'];

// URL encode the summoner name
$encodedSummonerName = urlencode($summonerName);

// Check if summoner name already exists in the database
$normalizedName = strtolower($summonerName);
$checkQuery = "SELECT name FROM summoners WHERE name = '$normalizedName'";
$result = mysqli_query($conn, $checkQuery);

//Riot Game API
$API = "RGAPI-a50da25e-d289-44b0-ba02-862a62c767e3";

if (mysqli_num_rows($result) > 0) {
    // Summoner name already exists in the database, redirect to results page
    $url = "http://localhost/mains/results.php?summonerName=". urldecode($summonerName);
    header("Location: $url");
    exit();
}



// Format the API endpoint URL with the encoded summoner name
$url = "https://sg2.api.riotgames.com/lol/summoner/v4/summoners/by-name/{$encodedSummonerName}";


// Set the headers
$headers = [
    "User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:102.0) Gecko/20100101 Firefox/102.0",
    "Accept-Language: en-US,en;q=0.5",
    "Accept-Charset: application/x-www-form-urlencoded; charset=UTF-8",
    "Origin: https://developer.riotgames.com",
    "X-Riot-Token: $API"
];

// Initialize cURL
$curl = curl_init();

// Set cURL options
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

// Execute the cURL request
$response = curl_exec($curl);

// Get the response code
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if ($httpCode == 404) {
    // Display an alert message
    echo "<script>alert('Summoner not found');</script>";

    // Redirect back to the previous page
    echo "<script>window.location = '{$_SERVER['HTTP_REFERER']}';</script>";
    exit;
}

if ($response !== false) {
    // Decode the JSON response
    $data = json_decode($response, true);

    // Get the profile icon ID
    $profileIconId = $data['profileIconId'];

    // Generate the URL for the profile icon image
    $profileIconUrl = "http://ddragon.leagueoflegends.com/cdn/13.13.1/img/profileicon/{$profileIconId}.png";

    // Get the summoner's name
    $summonerName = $data['name'];

    // Get the summoner's level
    $summonerLevel = $data['summonerLevel'];

    // Get the summoner's ID
    $summonerId = $data['id'];

    //Inserting responses to database
    $sql = "INSERT INTO summoners(name, level, profile) 
    VALUES ('$normalizedName','$summonerLevel','$profileIconUrl')";
	  //die($sql);
    $result= mysqli_query($conn,$sql);

    if ($result == TRUE) {
      echo "New record created successfully.";
    }else{
      echo "Error:". $sql . "<br>". $conn->error;
    } 

    //Initialize a new cURL session for the second API request
    $championMasteryCurl = curl_init();

    // Make a second API request for the champion mastery score
    $championMasteryUrl = "https://sg2.api.riotgames.com/lol/champion-mastery/v4/scores/by-summoner/{$summonerId}";


    $championMasteryHeaders = [
        "User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:102.0) Gecko/20100101 Firefox/102.0",
        "Accept-Language: en-US,en;q=0.5",
        "Accept-Charset: application/x-www-form-urlencoded; charset=UTF-8",
        "Origin: https://developer.riotgames.com",
        "X-Riot-Token: $API"
    ];

    //Set cURL options for the second API request
    curl_setopt($championMasteryCurl, CURLOPT_URL, $championMasteryUrl);
    curl_setopt($championMasteryCurl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($championMasteryCurl, CURLOPT_HTTPHEADER, $championMasteryHeaders);
    
    //Execute the second API request
    $championMasteryResponse = curl_exec($championMasteryCurl);

    if ($championMasteryResponse !== false) {
        // Decode the JSON response
        $data = json_decode($championMasteryResponse, true);
    
        // Get the total mastery
        $championMasteryScore = intval($championMasteryResponse);
        echo "<h1>{$championMasteryScore}</h1>";

        //Inserting champion mastery to database
        $sql = "UPDATE `summoners` SET `total_mastery`='$championMasteryScore' WHERE `name`='$summonerName'"; 

	    //die($sql);
        $result= mysqli_query($conn,$sql);

        if ($result == TRUE) {
        echo "mastery updated successfully.";
    }else{
        echo "Error:". $sql . "<br>". $conn->error;
    }

}

else {
    // Print an error message along with the cURL error for the second API request
    echo 'Second API request failed: ' . curl_error($championMasteryCurl);
    
    // Additional debug information
    echo 'Curl info for the second API request:';
    var_dump(curl_getinfo($championMasteryCurl));
}

// Close the second cURL session
curl_close($championMasteryCurl);

} else {
    // Print an error message along with the curl error
    echo 'Request failed: ' . curl_error($curl);
    
    // Additional debug information
    echo 'Curl info:';
    var_dump(curl_getinfo($curl));
}
// Close the cURL session
curl_close($curl);

  // Construct the API URL
  $url = "https://sg2.api.riotgames.com/lol/champion-mastery/v4/champion-masteries/by-summoner/{$summonerId}/top";


  // Set the API headers
  $headers = array(
    'X-Riot-Token: ' . $API
  );

  // Initialize cURL and set the options
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

  // Execute the cURL request
  $response = curl_exec($curl);

  // Close cURL
  curl_close($curl);
  
// Decode the JSON response into an array
$data = json_decode($response, true);

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO mastery (name, championId, championLevel, championPoints) VALUES (?, ?, ?, ?)");

// Bind the parameters and execute the statement for each data entry
foreach ($data as $entry) {
    $championId = $entry['championId'];
    $championLevel = $entry['championLevel'];
    $championPoints = $entry['championPoints'];

    $stmt->bind_param("siii",$normalizedName, $championId, $championLevel, $championPoints);
    $stmt->execute();
}

// Close the statement
$stmt->close();
// Close the connection
$conn->close();
$url = "http://localhost/mains/results.php?summonerName=". urldecode($summonerName);
header("Location: $url");
exit();
?>
