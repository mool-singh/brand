<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {


	public function dashboard()
	{
		$user_id = (int) $this->session->userdata('id');

		if(!$user_id)
		{
			$this->session->set_flashdata('error', "You need to login first");
			return redirect(base_url('login'));
		}

		

		$name =  $this->session->userdata('name');
		
		$title = "Dashboard";
		
		$this->load->view('includes/header', compact('title'));
        $this->load->view('profile/dashboard',compact('title','name'));
        $this->load->view('includes/footer', []);
	}
	
	

}
