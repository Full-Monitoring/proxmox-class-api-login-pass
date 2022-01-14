# Proxmox <-> PHP <-> API[username+password]

> Request to proxmox api with username and password. No need for api token.

## Request example using get method

### To get the version

```md
http://127.0.0.1/proxmox?PM_URL=version&PM_IP=172.33.255.2&PM_PORT=8006&PM_USER=roo@pam&PM_PASS=12345678

```

### To get cluster

``` txt
http://127.0.0.1/proxmox?PM_URL=cluster&PM_IP=172.33.255.2&PM_PORT=8006&PM_USER=roo@pam&PM_PASS=12345678
```

### To get cluster/resources

``` txt
http://127.0.0.1/proxmox?PM_URL=cluster/resources&PM_IP=172.33.255.2&PM_PORT=8006&PM_USER=roo@pam&PM_PASS=12345678
```

## Directories and files

```sh
.
├── class
│   └── Proxmox.php
└── index.php
```

## .index.php

```php
<?php
include_once('./class/Proxmox.php');

$proxmox = new Proxmox;
$proxmox->setPM_IP($_GET['PM_IP']);
$proxmox->setPM_PORT($_GET['PM_PORT']);
$proxmox->setPM_USER($_GET['PM_USER']);
$proxmox->setPM_PASS($_GET['PM_PASS']);
$proxmox->PM_URL = $_GET['PM_URL'];

// login => request => logout
$proxmox->login();
echo ($proxmox->reqPrxmox());
```

## .class/Proxmox.php

```php
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
```

## How to use?

> Access your web server and install git

```sh
apt install git
```
