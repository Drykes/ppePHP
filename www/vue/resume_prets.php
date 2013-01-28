<h2>Page récapitulative sur les prêts d'une personne</h2>
<div id="msg">
	<div class='info'>Recherchez une personne pour afficher un résumé sur ses prêts :</div>
</div>
<br/>						
<div style="height:26px;">
	<input style="width:200px;" type="text" id="nomRecherche" name="nomRecherche" onKeyUp="xajax_charger(this.value);" onBlur="$('#noms').empty();" onFocus="xajax_charger(this.value);" onMouseUp="$('#nomRecherche').select();" />
	<div style="" id="noms"></div>
</div><br/>
<div id='resultat' style='color:red;'></div>
<div id='bloc_frm_modif'></div>
<div id='resume'></div>