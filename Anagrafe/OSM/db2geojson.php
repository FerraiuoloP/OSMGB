<?php
$config_path = __DIR__;
$util1="../util.php";
$util2="../db/db_conn.php";
require_once $util1;
require_once $util2;
setup();
isLogged("gestore");
unsetPag(basename(__FILE__));
/**
*** PHP GeoJSON Constructor, adpated from https://github.com/bmcbride/PHP-Database-GeoJSON
***
*** Legge le case da DB e genera il file geojson
**/
//header("Content-Type: application/json; charset=UTF-8");

$query = "SELECT c.id, c.nome,";
$query .= " z.nome zona, c.id_moranca, m.nome nome_moranca,";
$query .= " c.nome, p.id id_pers, p.nominativo as capo_famiglia,";
$query .= " c.id_osm as id_osm, c.lat, c.lon,";
$query .= " DATE_FORMAT(c.data_inizio_val, \"%d/%m/%Y\") as data_val ";
$query .= " FROM morance m INNER JOIN casa c ON m.id = c.id_moranca ";
$query .= " INNER JOIN zone z  ON  z.cod = m.cod_zona ";
$query .= " LEFT JOIN pers_casa pc ON c.id  = pc.id_casa ";
$query .="  AND pc.cod_ruolo_pers_fam = 'CF'";
$query .="  LEFT JOIN persone p ON p.id = pc.id_pers";
$query .= " WHERE c.DATA_FINE_VAL is null";
$query .= " AND (lat is NOT NULL AND lat !=0)";
$query .= " AND (lon is NOT NULL AND lon !=0)";


$result = $conn->query($query);  

//echo "query:"  . $query;

$result = mysqli_query($conn, $query);

if (!$result) {
    echo 'Errore istruzione SQL\n';
    echo  $query;
    exit;
}

# Build GeoJSON feature collection array
$geojson = array(
   'type'      => 'FeatureCollection',
   'features'  => array()
);

# Loop through rows to build feature arrays
  while ($row = mysqli_fetch_assoc($result)) 
   {

	$query2="SELECT COUNT(pers_casa.ID_PERS) as num_persone from pers_casa WHERE ID_CASA='$row[id]'";
                $result2 = $conn->query($query2);
                $row2 = $result2->fetch_array();
//                echo "num persone".$row2['num_persone']."<br>";
	//printf ("-nome casa:%s \n", $row['nome']);
    $feature = array(
		'type' => 'Feature',
        'geometry' => array(
            'type' => 'Point',
            # Pass Longitude and Latitude Columns here
            'coordinates' => array(doubleval($row['lon']),doubleval($row['lat']))
        ),
        # Pass other attribute columns here
        'properties' => array(
			'name' => $row['id'],		//name = id Casa
			'tag' =>  $row['zona'],
			'verified' => $row['data_val'],
			'description' => array(
				'id OSM' => $row['id_osm'],
				'Nome Casa' => utf8_encode ($row['nome']),
				'Moranca' => utf8_encode ($row['nome_moranca']),
				'Capo Famiglia' => utf8_encode ($row['capo_famiglia']),
				'Numero Persone' => $row2['num_persone']
            ))
        );
    # Add feature arrays to feature collection array
    array_push($geojson['features'], $feature);
   }
  header('Content-type: application/json');
  //echo json_encode($geojson, JSON_NUMERIC_CHECK);

  /* free result set */
  mysqli_free_result($result);
  /* close connection */
  mysqli_close($conn);


//write json data into data.json file
//Convert updated array to JSON

$jsondata = json_encode($geojson, JSON_PRETTY_PRINT);

$myFile = "points.geojson";

// Nb: Controllare se si hanno i permessi di scrittura (777) sulla cartella su server
/*$file = fopen($myFile,"w+");
fwrite($file,$jsondata);
fclose($file);
*/

if(file_put_contents($myFile, $jsondata))
  {
	 echo 'Dati salvati correttamente sul file';
  }
 else 
	echo "Errore nel salvataggio dati su  file  points.geojson";

header('Content-Type: text/html; charset=utf-8');
header("Location:index_text.html");
?>