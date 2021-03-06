<?php
    include_once $_SERVER['SRV_DOC_ROOT'] . '/php/pagina/gedeeld/utilities.php';
    $gebruiker = $sessie_controller->geef_aangemelde_gebruiker();
    $profiel = $gebruiker->geef_voornaam() . " " . $gebruiker->geef_familienaam() . " (" . $gebruiker->geef_gebruikertype() . ")";
    // kijken of we een profielfoto vinden of niet
    $profiel_foto = UTILITIES::GEEF_HTML_PROFIELFOTO_KLEIN($gebruiker->geef_gebruikertype(),$gebruiker->geef_gebruikersnaam());
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary navigatiebalk fixed-top">
    <img class="logo" src="<?php echo $_SERVER['SRV_ALIAS']?>/afbeelding/logo/hogent.png" />

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <!-- lege ul om rest rechts uit te lijnen -->
        <ul class="navbar-nav mr-auto"></ul>
        <!-- rechtse deel -->
        <?php echo $profiel_foto ?>
        <!-- 2 spaties voor ruimte tussen foto en menu -->
        &nbsp;&nbsp;
        <!-- gebruikersnemnu -->
        <ul class="navbar-nav">
            <li class="nav-item dropdown active">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <!-- Hier via php de volledige naam en type van aangemeld profiel tonen -->
                    <?php echo $profiel ?> &nbsp;
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="<?php echo $_SERVER['SRV_ALIAS'] ?>/php/pagina/wijzig_periode.php">
                        <span class="ion-android-calendar"></span>&nbsp;&nbsp;Wijzig academiejaar / zittijd
                    </a>
                    <a class="dropdown-item" href="<?php echo $_SERVER['SRV_ALIAS'] ?>/php/pagina/wijzig_wachtwoord.php">
                        <span class="ion-key"></span>&nbsp;&nbsp;Wijzig wachtwoord
                    </a>
                    <a class="dropdown-item" href="mailto:sebastiaan.labijn@hogent.be?subject=[Projecten TIN] Probleem/Bug">
                        <span class="ion-alert-circled"></span>&nbsp;&nbsp;Meld een probleem
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo $_SERVER['SRV_ALIAS'] ?>/php/pagina/afmelden.php">
                        <span class="ion-power"></span>&nbsp;&nbsp;Afmelden
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
