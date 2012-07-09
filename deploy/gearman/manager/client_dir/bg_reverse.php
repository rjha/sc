<?php
/**
 * Run the reverse function.
 *
 * @link http://de2.php.net/manual/en/gearman.examples-reverse.php
 */
$gmclient= new GearmanClient();

# Add default server (localhost).
$gmclient->addServer();

$function = "reverse_string";
$data     = 'Hello!';


$job_handle = $gmclient->doBackground($function,$data);

if ($gmclient->returnCode() != GEARMAN_SUCCESS)
{
  echo "bad return code\n";
  exit;
}

echo "done!\n";

?>
