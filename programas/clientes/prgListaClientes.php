<?php
require("../../conexionmysqli.inc");
require("../../estilos_almacenes.inc");

$globalAgencia=$_COOKIE["global_agencia"];
$globalAlmacen=$_COOKIE["global_almacen"];

// DATOS DE FILTROS
$fil_nombre = $_GET['fil_nombre'] ?? '';
$fil_nit    = $_GET['fil_nit'] ?? '';
$fil_razon  = $_GET['fil_razon'] ?? '';
$fil_telf   = $_GET['fil_telf'] ?? '';
?>

<h1>Clientes</h1>

<div class='divBotones'>
    <!-- <input class='btn btn-success' type='button' value='Adicionar' onclick='javascript:frmAdicionar();'>
    <input class='btn btn-warning' type='button' value='Editar' onclick='javascript:frmModificar();'>
    <input class='btn btn-danger' type='button' value='Eliminar' onclick='javascript:frmEliminar();'> -->
    
    <button class="btn btn-sm btn-success" onclick='javascript:frmAdicionar();'>
        <i class="material-icons">add</i> Adicionar
    </button>
    <button class="btn btn-sm btn-warning" onclick='javascript:frmModificar();'>
        <i class="material-icons">edit</i> Editar
    </button>
    <button class="btn btn-sm btn-danger" onclick='javascript:frmEliminar();'>
        <i class="material-icons">delete</i> Eliminar
    </button>
    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#filtroModal">
        <i class="material-icons">filter_list</i> Filtrar
    </button>
</div>

<center>
  <table class='texto'>
    <tr>
      <th>&nbsp;</th>
      <th>Nombre</th>
      <th>Direccion</th>
      <th>NIT</th>
      <th>Email /<br>Telefono</th>
      <th># Productos Registrados</th>
      <th>Acciones</th>
    </tr>
    <?php
    $consulta = "SELECT c.cod_cliente, 
                    c.nombre_cliente, 
                    c.nit_cliente, 
                    c.ci_cliente, 
                    c.email_cliente, 
                    c.telf1_cliente, 
                    c.dir_cliente, 
                    c.cod_area_empresa, 
                    a.descripcion, 
                    (select count(*) 
                    from clientes_precios cp, clientes_preciosdetalle cpd 
                    where cp.codigo = cpd.cod_clienteprecio 
                    and cp.cod_cliente = c.cod_cliente) as numeroproductos
        FROM clientes AS c 
        INNER JOIN ciudades AS a ON c.cod_area_empresa = a.cod_ciudad 
        WHERE c.cod_area_empresa = '$globalAgencia' ";
    if (!empty($fil_nombre) || !empty($fil_nit) || !empty($fil_razon) || !empty($fil_telf)) {
        $consulta .= " AND (";
        $condiciones = array();
        if (!empty($fil_nombre)) {
            $condiciones[] = "c.nombre_cliente LIKE '%$fil_nombre%'";
        }
        if (!empty($fil_nit)) {
            $condiciones[] = "c.nit_cliente LIKE '%$fil_nit%'";
        }
        if (!empty($fil_razon)) {
            $condiciones[] = "c.nombre_factura LIKE '%$fil_razon%'";
        }
        if (!empty($fil_telf)) {
            $condiciones[] = "c.telf1_cliente LIKE '%$fil_telf%'";
        }
        $consulta .= implode(" OR ", $condiciones);
        $consulta .= ")";
    }

    $consulta .= " ORDER BY c.nombre_cliente ASC";
    $rs=mysqli_query($enlaceCon,$consulta);
    $cont=0;
    while($reg=mysqli_fetch_array($rs))
    {
        $cont++;
        $codCliente = $reg["cod_cliente"];
        $nomCliente = $reg["nombre_cliente"];
        $nitCliente = $reg["nit_cliente"];
        $ciCliente = $reg["ci_cliente"];
        $emailCliente = $reg["email_cliente"];
        $telefonoCliente = $reg["telf1_cliente"];
        $dirCliente = $reg["dir_cliente"];
        $codArea = $reg["cod_area_empresa"];
        $nomArea = $reg["descripcion"];
        $numeroProductos=$reg["numeroproductos"];
        if($numeroProductos==0){
            $numeroProductos="-";
        }
        ?>
        <tr>
          <td><input type='checkbox' id='idchk<?php echo $cont; ?>' value='<?php echo $codCliente; ?>' ></td>
          <td><?php echo $nomCliente; ?></td>
          <td><?php echo $dirCliente; ?></td>
          <td><?php echo $nitCliente; ?></td>
          <td><?php echo $emailCliente; ?><br><?php echo $telefonoCliente; ?></td>
          <td><?php echo $numeroProductos; ?></td>
          <td>
            <a href='../../clientePrecio.php?cod_cliente=<?php echo $codCliente; ?>' title='Registrar Precios Clientes' class='text-dark'><i class='material-icons'>description</i></a>
            <a href='../../clienteDocumento.php?cod_cliente=<?php echo $codCliente; ?>' target='_blank' title='Carga de Documentos' class='text-dark'><i class='material-icons'>cloud_upload</i></a>
            <a href='#' title='Carga de Precio Clientes' class='text-primary modal_documento' data-cod_cliente="<?php echo $codCliente; ?>">
              <i class='material-icons'>description</i>
            </a>
            <a href="../../clientePrecioVer.php?cod_cliente=<?php echo $codCliente; ?>" title="Ver Precios Clientes" class="text-info"><i class="material-icons">visibility</i></a>
            <a href='#' title='Clonar Precio Clientes' class='text-warning modal_clonar' data-cod_cliente="<?php echo $codCliente; ?>">
              <i class='material-icons'>file_copy</i>
            </a>
          </td>
        </tr>
        <?php
    }
    ?>
  </table>
  <input type='hidden' id='idtotal' value='<?php echo $cont; ?>' >
</center>

<div class='divBotones'>
  <input class='boton-verde' type='button' value='Adicionar' onclick='javascript:frmAdicionar();'>
  <input class='boton' type='button' value='Editar' onclick='javascript:frmModificar();'>
  <input class='boton2' type='button' value='Eliminar' onclick='javascript:frmEliminar();'>
</div>

<!-- Modal Carga de Datos -->
<div class="modal fade" id="cargarModal" tabindex="-1" aria-labelledby="cargarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cargarModalLabel">Cargar Archivo - Precio Cliente</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cargar_cod_cliente">
                <input type="file" id="cargar_doc" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onClick="$('#cargarModal').modal('hide');">Cancelar</button>
                <button id="cargar_save" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Carga de Datos -->
<div class="modal fade" id="clonarModal" tabindex="-1" aria-labelledby="clonarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clonarModalLabel">Copiar - Precio Cliente</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="hidden" id="cliente_actual">
                    <label for="cliente_clonado">Clonar de cliente:</label>
                    <select id="cliente_clonado" class="form-control select2">
                        <?php
                        $consulta=" SELECT c.cod_cliente, CONCAT(c.nombre_cliente, ' ', c.paterno) as nombre_cliente
                              FROM clientes AS c INNER JOIN ciudades AS a ON c.cod_area_empresa = a.cod_ciudad 
                              WHERE c.cod_area_empresa='$globalAgencia' ORDER BY c.nombre_cliente ASC
                          ";
                          $rs = mysqli_query($enlaceCon,$consulta);
                          while($row=mysqli_fetch_array($rs))
                          {
                        ?>
                            <option value="<?=$row['cod_cliente']?>"><?=$row['nombre_cliente']?></option>
                        <?php
                          }
                        ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onClick="$('#clonarModal').modal('hide');">Cancelar</button>
                <button id="clonar_save" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Buscador -->
<div class="modal fade" id="filtroModal" tabindex="-1" aria-labelledby="cargarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cargarModalLabel">Filtrar Registros</h5>
            </div>
            <div class="modal-body">
                <label class="mb-0"><b>Nombre:</b></label>
                <input type="text" id="fil_nombre" name="fil_nombre" value="<?=$fil_nombre?>" class="form-control mb-2" placeholder="Ingresar el nombre">
                <label class="mb-0"><b>NIT/CI:</b></label>
                <input type="text" id="fil_nit" name="fil_nit" value="<?=$fil_nit?>" class="form-control mb-2" placeholder="Ingresar el NIT/CI">
                <label class="mb-0"><b>Razón Social:</b></label>
                <input type="text" id="fil_razon" name="fil_razon" value="<?=$fil_razon?>" class="form-control mb-2" placeholder="Ingresar el razón social">
                <label class="mb-0"><b>Teléfono:</b></label>
                <input type="text" id="fil_telf" name="fil_telf" value="<?=$fil_telf?>" class="form-control mb-2" placeholder="Ingresar el teléfono">
            </div>
            <div class="modal-footer pb-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick='javascript:filtroRegistros();'>Filtrar</button>
            </div>
        </div>
    </div>
</div>
