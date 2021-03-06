<?php
    include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/sessie.php';

    // Als gebruiker al aangemeld is naar zijn startpagina sturen
    if($sessie_controller->is_aangemeld()) {
        $sessie_controller->ga_naar_startpagina();
    }

    // Kijken of gebruiker wil inloggen
    if(isset($_POST['aanmelden'])){
        $gebruikersnaam = $_POST['gebruikersnaam'];
        $wachtwoord = $_POST['wachtwoord'];
        // Proberen aan te melden
        if($sessie_controller->meld_aan($gebruikersnaam, $wachtwoord)){
            $sessie_controller->ga_naar_startpagina();
        }
        // Niet gelukt
        else{
            $fout = $sessie_controller->geef_fout();
        }
    }

    $_GET['pagina_titel'] = 'Projecten TIN - Aanmelden';
    include $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/header.php';
?>
    <?php echo maak_invoerveld_label_links("Gebruikersnaam"); ?>
    <?php echo maak_invoerveld_label_links("Wachtwoord", "password"); ?>
    <div class="clearfix"></div>
    <hr/>
    <?php echo maak_submit_knop("Aanmelden", "log-in", "aanmelden"); ?>
    <br/>
    <label style="font-size: small">Wachtwoord vergeten? Reset het <a href="<?php echo $_SERVER['SRV_ALIAS'] ?>/reset_wachtwoord.php">hier</a>!</label>
    <br />
<!-- footer includen -->
<?php include $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/footer.php'; ?>
