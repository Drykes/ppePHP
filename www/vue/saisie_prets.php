<?php include('./include/conn_bdd.php');	?>

<h2>Page de saisie d'un prêt</h2>
<div id="msg">
	<div class='info'>Completez le formulaire : </div>
</div>
<br/>
<form id="frmSaisie" onSubmit="$('#msg').append('<div style=\'display:block;z-index:1005;outline:0px;position:fixed;left:50%;right:50%;margin-top:-150px;margin-left:-150px;top:50%;width:300px;height:300px;\'><img src=\'images/chargement.gif\'/></div>');$('#resultat').append('<img src=\'images/chargement.gif\' />'); xajax_saisiePret($('#id').val(),$('#1').attr('checked'),$('#2').attr('checked'),$('#3').attr('checked'),$('#marque').val(),$('#type').val(),$('#date_circulation').val(),$('#garantie').val(),$('#nom_licence_duree').val(),$('#debut_licence').val(),$('#duree_licence').val(),$('#nom_licence_version').val(),$('#4').attr('checked'),$('#5').attr('checked'),$('#nom_licence_duree_associe').val(),$('#debut_licence_associe').val(),$('#duree_licence_associe').val(),$('#nom_licence_version_associe').val(), $('#nvl_marque').val()); return false;">
<table  style="border:solid 1px black;margin:auto;">
	<CAPTION></CAPTION>
	<tr>
		<td>
			Qui :
		</td>
		<td> 
			<div style="height:26px;">
				<input type="hidden" id="id" name="id" />
				<input style="width:200px;" type="text" id="nomRecherche" name="nomRecherche" onKeyUp="xajax_charger(this.value);" onBlur="$('#noms').empty(); " onFocus="xajax_charger(this.value);"/>
				<div style="" id="noms"></div>
			</div>
		</td>
		<td>
			Quoi :
		</td>
		<td style="text-align:left;">
			<label for="1"><input type="checkbox" id="1" name="quoi" value="1" onclick="choix_type(1);">Matériel</label><br/>
			<label for="2"><input type="checkbox" id="2" name="quoi" value="2" onclick="choix_type(2);">Licence de durée</label><br/>
			<label for="3"><input type="checkbox" id="3" name="quoi" value="3" onclick="choix_type(3);">Licence de version</label><br/>
		</td>
	</tr>
</table>

<table id="type_materiel" style="display:none;margin:auto;">
	<CAPTION><br><b>Pour un materiel :</b></CAPTION>
	<tr>
		<td>Marque : </td>
		<td>
			<select name="marques" id="marque">
				<option value=0 selected="selected"></option>
			<?php 
				$req="SELECT * FROM MARQUE";
				$resultat=$bdd->query($req);
				
				while($lignes=$resultat->fetch(PDO::FETCH_OBJ))
				{
					echo "<option value=$lignes->id_marque onclick='$(\"#ajout_nvl_marque\").hide();' >$lignes->nom_marque</option>";
				}
			?>
				<option value=-1 onclick='$("#ajout_nvl_marque").show();'>Nouvelle marque</option>
			</select>
		</td>
	</tr>
	<tr id="ajout_nvl_marque" name="ajout_nvl_marque" style="display:none;">
		<td>Nouvelle marque : </td>
		<td>
			<input style="width:200px;" type="text"  name="nvl_marques" id="nvl_marque" />
		</td>
	</tr>
	<tr>
		<td>Type : </td>
		<td>
			<select name="type" id="type">
				<option value=0 selected="selected"></option>
			<?php 
				$req="SELECT * FROM TYPE_MATERIEL";
				$resultat=$bdd->query($req);
				
				while($lignes=$resultat->fetch(PDO::FETCH_OBJ))
				{
					echo "<option value=$lignes->id_type_materiel >$lignes->type_materiel</option>";
				}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Date de mise en circulation (jj/mm/aaaa) : </td>
		<td>
			<input class="mCalendarFR" style="width:200px;" type="text" id="date_circulation" name="date_circulation" />
		</td>
	</tr>
	<tr>
		<td>Garantie : </td>
		<td>
			<select name="garantie" id="garantie">
				<option value="" selected="selected"></option>
				<option value=0 >0</option>
				<option value=1 >1 an</option>
				<option value=2 >2 ans</option>
				<option value=3 >3 ans</option>
				<option value=4 >4 ans</option>
				<option value=5 >5 ans</option>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for="4"><input type="checkbox" id="4" name="4" value=4 onclick="choix_type(4);"/>Associer une licence de durée</label></td>
		<td><label for="5"><input type="checkbox" id="5" name="5" value=5 onclick="choix_type(5);"/>Associer une licence de version</label></td>
	</tr>
</table>
<table id="type_lic_duree_associe" style="display:none;margin:auto;">
	<CAPTION><br/>Pour une licence de durée :</CAPTION>
	<tr>
		<td>Nom de la licence : </td>
		<td>
			<input style="width:200px;" type="text" id="nom_licence_duree_associe" name="nom_licence_duree_associe" />
		</td>
	</tr>
	<tr>
		<td>Date du début de licence (jj/mm/aaaa) : </td>
		<td>
			<input class="mCalendarFR" style="width:200px;" type="text" id="debut_licence_associe" name="debut_licence_associe" />
		</td>
	</tr>
	<tr>
		<td>Durée de la licence (en années) : </td>
		<td>
			<input style="width:200px;" type="text" id="duree_licence_associe" name="duree_licence_associe" />
		</td>
	</tr>
</table>
<table id="type_lic_version_associe" style="display:none;margin:auto;">	
	<CAPTION><br/>Pour une licence de version :</CAPTION>
	<tr>
		<td>Nom de licence : </td>
		<td>
			<input style="width:200px;" type="text" id="nom_licence_version_associe" name="nom_licence_version_associe" />
		</td>
	</tr>
</table>

<!------------------------------>


<table id="type_lic_duree" style="display:none;margin:auto;">
	<CAPTION><br/><hr/><br/><b>Pour une licence de durée :</b></CAPTION>
	<tr>
		<td>Nom de la licence : </td>
		<td>
			<input style="width:200px;" type="text" id="nom_licence_duree" name="nom_licence_duree" />
		</td>
	</tr>
	<tr>
		<td>Date du début de licence (jj/mm/aaaa) : </td>
		<td>
			<input class="mCalendarFR" style="width:200px;" type="text" id="debut_licence" name="debut_licence" />
		</td>
	</tr>
	<tr>
		<td>Durée de la licence (en années) : </td>
		<td>
			<input style="width:200px;" type="text" id="duree_licence" name="duree_licence" />
		</td>
	</tr>
</table>

<table id="type_lic_version" style="display:none;margin:auto;">		
	<CAPTION><br/><hr/><br/><b>Pour une licence de version :</b></CAPTION>
	<tr>
		<td>Nom de licence : </td>
		<td>
			<input style="width:200px;" type="text" id="nom_licence_version" name="nom_licence_version" />
		</td>
	</tr>
</table>
<br/>	
<table style="margin:auto;">	
	<tr>
		<td colspan=2><input id="validation" name="validation" type="submit" value="Valider"/></td>
	</tr>
</table>

</form>