<!DOCTYPE html>
<html>
<head>
    <title>Google</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="validateForm.js"></script>
   <!-- <script src="script.js"></script> -->
</head>
<body>
    <div id="header">
        <img src="/mains/logo.png" alt="website logo" height="350px" width="550px">
    </div>
    <div id="content">
        <form id="search-box" action="retrieve_summoner.php" method="post" onsubmit="return validateForm()">
            <input type="text" name="summoner_name" id="summoner_name" placeholder="Search Summoner">
            <input type="submit" value="Search">
        </form>
    </div>
    <div id="footer">
        &copy; lolstat.gg
    </div>
    <pre>HI sdfakfj</pre>
</body>
</html>
