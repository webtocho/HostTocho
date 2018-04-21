<?php 
	require 'SRV_CONEXION.php';
	$db = new SRV_CONEXION();
	$conn = $db->getConnection();
        //En este php retornamos los rosters de un equipo y su logo
	$id = $_POST['id_equipo'];
        $html = array("logo","option");
        $html[1]="";
        $html[0]="";
        //En esta parte recuperamos el logo de un equipo
        $foto_equipo = "SELECT LOGOTIPO_EQUIPO FROM equipos WHERE ID_EQUIPO=? ";
        $pre = $conn->prepare($foto_equipo);
        $pre->bind_param("i", $id);
        $pre->execute();
        $equipo = $pre->get_result();
        $LOGO = $equipo->fetch_array(MYSQLI_ASSOC);
         if($equipo && $equipo->num_rows>0){
             //Si se obtuvo la foto la añadimos al arreglo de html que regresaremos
              $imagen["LOGOTIPO_EQUIPO"] = base64_encode($LOGO["LOGOTIPO_EQUIPO"]);
              $html[0]= "<div ><img id='imgLogoEquipo' class='img-rounded img-responsive' src='data:image/png;base64," .  $imagen["LOGOTIPO_EQUIPO"]. "'  ></div>";
          }
        //Seleccionamos todas las categorias que hayan en el sistema
        $categorias = "SELECT NOMBRE_CATEGORIA,ID_CATEGORIA FROM categorias";
        $result = $conn->query($categorias);
	if($result && $result->num_rows>0){
            //Cuando tenemos las categorias, seleccionamos el roster mas reciente del equipo, dado que se guardan los rosters anteriores
                $pre = $conn->prepare("SELECT ID_ROSTER FROM rosters WHERE ID_EQUIPO=? AND ID_CATEGORIA=? ORDER BY ID_ROSTER DESC LIMIT 1");
		 $html[1].= "<option value='' disabled selected hidden>Selecciona un Roster</option>";
		while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    //Realizamos un ciclo para buscar que haya un roster (para cada categoria)
                    $pre->bind_param("ii", $id,$row['ID_CATEGORIA']);
                    $pre->execute();
                    $roster = $pre->get_result();
                    $info = $roster->fetch_array(MYSQLI_ASSOC);
                    if($roster && $roster->num_rows>0){
                        //SI el roster de la categoria existe retornamos el option para el select
                        $html[1].=  "<option value='".$info['ID_ROSTER']."'>".$row['NOMBRE_CATEGORIA']."</option>";
                    }
		}
                $pre->close();
	}else{
		 $html[1].= "<option value='' disabled selected hidden> No se encontraron rosters</option>";
	}
         echo json_encode($html);
         
	$conn->close();
?>