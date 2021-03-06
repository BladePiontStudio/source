<?php

class FlexiHash
{
    private $serverList = array();
    private $isSorted = false;

    public function mHash($key)
    {
        $md5  = substr(md5($key), 0, 8);
        $seed = 31;
        $hash = 0;
        for ($i = 0; $i < 8; $i++) {
            $hash = $hash * $seed + ord($md5{$i});
            $i++;
        }
        return $hash & 0x7FFFFFFF;
    }

    public function addServer($server)
    {
        $hash = $this->mHash($server);
        if (!isset($this->serverList[$hash])) {
            $this->serverList[$hash] = $hash;
        }
        $this->isSorted = true;
        return true;
    }

    public function removeServer($server)
    {
        $hash = $this->mHash($server);
        if (isset($this->serverList[$hash])) {
            unset($this->serverList[$hash]);
        }
        $this->isSorted = true;
        return true;
    }

    public function lookup($key)
    {
        $hash = $this->mHash($key);
        if (!$this->isSorted) {
            krsort($this->serverList, SORT_NUMERIC);
            $this->isSorted = true;
        }
        foreach ($this->serverList as $pos => $server) {
            if ($hash >= $pos) {
                return $server;
            }
        }
        return $this->serverList[count($this->serverList) - 1];
    }
}