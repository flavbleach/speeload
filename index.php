<?php
	include("functions.php");
	include("head.html");
?>
<center>
	<form method="POST" action="" enctype="multipart/form-data">
		<?php
			if(isset($_POST["sb_u"])){
				$f = $_FILES['files'];
				$d1="users/";
				$d2="uploads/";
				if(!empty($f)&&$f["size"][0]!=0){
					for($i=0;$i<count($f["name"]);$i++){
						$name=$f["name"][$i];
						if($f["error"][$i]==0){
							$ex=explode(".",$name);
							$ext=$ex[count($ex)-1];
							$tmp=$f["tmp_name"][$i];
							$size=$f["size"][$i];
							$cf=false;
							$md5=substr(base64_encode(md5(uniqid().time())),0,10);
							$comp=$md5.".".$ext;
							$ova=array("name"=>$name,"md5"=>$md5,"comp"=>$comp,"ext"=>$ext,"size"=>$size,"date"=>time());
							if(move_uploaded_file($tmp,$d2.$comp)){
								$ufn=getIp();
								$usru=$_POST["usr_u"];
								$usrp=$_POST["pw_u"];
								if(isset($usru)&&!empty($usru)&&isset($usrp)&&!empty($usrp)){
									$nf=$d1.sha1(md5($usru));
									$usrp=sha1($_POST["pw_u"]."spd");
									if(file_exists($nf)){
										$json=json_decode(file_get_contents($nf),true);
										if($json["pwd"]==$usrp){
											array_push($json["files"],$ova);
											del($nf);
											$a=file_put_contents($nf,json_encode($json));
											if($a){
												$cf=true;
											}
											}else{
											msg_err("Switching to default","Wrong password.");
										}
										}else{
										$a=file_put_contents($nf,json_encode(array("pwd"=>$usrp,"files"=>array(0=>$ova))));
										if($a){
											$cf=true;
										}
									}
								}
								
								if($cf!==true){
									$usru=sha1(md5($ufn));
									$nf=$d1.$usru;
									if(file_exists($nf)){
										$c=json_decode(file_get_contents($nf),true);
										array_push($c["files"],$ova);
										}else{
										$c=array("files"=>array(0=>$ova));
									}
									$cfu=createFileU($usru,json_encode($c));
									if ($cfu) {
										msg_ok("","User file created : <b><a target='blank' href='$d2$comp'>{$name}</a></b>.");
										}else{
										echo msg_err("Switching to default","Impossible to create the file for the user");
										del($d2.$md5);
									}
									}else{
									echo msg_ok("User file updated.");
								}
							}
							}else{
							echo msg_err("Error while uploading.","The file : {$name} was not able to be downloaded.");
						}
					}
					}else{
					echo msg_err("No file.","Please place a file before validating.");
				}
			}
		?>
		<div class="file-field input-field">
			<div class="btn">
				<span>Files</span>
				<input type="file" name="files[]" multiple>
			</div>
			<div class="file-path-wrapper">
				<input class="file-path validate" type="text" placeholder="Upload one or more files">
			</div>
		</div>
		<h5>Upload in account</h5>
		<p>Please let theses inputs empty if you don't want to save the file(s) in an account.</p>
		<div class="input-field col l6 m12 s12">
			<input placeholder="" name="usr_u" type="text" class="validate">
			<label for="usr_u">Username</label>
		</div>
		<div class="input-field col l6 m12 s12">
			<input name="pw_u" type="text" class="validate">
			<label for="pw_u">Password</label>
		</div>
		<button class="btn" type="submit" name="sb_u">Validate</button>
	</form>
</center>
<hr/>
<h3>Find back your files</h3>
<center>
	<a class="btn">Auto search my files</a><br/>
	<form method="POST" action="">
		<div class="input-field col l6 m12 s12">
			<input placeholder="" name="usr_s" type="text" class="validate">
			<label for="usr_s">Username</label>
		</div>
		<div class="input-field col l6 m12 s12">
			<input name="pw_s" type="text" class="validate">
			<label for="pw_s">Password</label>
		</div>
		<button class="btn" type="submit" name="sb_s">Search</button>
	</form>
</center>
<?php include("foo.html");?>