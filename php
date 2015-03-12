<?php

header('Content-Type: text/html;charset=utf-8');
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"http://www.acas.rs/acasPublic/funkcionerSearch.htm");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,
            " sEcho=2&iColumns=5&sColumns=&iDisplayStart=0&iDisplayLength=10&mDataProp_0=0&mDataProp_1=1&mDataProp_2=2&mDataProp_3=3&mDataProp_4=4&funkcioner.prezime=&funkcioner.ime=&datumStupanja=&datumStupanjaDo=&datumPrestanka&=datumPrestankaDo=&funkcija.id=&funkcijaName=&organizacija.id=&organizacija.mesto.id&mestoOrganizacijeNaziv=&aktivna=&");

// in real life you should use something like:
// curl_setopt($ch, CURLOPT_POSTFIELDS, 
//          http_build_query(array('postvar1' => 'value1')));

// receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);

curl_close ($ch);
$result=json_decode($server_output);
// further processing ....
echo "<pre>";
print_r($result->aaData);
	echo "</pre>";

?>
