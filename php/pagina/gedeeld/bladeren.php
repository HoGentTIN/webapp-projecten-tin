<?php
/**
 * Generieke code voor elke pagina met tabel die bladerbaar is
 */

// altijd 1 element zetten, om / 0 te vermijden
if($totaal_aantal_elementen <= 0)  {
    $totaal_aantal_elementen = 1;
}

// Aantal elementen opvragen die we op deze pagina gaan tonen, standaard 10
// nieuwe waarde gekozen in selecht list
if(isset($_POST['toon-aantal'])){
    $aantal_elementen_per_pagina = intval($_POST['toon-aantal']);
    // exacte waarde instellen als we alles willen tonen
    if($aantal_elementen_per_pagina === TOON_AANTAL_ELEMENTEN_PER_PAGINA::ALLES){
        $aantal_elementen_per_pagina = $totaal_aantal_elementen;
    }
}
// huidige waarde opvragen
else {
    if(!isset($_POST['aantal-elementen'])) { $aantal_elementen_per_pagina = 10; }
    else { $aantal_elementen_per_pagina = $_POST['aantal-elementen']; }
}

// aantal maximale paginas berekenen
$max_paginas = intval(ceil($totaal_aantal_elementen / $aantal_elementen_per_pagina));
// Huidige pagina opvragen, indien niet ingezet nemen we eerste pagina
if(!isset($_POST['huidige-pagina'])) { $huidige_pagina = 1; }
else { $huidige_pagina = $_POST['huidige-pagina']; }
// kijken of we op een blader knop geduwt hebben
if (isset($_POST['eerste-pagina'])) { $huidige_pagina = 1; }
if (isset($_POST['vorige-pagina'])) { $huidige_pagina -= 1; }
if (isset($_POST['volgende-pagina'])) { $huidige_pagina += 1; }
if (isset($_POST['laatste-pagina'])) { $huidige_pagina = $max_paginas; }

// na berekeningen de waarde teurgzetten van aantal_elementen_per_pagina
if($aantal_elementen_per_pagina === $totaal_aantal_elementen){
    $aantal_elementen_per_pagina = TOON_AANTAL_ELEMENTEN_PER_PAGINA::ALLES;
}