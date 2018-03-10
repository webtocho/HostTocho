<?php
    require 'SRV_CONEXION.php';
    $db = new SRV_CONEXION();
    $conn = $db->getConnection();    
    
    switch($_POST['tipo']){	
        case "get":// obtener las categorias
            $query = "SELECT * FROM categorias";
            $result = $conn->query($query);
                /*** crear una tabla  ***/
                echo "<thead><tr><th>Categorias</th><th></th></tr></thead> <tbody>";
                /************************/
                if($result){
                    if(mysqli_num_rows($result)<=0){// validamos que la consulta contenga informacion
                         echo "<tr> <td>Sin Categorias</td>";
                    }
                    else{
                        while($row = mysqli_fetch_array($result)){// recorremos el arreglo de la consulta
                            echo $row['NOMBRE_CATEGORIA'];
                            // llenar la tabla con las categorias       
                             echo "<tr> <td>".$row['NOMBRE_CATEGORIA']."</td>"; //imprimimos el nombre
                             echo "<td><button type='button' class='btn btn-warning'  data-toggle='modal' data-target='#eliminar_modal' onclick='setIdCategoria(".$row['ID_CATEGORIA'].",\"".$row['NOMBRE_CATEGORIA']."\")'  >Eliminar</button></td></tr>"; //imprimimo el boton
                        }
                    }
                }else{ }//eror
                echo  "</tbody>";
        break;
        case "delete":// eliminar una categoria
             $id = $_POST['id'];
            $result = $conn->prepare("DELETE FROM categorias WHERE ID_CATEGORIA = ? ");
            $result->bind_param("i", $id);
            if ($result->execute()) {//funciono
		echo "1";
            } else {
		echo "0";//$result->error;
            }            
        break;
        case "add":// eliminar una categoria
            $nombre = $_POST['nombre'];
            $result = $conn->prepare("INSERT INTO categorias(NOMBRE_CATEGORIA) VALUES (?)");
            $result->bind_param("s",$nombre);
            if ($result->execute()) {
		echo "1";
            } else {
		echo "0";//$result->error;
            }           
        break;
    }
	$conn->close();
?>