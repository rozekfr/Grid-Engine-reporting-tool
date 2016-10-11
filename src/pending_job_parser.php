<?php
    include 'pripojeniDB.php';

	$dotazParsovat = $db->query("SELECT IF(update_time < DATE_SUB(now(),INTERVAL 1 MINUTE),'ano','ne') AS parsovat FROM information_schema.tables WHERE TABLE_NAME = 'cekajici_ulohy'");
	$parsovat = $dotazParsovat->fetch_assoc();
	$parsovat = $parsovat["parsovat"];
	if($parsovat == "ano"){
		//parser
		$handle = fopen("pending_jobs","r");
		$pj = fread($handle, filesize("pending_jobs"));
		$pj = preg_split("/.*---.*/",$pj);

		$jobs = explode("\n",$pj[1]);

		$pending_jobs = array();
		foreach($jobs as $job){
			if(!empty($job)) {
				$stat = explode(" ", $job);
				$job_stat = array();
				$key = $stat[0];
				foreach ($stat as $data) {
					if (!empty($data)) {
						array_push($job_stat, $data);
					}
				}
				$pending_jobs[$key] = $job_stat;
			}
		}
		//konec parseru

		//vymazání dat
		$db->query("TRUNCATE TABLE cekajici_ulohy");

		//úprava dat a vložení do databáze
		foreach($pending_jobs as $job) {
			//datum a čas
			$date = explode("/", $job[5]);
			$datetime = "$date[2]-$date[0]-$date[1] $job[6]";
			//pocet tasku
			$tasks = 1;
			if (isset($job[7])) {
				$tasks = 0;
				$t = explode(",", $job[7]);
				foreach ($t as $task) {
					$tmp = explode(":", $task);
					$rozsah = $tmp[0];
					$krok = isset($tmp[1]) ? intval($tmp[1]) : 1;
					if (strpos($rozsah, "-") !== false) {
						$tmp = explode("-", $rozsah);
						$start = intval($tmp[0]);
						$end = intval($tmp[1]);
						$tasks += ($end - $start) / $krok + 1;
					} else {
						$tasks++;
					}
				}
			}


			//vložení dat
			$db->query("INSERT INTO `cekajici_ulohy`(`id_ulohy`, `uzivatel`, `cas_odeslani`, `stav`, `pocet_tasku`) VALUES ($job[0],'$job[3]','$datetime','$job[4]',$tasks)");
		}
    }
?>