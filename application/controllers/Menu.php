<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('backend/M_menu');
		$this->M_login->getsecurity();
	}

	function index()
	{
		$akses = $this->M_app->hak_akses($this->session->userdata('id_level'), $this->router->fetch_class());
		($akses->view_level == 'N' ? redirect('auth') : '');
		$data['add'] = $akses->add_level;
		$data['title'] = "backend/menu";
		$data['titleview'] = "Data Menu";
		$this->load->view('backend/home', $data);
	}

	function get_list()
	{
		$list = $this->M_menu->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$akses = $this->M_app->hak_akses($this->session->userdata('id_level'), $this->router->fetch_class());
		foreach ($list as $field) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = ($akses->edit_level == 'Y' ? '<a class="btn btn-warning btn-circle btn-sm" title="Edit" onclick="edit_data(' . "'" . $field->id_menu . "'" . ')"><i class="fa fa-edit"></i></a>&nbsp;' : '') .
				($akses->delete_level == 'Y' ? '<a class="btn btn-danger btn-circle btn-sm" title="Delete" onclick="delete_data(' . "'" . $field->id_menu . "'" . ')"><i class="fa fa-trash"></i></a>' : '');
			$row[] = $field->nama_menu;
			$row[] = $field->link;
			$row[] = $field->icon;
			$row[] = $field->urutan;
			$row[] = ($field->is_active == 'Y' ? 'Yes' : 'No');

			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_menu->count_all(),
			"recordsFiltered" => $this->M_menu->count_filtered(),
			"data" => $data,
		);
		//output dalam format JSON
		echo json_encode($output);
	}

	function delete($id)
	{
		$this->M_menu->delete_id($id);
		echo json_encode(array("status" => TRUE));
	}

	function add()
	{
		$data = array(
			'nama_menu' => $this->input->post('menu'),
			'link' => $this->input->post('link'),
			'icon' => $this->input->post('icon'),
			'urutan' => $this->input->post('urutan'),
			'is_active' => $this->input->post('aktif'),
		);
		$this->M_menu->save($data);
		echo json_encode(array("status" => TRUE));
	}

	function update()
	{
		$data = array(
			'nama_menu' => $this->input->post('menu'),
			'link' => $this->input->post('link'),
			'icon' => $this->input->post('icon'),
			'urutan' => $this->input->post('urutan'),
			'is_active' => $this->input->post('aktif'),
		);
		$this->db->where('id_menu', $this->input->post('id'));
		$this->db->update('tbl_menu', $data);
		echo json_encode(array("status" => TRUE));
	}

	function get_id($id)
	{
		$data = $this->M_menu->get_by_id($id);
		echo json_encode($data);
	}

	function get_max()
	{
		$max = $this->M_menu->get_max();
		$data = $max->urutan + 1;
		echo json_encode($data);
	}
}
