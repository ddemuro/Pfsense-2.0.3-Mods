<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
include('Net/SSH2.php');
clearstatcache();
$usr = $_GET['usr'];
$date = date("Y-m-d H:i:s");
$dynUpdateLog = "dynUpdateLog.txt";
$ip = $_GET['myip'];
$pass = $_GET['pass'];
//Normally a simple user and password to passs through GET req from PfSENSE (Don't allow it public access)...
$validUser = 'Username to validate IP from';
$validPass = 'Password to validate IP from';
//As we need to elevate we enter the same machine through SSH for that we use seclib
$validSSHUsr = 'System user to call another script to update DNS zone';
$validSSHPass = 'System users password to log into SSH';
//The IP of the elevation server, normally should be loopback
$validSSHDomain = '127.0.0.1';
//Example Call
//http://doamin/pf/update.php?myip=10.9.10.0&usr=username&pass=password
if(strcmp($usr,$validUser) == 0 && strcmp($pass,$validPass) == 0){
        $ssh = new Net_SSH2($validSSHDomain);
        if (!$ssh->login($validSSHUsr, $validSSHPass)) {
           echo('LOGIN FAILED.');
           exec('echo Error logging in SSH /var/www/dyn/update.php > /tmp/errorDynUpdate.txt');
        }
		//Script callback... as we need to update a list of DNS
        $ssh->exec("/opt/scripts/dynDNSextended.sh $ip > /tmp/$dynUpdateLog");
	$ssh->exec("echo Seems $ip has been updated to DYNDNS: $date >> /tmp/$dynUpdateLog");
        echo "Updated 1 host(s) to $ip";
}else{
        echo("ERROR: Address $ip has not changed..");
}
die();
?>
