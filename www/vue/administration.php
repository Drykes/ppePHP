<h2>Administration</h2>
<div id="msg">
	<div class='info'>Créez un compte</div>
</div><br/>
<div id="frm_nvo_utilisateur">
	<label for="ajout_admin"><input type="radio" id="ajout_admin" name="group1" value="Admin" onclick="choix_personne(1);"> Administrateur</label>
	<label for="ajout_employe"><input type="radio" id="ajout_employe" name="group1" value="Employe" onclick="choix_personne(2);"> Employé</label>

	<form id="frm1" style="display:none" onSubmit="$('#msg').append('<div style=\'display:block;z-index:1005;outline:0px;position:fixed;left:50%;right:50%;margin-top:-150px;margin-left:-150px;top:50%;width:300px;height:300px;\'><img src=\'images/chargement.gif\'/></div>');xajax_nvo_utilisateur(0,$('#nom_admin').val(),$('#prenom_admin').val(),$('#mail_admin').val(),$('#login_admin').val(),$('#motdepass_admin').val(),$('#mdp_confirm_admin').val()); return false;">
		<br/>
		<table>
			<tr>
				<td>Nom : </td>
				<td><input id="nom_admin" name="nom_admin" type="text">
				</td>
			</tr>
			<tr>
				<td>Prénom : </td>
				<td><input id="prenom_admin" name="prenom_admin" type="text"/></td>
			</tr>
			<tr>
				<td>Mail : </td>
				<td><input id="mail_admin" name="mail_admin" type="text"/></td>
			</tr>
			<tr>
				<td>Login : </td>
				<td><input id="login_admin" name="login_admin" type="text"/></td>
			</tr>
			<tr>
				<td>Mot de passe  : </td>
				<td><input id="motdepass_admin" name="motdepass_admin" type="password"/></td>
			</tr>
			<tr>
				<td>Confimartion : </td>
				<td><input id="mdp_confirm_admin" name="mdp_confirm_admin" type="password"/></td>
			</tr>
		</table>
		<input id="valider1" value="Valider" type="submit">
	</form>

	<form id="frm2" style="display:none" onSubmit="$('#msg').append('<div style=\'display:block;z-index:1005;outline:0px;position:fixed;left:50%;right:50%;margin-top:-150px;margin-left:-150px;top:50%;width:300px;height:300px;\'><img src=\'images/chargement.gif\'/></div>');xajax_nvo_utilisateur(1,$('#nom').val(),$('#prenom').val(),$('#mail').val(),'','',''); return false;" />
		<br/>
		<table>
			<tr>
				<td>Nom:</td>
				<td><input id="nom" name="nom" type="text"></td>
			</tr>
			<tr>
				<td>Prénom:</td>
				<td><input id="prenom" name="prenom" type="text"></td>
			</tr>
			<tr>
				<td>Mail:</td>
				<td><input id="mail" name="mail" type="text"></td>
			</tr>
		</table>
		<input  id="valider2" value="Valider" type="submit">
	</form>
</div>

<?php //include('./include/frmAdmin.php');	?>