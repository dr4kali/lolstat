<?php

include "dbconf.php";

// Get the summoner name from the form input
$summonerName = $_GET['summonerName'];

// URL encode the summoner name
$encodedSummonerName = rawurlencode($summonerName);

// Check if summoner name already exists in the database
$normalizedName = strtolower($summonerName);

//Riot Game API
$API = "RGAPI-e1731840-f62b-4fba-9afc-13fac8fce1f0";

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

$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
if ($response !== false) {
    // Decode the JSON response
    $data = json_decode($response, true);

    // Get the profile icon ID
    $profileIconId = $data['profileIconId'];

    // Generate the URL for the profile icon image
    $profileIconUrl = "http://ddragon.leagueoflegends.com/cdn/13.12.1/img/profileicon/{$profileIconId}.png";

    // Get the summoner's name
    $sName = $data['name'];

    // Get the summoner's level
    $summonerLevel = $data['summonerLevel'];

    // Get the summoner's ID
    $summonerId = $data['id'];
}else {
    $error = curl_error($curl);
    // Handle the error
    echo "cURL Error: " . $error;
}

curl_close($curl);

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
}
    $updateQuery = "UPDATE summoners SET profile='$profileIconUrl', level='$summonerLevel', total_mastery='$championMasteryScore'   WHERE name='$normalizedName'";
    $result= mysqli_query($conn,$updateQuery);

// Close the second cURL session
curl_close($championMasteryCurl);

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
$stmt = $conn->prepare("UPDATE mastery SET championLevel = ?, championPoints = ? WHERE name = ? AND championId = ?");

// Bind the parameters and execute the statement for each data entry
foreach ($data as $entry) {
    $championId = $entry['championId'];
    $championLevel = $entry['championLevel'];
    $championPoints = $entry['championPoints'];

    $stmt->bind_param("iisi",$championLevel, $championPoints, $normalizedName, $championId);
    $stmt->execute();
}

// Close the statement
$stmt->close();
// Close the connection
$conn->close();
$cleanedName = str_replace('  ', ' ', $sName);
$url = "http://localhost/mains/results.php?summonerName=". urlencode($cleanedName);
header("Location: $url");
exit();
?>