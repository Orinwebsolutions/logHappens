<?php
include("config.php");

/**
 * Security check
 */

$ip = $_SERVER['REMOTE_ADDR'];
if ($ipwhitelists && !is_ip_in($ip, $ipwhitelists)) {
	http_response_code(403);
	echo 'ip banned: ' . $ip;
	exit;
}

$get_token = isset($_GET['token']) ? htmlspecialchars(trim($_GET['token'])) : '';
if ($get_token != $token) {
    http_response_code(403);
    echo 'token invalid';
    // header('Location: https://www.google.com', true, 301);
    exit;
}

/**
 * Checks if a given IP address matches the specified CIDR subnet/s
 *
 * @param string $ip The IP address to check
 * @param mixed $cidrs The IP subnet (string) or subnets (array) in CIDR notation
 * @param string $match optional If provided, will contain the first matched IP subnet
 * @return boolean TRUE if the IP matches a given subnet or FALSE if it does not
 */

function is_ip_in($ip, $cidrs, &$match = null) {
	foreach((array) $cidrs as $cidr) {
		if (strpos($cidr, '/') == false) {
			$cidr .= '/32';
		}
		list($subnet, $mask) = explode('/', $cidr);
		if(((ip2long($ip) & ($mask = ~ ((1 << (32 - $mask)) - 1))) == (ip2long($subnet) & $mask))) {
			$match = $cidr;
			return true;
		}
	}
	return false;
}

/*
* Initial configurations
*/
header("Content-type: text/html;charset=utf-8");
session_start();

if (!isset($_SESSION["pagelength"])) {
    $_SESSION["pagelength"] = $pagelength;
}

/*
* Misc functions
*/
function redirect($destination)
{
    header("Refresh:0; url=" . $destination);
}

/*
* Data sanitization
*/
function checkExist($name)
{
    return filter_input(INPUT_GET, $name, FILTER_DEFAULT);
}

function filterString($name)
{
    return filter_input(INPUT_GET, $name, FILTER_SANITIZE_STRING);
}

function filterInt($name)
{
    return filter_input(INPUT_GET, $name, FILTER_SANITIZE_NUMBER_INT);
}
