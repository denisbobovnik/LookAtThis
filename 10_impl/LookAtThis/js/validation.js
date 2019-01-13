function registrationValidation() {
    var ide = [
        "first_name",
        "last_name",
        "email",
        "pass",
        "pass1"
    ];
    var regex = [
    	/^[a-zA-Z_\u00A1-\uFFFF_\s]{2,45}$/,
    	/^[a-zA-Z_\u00A1-\uFFFF_\s]{2,45}$/,
    	/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
    	/^(?=.*[a-zšđžćč])(?=.*[A-ZŠĐŽĆČ])(?=.*\d.*)(?=.*\W.*)[a-zšđžćčA-ZŠĐŽĆČ0-9\S]{8,15}$/,
    	/^(?=.*[a-zšđžćč])(?=.*[A-ZŠĐŽĆČ])(?=.*\d.*)(?=.*\W.*)[a-zšđžćčA-ZŠĐŽĆČ0-9\S]{8,15}$/
    ];
    var message = [
        "Wrong first name style. Requirements: 2-45 characters, only lower and upper case letters and spaces.",
        "Wrong last name style. Requirements: 2-45 characters, only lower and upper case letters and spaces.",
        "Wrong email style. Requirements: classic email style(xxx@xxx.xxx).",
        "Wrong password style. Requirements: 8-15 characters, at least 1 lower case and upper case letter, a number and a special character (no spaces allowed).",
        "Wrong password style. Requirements: 8-15 characters, at least 1 lower case and upper case letter, a number and a special character (no spaces allowed)."
    ];
    for(var i=0; i<ide.length; i++)
        document.getElementById(ide[i]).style.borderColor = "green";

    var flag = 0;
    for(var i=0; i<ide.length; i++) {
        if((validateOne(ide[i], regex[i], message[i])) != true) {
            flag++;
            break;
        }
    }
    if(flag != 0)
        return false;
    return true;
}
function loginValidation() {
    var ide = [
        "email",
        "pass"
    ];
    var regex = [
    	/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
    	/^(?=.*[a-zšđžćč])(?=.*[A-ZŠĐŽĆČ])(?=.*\d.*)(?=.*\W.*)[a-zšđžćčA-ZŠĐŽĆČ0-9\S]{8,15}$/
    ];
    var message = [
        "Wrong email style. Requirements: classic email style(xxx@xxx.xxx).",
        "Wrong password style. Requirements: 8-15 characters, at least 1 lower case and upper case letter, a number and a special character (no spaces allowed)."
    ];
    for(var i=0; i<ide.length; i++)
        document.getElementById(ide[i]).style.borderColor = "green";

    var flag = 0;
    for(var i=0; i<ide.length; i++) {
        if((validateOne(ide[i], regex[i], message[i])) != true) {
            flag++;
            break;
        }
    }
    if(flag != 0)
        return false;
    return true;
}
function validateOne(ide, regex, message) {
    var fieldValue = document.getElementById(ide).value;
    if((fieldValue == "")||(fieldValue.search(regex)==-1)) {
        document.getElementById(ide).style.borderColor = "red";
        document.getElementById("feedback_reg").innerHTML = message.replace(/(.{54})/g, "$1<br />");;
        return false;
    }
    return true;
}
function validateDelete(form) {
  return confirm('WARNING! This is going to delete ALL photos in the selected gallery aswell!');
}
function jeIzbranaVsajEnaSlik() {
	var steviloIzbranih = $('input[name="checked[]"]:checked').length;
	if(steviloIzbranih>0) {
		return true;
	} else {
		alert("You have to select at least one photo!");
		return false;
	}
}
function jeIzbranaVsajEnaSlikDelete() {
	var steviloIzbranih = $('input[name="checked[]"]:checked').length;
	if(steviloIzbranih>0) {
		if(confirm('WARNING! This is going to permanantely delete the selected photos!'))
			return true;
		return false;
	} else {
		alert("You have to select at least one photo!");
		return false;
	}
}
