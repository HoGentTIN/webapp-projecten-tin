                    </form>
                </div>
            </div>
        </div>
        <script src="/prjtin/vendor/components/jquery/jquery.js"></script>
        <script src="/prjtin/vendor/bootstrap/dist/js/bootstrap.bundle.js"></script>
        <script src="/prjtin/js/bootstrap-toggle.min.js"></script>
        <!-- gedeelde javascript code -->
        <script type="application/javascript">
            // wijzig de filters van de tabel => reload
            function wijzig_filters() {
                filters = "";
                // alle filter velder ophalen en hun waarde in de hidden value plaatsen
                $('*[id*=filter]:visible').each(function() {
                    filters += $(this).val() + "_";
                });
                // waardes van alle filters bijhouden
                document.getElementById('form-rij-id').value = filters;
                // submit waarde op filters zetten zodat data op pagina goed geladen wordt
                document.getElementById('form-actie').value = "filters";
                // formulier submitten
                document.getElementById("form").submit();
            }

            // Toevoegen van de periode
            function submit_na_opslaan_id(id){
                // id bijhouden om na submit te kunnen opvragen
                document.getElementById('form-rij-id').value = id;
                // formulier submitten
                document.getElementById("form").submit();
            }
        </script>
    </body>
</html>