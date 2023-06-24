$(document).ready(function() {
    $('.intmask').mask("#0", {reverse: true});
    $('.nummask').mask("#.##0", {reverse: true});
    $('.num1mask').mask("#.##0,0", {reverse: true});
    $('.num2mask').mask("#.##0,00", {reverse: true});
    $('.cepmask').mask('00000-000');
    $('.percmask').mask('#0,0#%', {reverse: true});
    $('.datamask').mask('00/00/0000');
    $('.horamask').mask('00:00:00');
    $('.datahmask').mask('00/00/0000 00:00:00');

    $(".dinmask").maskMoney({prefix: "R$ ", decimal: ",", thousands: "." });	

    var fonemask = function (val) {
        var tam = val.replace(/\D/g, '').length;
        var ret = "";
    
        switch (tam) {
            case 7 : ret =       "000-00009"; break;
            case 8 : ret =      "0000-00009"; break;
            case 9 : ret =     "00000-00009"; break;
            case 10: ret = "(00) 0000-00009"; break;
            case 11: ret = "(00) 00000-0000"; break;
            default: ret = "###############";
        }
    
        return ret; 
    },
    fonemaskOptions = {
        onKeyPress: function(val, e, field, options) {
                        field.mask(fonemask.apply({}, arguments), options); 
                    } 
    };
    $('.fonemask').mask(fonemask, fonemaskOptions);

    var cpfcnpjmask = function (val) {
                          return val.replace(/\D/g, '').length <= 11 ? '000.000.000-009' : '00.000.000/0000-00'; 
    },
    cpfcnpjmaskOptions = {
        onKeyPress: function(val, e, field, options) {
                        field.mask(cpfcnpjmask.apply({}, arguments), options); 
                    } 
    };
    $('.cpfcnpjmask').mask(cpfcnpjmask, cpfcnpjmaskOptions);

    var rgiemask = function (val) {
        return val.replace(/\D/g, '').length <= 9 ? '00.000.000-09' : '000.000.000.000'; 
    },
    rgiemaskOptions = {
        onKeyPress: function(val, e, field, options) {
                        field.mask(rgiemask.apply({}, arguments), options); 
                    } 
    };
    $('.rgiemask').mask(rgiemask, rgiemaskOptions);
});
