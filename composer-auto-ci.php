<?php

declare(strict_types=1);

$output = null;
$retval = null;
exec('composer run-script auto-ci', $output, $retval);
if ($retval < 0) {
    print($output);
    exit(1);
}

exit(0);
