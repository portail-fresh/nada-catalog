<?php

class Nada_Statistics_Service
{
    private Nada_API $api;
    public function __construct(
        Nada_Api $api,
    ) {
        $this->api = $api;
    }

    public function get_statistics_catalog_dashboard(string $lang): array
    {
        return $this->api->get_statistics_catalog_dashboard($lang);
    }
}
