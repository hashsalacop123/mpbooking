<?php
// TEMPLATE NAME: Review
get_header();
?>

<div class="dasboard-wrapper-page">
    <div class="container">
        <div class="row">

        <!-- SIDEBAR -->
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                    <?php include get_template_directory() . '/dashboard/dashboard-sidebar.php'; ?>
            </div>

            <!-- CONTENT -->
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                <?php 
                    
                ?>
            <table id="hash-reviews-table" class="tables-general display">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th> <!-- ✅ -->
            <th>Comment</th>
            <th>Rating</th>
            <th>Date</th>
            <th>Status</th>
            <th>View</th>
            <th>Action</th>
        </tr>
    </thead>
</table>



                

                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reviewModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Manage Review</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
    <form class = "container">
        <select id="review-action" class="form-control">
            <option value="approve">Approve</option>
            <option value="reject">Reject</option>
            <option value="pending">Pending</option>
        </select>
    </form>
      </div>

      <div class="modal-footer">
        <button class="btn btn-primary" id="save-review-action">update</button>
      </div>

    </div>
  </div>
</div>
<?php get_footer(); ?>