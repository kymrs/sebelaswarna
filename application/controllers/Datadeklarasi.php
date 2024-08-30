<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Datadeklarasi extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('backend/M_datadeklarasi');
        $this->M_login->getsecurity();
    }

    public function index()
    {
        $data['title'] = "backend/datadeklarasi/deklarasi_list";
        $data['titleview'] = "Data Deklarasi";
        $this->load->view('backend/home', $data);
    }

    function get_list()
    {
        $id_level = $this->session->userdata('id_level');
        $fullname = $this->session->userdata('fullname');
        $list = $this->M_datadeklarasi->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {

            $action = '<a href="datadeklarasi/read_form/' . $field->id . '" class="btn btn-info btn-circle btn-sm" title="Read"><i class="fa fa-eye"></i></a>
                                <a href="datadeklarasi/edit_form/' . $field->id . '" class="btn btn-warning btn-circle btn-sm" title="Edit"><i class="fa fa-edit"></i></a>
                                <a onclick="delete_data(' . "'" . $field->id . "'" . ')" class="btn btn-danger btn-circle btn-sm" title="Delete"><i class="fa fa-trash"></i></a>
                                <a class="btn btn-success btn-circle btn-sm" href="datadeklarasi/generate_pdf/' . $field->id . '"><i class="fas fa-file-pdf"></i></a>';

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $action;
            $row[] = strtoupper($field->kode_deklarasi);
            $row[] = date("d M Y", strtotime($field->tgl_deklarasi));
            $row[] = $field->name;
            $row[] = $field->jabatan;
            $row[] = $field->nama_dibayar;
            $row[] = $field->tujuan;
            $row[] = $field->sebesar;
            $row[] = $field->status;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->M_datadeklarasi->count_all(),
            "recordsFiltered" => $this->M_datadeklarasi->count_filtered(),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    function read_form($id)
    {
        $data['id'] = $id;
        $data['user'] = $this->M_datadeklarasi->get_by_id($id);
        $data['app_name'] = $this->db->select('name')
            ->from('tbl_data_user')
            ->where('id_user', $this->session->userdata('id_user'))
            ->get()
            ->row('name');
        $data['app2_name'] = $this->db->select('name')
            ->from('tbl_data_user')
            ->where('id_user', $this->session->userdata('id_user'))
            ->get()
            ->row('name');
        $data['title_view'] = "Data deklarasi";
        $this->load->view('backend/datadeklarasi/deklarasi_read', $data);
    }

    function add_form()
    {
        $data['id'] = 0;
        $data['title_view'] = "Data deklarasi Form";
        $data['aksi'] = 'update';
        $data['title'] = 'backend/datadeklarasi/deklarasi_form';
        $this->load->view('backend/home', $data);
    }

    function edit_form($id)
    {
        $data['id'] = $id;
        $data['title_view'] = "Edit Data Deklarasi";
        $data['title'] = 'backend/datadeklarasi/deklarasi_form';
        $this->load->view('backend/home', $data);
    }

    function edit_data($id)
    {
        $data['master'] = $this->M_datadeklarasi->get_by_id($id);
        $data['nama'] = $this->db->select('name')
            ->from('tbl_data_user')
            ->where('id_user', $data['master']->id_pengaju)
            ->get()->row('name');
        echo json_encode($data);
    }

    // MEREGENERATE KODE DEKLARASI
    public function generate_kode()
    {
        $date = $this->input->post('date');
        $kode = $this->M_datadeklarasi->max_kode($date)->row();
        if (empty($kode->kode_deklarasi)) {
            $no_urut = 1;
        } else {
            $bln = substr($kode->kode_deklarasi, 3, 2);
            $no_urut = substr($kode->kode_deklarasi, 5) + 1;
        }
        $urutan = str_pad($no_urut, 3, "0", STR_PAD_LEFT);
        $month = substr($date, 3, 2);
        $year = substr($date, 8, 2);
        $data = 'b' . $year . $month . $urutan;
        echo json_encode($data);
    }

    public function add()
    {
        // INSERT KODE DEKLARASI
        $date = $this->input->post('tgl_deklarasi');
        $kode = $this->M_datadeklarasi->max_kode($date)->row();
        if (empty($kode->kode_deklarasi)) {
            $no_urut = 1;
        } else {
            $bln = substr($kode->kode_deklarasi, 3, 2);
            $no_urut = substr($kode->kode_deklarasi, 5) + 1;
        }
        $urutan = str_pad($no_urut, 3, "0", STR_PAD_LEFT);
        $month = substr($date, 3, 2);
        $year = substr($date, 8, 2);
        $kode_deklarasi = 'b' . $year . $month . $urutan;

        // MENCARI SIAPA YANG AKAN MELAKUKAN APPROVAL PERMINTAAN
        $approval = $this->M_datadeklarasi->approval($this->session->userdata('id_user'));
        $id = $this->session->userdata('id_user');

        $data = array(
            'kode_deklarasi' => $kode_deklarasi,
            'tgl_deklarasi' => date('Y-m-d', strtotime($this->input->post('tgl_deklarasi'))),
            'id_pengaju' => $id,
            'jabatan' => $this->db->select('jabatan')
                ->from('tbl_data_user')
                ->where('id_user', $id)
                ->get()
                ->row('jabatan'),
            'nama_dibayar' => $this->input->post('nama_dibayar'),
            'tujuan' => $this->input->post('tujuan'),
            'sebesar' => $this->input->post('hidden_sebesar'),
            'app_name' => $this->db->select('name')
                ->from('tbl_data_user')
                ->where('id_user', $approval->app_id)
                ->get()
                ->row('name'),
            'app2_name' => $this->db->select('name')
                ->from('tbl_data_user')
                ->where('id_user', $approval->app2_id)
                ->get()
                ->row('name')
        );
        $this->M_datadeklarasi->save($data);
        echo json_encode(array("status" => TRUE));
    }

    public function update()
    {
        $data = array(
            'tgl_deklarasi' => date('Y-m-d', strtotime($this->input->post('tgl_deklarasi'))),
            'nama_dibayar' => $this->input->post('nama_dibayar'),
            'tujuan' => $this->input->post('tujuan'),
            'sebesar' => $this->input->post('hidden_sebesar'),
            'status' => $this->input->post('status'),
        );
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('tbl_deklarasi', $data);
        echo json_encode(array("status" => TRUE));
    }

    function delete($id)
    {
        $this->M_datadeklarasi->delete($id);
        echo json_encode(array("status" => TRUE));
    }

    //APPROVE DATA
    public function approve()
    {
        $data = array(
            'app_name' => $this->input->post('app_name'),
            'app_keterangan' => $this->input->post('app_keterangan'),
            'app_status' => $this->input->post('app_status'),
            'app_date' => date('Y-m-d H:i:s'),
        );
        //UPDATE APPROVAL PERTAMA
        $this->db->where('id', $this->input->post('hidden_id'));
        $this->db->update('tbl_deklarasi', $data);

        // UPDATE STATUS PREPAYMENT
        if ($this->input->post('app_status') == 'rejected') {
            $this->db->where('id', $this->input->post('hidden_id'));
            $this->db->update('tbl_deklarasi', ['status' => 'rejected']);
        } elseif ($this->input->post('app_status') == 'revised') {
            $this->db->where('id', $this->input->post('hidden_id'));
            $this->db->update('tbl_deklarasi', ['status' => 'revised']);
        }

        echo json_encode(array("status" => TRUE));
    }

    function approve2()
    {
        $data = array(
            'app2_name' => $this->input->post('app2_name'),
            'app2_keterangan' => $this->input->post('app2_keterangan'),
            'app2_status' => $this->input->post('app2_status'),
            'app2_date' => date('Y-m-d H:i:s'),
        );
        // UPDATE APPROVAL 2
        $this->db->where('id', $this->input->post('hidden_id'));
        $this->db->update('tbl_deklarasi', $data);

        // UPDATE STATUS PREPAYMENT
        if ($this->input->post('app2_status') == 'rejected') {
            $this->db->where('id', $this->input->post('hidden_id'));
            $this->db->update('tbl_deklarasi', ['status' => 'rejected']);
        } elseif ($this->input->post('app2_status') == 'revised') {
            $this->db->where('id', $this->input->post('hidden_id'));
            $this->db->update('tbl_deklarasi', ['status' => 'revised']);
        } elseif ($this->input->post('app2_status') == 'approved') {
            $this->db->where('id', $this->input->post('hidden_id'));
            $this->db->update('tbl_deklarasi', ['status' => 'approved']);
        }
        echo json_encode(array("status" => TRUE));
    }

    // PRINTOUT FPDF
    public function generate_pdf($id)
    {
        // Load FPDF library
        $this->load->library('fpdf');

        // Load data from database based on $id
        $data['master'] = $this->M_datadeklarasi->get_by_id($id);
        $data['user'] = $this->db->select('name')
            ->from('tbl_data_user')
            ->where('id_user', $data['master']->id_pengaju)
            ->get()
            ->row('name');
        $data['app_status'] = strtoupper($data['master']->app_status);
        $data['app2_status'] = strtoupper($data['master']->app2_status);

        // Start FPDF
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->SetTitle('Form Deklarasi');
        $pdf->AddPage();

        // Set font for title
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'PT. MANDIRI CIPTA SEJAHTERA', 0, 1, 'C');

        // Title of the form
        $pdf->Ln(8);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'FORM DEKLARASI', 0, 1, 'C');
        $pdf->Ln(5);

        // Set font for form data
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 10, 'Tanggal:', 0, 0);
        $pdf->Cell(60, 10, $data['master']->tgl_deklarasi, 0, 1);
        $pdf->Cell(40, 10, 'Nama:', 0, 0);
        $pdf->Cell(60, 10, $data['user'], 0, 1);
        $pdf->Cell(40, 10, 'Jabatan:', 0, 0);
        $pdf->Cell(60, 10, $data['master']->jabatan, 0, 1);

        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(60, 10, 'Telah/akan melakukan pembayaran kepada:', 0, 1);

        // Set font for form data
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 10, 'Nama:', 0, 0);
        $pdf->Cell(60, 10, $data['master']->nama_dibayar, 0, 1);
        $pdf->Cell(40, 10, 'Tujuan:', 0, 0);
        $pdf->Cell(60, 10, $data['master']->tujuan, 0, 1);
        $pdf->Cell(40, 10, 'Sebesar:', 0, 0);
        $pdf->Cell(60, 10, $data['master']->sebesar, 0, 1);

        // Add Signature Section
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(0, 123, 255); // Background color for headers
        $pdf->SetTextColor(255, 255, 255); // White text color
        $pdf->Cell(60, 10, 'Yang melakukan', 1, 0, 'C', true);
        $pdf->Cell(60, 10, 'Mengetahui', 1, 0, 'C', true);
        $pdf->Cell(60, 10, 'Menyetujui', 1, 1, 'C', true);

        // Empty cells for signatures
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0); // Reset text color
        $pdf->Cell(60, 20, '', 1, 0, 'C');
        $pdf->Cell(60, 20, $data['app_status'], 1, 0, 'C');
        $pdf->Cell(60, 20, $data['app2_status'], 1, 1, 'C');

        // Empty cells for signatures
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(0, 0, 0); // Reset text color
        $pdf->Cell(60, 8, $data['user'], 1, 0, 'C');
        $pdf->Cell(60, 8, $data['master']->app_name, 1, 0, 'C');
        $pdf->Cell(60, 8, $data['master']->app2_name, 1, 1, 'C');

        // Add keterangan
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 10, 'Keterangan:', 0, 0);
        $pdf->Ln(8);
        if ($data['master']->app_keterangan != null) {
            $pdf->Cell(60, 10, '*' . $data['master']->app_keterangan, 0, 1);
        } elseif ($data['master']->app2_keterangan != null) {
            $pdf->Cell(60, 10, '*' . $data['master']->app2_keterangan, 0, 1);
        }

        // Output the PDF
        $pdf->Output('I', 'Prepayment.pdf');
    }
}
