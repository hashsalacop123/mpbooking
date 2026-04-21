jQuery(document).ready(function($){

    // ================================
    // INIT DATATABLE
    // ================================
const table = $('#hash-reviews-table').DataTable({
    responsive: true, // 👈 REQUIRED
    autoWidth: false,

    ajax: {
        url: booking_ajax.ajax_url,
        type: 'POST',
        data: {
            action: 'hash_get_reviews_table'
        },
        dataSrc: function(json) {
            console.log(json);
            return json.data;
        }
    },

    columns: [
        { data: 'court' },   // 0
        { data: 'name' },    // 1
        { data: 'role' },    // 2
        { data: 'comment' }, // 3
        { data: 'rating' },  // 4
        { data: 'date' },    // 5
        { data: 'status' },  // 6 👈 IMPORTANT
        {
            data: 'view',
            render: function(data){
                return `<a href="${data}" target="_blank">View</a>`;
            }
        },                   // 7
        { data: 'action' }   // 8
    ],

    columnDefs: [
        { responsivePriority: 1, targets: 0 }, // court
        { responsivePriority: 2, targets: 1 }, // name
        { responsivePriority: 3, targets: 6 }, // status 👈 KEEP THIS
        { responsivePriority: 4, targets: -1 } // action
    ]
});


    // ================================
    // STORE SELECTED REVIEW ID
    // ================================
    let selectedReviewID = null;


    // ================================
    // OPEN MODAL (FIXED FOR DATATABLES)
    // ================================
    $('#hash-reviews-table').on('click', '.manage-review', function(){
        selectedReviewID = $(this).data('id');
        $('#reviewModal').modal('show');
    });


    // ================================
    // HANDLE SAVE ACTION (MODAL)
    // ================================
   $('#save-review-action').on('click', function(){

    if(!selectedReviewID){
        Swal.fire({
            icon: 'warning',
            title: 'No review selected',
            text: 'Please select a review first'
        });
        return;
    }

    let action = $('#review-action').val();
    let ajaxAction = '';
    let actionText = '';

    if(action === 'approve'){
        ajaxAction = 'hash_approve_comment';
        actionText = 'approve';
    } else if(action === 'reject'){
        ajaxAction = 'hash_reject_comment';
        actionText = 'reject';
    } else {
        ajaxAction = 'hash_set_pending_comment';
        actionText = 'set to pending';
    }

    // ✅ CONFIRMATION MODAL
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to ${actionText} this review.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, continue',
        cancelButtonText: 'Cancel'
    }).then((result) => {

        if(result.isConfirmed){

            // ✅ LOADING STATE
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.post(booking_ajax.ajax_url, {
                action: ajaxAction,
                comment_id: selectedReviewID
            }, function(response){

                if(response.success){

                    // ✅ SUCCESS MESSAGE
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Review ${actionText} successfully`,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    $('#reviewModal').modal('hide');

                    // ✅ RELOAD DATATABLE
                    table.ajax.reload(null, false);

                } else {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.data || 'Something went wrong'
                    });

                }

            });

        }

    });

});

});