<?php
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/klasse/controller/lector_controller.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/klasse/databank/databank.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/klasse/domein/bevraging.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/klasse/domein/vragenlijst.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/klasse/domein/gebruiker.php';


    $time_start = microtime(true);



    // een score toevoegen aan elke bevraging, zonder telkens vragenlijst te moeten overlopen en berekenen
    $lector_controller = new LectorController("slab754");
    $bevragingen = $lector_controller->geef_bevragingen("%", "%", true);
    $aantal_bevragingen = 0;
    $aantal_records = 0;
    foreach($bevragingen as $bevraging){
        $bevraging->bereken_score();
        // details zelf verwijderen
        $sql = "UPDATE bevraging ".
            "SET score = :score " .
            "WHERE id = :bid";
        $parameters = [
            [':score', $bevraging->geef_score()],
            [':bid', $bevraging->geef_id()]
        ];
        $aantal_bevragingen += 1;
        $aantal_records += DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('DELETE'), $sql, $parameters);
    }

    $time_end = microtime(true);
    //Subtract the two times to get seconds.
    $tijdnodig = $time_end - $time_start;
    $geheugen_gebruikt = memory_get_usage()/(1024*1024) . ' MB';
    $max_geheugen_gebruikt = memory_get_peak_usage()/(1024*1024) . 'MB';

    function test_mappen_bevraging(){

        $sql = "SELECT b.id, b.groepnaam, b.voltooid, b.deadline, b.vragenlijst_naam, b.gebruiker, g.voornaam, g.familienaam, b.is_anoniem, b.met_score, vva.vraagid, vva.vraagvolgorde, vva.antwoordgroepnaam,  ag.antwoord_id " .
            "FROM bevraging as b " .
            "JOIN gebruiker as g on g.gebruikersnaam = b.gebruiker " .
            "JOIN vragenlijst_vraag_antwoord as vva on vva.vragenlijstnaam = b.vragenlijst_naam " .
            "JOIN antwoord_groep as ag on ag.naam = vva.antwoordgroepnaam " .
            "ORDER BY b.id, vva.vraagvolgorde, ag.volgorde";
        $resultaten = DATABANK::geef_instantie()->voer_query_uit(SQL_QUERY_TYPE::value('SELECT_ALL'), $sql);
        $sql_fout = DATABANK::geef_instantie()->geef_sql_fout();
        $aantal_records = DATABANK::geef_instantie()->geef_sql_aantal_rijen();

        // parse the records
        $huidige_id = 0;
        $aantal_bevragingen = 0;

        $bevragingen = [];

        foreach($resultaten as $resultaat){
            // nieuwe bevraging parsen
            $id = $resultaat['id'];
            if ($id !== $huidige_id) {
                $doelgroep = $resultaat['groepnaam'];
                $vragenlijst = new Vragenlijst($resultaat['vragenlijst_naam']);
                $intedienen_door = new Gebruiker($resultaat['gebruiker'], null, $resultaat['voornaam'], $resultaat['familienaam']);
                $deadline = new DateTime($resultaat['deadline']);
                // enkel datum invullen indien opgevuld
                $voltooid_op = ($resultaat['voltooid'] !== null) ? new DateTime($resultaat['voltooid']) : null;
                // is anoniem?
                $is_anoniem = ($resultaat['is_anoniem'] === "J") ? true : false;
                // met score?
                $met_score = ($resultaat['met_score'] === "J") ? true : false;
                $bevragingen[$id] = new Bevraging($id, $doelgroep, $vragenlijst, $intedienen_door, $deadline, $voltooid_op, $is_anoniem, $met_score);
                $aantal_bevragingen += 1;
                $huidige_id = $id;
            }
            // bestaande bevraging aanvullen
            else {
                $huidige_bevraging = $bevragingen[$id];
                $vraag_id = $resultaat['vraagid'];
                // nieuwe vraag toevoegen
                $vraag = $huidige_bevraging->geef_vragenlijst()->geef_vraag($vraag_id);
                if ($vraag === null){

                }
                // aantwoord aan vraag toevoegenw
                else {

                }
            }
        }
    }
?>

<h2>Test pagina</h2>
<p>
    Statistieken laden pagina:<br/>
    query: <?php echo $sql ?><br/>
    fout bij uitvoeren? <?php if (empty($sql_fout)) { echo "Nee"; } else { echo $sql_fout; }?><br/>
    aantal records: <?php echo $aantal_records ?><br/>
    aantal bevragingen: <?php echo $aantal_bevragingen ?><br/>
    tijd nodig: <?php echo $tijdnodig ?><br/>
    geheugen gebruikt: <?php echo $geheugen_gebruikt; ?><br/>
    max geheugen gebruikt: <?php echo $max_geheugen_gebruikt; ?>
</p>
