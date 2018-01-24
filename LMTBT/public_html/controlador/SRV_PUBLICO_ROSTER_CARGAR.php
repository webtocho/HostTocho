<?php 
	require 'SRV_CONEXION.php';
	$db = new SRV_CONEXION();
	$conn = $db->getConnection();
	$id = $_POST['id_equipo'];
        $categorias = "SELECT NOMBRE_CATEGORIA,ID_CATEGORIA FROM categorias";
        $result = $conn->query($categorias);
	if($result && $result->num_rows>0){
                $pre = $conn->prepare("SELECT ID_ROSTER FROM rosters WHERE ID_EQUIPO=? AND ID_CATEGORIA=? ORDER BY ID_ROSTER DESC LIMIT 1");
		echo "<option value='' disabled selected hidden>Selecciona una categoria</option>";
		while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $pre->bind_param("ii", $id,$row['ID_CATEGORIA']);
                    $pre->execute();
                    $roster = $pre->get_result();
                    $info = $roster->fetch_array(MYSQLI_ASSOC);
                    if($roster && $roster->num_rows>0){
                        echo "<option value='".$info['ID_ROSTER']."'>".$row['NOMBRE_CATEGORIA']."</option>";
                    }
		}
                $pre->close();
	}else{
		echo "<option value='' disabled selected hidden> No se encontraron rosters</option>";
	}
	$conn->close();
?>