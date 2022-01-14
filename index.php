<?php
include_once('./class/Proxmox.php');

$proxmox = new Proxmox;
$proxmox->setPM_IP($_GET['PM_IP']);
$proxmox->setPM_PORT($_GET['PM_PORT']);
$proxmox->setPM_USER($_GET['PM_USER']);
$proxmox->setPM_PASS($_GET['PM_PASS']);
$proxmox->PM_URL = $_GET['PM_URL'];

// login => requisicao => logout
$proxmox->login();
echo ($proxmox->reqPrxmox());
$proxmox->logout();
