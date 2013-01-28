<h2>Mon profil</h2>

<div id="msg">
	<div class='info'>Modifiez votre mot de passe</div>
</div><br/>

<form id="frm_mdp" onSubmit="$('#msg').append('<div style=\'display:block;z-index:1005;outline:0px;position:fixed;left:50%;right:50%;margin-top:-150px;margin-left:-150px;top:50%;width:300px;height:300px;\'><img src=\'images/chargement.gif\'/></div>');xajax_changer_mdp($('#ancien_mdp').val(),$('#nvo_mdp').val(),$('#confirm_nvo_mdp').val()); return false;" >
	<table>
		<tr>
			<td>Ancien mot de passe : </td>
			<td><input id="ancien_mdp" name="ancien_mdp" type="password"></td>
		</tr>
		<tr>
			<td>Nouveau mot de passe : </td>
			<td><input id="nvo_mdp" name="nvo_mdp" type="password"></td>
		</tr>
		<tr>
			<td>Confirmation du nouveau mot de passe : </td>
			<td><input id="confirm_nvo_mdp" name="nom" type="password"></td>
		</tr>
		<tr>
			<td colspan=2><input id="valider" name="valider" type="submit" value="Valider"></td>
		</tr>
	</table>
</form>