<?php
	require("../conexionmysqli.inc");
	require("../estilos2.inc");
	require("configModule.php");

	$vector=explode(",",$datos);
	if(isset($_GET["admin"])){
		$urlList2=$urlList3;
	}
	if(isset($_COOKIE['global_usuario'])){
      $user=$_COOKIE['global_usuario'];
    }
    
	$n=sizeof($vector);
	for($i=0;$i<$n;$i++)
	{
		$sql="update $table set estado=2,cod_funcionario='$user' where codigo=$vector[$i]";
		$resp=mysqli_query($enlaceCon,$sql);
	}
	echo "<script language='Javascript'>
			alert('Los datos fueron eliminados.');
			location.href='$urlList2';
			</script>";

?>