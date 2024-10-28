<?php

  // RECURSIVE DIRECTORY FTP PUSH

  $servers = [
    [
      'ftp_server' => 'ftpupload.net',
      'ftp_user' => 'if0_37603182',
      'ftp_pass' => 'lQIbtdb1mArYj',
      'remote_dir' => '/htdocs',
      'local_dir' => '/home/whitehotrobot/assets.free.nf/dist_public',
    ],
  ];

  function recurse($dir){
    global $remote_dir, $local_dir, $ftp;
    forEach(glob("$dir/{,.}[!.,!..]*", GLOB_MARK|GLOB_BRACE) as $entry){
      if(is_dir($entry)){
        $mkdir = $remote_dir . '/' . explode("$local_dir/", $entry)[1];
        @ftp_mkdir($ftp, $mkdir);
        recurse($entry);
      }else{
        $local_file = explode("$local_dir/", $entry)[1];
        $remote_file = "$remote_dir/$local_file";
        echo "uploading: $local_file -> $remote_file\n";
        ftp_put($ftp, $remote_file, $entry, FTP_BINARY);
      }
    }
  }

  function push($ftp_server, $ftp_user, $ftp_pass, $local_dir, $remote_dir){
    global $ftp;
    echo "\n\n\n$ftp_server\n$ftp_user\n$ftp_pass\n$local_dir\n$remote_dir\n\n\n";
    $ftp = ftp_connect($ftp_server) or die("Couldn't connect to $ftp_server"); 

    if (@ftp_login($ftp, $ftp_user, $ftp_pass)) {
      echo "Connected as $ftp_user@$ftp_server\n";
    } else {
      echo "Couldn't connect as $ftp_user\n";
    }

    ftp_pasv($ftp, true);

    @ftp_rmdir($ftp, $remote_dir);
    @ftp_mkdir($ftp, $remote_dir);
    recurse($local_dir);
  }

  forEach($servers as $server){
    $ftp_server = $server['ftp_server'];
    $ftp_user = $server['ftp_user'];
    $ftp_pass = $server['ftp_pass'];
    $local_dir = $server['local_dir'];
    $remote_dir = $server['remote_dir'];
    push($ftp_server, $ftp_user, $ftp_pass, $local_dir, $remote_dir);
  }
?>
