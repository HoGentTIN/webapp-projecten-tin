<?php

require_once '/srv/prjtinapp' . . '/vendor/autoload.php';

/**
 * Maakt een pdf aan op basis van aangeleverede htmlcode
 * @param $stylesheet_naam  Het css bestand voor de layout
 * @param $html_code        De htmlcode voor de inhoud
 * @param $pdf_naam         De naam van de pdf
 */
function download_pdf($html_code, $pdf_naam){
    $mpdf = new \Mpdf\Mpdf( ['format' => 'A4-L']);
    // Nieuwe pagina toevoegen in 'L' (landscape)q
    $mpdf->pdf_version = '1.5';
    $stylesheets = file_get_contents('/srv/prjtinapp' . . '/vendor/bootstrap/css/bootstrap.min.css');
    $stylesheets = file_get_contents('/srv/prjtinapp' . . '/css/style.css');
    $mpdf->WriteHTML($stylesheets,1);
    $mpdf->WriteHTML($html_code,2);
    // D optie om te downloaden
    $mpdf->Output($pdf_naam, 'D');
}