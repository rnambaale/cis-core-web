require("./bootstrap");
import 'select2';
import 'jquery-ui/ui/widgets/datepicker.js';

$("#sidemenu")
    .metisMenu()
    .show();

$('.datepicker').datepicker({
    dateFormat: "yy-mm-dd"
});