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
        $data['titleview'] = "Deklarasi";
        $name = $this->db->select('name')
            ->from('tbl_data_user')
            ->where('id_user', $this->session->userdata('id_user'))
            ->get()
            ->row('name');
        $data['approval'] = $this->db->select('COUNT(*) as total_approval')
            ->from('tbl_deklarasi')
            ->where('app_name', $name)
            ->or_where('app2_name', $name)
            ->get()
            ->row('total_approval');
        $this->load->view('backend/home', $data);
    }

    function get_list()
    {
        // INISIAI VARIABLE YANG DIBUTUHKAN
        $fullname = $this->db->select('name')
            ->from('tbl_data_user')
            ->where('id_user', $this->session->userdata('id_user'))
            ->get()
            ->row('name');
        $list = $this->M_datadeklarasi->get_datatables();
        $data = array();
        $no = $_POST['start'];

        //LOOPING DATATABLES
        foreach ($list as $field) {

            // MENENTUKAN ACTION APA YANG AKAN DITAMPILKAN DI LIST DATA TABLES
            if ($field->app_name == $fullname) {
                $action = '<a href="datadeklarasi/read_form/' . $field->id . '" class="btn btn-info btn-circle btn-sm" title="Read"><i class="fa fa-eye"></i></a>
                                <a class="btn btn-success btn-circle btn-sm" href="datadeklarasi/generate_pdf/' . $field->id . '"><i class="fas fa-file-pdf"></i></a>';
            } elseif ($field->app2_name == $fullname) {
                $action = '<a href="datadeklarasi/read_form/' . $field->id . '" class="btn btn-info btn-circle btn-sm" title="Read"><i class="fa fa-eye"></i></a>     
                                <a class="btn btn-success btn-circle btn-sm" href="datadeklarasi/generate_pdf/' . $field->id . '"><i class="fas fa-file-pdf"></i></a>';
            } elseif (in_array($field->status, ['rejected', 'approved'])) {
                $action = '<a href="datadeklarasi/read_form/' . $field->id . '" class="btn btn-info btn-circle btn-sm" title="Read"><i class="fa fa-eye"></i></a>
                <a class="btn btn-success btn-circle btn-sm" href="datadeklarasi/generate_pdf/' . $field->id . '"><i class="fas fa-file-pdf"></i></a>';
            } elseif ($field->app_status == 'approved') {
                $action = '<a href="datadeklarasi/read_form/' . $field->id . '" class="btn btn-info btn-circle btn-sm" title="Read"><i class="fa fa-eye"></i></a>
                            <a class="btn btn-success btn-circle btn-sm" href="datadeklarasi/generate_pdf/' . $field->id . '"><i class="fas fa-file-pdf"></i></a>';
            } else {
                if ($field->app_status == 'revised' || $field->app2_status == 'revised') {
                    $action = '<a href="datadeklarasi/read_form/' . $field->id . '" class="btn btn-info btn-circle btn-sm" title="Read"><i class="fa fa-eye"></i></a>
                        <a href="datadeklarasi/edit_form/' . $field->id . '" class="btn btn-warning btn-circle btn-sm" title="Edit"><i class="fa fa-edit"></i></a>
                        <a class="btn btn-success btn-circle btn-sm" href="datadeklarasi/generate_pdf/' . $field->id . '"><i class="fas fa-file-pdf"></i></a>';
                } else {
                    $action = '<a href="datadeklarasi/read_form/' . $field->id . '" class="btn btn-info btn-circle btn-sm" title="Read"><i class="fa fa-eye"></i></a>
                        <a href="datadeklarasi/edit_form/' . $field->id . '" class="btn btn-warning btn-circle btn-sm" title="Edit"><i class="fa fa-edit"></i></a>
			            <a onclick="delete_data(' . "'" . $field->id . "'" . ')" class="btn btn-danger btn-circle btn-sm" title="Delete"><i class="fa fa-trash"></i></a>
                        <a class="btn btn-success btn-circle btn-sm" href="datadeklarasi/generate_pdf/' . $field->id . '"><i class="fas fa-file-pdf"></i></a>';
                }
            }

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
            $row[] = 'Rp. ' . number_format($field->sebesar, 0, ',', '.');;
            // $row[] = $field->sebesar;
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
        $data['title'] = 'backend/datadeklarasi/deklarasi_read';
        $this->load->view('backend/home', $data);
    }

    function add_form()
    {
        $data['id'] = 0;
        $data['title_view'] = "Deklarasi Form";
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
        $data = 'd' . $year . $month . $urutan;
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
        $kode_deklarasi = 'D' . $year . $month . $urutan;

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

        // BILA YANG MEMBUAT PREPAYMENT DAPAT MENGAPPROVE SENDIRI
        if ($approval->app_id == $this->session->userdata('id_user')) {
            $data['app_status'] = 'approved';
            $data['app_date'] = date('Y-m-d H:i:s');
        }

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
            'app_status' => 'waiting',
            'app_date' => null,
            'app_keterangan' => null,
            'app2_status' => 'waiting',
            'app2_date' => null,
            'app2_keterangan' => null,
            'status' => 'on-process'
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
            'app_keterangan' => $this->input->post('app_keterangan'),
            'app_status' => $this->input->post('app_status'),
            'app_date' => date('Y-m-d H:i:s'),
        );

        // UPDATE STATUS DEKLARASI
        if ($this->input->post('app_status') === 'revised') {
            $data['status'] = 'revised';
        } elseif ($this->input->post('app_status') === 'approved') {
            $data['status'] = 'on-process';
        } elseif ($this->input->post('app_status') === 'rejected') {
            $data['status'] = 'rejected';
        }

        //UPDATE APPROVAL PERTAMA
        $this->db->where('id', $this->input->post('hidden_id'));
        $this->db->update('tbl_deklarasi', $data);

        echo json_encode(array("status" => TRUE));
    }

    function approve2()
    {
        $data = array(
            'app2_keterangan' => $this->input->post('app2_keterangan'),
            'app2_status' => $this->input->post('app2_status'),
            'app2_date' => date('Y-m-d H:i:s'),
        );

        // UPDATE STATUS DEKLARASI
        if ($this->input->post('app2_status') === 'revised') {
            $data['status'] = 'revised';
        } elseif ($this->input->post('app2_status') === 'approved') {
            $data['status'] = 'approved';
        } elseif ($this->input->post('app2_status') === 'rejected') {
            $data['status'] = 'rejected';
        }

        // UPDATE APPROVAL 2
        $this->db->where('id', $this->input->post('hidden_id'));
        $this->db->update('tbl_deklarasi', $data);

        echo json_encode(array("status" => TRUE));
    }

    function formatIndonesianDate($date)
    {
        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $date = new DateTime($date);
        $day = $date->format('d');
        $month = $bulan[(int)$date->format('m')];
        $year = $date->format('Y');

        return "$day $month $year";
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

        // Format tgl_prepayment to Indonesian date
        $formattedDate = $this->formatIndonesianDate($data['master']->tgl_deklarasi);
        $created_at = $this->formatIndonesianDate($data['master']->created_at);
        $app_date = $this->formatIndonesianDate($data['master']->app_date);
        $app2_date = $this->formatIndonesianDate($data['master']->app2_date);

        // Start FPDF
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->SetTitle('Form Deklarasi');
        $pdf->AddPage('P', 'Letter');

        // Logo
        $pdf->Image(base_url('') . '/assets/backend/img/reimbust/kwitansi/default.jpg', 14, -3, 46, 46);

        // Set font for title
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 25, 'PT. MANDIRI CIPTA SEJAHTERA', 0, 1, 'C');

        // Title of the form
        $pdf->Ln(7);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'FORM DEKLARASI', 0, 1, 'C');
        $pdf->Ln(5);

        // Set font for form data
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 10, 'Tanggal:', 0, 0);
        $pdf->Cell(60, 10, $formattedDate, 0, 1);
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
        $pdf->Cell(60, 10, number_format($data['master']->sebesar, 0, ',', '.'), 0, 1);

        //APPROVAL
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 8.5, 'YANG MELAKUKAN', 1, 0, 'C');
        $pdf->Cell(50, 8.5, 'MENGETAHUI', 1, 0, 'C');
        $pdf->Cell(50, 8.5, 'MENYETUJUI', 1, 1, 'C');

        $pdf->Cell(50, 18, 'CREATED', 1, 0, 'C');

        // Menyimpan posisi saat ini
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // Mengatur posisi X dan Y dengan margin tambahan untuk teks tanggal
        $pdf->SetXY($x + -50, $y + 5); // Menambahkan margin horizontal dan vertikal

        // Menggunakan Cell() untuk mencetak teks tanggal dengan margin
        $pdf->Cell(50, 18, $created_at, 0, 0, 'C');

        // Kembali ke posisi sebelumnya untuk elemen berikutnya
        $pdf->SetXY($x + 0, $y); // Mengatur posisi untuk elemen berikutnya jika diperlukan

        // Approval 1
        $pdf->Cell(50, 18, strtoupper($data['master']->app_status), 1, 0, 'C');

        // Menyimpan posisi saat ini
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // Mengatur posisi X dan Y dengan margin tambahan untuk teks tanggal
        $pdf->SetXY($x + -50, $y + 5); // Menambahkan margin horizontal dan vertikal

        if ($data['master']->app_date == null) {
            $date = '';
        }
        if ($data['master']->app_date != null) {
            $date = $app_date;
        }

        // Menggunakan Cell() untuk mencetak teks tanggal dengan margin
        $pdf->Cell(50, 18, $date, 0, 0, 'C');

        // Kembali ke posisi sebelumnya untuk elemen berikutnya
        $pdf->SetXY($x + 0, $y); // Mengatur posisi untuk elemen berikutnya jika diperlukan

        // Approval 2
        $pdf->Cell(50, 18, strtoupper($data['master']->app2_status), 1, 0, 'C');

        // Menyimpan posisi saat ini
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // Mengatur posisi X dan Y dengan margin tambahan untuk teks tanggal
        $pdf->SetXY($x + -50, $y + 5); // Menambahkan margin horizontal dan vertikal

        if ($data['master']->app2_date == null) {
            $date2 = '';
        }
        if ($data['master']->app2_date != null) {
            $date2 = $app2_date;
        }

        // Menggunakan Cell() untuk mencetak teks tanggal dengan margin
        $pdf->Cell(50, 18, $date2, 0, 0, 'C');

        // Kembali ke posisi sebelumnya untuk elemen berikutnya
        $pdf->SetXY($x + -150, $y + 18); // Mengatur posisi untuk elemen berikutnya jika diperlukan

        // Menulis elemen selanjutnya dengan ukuran baris yang lebih kecil
        $pdf->Cell(50, 8.5, $data['user'], 1, 0, 'C');
        $pdf->Cell(50, 8.5, $data['master']->app_name, 1, 0, 'C');
        $pdf->Cell(50, 8.5, $data['master']->app2_name, 1, 1, 'C');


        // Add keterangan
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        if (($data['master']->app_keterangan != null && $data['master']->app_keterangan != '') || ($data['master']->app2_keterangan != null && $data['master']->app2_keterangan != '')) {
            $pdf->Cell(40, 10, 'Keterangan:', 0, 0);
        }
        $pdf->Ln(8);
        if ($data['master']->app_keterangan != null && $data['master']->app_keterangan != '') {
            $pdf->Cell(60, 10, '*' . $data['master']->app_keterangan . '(' . $data['master']->app_name . ')', 0, 1);
        }
        if ($data['master']->app2_keterangan != null && $data['master']->app2_keterangan != '') {
            $pdf->Cell(60, 10, '*' . $data['master']->app2_keterangan . '(' . $data['master']->app2_name . ')', 0, 1);
        }

        // Output the PDF
        $pdf->Output('I', 'Deklarasi.pdf');
    }
}
