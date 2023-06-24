var table;

//////////////////////////
function load_data(url) {
/////////////////////////

    table = $('#table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        order: [],
        ajax: url,
        language: {
            url: "<?= base_url('/assets/plugins/DataTables/portugues.json'); ?>"
        },
        dom: 'Bfrtlip',
        columnDefs: [{
            "targets": [-1],
            "orderable": false,
        },],
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel" aria-hidden="true"></i>' + '',
            titleAttr: 'Exportar para Excel'
        },
        {
            extend: 'pdfHtml5',
            text: '<i class="fa fa-file-pdf"></i>' + '',
            titleAttr: 'Exportar para PDF'
        },
        {
            text: '<i class="fa fa-sync-alt" aria-hidden="true"></i>' + '',
            titleAttr: 'Atualizar Dados',
            action: function () {
                reload_table();
            }
        },
        {
            text: '<i class="fa fa-plus" aria-hidden="true"></i>' + '',
            titleAttr: 'Atualizar Dados',
            action: function () {
                add();
            }
        },
        ],
    });
}

//////////////////////////
function reload_table() {
//////////////////////////
    table.ajax.reload(null, false);
}

////////////////
function add() {
////////////////
    remove_validate();
    $('#form')[0].reset();
    $('#id').val('');
    $('#modal_form').modal('show');
    $('.modal-title').text('Novo Registro');
}

////////////////////
function save(url) {
////////////////////
    remove_validate();
    $('.form-group').removeClass('is-valid');
    $('.form-group').removeClass('is-invalid');
    $('#btnSalvar').text('Salvando...');
    $('#btnSalvar').attr('disabled', true);

    // ajax adding data to database
    $.ajax({
        url: url,
        type: "POST",
        data: $('#form').serialize(),
        success: function (data) {
            var retorno = JSON.parse(data.trim());

            // VERIFICA SE NÃO RETORNOU ERRO DA CAMADA DE VALIDAÇÃO
            $.each(get_validate(), function (key, value) {

                // COLACA EM ARRAY OS CAMPOS QUE NÃO FORAM VALIDADOS
                arr_fields = (Object.keys(retorno.erros_validacao));

                if(arr_fields.indexOf(value.replace('_msg', '')) < 0) {
                    $('#' + value).addClass('valid-feedback');
                    $('#' + value.replace('_msg', '')).addClass('is-valid');
                }
            });

            // ADICIONA A DESCRIÇÃO DO ERRO RETORNADO DA VALIDAÇÃO NO CAMPO
            $.each(retorno.erros_validacao, function (key, value) {
                $('#' + key + '_msg').addClass('invalid-feedback');
                $('#' + key + '_msg').html(value);
                $('#' + key).addClass('is-invalid');
            });

            // SE RETORNOU COM SUCESSO
            if (retorno.status == true) {
                $('#modal_form').modal('hide');
                reload_table();
                if(retorno.mensagem != '') {
                    //bootbox.alert(retorno.mensagem);
                }
            }

            $('#btnSalvar').text('Salvar');
            $('#btnSalvar').attr('disabled', false);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            bootbox.alert('Erro ao inserir/atualizar os dados no servidor! Tente novamente mais tarde.');
            $('#btnSalvar').text('Salvar');
            $('#btnSalvar').attr('disabled', false);
        }
    });
}

//////////////////////////
function remove(url, id) {
//////////////////////////

    bootbox.confirm({
        message: "Deseja excluir o item de código: " + id + "?",
        buttons: {
            confirm: {
                label: 'Excluir',
                className: 'btn-primary',
            },
            cancel: {
                label: 'Cancelar',
                className: 'btn-danger',
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    url: url + "/" + id,
                    type: "POST",
                    dataType: "JSON",
                    success: function (data) {
                        $('#modal_form').modal('hide');
                        bootbox.alert('Registro excluído com sucesso!');
                        reload_table();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        bootbox.alert('Erro ao excluir o registro!');
                    }
                });
            }
        }
    })
}

//////////////////////////
function get_validate() {
//////////////////////////
    var id_array = [];
    $('.validate').each(function () {
        id_array.push(this.id);
    });

    return id_array;
}

////////////////////////////
function remove_validate() {
////////////////////////////

    $.each(get_validate(), function (key, value) {

        $('#' + value.replace('_msg', '')).removeClass('is-invalid');
        $('#' + value.replace('_msg', '')).removeClass('is-valid');

        $('#' + value).removeClass('invalid-feedback');
        $('#' + value).removeClass('valid-feedback');
        $('#' + value).html('');
    });
}

//////////////////////////////////////
function clear_selectize(component) {
//////////////////////////////////////
    var $select = $('#' + component).selectize();
    var control = $select[0].selectize;
    control.clear();
}