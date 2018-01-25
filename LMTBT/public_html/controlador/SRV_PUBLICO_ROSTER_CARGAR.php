<?php 
	require 'SRV_CONEXION.php';
	$db = new SRV_CONEXION();
	$conn = $db->getConnection();
	$id = $_POST['id_equipo'];
        $html = array("logo","option");
        $html[1]="";
        $html[0]="";
        
        $foto_equipo = "SELECT LOGOTIPO_EQUIPO FROM equipos WHERE ID_EQUIPO=? ";
        $pre = $conn->prepare($foto_equipo);
        $pre->bind_param("i", $id);
        $pre->execute();
        $equipo = $pre->get_result();
        $LOGO = $equipo->fetch_array(MYSQLI_ASSOC);
         if($equipo && $equipo->num_rows>0){
              $imagen["LOGOTIPO_EQUIPO"] = base64_encode($LOGO["LOGOTIPO_EQUIPO"]);
              $html[0]= "<div ><img class='media-object' style='width:300px' src='data:image/png;base64," .  $imagen["LOGOTIPO_EQUIPO"]. "'  ></div>";
          }
        
        $categorias = "SELECT NOMBRE_CATEGORIA,ID_CATEGORIA FROM categorias";
        $result = $conn->query($categorias);
	if($result && $result->num_rows>0){
                $pre = $conn->prepare("SELECT ID_ROSTER FROM rosters WHERE ID_EQUIPO=? AND ID_CATEGORIA=? ORDER BY ID_ROSTER DESC LIMIT 1");
		 $html[1].= "<option value='' disabled selected hidden>Selecciona un Roster</option>";
		while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $pre->bind_param("ii", $id,$row['ID_CATEGORIA']);
                    $pre->execute();
                    $roster = $pre->get_result();
                    $info = $roster->fetch_array(MYSQLI_ASSOC);
                    if($roster && $roster->num_rows>0){
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