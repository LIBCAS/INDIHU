jQuery(document).ready(function(){
    jQuery('.sweet-delete-admin').click(function(){
        var title = jQuery(this).data('title');
        var description = jQuery(this).data('description');
        var action = jQuery(this).data('action');
        swal({
            title: title,
            text: description,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Smazat",
            cancelButtonText: "Zrušit"
        })
        .then(function(result){
            if(result.value){
                window.location.href = action;
            }
        });
    });
});
