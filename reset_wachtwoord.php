<?php
include_once '/srv/prjtinapp' . '/php/pagina/gedeeld/sessie.php';
include_once '/srv/prjtinapp' . '/php/klasse/controller/gebruiker_controller.php';

if(isset($_POST['reset']))
{
    $gebruikersnaam = $_POST['gebruikersnaam'];
    // geen validatie fouten dus updaten in databank
    if(!isset($fout)) {
        $gc = new GebruikerController($gebruikersnaam);
        // reset het wachtwoord van de gebruiker
        if($gc->reset_wachtwoord()) {
            $succes = "Wachtwoord succesvol reset! Controleer uw e-mail voor uw nieuwe wachtwoord.";
        }
        // Niet gelukt te resetten
        else {
            $fout = $gc->geef_fout();
        }
    }
}

$_GET['pagina_titel'] = 'Wachtwoord resetten';
include $_SERVER['DOCUMENT_ROOT'] . '/php/pagina/gedeeld/header.php';
?>
    <?php echo maak_invoerveld_label_links("Gebruikersnaam"); ?>
    <div class="clearfix"></div><hr />
    <?php echo maak_submit_knop("Reset wachtwoord", "loop", "reset"); ?>
    <br />
    <?php echo maak_dashboard_knop("aanmelden"); ?>
    <!-- footer includen -->
<?php include $_SERVER['DOCUMENT_ROOT'] . '/php/pagina/gedeeld/footer.php'; ?>