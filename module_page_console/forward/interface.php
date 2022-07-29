<?php
/**
 * @author SAPSAN 隼 #3604
 *
 * @link https://hlmod.ru/members/sapsan.83356/
 * @link https://github.com/sapsanDev
 *
 * @license GNU General Public License Version 3
 */
if(empty( $_SESSION['user_admin'] ) ) get_iframe( '013','Доступ закрыт' );?>

<div class="col-md-12">
    <div class="card">
        <div class="card-header"><h5 class="badge">RCON Console</h5></div>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-container ">
                        <div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_console','_Server')?></div>
                            <select class="select" name="sp_rcon_server[]" size="3" style="height: unset;" multiple="multiple">
                                <?php foreach($General->server_list as $key ):?>
                                    <option value="<?=$key['id']?>"><?=$key['name']?></option>
                                <?php endforeach?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-container ">
                        <div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_console','_ChangeMap')?></div>
                        <div class="row" id="sp_maps"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-container ">
                        <div class="sp-rcon-console" style="border:1px solid var(--bg-color);width: 100%; min-height: 300px; max-height: 500px; overflow-x: scroll; background-color: var(--navbar-color)">
                            <pre id="console_content" style="font-size: 11.5px; padding: 10px;" ></pre>
                        </div>
                        <div class="input-form">
                            <div class="input_text"><a onclick="selectCommand('status')">status</a> | sm plugins <a onclick="selectCommand('sm plugins list')">list</a> / <a onclick="selectCommand('sm plugins load ')">load</a> / <a onclick="selectCommand('sm plugins reload ')">reload</a> / <a onclick="selectCommand('sm plugins unload ')">unload</a> | <a onclick="selectCommand('sm_reloadadmins')">sm_reloadadmins</a> | <a onclick="selectCommand('sm_lvl_reload')">sm_lvl_reload</a>
                            </div>
                            <input type="text" name="sp_rcon" placeholder="<?php echo $Translate->get_translate_module_phrase('module_page_console','_EnterСommand')?>" style="height: 30px;">
                        </div>
                        <input style="float: right;" class="btn" name="sp_send" type="submit" value="<?php echo $Translate->get_translate_module_phrase('module_page_console','_Send')?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
