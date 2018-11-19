<?php
include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/sessie.php';
include_once '/srv/prjtinapp' . '/php/klasse/controller/gebruiker_controller.php';

    // Als gebruiker niet aangemeld is naar zijn startpagina sturen
    if(!$sessie_controller->is_aangemeld()) {
        $sessie_controller->ga_naar_startpagina();
    }

    // gebruiker wil zijn wachtwoord bijwerken
    if(isset($_POST['wijzig-wachtwoord']))
    {
        $gebruikersnaam = $sessie_controller->geef_aangemelde_gebruiker()->geef_gebruikersnaam();
        $oud_wachtwoord = $_POST['huidige_wachtwoord'];
        $nieuw_wachtwoord = $_POST['nieuwe_wachtwoord'];
        $herhaal_wachtwoord = $_POST['herhaal_wachtwoord'];

        $fout = valideer_invoer($oud_wachtwoord, $nieuw_wachtwoord, $herhaal_wachtwoord);

        // geen validatie fouten dus updaten in databank
        if(!isset($fout)) {
            $gc = new GebruikerController($gebruikersnaam);
            if($gc->wijzig_wachtwoord($oud_wachtwoord, $nieuw_wachtwoord)) {
                $sessie_controller->ga_naar_startpagina("?succes=Wachtwoord succesvol bijgewerkt!");
            }
            else
            {
                $fout = $gc->geef_fout();
            }
        }
    }

    function valideer_invoer($oud_wachtwoord, $nieuw_wachtwoord, $herhaal_wachtwoord){
        if($nieuw_wachtwoord != $herhaal_wachtwoord) { return '"Nieuwe wachtwoord" en "Herhaal wachtwoord" zijn niet gelijk!'; }
        if($nieuw_wachtwoord == $oud_wachtwoord) { return '"Huidige wachtwoord" en "Nieuwe wachtwoord" mogen niet gelijk zijn!'; }
        if(strlen($nieuw_wachtwoord) < 6) { return '"Nieuwe wachtwoord" moet minstens 6 tekens lang zijn!'; }
        return null;
    }

    $_GET['pagina_titel'] = 'Wijzig wachtwoord';
    include '/srv/prjtinapp' . '/php/pagina/gedeeld/header.php';
?>
    <?php echo maak_invoerveld_label_links("Huidige wachtwoord",  "password"); ?>
    <?php echo maak_invoerveld_label_links("Nieuwe wachtwoord", "password"); ?>
    <?php echo maak_invoerveld_label_links("Herhaal wachtwoord", "password"); ?>
    <div class="clearfix"></div>
    <hr/>
    <?php echo maak_submit_knop("Wijzig wachtwoord", "edit", "wijzig-wachtwoord"); ?>
    <br/>
    <?php echo maak_dashboard_knop(); ?>
    <!-- footer includen -->
<?php include '/srv/prjtinapp' . '/php/pagina/gedeeld/footer.php'; ?>