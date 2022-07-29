function selectCommand( $_Command )
{
    $('input[name="sp_rcon"]').val($_Command).focus();
}

$('input[name="sp_send"]').on( 'click', function(){
   CE_Rcon_Send();
});

$('input[name="sp_rcon"]').keyup(function(event){
    if(event.keyCode == 13)
    {
      CE_Rcon_Send();
    }
});

$('select[name="sp_rcon_server[]"]').change(function(){
    $.ajax({
        type: 'POST',
        url: window.location.href,
        data: {
            sp_m: true,
            sp_rcon_server: $('select[name="sp_rcon_server[]"]').val(),
        },
        success: function( reuslt )
        {   
            let data = jQuery.parseJSON( reuslt.trim() );
            if(data.error)
            {
                note({
                    content: data.error,
                    type: 'error',
                    time: 3
                });
            }
            else
            {   
                $('#sp_maps').html(data.maps);
            }
        }
    });
});

function CE_Rcon_Send()
{
     $.ajax({
        type:'POST',
        url:window.location.href,
        data:{
            sp_rcon_server: $('select[name="sp_rcon_server[]"]').val(),
            sp_rcon: $('input[name="sp_rcon"]').val()
        },
        success: function( result )
        {
            data = jQuery.parseJSON( result.trim() );
            if(data.error)
            {
                note({
                    content: data.error,
                    type: 'error',
                    time: 3
                });
            }
            else
            {   
                $('input[name="sp_rcon"]').val('');
                $('#console_content').append(data.console+"\n");
                $('.sp-rcon-console').scrollTop(99999);
            }
        }
    });
}

function CE_Set_Map( $_Sid )
{
    $.ajax({
        type:'POST',
        url:window.location.href,
        data:{
            sp_sm: true,
            sp_rcon_server: $_Sid,
            sp_map: $('select[name="sp_map'+$_Sid+'"]').val()
        },
        success: function( result )
        {
            data = jQuery.parseJSON( result.trim() );
            if(data.error)
            {
                note({
                    content: data.error,
                    type: 'error',
                    time: 3
                });
            }
            else
            {   
                $('#console_content').append(data.console+"\n");
                $('.sp-rcon-console').scrollTop(99999);
            }
        }
    });
}


function Buffering( Buffer )
{
    let input = $("<textarea>");
    $("body").append(input);
    input.val($(Buffer).text()).select();
    document.execCommand("copy");
    input.remove();

    note({
         content: 'Copied!',
         type: 'success',
         time: 3
    });
}
