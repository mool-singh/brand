<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<link rel="stylesheet" href="<?=base_url('assets/plugins/datatables-custom/datatables.min.css')?>">

<div class="card-header d-flex justify-content-between">
	<h4 class="mb-0">Invoices</h4>
	<div class="buttons">
		<button type="button" onclick="sync(this)" class="btn btn-sm btn-primary"> Sync Invoices</button>
	</div>
</div>
<div class="card-body">

	<div class="table-responsive">
		<table class="table table-striped" id="datatable">
			<thead>
				<tr>
					<th>S.no</th>
					<th>Invoice No</th>
					<th>Customer</th>
					<th>Date</th>
					<th>Due Date</th>
					<th>Status</th>
					<th>Amount</th>
					<th>Customer Contact</th>
					<th>Services</th>
				</tr>
			</thead>
			<tbody>

			</tbody>
		</table>
	</div>

</div>

<script src="<?=base_url('assets/plugins/datatables-custom/datatables.min.js')?>"></script>
<script>
  //---------------------------------------------------
  var table = $('#datatable').DataTable( {
    "processing": true,
    "serverSide": true,
    "ajax": "",
    "order": [[0,'desc']],
    "columnDefs": [
    { "targets": 0, "name": "id", 'searchable':true, 'orderable':true}, 
    { "targets": 1, "name": "invoice_no", 'searchable':true, 'orderable':true},
    { "targets": 2, "name": "customer_name", 'searchable':true, 'orderable':true},
    { "targets": 3, "name": "date", 'searchable':true, 'orderable':true},
    { "targets": 4, "name": "due_date", 'searchable':true, 'orderable':true},
    { "targets": 5, "name": "status", 'searchable':true, 'orderable':true},
    { "targets": 6, "name": "customer_phone", 'searchable':true, 'orderable':true},
    { "targets": 7, "name": "total_amount", 'searchable':true, 'orderable':true},
    { "targets": 8, "name": "services", 'searchable':true, 'orderable':true}
    ]
  });


  function sync(btn)
  {
	$(btn).attr('disabled',true);
	$(btn).html('<i class="fa-solid fa-rotate fa-spin-pulse"></i> Sync in progress');

	$.ajax({
		url:"<?=base_url('sync-invoices')?>",
		success:function(data){
			let res = JSON.parse(data);

			if(res.status == 1)
			{
				toastr.success(res.msg);
			}
			else
			{
				toastr.error(res.msg);
			}

			if(res.redirect_url != '')
			{
				setTimeout(() => {
					location.href = res.redirect_url;
				}, 2000);
			}

		},
		error:function(reponse)
		{
			toastr.error("Unable to login, Please reload and try again");
		},
		complete:function()
		{
			$(btn).html('Sync invoice');
			$(btn).attr('disabled',false);
		},
	})
  }

</script>