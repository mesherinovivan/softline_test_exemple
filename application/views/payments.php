<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<script src="js/jquery-1.7.2.js" type="text/javascript" charset="utf-8"></script>
	<title>Транзакции пользователя</title>
</head>
<body>
<script type="text/javascript">
	$(document).ready(function(){
		$("#search").click(function(){
			var user_name = $("#username").val();
			$("#respose").empty();
				$.getJSON("index.php/integration/translist/",{"username":user_name},
						function(data){
							$.each( data, function( i, item ){
								$("#respose").append("<li>id = "+item.id+" amount = "+item.amount	+"</li>");
							});
			});
		});
	});
</script>
<div id="container">
	<h1>Транзакции пользователя</h1>
</div>
<div>
	<label>Имя пользователя </label><input type="text" id="username" value="">
	<input id="search" type="button" value="Найти">
</div>
<br/>
<div id="respose">

</div>
</body>
</html>
