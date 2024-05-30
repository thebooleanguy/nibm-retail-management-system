function validatelogin(){
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;

    if(username==""){
        alert("username must be filled out");
        return false;
    }
    if(password==""){
        alert("password must entered");
        return false;
    }
    if(password.length >  8){
        alert("password must be less than 8 characters")
    }
    return true;
}