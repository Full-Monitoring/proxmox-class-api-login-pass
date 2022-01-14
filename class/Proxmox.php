<?php

class Proxmox
{
    private $PM_IP;
    private $PM_PORT;
    private $PM_USER;
    private $PM_PASS;
    public $PM_URL;
    public $token;
    public $cookie;

    /**
     * set
     */

    public function  setPM_IP($v)
    {
        $this->PM_IP = $v;
        return $this;
    }

    public function  setPM_PORT($v)
    {
        $this->PM_PORT = $v;
        return $this;
    }

    public function  setPM_USER($v)
    {
        $this->PM_USER = $v;
        return $this;
    }

    public function  setPM_PASS($v)
    {
        $this->PM_PASS = $v;
        return $this;
    }

    public function  getPM_ADDRESS()
    {
        $this->PM_ADDRESS = "https://$this->PM_IP:$this->PM_PORT/api2/json/";
        return $this->PM_ADDRESS;
    }

    public function getPM_FIELDS()
    {
        return "username=$this->PM_USER&password=$this->PM_PASS";
    }

    public function login()
    {
        global $proxmox;
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $proxmox->getPM_ADDRESS() . "access/ticket",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ["Content-Type: application/x-www-form-urlencoded"],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $proxmox->getPM_FIELDS(),
        ]);
        $res = curl_exec($ch);
        curl_close($ch);
        $proxmox->token = json_decode($res)->data->CSRFPreventionToken;
        $proxmox->cookie = json_decode($res)->data->ticket;
    }

    public function logout()
    {
        /* ainda não encontrei essa opcao na documentacao do proxmox */
    }

    public function reqPrxmox()
    {
        global $proxmox;
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $proxmox->getPM_ADDRESS() . $proxmox->PM_URL,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ["CSRFPreventionToken=$this->token"],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_COOKIE => "PVEAuthCookie=$this->cookie"
        ]);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
}
