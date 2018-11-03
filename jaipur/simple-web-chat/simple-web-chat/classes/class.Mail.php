<?php
/**
 * Common class to sends mail
*/
class Mail
{

	/**
	 * constructor inits mailer object
	*/
	function __construct()
	{
		global $site_path, $site_url;
		include_once($site_path.'lib'.DIRECTORY_SEPARATOR.'phpmailer'.DIRECTORY_SEPARATOR.'class.phpmailer.php');
		$this->mailer_obj = new PHPMailer();
	}

	/**
	 * function to send mail
	 * @param email email it of receiver
	 * @param mbody mail content
	*/
	function sendMail($email, $mbody)
	{
		return mail($email, "Email Verification", $mbody);
		// exit;
		/*
		$this->mailer_obj->IsSMTP();
		$this->mailer_obj->SMTPAuth = true;
		$this->mailer_obj->SMTPSecure = "ssl";
		$this->mailer_obj->Host = "smtp.gmail.com";
		$this->mailer_obj->Port = 465;
		$this->mailer_obj->Username = "";
		$this->mailer_obj->Password = "";
		*/
		/*
		$this->mailer_obj->SetFrom("admin@html5localwebchat.com");
		$this->mailer_obj->IsHTML(true);
		$this->mailer_obj->Subject = "Email Verification";
		$this->mailer_obj->Body = $mbody;
		$this->mailer_obj->AddAddress($email);
		$return_val = $this->mailer_obj->Send();
		return $return_val;
		*/
	}
}
?>
