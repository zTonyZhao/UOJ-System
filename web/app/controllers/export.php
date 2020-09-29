<?php
	global $myUser;
	$filename = UOJContext::storagePath()."/tmp/{$myUser['username']}_".date('Y-m-d').".zip";
	$exportzip = new ZipArchive();
	$exportzip->open($filename,ZipArchive::CREATE);

	$csvf = fopen('php://memory','r+');
	$detailTitle = array('id', 'problem_id','contest_id', 'submit_time', 'score');
	fputcsv($csvf, $detailTitle);
	$submissions = DB::selectAll("select id, problem_id, contest_id, submit_time, content, score from submissions where submitter = '{$myUser['username']}'");
	foreach($submissions as $submission){
		$exportzip->addFile(UOJContext::storagePath().json_decode($submission['content'],true)['file_name'],$submission['id'].".zip");
		unset($submission['content']);
		fputcsv($csvf, $submission);
	}
	rewind($csvf);
	$exportzip->addFromString('submissionDetail.csv',stream_get_contents($csvf));

	$blogs = DB::selectAll("select title, content_md from blogs where poster = '{$myUser['username']}'");
	foreach($blogs as $blog){
		$exportzip->addFromString('blogs/'.$blog['title'].'.md',$blog['content_md']);
	}

	$exportUser = $myUser;
	unset($exportUser['password']);
	unset($exportUser['svn_password']);
	unset($exportUser['remote_addr']);
	unset($exportUser['http_x_forwarded_for']);
	unset($exportUser['remember_token']);
	$exportzip->addFromString('user_info.json',json_encode($exportUser));

	$exportzip->close();

	$fileHandle = fopen($filename, "rb");
	header("Content-type:application/octet-stream; charset=utf-8");
	header("Content-Transfer-Encodingf: binary");
	header("Accept-Ranges: bytes");
	header("Content-Length: ".filesize($filename));
	header('Content-Disposition:attatchment;filename="'.urlencode("{$myUser['username']}_".date('Y-m-d').".zip").'"');
	while(!feof($fileHandle)){
		echo fread($fileHandle, 10240);
	}
	fclose($fileHandle);
	unlink($filename);
?>
