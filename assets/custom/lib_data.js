function converte_data_formato_br(data) {
    if(data == '0000-00-00' || data == null)
        data_formatada = '';
    else {
        data_formatada = data.split('-').reverse().join('/');
    }
    return data_formatada;
}


   