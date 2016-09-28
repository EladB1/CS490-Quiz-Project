<html>
<body>

<?php

$info = array("username" => $_POST["username"],"password" => $_POST["password"] );//puts user and pass info into an array

$json_data = json_encode($info);

$ch = curl_init();
//sends the info
curl_setopt($ch, CURLOPT_URL, "https://web.njit.edu/~dyp6/");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $info);

//receives the info
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch); //stores output of url into variable

curl_close($ch); //closes the connection 

var_dump($response); //shows output, only for test purposes



?>

</body>
</html>

