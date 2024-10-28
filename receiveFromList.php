<?php

  $servers = [
    [
      'ftp_server' => 'ftpupload.net',
      'ftp_user' => 'if0_36394634',
      'ftp_pass' => '49DhSvcug8B',
      'remote_dir' => '/htdocs/resources/',
      'local_dir' => '/home/whitehotrobot/migrate/rec/',
    ],
  ];

  function rec(){
    global $remote_dir, $local_dir, $ftp, $list;
    forEach($list as $entry){
      echo "receiving: $entry\n";
      ftp_get($ftp, $local_dir.$entry, $remote_dir.$entry, FTP_BINARY);
    }
  }

  $list = ["20HsGI.jpeg", "20nqiL.mp4", "20zxpm.jpeg", "21HbvN.jpeg", "21L5Ih.jpeg", "21XqJB.mp4", "222YWI.mp4", "22t0vG.jpeg", "22wLWB.jpeg", "23NuX8.mp4", "23PQA0.jpeg", "23sJWG.jpeg", "23ubMe.gif", "23zrfK.mp4", "241era.mp4", "249BSU.mp4", "24t4is.mp4", "24wctL.mp4", "24Y7Og.jpeg", "25DJkH.jpeg", "25iY1s.jpeg", "25K5xO.jpeg", "25lgMA.mp4", "25V15p.jpeg", "25W7Lb.jpeg", "26aXcP.jpeg", "26fW0a.jpeg", "26XadZ.jpeg", "27I1tr.jpeg", "27oflO.jpeg", "27PPWp.jpeg", "28kjmA.jpeg", "28MxBl.jpeg", "28rccc.mp4", "28XO7E.webp", "29XPmS.jpeg", "2amgrM.mp4", "2aRBfb.jpeg", "2b3Fmk.mp4", "2bFrHM.jpeg", "2BHyeA.mp3", "2bN9XT.mp4", "2bVYT9.jpeg", "2bYmsQ.jpeg", "2c3q2O.jpeg", "2ce1th.jpeg", "2czluO.jpeg", "2d8I3F.jpeg", "2Dcxwv.jpeg", "2dHQlP.jpeg", "2dQ2HV.mp4", "2dQODG.mp4", "2dUKmy.mp4", "2dxwbo.mp4", "2eGhvw.png", "2eSQdc.mp4", "2eZVUa.mp4", "2ff7LR.gif", "2fjChf.mp4", "2fljy0.jpeg", "2fnw13.gif", "2fObQe.jpeg", "2g8u7J.jpeg", "2glXRH.mp4", "2GyWIb.jpeg", "2H7od0.jpeg", "2HalQZ.jpeg", "2hHeJW.mp4", "2hIYJL.mp4", "2hsLhL.jpeg", "2hSXwP.jpeg", "2hwhGG.jpeg", "2hYTn9.jpeg", "2hyzxB.png", "2hzZSv.jpeg", "2ifEZL.mp4", "2iFyGt.mp4", "2IJqDg.png", "2iMbr3.jpeg", "2iO1eZ.mp4", "2iplml.gif", "2JdMy7.jpeg", "2jhWnT.jpeg", "2jJ4b0.mp4", "2jluFg.mp4", "2jmFpq.jpeg", "2k8guz.mp4", "2ko34s.jpeg", "2kp6Qv.png", "2kuwBL.gif", "2kXKOa.jpeg", "2L9Fdj.jpeg", "2LUEjR.jpeg", "2M3oUC.jpeg", "2nGCIx.jpeg", "2NvFH4.jpeg", "2NwJgL.jpeg", "2PLPX7.jpeg", "2Q4Xjm.jpeg", "2q7Pj8.jpeg", "2R6Co2.jpeg", "2S8oTm.jpeg", "2sbUVe.jpeg", "2TNHPW.jpeg", "2UEkPt.jpeg", "2UhOS5.jpeg", "2untxq.jpeg", "2UONuZ.jpeg", "2UQCL8.gif", "2VeZsO.jpeg", "2Vmsvu.jpeg", "2vut7O.jpeg", "2WZ40U.jpeg", "2YEryN.mp4", "2YIefQ.jpeg", "2YY2EL.jpeg", "2zaoi8.jpeg", "2ZdnlW.mp4", "2ZqjDk.jpeg", "30ldqE.jpeg", "30XCbu.jpeg", "31rNmm.jpeg", "32q4Xu.jpeg", "35pcZE.jpeg", "368uFW.jpeg", "36QWa8.jpeg", "36SIu8.jpeg", "37iaV0.mp3", "38rMhd.jpeg", "398dJ7.jpeg", "39SggG.jpeg", "3dp9Mk.jpeg", "3fA4EM.jpeg", "3g15sX.jpeg", "3hLuCm.jpeg", "3iAln3.jpeg", "3icXAh.mp4", "3iwFvG.jpeg", "3k5udf.jpeg", "3kY9jG.jpeg", "3lyYtV.jpeg", "3mlILI.jpeg", "3mwvaV.jpeg", "3q3nIr.jpeg", "3qjnNr.jpeg", "3qkxpj.jpeg", "3sbPCi.jpeg", "3sQ5Cp.jpeg", "3uGdUJ.jpeg", "3uxNbH.jpeg", "3v0UuC.webm", "3vxXCE.jpeg"];



  forEach($servers as $server){
    $ftp_server = $server['ftp_server'];
    $ftp_user = $server['ftp_user'];
    $ftp_pass = $server['ftp_pass'];
    $local_dir = $server['local_dir'];
    $remote_dir = $server['remote_dir'];

    $ftp = ftp_connect($ftp_server) or die("Couldn't connect to $ftp_server"); 
    if (@ftp_login($ftp, $ftp_user, $ftp_pass)) {
      echo "Connected as $ftp_user@$ftp_server\n";
    } else {
      echo "Couldn't connect as $ftp_user\n";
    }
    ftp_pasv($ftp, true);
    rec();
  }
?>
