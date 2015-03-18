<?php

header('Content-Type: text/html;charset=utf-8');


	
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"http://www.acas.rs/acasPublic/imovinaFunkcioneraSearch.htm");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,
            " sEcho=2&iColumns=3&sColumns=&iDisplayStart=0&iDisplayLength=10&mDataProp_0=0&mDataProp_1=1&mDataProp_2=2&mDataProp_3=3&mDataProp_4=4&prezime=&ime=&");

// in real life you should use something like:
// curl_setopt($ch, CURLOPT_POSTFIELDS, 
//          http_build_query(array('postvar1' => 'value1')));

// receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);

curl_close ($ch);
$result=json_decode($server_output);

	/* database connection */ 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datascrapp";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
mysqli_set_charset($conn, 'utf8');
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

foreach($result->aaData as $item)
{

	$sql = "INSERT INTO table1(ime_prezime,funkcija)
    VALUES ('".strip_tags($item['0'])."', '".strip_tags($item['1'])."')";   
	$conn->query($sql);
	$user_id= $conn->insert_id;
	
	
	$array=explode('<br>',$item['2']);
    foreach($array as $a)
		{
			
			$html = "{$a}";
			preg_match_all('/href=[\'"\>]*[\'"]([^\s\>\'"]*)/', $html, $matches);
			$hrefs = ($matches[1] ? $matches[1] : false);
			$url = implode('',$hrefs);
			$url1= preg_replace('/[^A-Za-z0-9\-]/', '', $url);
			$good= str_replace('javascriptprikazIzvestaja', '', $url1);
			$sql1 = "INSERT INTO table2(id_funkcionera,datum,url)
			VALUES ('".$user_id."', '".strip_tags($a)."','".$good."')";
			$conn->query($sql1);
			$ch1 = curl_init();
			curl_setopt($ch1, CURLOPT_URL,"http://www.acas.rs/acasPublic/izvestajDetails.htm?parent=pretragaIzvestaja&izvestajId={$good}");
			curl_setopt($ch1, CURLOPT_POST, 1);

curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);

$server_output1 = curl_exec ($ch1);

curl_close ($ch1);
//echo $server_output1;
		}
}
$conn->close();  


/* end of database connection */
	
// further processing ....
echo "<pre>";
print_r($result->aaData);
	echo "</pre>";

?>
