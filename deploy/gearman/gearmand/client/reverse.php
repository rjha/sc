<?php

 echo "Starting\n";

 # Create our client object.
 $gmclient= new GearmanClient();

 # Add default server (localhost).
 $gmclient->addServer();

 echo "Sending job\n";
 $result = $gmclient->doNormal("reverse", "TimTim is now Diptansu!");
 echo "Success: $result\n";

?>
