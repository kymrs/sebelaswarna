<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reimbust extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('backend/M_reimbust');
        $this->M_login->getsecurity();
    }

    public function index()
    {
        $data['title'] = "backend/reimbust/reimbust_list";
        $data['titleview'] = "Data Reimbust";
        $this->load->view('backend/home', $data);
    }

    function get_list()
    {
        $list = $this->M_reimbust->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="reimbust/read_form/' . $field->id . '" class="btn btn-info btn-circle btn-sm" title="Read"><i class="fa fa-eye"></i></a>
            <a href="reimbust/edit_form/' . $field->id . '" class="btn btn-warning btn-circle btn-sm" title="Edit"><i class="fa fa-edit"></i></a>
			<a onclick="delete_data(' . "'" . $field->id . "'" . ')" class="btn btn-danger btn-circle btn-sm" title="Delete"><i class="fa fa-trash"></i></a>';
            $row[] = $field->kode_prepayment;
            $row[] = $field->nama;
            $row[] = $field->jabatan;
            $row[] = $field->departemen;
            $row[] = $field->sifat_pelaporan;
            $row[] = date("d M Y", strtotime($field->tgl_pengajuan));
            $row[] = $field->tujuan;
            $row[] = $field->status;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->M_reimbust->count_all(),
            "recordsFiltered" => $this->M_reimbust->count_filtered(),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    function read_form($id)
    {
        $data['aksi'] = 'read';
        $data['id'] = $id;
        $data['title_view'] = "Data Reimbust";
        $data['title'] = 'backend/reimbust/reimbust_form';
        $this->load->view('backend/home', $data);
    }

    public function add_form()
    {
        $kode = $this->M_reimbust->max_kode()->row();
        if (empty($kode->kode_prepayment)) {
            $no_urut = 1;
        } else {
            $bln = substr($kode->kode_prepayment, 3, 2);
            if ($bln != date('m')) {
                $no_urut = 1;
            } else {
                $no_urut = substr($kode->kode_prepayment, 5) + 1;
            }
        }
        $urutan = str_pad($no_urut, 3, "0", STR_PAD_LEFT);
        $data['kode'] = 'B' . date('ym') . $urutan;
        $data['id'] = 0;
        $data['title_view'] = "Data Reimbust Form";
        $data['title'] = 'backend/reimbust/reimbust_form';
        $this->load->view('backend/home', $data);
    }

    function edit_form($id)
    {
        $data['id'] = $id;
        $data['title_view'] = "Edit Data Reimbust";
        $data['title'] = 'backend/reimbust/reimbust_form';
        $this->load->view('backend/home', $data);
    }

    function edit_data($id)
    {
        $data = $this->M_reimbust->get_by_id($id);
        echo json_encode($data);
    }

    public function add()
    {
        $data1 = array(
            'kode_prepayment' => $this->input->post('kode_prepayment'),
            'nama' => $this->input->post('nama'),
            'jabatan' => $this->input->post('jabatan'),
            'departemen' => $this->input->post('departemen'),
            'sifat_pelaporan' => $this->input->post('sifat_pelaporan'),
            'tgl_pengajuan' => date('Y-m-d', strtotime($this->input->post('tgl_pengajuan'))),
            'tujuan' => $this->input->post('tujuan'),
            'status' => $this->input->post('status')
        );

        $inserted = $this->M_reimbust->save($data1);

        if ($inserted) {
            $pemakaian = $this->input->post('pemakaian[]');
            $tgl_nota = $this->input->post('tgl_nota[]');
            $jumlah = $this->input->post('jumlah[]');

            for ($i = 1; $i <= count($_POST['pemakaian']); $i++) {
                $data2[] = array(
                    'id_reimbust' => $inserted,
                    'pemakaian' => $pemakaian[$i],
                    'tgl_nota' => $tgl_nota[$i],
                    'jumlah' => $jumlah[$i]
                    // 'kwitansi' => $this->input->post('kwitansi'),
                    // 'deklarasi' => $this->input->post('deklarasi')
                );
                $this->M_reimbust->save_detail($data2);
                echo json_encode(array("status" => TRUE));
            }
        }
    }

    public function update()
    {
        $data = array(
            'nama' => $this->input->post('nama'),
            'jabatan' => $this->input->post('jabatan'),
            'departemen' => $this->input->post('departemen'),
            'sifat_pelaporan' => $this->input->post('sifat_pelaporan'),
            'tgl_pengajuan' => date('Y-m-d', strtotime($this->input->post('tgl_pengajuan'))),
            'tujuan' => $this->input->post('tujuan'),
            'status' => $this->input->post('status')
        );
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('tbl_reimbust', $data);
        echo json_encode(array("status" => TRUE));
    }

    function delete($id)
    {
        $this->M_reimbust->delete($id);
        echo json_encode(array("status" => TRUE));
    }
}
