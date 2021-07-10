$(document).ready(function(){
    $(".btn-dlt-alert").click(function(event){
        button = $(this);
        index = $(".btn-dlt-alert").index(button);
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false,
        })
        
        swalWithBootstrapButtons.fire({
            title: button.data("title"),
            text: button.data("text"),
            type: 'warning',
            showCancelButton: true,
            confirmButtonClass: 'ml-2',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $(".frm-dlt-alert").eq(index).submit();
            }
        });
    });

    if ($(".select2-bimbel").length) {
        $(".select2-bimbel").select2();
    }
});