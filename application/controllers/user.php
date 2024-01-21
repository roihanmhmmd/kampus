<?php
defined('BASEPATH') or exit('No direct script access allowed');

class user extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->load->view('template/header');
        $this->load->view('auth/login');
        $this->load->view('template/footer');
    }
    public function register()
    {
        $this->load->view('template/header');
        $this->load->view('auth/register');
        $this->load->view('template/footer');
    }
    public function registration()
    {
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]');
        $this->form_validation->set_rules(
            'password',
            'Password',
            'required|trim|min_length[6]|matches[confirm password]',
            [
                'matches' => 'Password dont match', 'min_length' => 'Password to short'
            ]
        );
        $this->form_validation->set_rules('password', 'Password', 'required|trim|matches[password]');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Register';
            redirect('user/register');
        } else {
            $data = [
                'name' => htmlspecialchars($this->input->post('name', true)),
                'email' => htmlspecialchars($this->input->post('email', true)),
                'image' => 'user.jpg',
                'pass' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'id_role' => '2',
                'is_active' => '1',
                'datecreated' => time()

            ];
            $this->db->insert('user', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Your account has been created
          </div>');
            redirect('user');
        }
    }
    public function login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $user = $this->db->get_where('user', ['email' => $email])->row_array();

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $data = [
                    'id_user' => $user['id_user'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'id_role' => $user['id_role'],
                ];
                $this->session->set_userdata($data);
            }
            if ($user['id_role'] == 1) {
                redirect('siswa');
            } else {
                redirect('siswa');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
            Wrong Password
          </div>');
            redirect('user');
        }
    }
}
