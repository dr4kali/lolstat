<!DOCTYPE html>
<html>
<head>
    <title>Google</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
   <!-- <script src="script.js"></script> -->
</head>
<body>
    <div id="header">
        <img src="https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png" alt="Google Logo">
    </div>
    <div id="content">
        <form id="search-box" action="retrieve_summoner.php" method="post">
            <input type="text" name="summoner_name" id="summoner_name" placeholder="Search Summoner">
            <input type="submit" value="Search">
        </form>
    </div>
    <div id="footer">
        &copy; lolstat.gg
    </div>
</body>
</html>
