<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends CI_Controller {

	public function __construct(){
		parent::__construct();
	
		$this->load->model('Invoice_model');
		$this->load->library('datatable'); 
		$this->load->library('ZohoBooks'); 
	}

	public function index()
	{
		$user_id = (int) $this->session->userdata('id');

		if(!$user_id)
		{
			$this->session->set_flashdata('error', "You need to login first");
			return redirect(base_url('login'));
		}

		if($this->input->is_ajax_request())
		{
			$records = $this->Invoice_model->get_all();
			$data = array();

			$i=0;
			foreach ($records['data']  as $row) 
			{  
				
				
				$status = '';
				$data[]= array(
					++$i,
					$row['invoice_no'],
					$row['customer_name'], 
					$row['date'],
					$row['due_date'], 
					$row['status'], 
					$row['total_amount'], 
					$row['customer_phone'], 
					$row['services']
				);
			}
				$records['data']=$data;
				echo json_encode($records);	die;
		}

		
		$this->load->view('includes/header');
        $this->load->view('invoice/index');
        $this->load->view('includes/footer', []);
	}


	public function sync()
	{
		if($this->input->is_ajax_request())
		{
			$response = ['status' =>1,'redirect_url' =>'','msg' => "Invoices have been synced."];

			$is_token_expire = $this->zohobooks->isTokenExpired();
			
			if($is_token_expire)
			{
				$response['status'] = 0;
				$response['redirect_url'] = base_url('request-token');
				$response['msg'] = "The token has expired. We are redirecting you to grant access again.";

			}
			else
			{
				$invoices = $this->zohobooks->getInvoices();
			
				if(!is_array($invoices))
				{
					$response['status'] = 0;
					$response['msg'] = "Failed to sync invoices, Please try again";
				}
				elseif(empty($invoices))
				{
					$response['status'] = 0;
					$response['msg'] = "There are no invoices to sync.";
				}
				else
				{
					if(!empty($invoices))
					{
						$new_invoices = [];
						$existing_invoices = [];
						foreach($invoices as $invoice)
						{
							$invoice_data = $this->zohobooks->getInvoice($invoice['invoice_id']);
							
							$invoice_items = [];

							if(!empty($invoice_data['invoice']) && !empty($invoice_data['invoice']['line_items']))
							{
								foreach($invoice_data['invoice']['line_items'] as $item)
								{
									$invoice_items[] = ['name' => $item['name'],'unit' => $item['unit'],'quantity' => $item['quantity'],'discount_amount' => $item['discount_amount'],'rate' => $item['rate'],'item_total' => $item['item_total']];
								}
							}

							$invoice_detail = [
								'zoho_invoice_id' => $invoice['invoice_id'],
								'invoice_no' => $invoice['invoice_number'],
								'customer_name' => $invoice['customer_name'],
								'date' => $invoice['date'],
								'due_date' => $invoice['due_date'],
								'status' => $invoice['status'],
								'customer_phone' => $invoice['phone'],
								'services' => json_encode($invoice_items),
								'total_amount' => $invoice['total'],
							];

							$is_exists = $this->Invoice_model->isExists($invoice['invoice_id']);

							if(empty($is_exists))
							{
								$new_invoices[] = $invoice_detail;
							}
							else
							{
								$existing_invoices[] = $invoice_detail;
							}
						}

						$this->Invoice_model->insertInvoices($new_invoices);
						$this->Invoice_model->updateInvoices($existing_invoices);
					}
				}
			}

			


			echo json_encode($response);
			die;

		}
		else
		{
			$this->session->set_flashdata('error', "Invalid request");
			return redirect(base_url('dashboard'));
		}
		
	}
	
	

}
