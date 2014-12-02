<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
<?php
 // AQUESTA ÉS LA PÀGINA DE RESULTATS DEL FOMRULARI    
        
require_once('TwitterAPIExchange.php');
class Twitter{

	function getTweets($link){	

		$settings = array(
    'oauth_access_token' => "202370583-SREY1WMP9sZdFNhqZVQDtRZVZ1h8Ht8YBwLorS9V",
    'oauth_access_token_secret' => "so2s4plJkBsafCf2o0uwZDFcB8PQrmhdbzHAnaooIHteH",
    'consumer_key' => "vdpF91HTbrr4KYOnrwOTNv78j",
    'consumer_secret' => "IzKe9THMCsNiQxJpX7ERGfcoZXxKx6h7jSr2RWRXdiIXKZaWRH"
		);

		$url = 'https://api.twitter.com/1.1/search/tweets.json';
		$requestMethod = 'GET';
		$getfield = $link;
		
            try{
		$twitter = new TwitterAPIExchange($settings);
		$json =  $twitter->setGetfield($getfield)
		->buildOauth($url, $requestMethod)
		->performRequest();
		return $json;
		
		}catch (Exception $e){
			return $e->getMessage();
		}

	}

	function getArrayTweets($jsonraw){
		$rawdata = "";
		$json = json_decode($jsonraw);
		
		$arreglo=$json->statuses;// Accedo al arreglo que esta guardado en el Objeto

		$num_items = count($arreglo);// Cuenta la cantidad de elementos que tiene el arreglo

		for($i=0; $i<$num_items; $i++){

			$user = $arreglo[$i];

			$fecha = $user->created_at;
			$url_imagen = $user->user->profile_image_url;
			$screen_name = $user->user->screen_name;
			$tweet = $user->text;

		$imagen = "<a href='https://twitter.com/".$screen_name."' target=_blank><img src=".$url_imagen."></img></a>";
		$name = "<a href='https://twitter.com/".$screen_name."' target=_blank>@".$screen_name."</a>";

			$rawdata[$i][0]=$fecha;
			$rawdata[$i]["FECHA"]=$fecha;
			$rawdata[$i][1]=$imagen;
			$rawdata[$i]["imagen"]=$imagen;
			$rawdata[$i][2]=$name;
			$rawdata[$i]["screen_name"]=$name;
			$rawdata[$i][3]=$tweet;
			$rawdata[$i]["tweet"]=$tweet;
		}
		return $rawdata;
	}

	function displayTable($rawdata){

		//DIBUJAMOS LA TABLA
		echo '<table border=1>';
		$columnas = count($rawdata[0])/2;
		//echo $columnas;
		$filas = count($rawdata);
		//echo "<br>".$filas."<br>";
		//Añadimos los titulos

		for($i=1;$i<count($rawdata[0]);$i=$i+2){
			next($rawdata[0]);
			echo "<th><b>".key($rawdata[0])."</b></th>";
			next($rawdata[0]);
		}
		for($i=0;$i<$filas;$i++){
			echo "<tr>";
			for($j=0;$j<$columnas;$j++){
				echo "<td>".$rawdata[$i][$j]."</td>";

			}
			echo "</tr>";
		}
		echo '</table>';
	}
}

try{
    //fabricar el link de cerca amb les dades que vénen del formulari.
    //ERROR: Els camps no poden quedar buits. S'ha de fer un bucle que comprovi.
    //ERROR: Cal resoldre els paràmetres com f=, opcions de cerca, etc.
    //Totes les opcions de cerca: dev.twitter.com/rest/reference/get/search/tweets
    //El putu twitter et respon el que li dóna la gana. No queda clar quin criteri fa servir, investigar.
    
  //  $lat = "41.39479";2.1487679
  //  $lon = "2.1487679";
  //  $near = "10";
  //  $count = "100";
  //  $query = "patata";
    
    $query =  filter_input(INPUT_GET, 'query');
    $lat =  filter_input(INPUT_GET, 'lat');
    $lon =  filter_input(INPUT_GET, 'lon');
    $near =  filter_input(INPUT_GET, 'near');
    $count =  filter_input(INPUT_GET, 'count');
   
    
    $num = "&count=".$count;
    $geo = "&geocode=".$lat.",".$lon.",".$near."km";
    $cerca = '?q='.$query; //ERROR: les " i espais i d'altres caràcters especials s'han de codificar. Encodeurl() no serveix.
    
    $link = $cerca.'&result_type=recent'.$num.$geo; //'&f=realtime'
    // echo $link;
$twitterObject = new Twitter();
$jsonraw =  $twitterObject->getTweets($link);
$rawdata =  $twitterObject->getArrayTweets($jsonraw);
$twitterObject->displayTable($rawdata);
}

   catch (Exception $ex)
{
	echo $ex->getMessage();
}
?>     
    </body>
</html>
