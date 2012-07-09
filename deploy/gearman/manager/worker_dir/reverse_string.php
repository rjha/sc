<?php

class Net_Gearman_Job_reverse_string extends Net_Gearman_Job_Common {

    public function run($workload) {

        $result = strrev($workload);
        //waste time
        sleep(15);

        GearmanPearManager::$LOG[] = "Success";

        return $result;

    }

}

?>
