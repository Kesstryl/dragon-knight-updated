<?php // config.php :: Low-level app/database variables.

$dbsettings = Array(
        "server"        => "localhost",     // MySQL server name. (Default: localhost)
        "user"          => "root",              // MySQL username.
        "pass"          => "",              // MySQL password.
        "name"          => "dragonknight",              // MySQL database name.
        "prefix"        => "dk",            // Prefix for table names. (Default: dk)
		"safeserver"	=> "localhost",   //use url your actual server is on such as www.yoursitename.com, leave off http:// if using a localhost, some localhosts might use just "localhost" or "www.localhost"
);

?>