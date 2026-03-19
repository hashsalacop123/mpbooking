jQuery(function($) {

    // Click handler for logout
    $(document).on('click', '.logout-link', function(e) {
        e.preventDefault(); // prevent default logout
        var logoutUrl = $(this).attr('href'); // get the WordPress logout URL

        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out from your account!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, log me out',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to logout
                window.location.href = logoutUrl;
            }
        });

    });

});