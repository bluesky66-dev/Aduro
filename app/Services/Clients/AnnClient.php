<?php

namespace App\Services\Clients;

use App\Services\Contracts\MangaInterface;
use SimpleXMLElement;

class AnnClient extends Client implements MangaInterface
{
    protected $apiUrl = 'cdn.animenewsnetwork.com/encyclopedia/api.xml';
    protected $apiSecure = false;

    public function __construct()
    {
        parent::__construct($this->apiUrl);
    }

    public function find($key)
    {
        // TODO: Implement find() method.
    }

    public function manga($id)
    {
        $url = $this->apiUrl . '?manga=' . $id;
        $data = $this->request($url);
        $xml = new SimpleXMLElement($data);
        $mangas = $xml->manga;
        $staffs = [];
        foreach ($mangas->staff as $staff) {
            $staffs[] = [
                'task'  => $staff->task,
                'staff' => $staff->person,
            ];
        }

        return $staffs;
    }

    public function authors($id)
    {
        // TODO: Implement authors() method.
    }

    public function characters($id)
    {
        // TODO: Implement characters() method.
    }
}
