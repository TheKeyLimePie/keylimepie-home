function init()
{
	alignCaptcha();
}

function alignCaptcha()
{
	$("#captcha div").css("display", "inline-block");
}

function sendMsg(e)
{
	e.preventDefault();
	
	if(e.target.id == "contact-form")
	{
		$.post("contact.php",
			{
				"target": e.target.id,
				"email":$("#email").val(),
				"msg":$("#message").val(),
				"g-recaptcha-response":$("#g-recaptcha-response").val()
			},
			function(data)
			{
				data = parseInt(data);
				if(data == 0)
				{
					//SUCCESS!
					alert("SUCCESS!");
					document.getElementById("contact-form").reset();
					grecaptcha.reset();
					alignCaptcha();
				}
				else
				{
					var error;
					switch(data)
					{
						case -1: error = "We were unable to process this message due to an internal server error."; break;
						
						case -2: error = "The given information was incomplete."; break;
						
						case -3:  error = "You didn't pass the captcha test."; break;
						
						default: error = "Error! This is totally fucked up.";
					}
					alert(error);
				}
			}
		);
	}
	else
		alert("I was unable to gather the information out of this form!");
}