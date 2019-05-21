<?php
/*
* Color themes.
* Please set your favorite colors here.
* The "default" color is what you will see normally, while the "notice" color will show up for a few seconds when a log occours.
* http://materializecss.com/color.html#palette
*/
$colors = [
    "default" => "indigo",
    "notice" => "red"
];

/*
* Display values.
*/
$pagelength = 10;       // How many entries will show up in a log page.

/**
 * others
 */
// interval between ajax requests, in milliseconds
$interval = 30 * 1000;
// access the site with this parameter for security and privacy
$token = 'mytoken';
// ip scope which are allowed to access this site
$ipwhitelists = ['127.0.0.1', '192.168.99.1', '123.56.24.0/22'];
// load the static files(js/css/fonts) from local, not cdn
$local_static = 1;
