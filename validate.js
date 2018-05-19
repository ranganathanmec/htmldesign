function validatedata()
{
	var name=document.form.name;
	var email=document.form.email;
	var subject=document.form.subject;
	var message=document.form.message;
	var flag=true;

if (name.value.length==0 || !name.value.match(/^[a-zA-Z]+$/)) 
			{
				alert("Enter Valid Name");
				name.focus();
				flag=false;

			}
else if (!email.value.match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/)) {
				alert("Enter Valid Email");
				email.focus();
				flag=false;


			}


else if (subject.value.length<5 || !name.value.match(/^[a-zA-Z]+$/)) 
			{
				alert("Enter Valid Subject");
				name.focus();
				flag=false;

			}

else if (message.value.length<5 || !name.value.match(/^[a-zA-Z]+$/)) 
			{
				alert("Enter Valid Subject");
				name.focus();
				flag=false;

			}
	else if (flag) {

		var xhttp=new XMLHttpRequest();

		xhttp.onreadystatechange = function() {

            if (this.readyState == 4 && this.status == 200) 
            {
                var flag1=this.responseText;
                if (flag1=="false")
                 {
                	alert("Email Already Exist");  

                	
                }
                else if (flag1=="true") {
                	alert('done');

                }
                	
            }
        };

        xhttp.open("GET", "http://localhost/changetech/insertdata.php?name=" + name.value+"email="+email.value+"subject="+subject.value+"message="+message.value, true);
        xhttp.send();




	}




}