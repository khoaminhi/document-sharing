        <!-- bootstrap -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous">
        </script>


        
        <!-- modal bootstrap -->
        <script>
            $(window).on('load', function() {
                $('#warningBootstrapModal').modal('show');
            });
            $(document).ready(() => {
                $('#hideNoticeRelPerson').click(() => {
                    $('#warningBootstrapModal').modal('hide');
                })
            })
        </script>
    </body>
</html>