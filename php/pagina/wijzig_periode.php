<?php
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/sessie.php';
include_once $_SERVER['SRV_DOC_ROOT'] . '/php/klasse/controller/gebruiker_controller.php';

// Als gebruiker niet aangemeld is naar zijn startpagina sturen
if(!$sessie_controller->is_aangemeld()) {
    $sessie_controller->ga_naar_startpagina();
}
$gebruikersnaam = $sessie_controller->geef_aangemelde_gebruiker()->geef_gebruikersnaam();
$gc = new GebruikerController($gebruikersnaam);
$huidige_periode = $gc->geef_aangemelde_gebruiker()->geef_huiduige_periode();
if (isset($huidige_periode)) {
    $huidig_academiejaar = $huidige_periode->geef_academie_jaar();
    $huidige_zittijd = $huidige_periode->geef_zittijd();
}
else {
    $fout = "Gebruiker heeft geen huidige periode!";
}
// gebruiker wil zijn periode bijwerken
if(isset($_POST['wijzig-periode']))
{
    $academiejaar = $_POST['academiejaar'];
    $zittijd = $_POST['zittijd'];

    // geen validatie fouten dus updaten in databank
    if(!isset($fout)) {
        $gc = new GebruikerController($gebruikersnaam);
        if($gc->wijzig_periode($academiejaar, $zittijd)) {
            $sessie_controller->ga_naar_startpagina("?succes=Periode succesvol ingesteld naar academiejaar $academiejaar - Zittijd $zittijd!");
        }
        else
        {
            $fout = $gc->geef_fout();
        }
    }
}

$pm = new PeriodeMapper();
$periodes_aj= $pm->geef_academiejaren();
$periodes_zt= $pm->geef_zittijden();

$academiejaren = [] ;
foreach($periodes_aj as $aj) {
    $academiejaren[] = [ "naam" => round($aj, -2)/100 . ' - ' . ($aj%100), "waarde" => $aj ];
}

$zittijden = [];
foreach($periodes_zt as $zt) {
    $zittijden[] = [ "naam" => $zt, "waarde" => $zt ];
}

$_GET['pagina_titel'] = 'Wijzig academiejaar/zittijd';
include $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/header.php';
?>
<div class="row">
    <div class="col-lg-2">Huidige waarde:</div>
    <div class="col-lg-4">
        <strong><?php echo "Academiejaar $huidig_academiejaar - Zittijd $huidige_zittijd" ?></strong>
    </div>
</div>
<br/>
<div class="row">
    <div class="col-lg-2">Academiejaar:</div>
    <div class="col-lg-2">
    <?php echo maak_html_select ("academiejaar", 1, "", $academiejaren,
    2018, "waarde", "naam", true, true) ?>
    </div>
</div>
<br/>
<div class="row">
    <div class="col-lg-2">Zittijd:</div>
    <div class="col-lg-2">
    <?php echo maak_html_select ("zittijd", 1, "", $zittijden, 2,
    "waarde", "naam", true, true) ?>
    </div>
</div>
<div class="clearfix"></div>
<hr/>
<?php echo maak_submit_knop("Wijzig periode", "edit", "wijzig-periode"); ?>
<br/>
<?php echo maak_dashboard_knop(); ?>
    <!-- footer includen -->
<?php include $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/footer.php'; ?>