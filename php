<?php
ini_set("max_execution_time",0);
header('Content-Type: text/html;charset=utf-8');
include_once("simple_html_dom.php");


/* database connection */ 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scrap";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
mysqli_set_charset($conn, 'utf8');
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

/*call methods*/
getPage1($conn);
//getPage2($conn,"Ojo-NTU_PCYyPD43PQ,,",10);
//getPage3("EvidFunPrijavePrikaz.php?ID=Oz4-PzU8PyY6PzwwPQ,,&Godina=2005&Rbr=1",10,$conn);

/*get results from home page*/
function getPage1($conn){
	
	  $pages=array(1=>"Svi funkcioneri",13=>"Lica koja nijesu na spisku j. funkcionera");
	foreach($pages as $key=>$value){
			@$html = file_get_html("http://www.konfliktinteresa.me/new/evid_funkc/funkcioneri/EvidencijaFun.php?txtNaziv=&VR_FUN=$key");
	         if(empty($html))continue;
	         foreach( $html->find('table.table-hover a')  as $article) {
	            $link=parse_url($article->href);
			    $item['id']    = substr($link['query'],3,strlen($link['query'])-3);
			    $item['text']  = $article->plaintext;
			    $sql = "INSERT INTO table1(id_category,name)
				VALUES ('".$key."', '".$item['text']."')";		
				$conn->query($sql);
				$funkc_id= $conn->insert_id;
				getPage2($conn,$item['id'],$funkc_id);
			   
	         }
	        
	 }
}
/*get results from second link (page2)*/
function getPage2($conn,$id,$funkc_id)
{  
   $page2_html = file_get_html("http://www.konfliktinteresa.me/new/evid_funkc/funkcioneri/EvidFunPrijave.php?ID=$id");
    $i=0;
    foreach( $page2_html->find('table.t2 tr')  as $article) {
    	if($i==0){$i++;continue;}
           
            $item['year']=$article->find('font', 0)->plaintext;
            $item['paintext']=$article->find('a', 0)->plaintext;
            $item['link']=$article->find('a', 0)->href;
		    $sql1= "INSERT INTO table2(id_funkionera,godina,naziv)
						 VALUES ('".$funkc_id."', '".$item['year']."','".$item['paintext']."')";   
			$conn->query($sql1);
			$id_table=$conn->insert_id;
			if(!empty($item['link']))
			  {getPage3($item['link'],$id_table,$conn);}
         }  
}
/*get results from third link (page3)*/
function getPage3($link,$id_table,$conn)
{   
	$page3_html = file_get_html("http://www.konfliktinteresa.me/new/evid_funkc/funkcioneri/$link");
     $i=0;
    foreach( $page3_html->find('table.t2 tr')  as $article) {
    	if($i==0){$i++;continue;}
           
            $item['year']=$article->find('font', 0)->plaintext;
            $item['plaintext']=$article->find('font', 1)->plaintext;
		    $articles[]=$item;
		   $i++;

         }
           $sql2= "INSERT INTO table3(id_table,adresa,javna_funkcija,mjesecna_n,nepokretna_im,mjesecna_p)
						 VALUES ('".$id_table."','".$articles[0]['plaintext']."', '".$articles[1]['plaintext']."','".$articles[5]['plaintext']."','".$articles[8]['plaintext']."','".$articles[12]['plaintext']."')";   
			$conn->query($sql2);     
}
