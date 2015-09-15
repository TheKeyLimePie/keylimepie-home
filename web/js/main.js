function init()
{
	alignCaptcha();
	$("#msg-success").hide();
	$("#msg-internalserver").hide();
	$("#msg-incomplete").hide();
	$("#msg-captcha").hide();
	$("#msg-unexpected").hide();
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
					$("#msg-success").show(300);
					document.getElementById("contact-form").reset();
					grecaptcha.reset();
					alignCaptcha();
				}
				else
				{
					switch(data)
					{
						case -1: $("#msg-internalserver").show(300); break;
						
						case -2: $("#msg-incomplete").show(300); break;
						
						case -3: $("#msg-captcha").show(300); break;
						
						default: $("#msg-unexpected").show(300);
					}
				}
			}
		);
	}
	else
		alert("I was unable to gather the information out of this form!");
}