<?php
   /**
    * Maakt nieuwe kaart aan voor op het dashboard
    * @param $panel_kleur  De kleur van de kaart
    * @param $aantal       Het aantal om te tonen
    * @param $type         Het type
    * @return string       De htmlcode
    */
    function maak_dashboard_kaart($panel_kleur, $aantal, $type, $url="#", $details="Bekijk details"): string {
    $html_code = '<div class="col-md-6 col-lg-4 col-xl-3">' .
                    '<div class="card text-white kaart-' . $panel_kleur. '">' .
                        '<div class="card-header">' .
                            '<div class="row">' .
                                '<div class="col-3">' .
                                    '<i class="fa fa-tasks fa-5x"></i>' .
                                '</div>' .
                                '<div class="col-9 text-right">' .
                                    '<div class="huge">'. $aantal . '</div>' .
                                    '<div>'.  ucwords($type) . '</div>' .
                                '</div>' .
                            '</div>' .
                        '</div>' .
                        '<a href="'. $url . '">' .
                            '<div class="card-footer">' .
                                '<span class="float-left">' .  ucwords($details) . '</span>' .
                                '<span class="float-right"><i class="fa fa-arrow-circle-right"></i></span>' .
                                '<div class="clearfix"></div>' .
                            '</div>' .
                        '</a>' .
                    '</div> '.
                    '<br />' .
                '</div>';
    return $html_code;
    }

    /**
     * Maakt een HTML input element dat fungeert als toggle op basis van
     * @param string $toggle_id        Het id voor het input element
     * @param string $wijzig_methode   De methode die opgeroepen wordt bij onchange ("" indien niet gewenst)
     * @param bool $actief           Boolean die aanduidt of de toggle aan of uit staat (Standaard = "true")
     * @param string $waarde_ja        Eigen tekst indien toggle "on" is (Standaard = "Ja")
     * @param string $waarde_nee       Eigen tekst indien toggle "off" is (Standaard = "Nee")
     * @return string           De opgebouwde HTML code
     */
    function maak_html_toggle(string $toggle_id, string $wijzig_methode="", bool $actief=true,string $waarde_ja="Ja",string $waarde_nee="Nee"): string {
        $html_toggle = '<input id="toggle-' . $toggle_id .'" name="' . $toggle_id .'"';
        if($wijzig_methode != "") {
            $html_toggle .= ' onchange="' . $wijzig_methode . '(' . "'" . $toggle_id . "'" . ');"';
        }
        if($actief) { $html_toggle .= 'checked'; }
        // Eigen ja/nee waarden voor toggler, groen/roode kleur
        $html_toggle .= ' data-toggle="toggle" type="checkbox" data-on="' . $waarde_ja . '" data-off="' . $waarde_nee . '" data-onstyle="success" data-offstyle="danger">';
        return $html_toggle;
    }

    /**
     * Maakt een HTML select element aan p basis van
     * @param string $lijst_id         Het id dat de lijst zal krijgen in HTML code
     * @param int $aantal_rijen     Het aantal zichtbare rijen
     * @param strin $wijzig_methode   De methode die opgeroepen wordt bij onchange ("" indien niet gewenst)
     * @param array $lijst            De lijst met elementen in
     * @param $gekozen_waarde   De huidige geselecteerde waarde uit de lijst
     * @param string $sleutel_waarde   De naam van de sleutel voor ophalen waarde
     * @param string $sleutel_tekst    De naam van de sleutel voor ophalen tekstsleutel
     * @param bool $is_verplicht     Geeft aan of de select verplicht is
     * @param bool $is_actief     Geeft aan of de select actief is
     * @return string           De opgebouwde html code
     */
    function maak_html_select (string $lijst_id, int $aantal_rijen, string $wijzig_methode, array $lijst,
                                $gekozen_waarde, string $sleutel_waarde, string $sleutel_tekst, bool $is_verplicht=false, bool $is_actief=true): string {
        $html_select = ' <select class="form-control" size="'. $aantal_rijen. '" id="sel-' . $lijst_id . '" name="' . $lijst_id . '"';
        if($wijzig_methode != "") {
            $html_select .= ' onchange="' . $wijzig_methode . '(' . "'" . $lijst_id . "'" . ');"';
        }
        if($is_verplicht){
            $html_select .= ' required="required" ';
        }
        if(!$is_actief){
            $html_select .= ' disabled="disabled" ';
        }
        // Kijken of er element zijn om toe te voegen
        if(count($lijst) <= 0){;
            // lijst disablen
            $html_select .=  ' disabled="disabled" ';
            // dummy lege optie toevoegen om alles mooi te renderen
            $html_select .= '<option>&nbsp;</option>';
        }
        else {
            // select tag afsluiten
            $html_select .=  '>';
            // elk element aan lijst toevoegen
            foreach ($lijst as $element) {
                $html_select .= '<option ';
                if ($gekozen_waarde === $element[$sleutel_waarde]) {
                    $html_select .= ' selected="selected" ';
                }
                // Er voor zorgen dat als we de select list uitklappen we deze waarde (= -- Kies xx --) niet kunnen selecteren
                if($element[$sleutel_waarde] === ""){
                    $html_select .= ' disabled="disabled" ';
                }
                // ucwords om getoonde tekst te tonen als camel casing
                $html_select .= ' value="' . $element[$sleutel_waarde] . '">' . ucwords($element[$sleutel_tekst]) . '</option>';
            }
        }
        $html_select .= '</select>';
        return $html_select;
    }

    /**
     * Maakt een invoerveld aan met label links van invoerveld
     * @param string $tekst
     * @param string $type   Standaard "text"
     * @return string
     */
    function maak_invoerveld_label_links($tekst, $type="text") : string {
        $tekst_ids = str_replace(' ', '_', strtolower($tekst));
        return '<div class="input-group mb-3">' .
                    '<div class="input-group-prepend">' .
                        '<span class="input-group-text" id="inp-' . $tekst_ids. '">' . $tekst . '</span>' .
                    '</div>' .
                    '<input type="'. $type. '" class="form-control" name="' . $tekst_ids . '" placeholder="'. $tekst. '" required="required" aria-describedby="inp-' . $tekst_ids . '" />' .
                '</div>';
    }

    // Apart gezet omdat deze knop veel gebruikt wordt
    /**
     * Maakt een "terug naar dashboard" knop aan in htmlcode (Gebruikt "maak_submit_knop")
     * @param string $naar_tekst     Indien andere tekst dan dashboard gewenst
     * @return string
     */
    function maak_dashboard_knop($naar_tekst="dashboard") : string {
        return maak_submit_knop("Terug naar " . $naar_tekst, "arrow-return-left", "start", false, false);
    }

    /**
     * Maakt een submitknop aan
     * @param string $icoon_klasse    De klasse van ion icoon
     * @param string $tekst           De tekst die getoond word op de knop
     * @param string $knop_naam       De naam van de knop
     * @param bool $volle_lijn        Geeft aan of de knop de volledige regel op de pagina inneemt of niet
     * @param bool $validatie         Geeft aan of het form moet gevalideert worden bij submit
     * @param string $knop_klasse     De klasse van de knop (Standaard "primary")
     * @return string
     */
    function maak_submit_knop($tekst, $icoon_klasse, $knop_naam, $volle_lijn=true, $validatie=true, $knop_klasse="primary", $onclick="") : string {
        $submit_knop = '<button type="submit" name="' . $knop_naam . '" class="btn ';
        if($volle_lijn) { $submit_knop .= ' btn-block '; }
        $submit_knop .= ' btn-' . $knop_klasse. '"';
        if(!$validatie) { $submit_knop .= " formnovalidate "; }
        if($onclick !== "") { $submit_knop .= ' onclick="javascript:' . $onclick . '"'; }
        $submit_knop .= '>';
        $submit_knop .= '<i class="ion-' . $icoon_klasse. '"></i>&nbsp;' . $tekst;
        $submit_knop .='</button>';
        return $submit_knop;
    }

    /**
     * @param $id
     * @param string $min
     * @param string $max
     * @param bool $isverplicht
     * @return string
     */
    function maak_datumveld($id, $min="", $max="", $isverplicht=false) : string {
        $datumveld = '<input class="form-control" id="inp-' . $id . '" name="' . $id . '" type="date" ';
        if($min !== "") { $datumveld .= ' min="' . $min . '" '; }
        if($max !== "") { $datumveld .= ' max="' . $max . '" '; }
        if($isverplicht) { $datumveld .= ' required '; }
        $datumveld .= '>';
        return $datumveld;
    }

    /**
     * @param string $naam
     * @param string $extensie
     * @param string $invoer_element
     * @return string
     */
    function maak_oplaad_knop(string $naam, string $extensie, string $invoer_element) : string {
        $oplaad_knop = '<label class="btn btn-primary" for="' . $naam . '">' .
                            '<input id="file" name="' . $naam . '" type="file" style="display: none !important;" ' .
                                'onchange="$(' . "'" . '#' . $invoer_element . "'" . ').val(this.files[0].name)" accept="' . $extensie . '">'.
                            'Bladeren ...'.
                        '</label>';
        return $oplaad_knop;
    }
?>