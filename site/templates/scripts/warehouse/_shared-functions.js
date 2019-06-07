$(function() {
    /**
	 * IF WAREHOUSE HAS A BIN LIST THEN SHOW A DROPDOWN LIST OF THE BIN LIST
	 * IF IT'S A BIN RANGE THEN WE SHOW THEM WHAT THE BIN RANGE IS
	 */
    $("body").on("click", ".show-possible-bins", function(e) {
       e.preventDefault();
       var button = $(this);
       var input_name = button.data('input');
       var input = button.closest('form').find('input[name='+input_name+']');

       if (whsesession.whse.bins.arranged == 'list') { // IF WAREHOUSE HAS A BIN LIST
           var bins = {};
           var binid = '';
           var spacesneeded = 0;
           var spaces = '';

           for (var key in whsesession.whse.bins.bins) {
               binid = key;
               spacesneeded = (8 - binid.length);
               spaces = '';
               for (var i = 0; i <= spacesneeded; i++) {
                   spaces += '&nbsp;';
               }
               bins[key] = binid + spaces + whsesession.whse.bins.bins[key];
           }
           swal_choosebin(bins, input);
       } else {
           var table = create_binrangetable();

           swal({
               type: 'info',
               title: 'Bin Ranges',
               html: table
           }).catch(swal.noop);
       }
   });

    function create_binrangetable() {
        var bootstrap = new JsContento();
        var table = bootstrap.open('table', 'class=table table-striped table-condensed');
            whsesession.whse.bins.bins.forEach(function(bin) {
                table += bootstrap.open('tr', '');
                    table += bootstrap.openandclose('td', '', bin.from);
                    table += bootstrap.openandclose('td', '', bin.through);
                table += bootstrap.close('tr');
            });
        table += bootstrap.close('table');
        return table;
    }
});

function swal_choosebin(bins, bininput) {
    swal({
        type: 'question',
        title: 'Choose a bin',
        input: 'select',
        inputClass: 'form-control',
        inputOptions: bins,
    }).then(function (input) {
        if (input) {
            bininput.val(input);
            swal.close();
        }
    }).catch(swal.noop);
}
