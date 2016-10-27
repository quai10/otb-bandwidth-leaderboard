<?php

namespace OtbBandwidthLeaderboard;

use GuzzleHttp\Client as GuzzleClient;

class OtbApi
{
    private $baseUrl;
    private $user;
    private $password;
    private $client;

    public function __construct($baseUrl, $user, $password)
    {
        $this->baseUrl = $baseUrl;
        $this->user = $user;
        $this->password = $password;
        $this->client = new GuzzleClient(['cookies' => true]);
    }

    private function sortHostsByUpload($a, $b)
    {
        if ($a->upload == $b->upload) {
            return 0;
        }

        return ($a->upload > $b->upload) ? -1 : 1;
    }

    public function getTrafficData()
    {
        $response = $this->client->request(
            'POST',
            $this->baseUrl.'/cgi-bin/luci/admin/overthebox/lan_traffic_data',
            [
                'verify'      => false,
                'form_params' => [
                    'luci_username' => $this->user,
                    'luci_password' => $this->password,
                ],
            ]
        );
        $data = json_decode($response->getBody()->getContents());
        $hosts = [];
        foreach ($data as $host) {
            if ($host->hostname == '*') {
                $host->hostname = null;
            }
            $hosts[] = new Host($host->ip, $host->hostname, $host->rx, $host->tx);
        }
        usort($hosts, [$this, 'sortHostsByUpload']);

        return $hosts;
    }
}
