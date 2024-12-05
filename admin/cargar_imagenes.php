<?php
header("Content-Type: application/json; charset=utf-8");

$uploaddir = "uploads/";
$conteo = count($_FILES["archivos"]["name"]);
$url=array();
for ($i = 0; $i < $conteo; $i++) {
    $ubicacionTemporal = $_FILES["archivos"]["tmp_name"][$i];
    
    $nombreArchivo = $_FILES["archivos"]["name"][$i];
    $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
    // Renombrar archivo
    $nuevoNombre = sprintf("%s_%d.%s", uniqid(), $i, $extension);
    // Mover del temporal al directorio actual


  /*   move_uploaded_file(); */
    if (move_uploaded_file($ubicacionTemporal, $uploaddir.$nuevoNombre)) {
        $url [$i] = $uploaddir.$nuevoNombre; 
       
     /*    $i=$i+1; */
        
    } else {
       
        echo json_encode(array("response" => "error", "data2" =>$_FILES['archivos']['error']));//$_FILES["uploadedfile"]["tmp_name"],));
    }
    
}
// Responder al cliente
echo json_encode(array("response" => "success", "data" => $url));

/*header("Content-Type: application/json; charset=utf-8");
function renameDuplicates($path, $file){   
    $fileName = pathinfo($path . $file, PATHINFO_FILENAME);
    $fileExtension = "." . pathinfo($path . $file, PATHINFO_EXTENSION);
    $returnValue = $fileName . $fileExtension;
    $copy = 1;
    while(file_exists($path . $returnValue)){
        $returnValue = $fileName . '-ml-'. $copy . $fileExtension;
        $copy++;
    }
    return $returnValue;
}

$uploaddir = "bluenergy_file/";
if (!file_exists($uploaddir))
    mkdir($uploaddir, 0777, true);

$file1 = str_replace(' ', '_', $_FILES['uploadedfile']['name']);
$file1 = renameDuplicates($uploaddir, strtolower(basename($file1)));

$uploadfile = $uploaddir . $file1;
if (is_writable($uploaddir)) {
    if (move_uploaded_file($_FILES["uploadedfile"]["tmp_name"], $uploadfile)) {
        $url = "bluenergy_file/" . $file1;
        $json = array("response" => "success", "data" => $url);
        echo json_encode($json);
    } else {
        echo json_encode(array("response" => "error", "data2" => $_FILES["uploadedfile"]["tmp_name"]));//$_FILES["uploadedfile"]["tmp_name"],));
    }
} else {
    echo json_encode(array("response" => "error", "data2" => "TERRIBLE ERROR!"));//$_FILES["uploadedfile"]["tmp_name"],));
}
*/



?>