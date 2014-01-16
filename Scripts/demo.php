<?php
$country=$city=$ip=$query_string=$http_referer=$http_user_agent=$isbot=NULL;
include_once('config.php');
include_once('ip2locationlite.class.php');

$ip = $_SERVER['REMOTE_ADDR'];
$query_string = $_SERVER['QUERY_STRING'];
if (isset($_SERVER['HTTP_REFERER'])) {
    $http_referer = $_SERVER['HTTP_REFERER'];
}
else {
    $http_referer = "unknown";
}
$http_user_agent = $_SERVER['HTTP_USER_AGENT'];

if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    throw new InvalidArgumentException("IP invalid");
}

$ipLite = new ip2location_lite;
$ipLite->setKey('b9afa6a06d06ac61e4879994d40bbd76af1137438cd1ca2d57a6334345a70f33');
$locations = $ipLite->getCity($ip);
$errors = $ipLite->getError();

if (!empty($locations) && is_array($locations)) {
    foreach ($locations as $field => $val) {
        if ($field == 'countryName')
            $country = $val;
        if ($field == 'cityName')
            $city = $val;
    }
}

function is_bot() {
    $botlist = array("Teoma", "alexa", "froogle", "Gigabot", "inktomi",
        "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory",
        "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot",
        "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp",
        "msnbot", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz",
        "Baiduspider", "Feedfetcher-Google", "TechnoratiSnoop", "Rankivabot",
        "Mediapartners-Google", "Sogou web spider", "WebAlta Crawler", "TweetmemeBot",
        "Butterfly", "Twitturls", "Me.dium", "Twiceler");
    foreach ($botlist as $bot) {
        if (strpos($_SERVER['HTTP_USER_AGENT'], $bot) !== false)
            return true;
    }
    return false;
}

if (is_bot()) {
    $isbot = 1;
}
else {
    $isbot = 0;
}

$query = "INSERT INTO tracker (country, city, ip, query_string, http_referer, http_user_agent, isbot)
				VALUES ('$country','$city', '$ip', '$query_string', '$http_referer' ,'$http_user_agent' , $isbot)";

$query = $database_connection->prepare($query);
$query->execute();
?>