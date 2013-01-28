<h2>Récupération de compte</h2>

<div id="msg">
<div class="info">Entrez votre adresse mail : </div><br/>
</div>

<form id="frm_recuperation" onsubmit="$('#msg').append('<div style=\'display:block;z-index:1005;outline:0px;position:fixed;left:50%;right:50%;margin-top:-150px;margin-left:-150px;top:50%;width:300px;height:300px;\'><img src=\'images/chargement.gif\'/></div>');xajax_recuperation($('#recup_email').val()); return false;">
	<table>
		<tr>
			<td>Votre adresse mail : </td>
			<td><input id="recup_email" name="recup_email" type="text" ></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Envoyer"></td>
		</tr>
	</table>
</form>

<a href="./">Aller à la page de connexion</a>