<?php
class Nada_User_Controller
{
    private Nada_User_Service $userService;

    private Nada_Study_Repository $studyRepository;

    public function __construct(Nada_User_Service  $userService, Nada_Study_Repository $studyRepository)
    {
        $this->userService = $userService;
        $this->studyRepository = $studyRepository;
    }

    public function register_hooks(): void
    {
        add_action('nada_user_registered', [$this, 'create_nada_user'], 10, 1);
    }

    /** Crée un utilisateur NADA lors de l'inscription WP */
    public function create_nada_user($user_id): void
    {
        // Récupérer les informations WP
        $user_info = get_userdata($user_id);
        $email     = $user_info->user_email;
        $username  = $user_info->user_login;
        $first_name = get_user_meta($user_id, 'first_name', true) ?: $username;
        $last_name  = get_user_meta($user_id, 'last_name', true)  ?: $username;

        // Limiter à 18 caractères
        $username   = substr($username, 0, 18);
        $first_name = substr($first_name, 0, 18);
        $last_name  = substr($last_name, 0, 18);

        // Préparer les données utilisateur
        $data = [
            'email'            => $email,
            'username'         => $username,
            'first_name'       => $first_name,
            'last_name'        => $last_name,
            'password'         =>  get_default_password_nada(),
            'password_confirm' => get_default_password_nada(),
            'company'          => "",
            'phone'            => null,
            'country'          => null,
            'active'           => true,
            'role_id'          => 2
        ];

        nada_id_log("data :" . print_r($data));

        // Créer l'utilisateur NADA
        $response = $this->userService->create_user($data);

        nada_id_log("response :" . print_r($response));
        // Stocker le token NADA en meta utilisateur WP
        if ($response['success'] && !empty($response['data']['user']['api_keys'][0])) {
            update_user_meta($user_id, 'nada_token', sanitize_text_field($response['data']['user']['api_keys'][0]));
            $this->studyRepository->assign_nada_studies_to_pi($user_id, $email);
            return;
        }
    }
}
