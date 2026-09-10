var logInProcess = (function(){
    var logInUI = function(){
        $('input.login-btn').click(function(){
            if(validate()){
                logInSubmit();
            }
        });
        $('input#pass, input#user').keypress(function(e){
            if(e.keyCode == 13){
                if(validate()){
                    logInSubmit();
                }
            }
        });        
    }
    
    var validate = function(){
        if($('input#user').val().trim() == ""){
            $.msgBox({ title: "Error", content: "Debe ingresar un nombre de usuario válido!" });
            //alert("Debe ingresar un nombre de usuario válido!");
            $('input#user').focus();
            return false;
        }
        if($('input#pass').val().trim() == ""){
            $.msgBox({ title: "Error", content: "Debe ingresar una contraseña válida!" });
            //alert("Debe ingresar una contraseña válida!");
            $('input#pass').focus();                
            return false;
        }        
        return true;
    }
    var logInSubmit = function(){
        $.ajax({    
            type: 'POST',
            url : 'inc/process.php',
            data: {
                accion:   'login',
                parametros: {
                    userName: $('input#user').val(),
                    password: $('input#pass').val(),
                    sucursalId: $('select#sucursal').val(),

                }

            },
            success: function(msj){
                if(msj == 1){
                    window.location = "index.php"
                }else{
                    //alert("Nombre de usuario o contraseña incorrecta.");
                    $.msgBox({ title: "Error", content: "Nombre de usuario o contraseña incorrecta." });
                    $('input#pass').attr('value', '');
                    
                }
            }   
        });
    }
    return{ 
        init: logInUI 
    }
    
})();

$(document).ready(function(){
    $('input#user').focus();
    logInProcess.init();
});
