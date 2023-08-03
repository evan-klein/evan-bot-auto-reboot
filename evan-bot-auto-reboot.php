<?php

try{
	require('ek.php');

	$args = \ek\getCommandLineArgs();

	// Get the server's hostname
	$hostname = gethostname();

	// Get the number of CPUs the server has
	$cpus = \ek\getNumCPUs();
	if($cpus<1) throw new Exception('\ek\getNumCPUs() returned a value less than 1', 500);

	// Get the server's CPU load averages
	$load_avgs = sys_getloadavg();

	// Determine if the 5 minute load average is too high
	switch(true){
		case $cpus<=4:
			$multiplier = 4;
			break;
		case $cpus<=16:
			$multiplier = 3;
			break;
		default:
			$multiplier = 2;
	}
	$load_avg_is_high = $load_avgs[1] > ($multiplier * $cpus);

	$meminfo = \ek\getMemInfo();
	$oom = ($meminfo['mem_available_ratio']<0.05 && $meminfo['mem_available']<64) || $meminfo['swap_free']==0;

	// If the 5 minute load average is too high...
	if($load_avg_is_high){
		// If an email address is passed to this script as a command-line argument...
		if( isset($args['email_to']) ){
			// Format an email notification
			$body = <<<HEREDOC
Hello,
<br><br>
$hostname is being rebooted because of a high load average.
<br><br>
Details:
<br><br>
1 minute load average: {$load_avgs[0]}<br>
5 minute load average: {$load_avgs[1]}<br>
15 minute load average: {$load_avgs[2]}<br>
Number of processors: $cpus
<br><br>
-🤖Evan Bot
HEREDOC;

			// Send the email notification
			$headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n";
			mail(
				$args['email_to'],
				"[URGENT] $hostname is being rebooted",
				$body,
				$headers
			);

			// Wait 15 seconds to give the mail transfer agent a moment to send the email
			sleep(15);
		}

		// Tell the system to reboot
		exit('sudo /sbin/shutdown -r now');
	}
	// Otherwise, exit
	else exit('exit');
}
catch(Exception $e){
	error_log($e->getMessage());
	exit(1);
}

?>