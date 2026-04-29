<?php

/**
 * Summary of Nada_User_service
 * Un service a tout mthi
 */
class Nada_User_Service
{
    private Nada_API $api;
    public function __construct(
        Nada_Api $api,
    ) {
        $this->api = $api;
    }

    public function create_user(array $data): array
    {
        return $this->api->create_user($data);
    }
}
