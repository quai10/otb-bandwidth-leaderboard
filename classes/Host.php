<?php

namespace OtbBandwidthLeaderboard;

use Rych\ByteSize\ByteSize;

class Host
{
    private $bytesize;

    public function __construct($ip, $hostname, $download, $upload)
    {
        $this->ip = $ip;
        $this->hostname = $hostname;
        $this->download = $download;
        $this->upload = $upload;
        $this->bytesize = new ByteSize();
    }

    public function getReadableUpload()
    {
        return $this->bytesize->format($this->upload);
    }

    public function getReadableDownload()
    {
        return $this->bytesize->format($this->download);
    }
}
