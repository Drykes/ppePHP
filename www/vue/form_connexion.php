<?php
//on vérifie si l'utilisateur est connecté ou pas avant d'afficher le formulaire
if (!isset($_SESSION['id']))
{
	$rediriger="";
		if(isset($_REQUEST['page']))
		{
			$rediriger=$_REQUEST['page'];
		}
	?>
		<div id="msg">
			<div class='info'>Veuillez vous identifier.</div>
		</div>
		<br/>
		<form id="frm_conn" onsubmit=" $('#msg').append('<div style=\'display:block;z-index:1005;outline:0px;position:fixed;left:50%;right:50%;margin-top:-150px;margin-left:-150px;top:50%;width:300px;height:300px;\'><img src=\'images/chargement.gif\'/></div>');xajax_connexion($('#login').val(),$('#mdp').val(),'<?php echo $rediriger; ?>'); return false;" >
			<TABLE>
				<TR>
					<TD>Identifiant : </TD>
					<TD><input type="text" id="login" name="login"/></TD>
				</TR>
				<TR>
					<TD>Mot de passe : </TD>
					<TD><input type="password" id="mdp" name="mdp"/></TD>
				</TR>
				<TR>
					<TD colspan=2 style="text-align:center;"><a href="./recuperation.php">Mot de passe oublié<a></TD>
				</TR>
				<TR>
					<TD colspan=2 style="text-align:center;"><input type="submit" value="Se connecter" /></TD>
				</TR>
			</TABLE>
		</form>
<?php
}
?>