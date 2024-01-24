<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <script>var base_url = "<?php echo base_url(); ?>";</script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>

    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script src="https://cdn.synorexcloud.com/template/159980217293/js/theme.js"></script>
    <script src="<?php echo base_url('assets/js/custom.js?v=1'); ?>"></script>

    <?php

    switch($thispage['group'].'/'.$thispage['title']) {

        case 'calendar/Calendar':
                echo '<script src="https://cdn.synorex.link/libraries/fullcalendar/4.2/packages/core/main.min.js"></script>';
                echo '<script src="https://cdn.synorex.link/libraries/fullcalendar/4.2/packages/daygrid/main.min.js"></script>';
                echo '<script src="https://cdn.synorex.link/libraries/fullcalendar/4.2/packages/list/main.min.js"></script>';
                echo '<script src="https://cdn.synorex.link/libraries/fullcalendar/4.2/packages/interaction/main.min.js"></script>';
                echo '<script src="https://cdn.synorex.link/libraries/fullcalendar/4.2/packages/timegrid/main.min.js"></script>';
            break;

        case 'payment/Add Payment':
                echo '<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>';
            break;

    }

    if(isset($thispage['js'])) echo '<script src="'.base_url('assets/pages/'.$thispage['js'].'.js?v='.time()).'"></script>';
    ?>
    
</body>

</html>
