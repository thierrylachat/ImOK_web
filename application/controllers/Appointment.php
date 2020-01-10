<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Appointment extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model(['Appointment_model', 'Appointment_types_model', 'Employee_model', 'Customer_model']);
        $this->load->helper(['url', 'form', 'date']);
        $this->load->library(['form_validation', 'pagination']);
    }

    // Méthode gérant la page d'accueil
    public function index() {
        // On passera dans le tableau data toutes les informations utiles pour la vue
        // Le titre de la page
        $data['title'] = "Liste des rendez-vous";
        $data['appointments'] = $this->Appointment_model->getAppointments();
        // Chargement des différentes vue, avec envoi du tableau data
        $this->load->view('common/_header', $data);
        $this->load->view('appointment/index', $data);
        $this->load->view('common/_footer', $data);
    }

    // Gère la page de création d'un credit
    public function create() {
        // Titre de la page
        $data['title'] = "Ajouter un Rendez-vous";
        // Récupération du crédit
        $data['appointments'] = $this->Appointment_model->getAppointments();
        $data['appointment_types'] = $this->Appointment_types_model->getAll();
        $data['customers'] = $this->Customer_model->getCustomers();
        $data['employees'] = $this->Employee_model->getAll();

    // Modification de l'affichage des erreurs
        $this->form_validation->set_error_delimiters('<small class="alert alert-danger p-1 ml-1 ">', '</small>');
    // S'il n'y a pas d'erreurs lors de l'application des règles de vérification
    // form_validation->run() renvoi TRUE si toutes les règles ont été appliquées sans erreurs
        // On appel la méthodes du model Users servant à la création d'un utilsilateur
        if ($this->form_validation->run() === TRUE) {

            $this->Appointment_model->createAppointments();
            redirect(base_url('appointment/create'));
        
        }
        // Puis on se redirige vers la page d'accueil
        // Chargement des différentes vues servant à la création d'un utilisateur
        $this->load->view('common/_header', $data);
        $this->load->view('appointment/create', $data);
        $this->load->view('common/_footer', $data);
    }

             // Méthode gérant la modification d'un client
             public function edit($id = 0) {
                // Titre de la page
                $data['title'] = "Modification du rendez-vous ";
                // Récupération des données
                $data['appointments'] = $this->Appointment_model->getAppointments();
                $data['appointment_types'] = $this->Appointment_types_model->getAll();
                $data['customers'] = $this->Customer_model->getCustomers();
                $data['employees'] = $this->Employee_model->getAll();
                // Si le formulaire de modification a été submit
                if ($_POST) {
                    // Modification de l'affichage des erreurs
                    $this->form_validation->set_error_delimiters('<small class="alert alert-danger p-1 ml-1 ">', '</small>');
                    // S'il n'y a pas eu d'erreurs lors de l'application des règles de sécurités
                    if ($this->form_validation->run() === TRUE) {
                        // On appel la méthodes du model Client afin de mettre à jour le client d'id = $id
                        $this->Appointment_model->updateAppointments($id);
                        // Puis on se redirige vers l'accueil
                        redirect(base_url());
                    }
                }
                // Récupération des informations du client choisi
                $data['appointment'] = $this->Appointment_model->getAppointmentById($id);
                // Si les informations sont vides, alors le client d'id = $id n'existe pas
                if (empty($data['appointment'])) {
                    show_404();
                } else {
                    // Affichage des vues correspondants à l'édition
                    $this->load->view('common/_header', $data);
                    $this->load->view('appointment/edit', $data);
                    $this->load->view('common/_footer', $data);
                }
            }

}
