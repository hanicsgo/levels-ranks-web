<?php if( !isset( $_SESSION['user_admin'] ) && IN_LR != true ) { header('Location: ' . $General->arr_general['site']); exit; };
$Servers = $CASES->getServersAdmin();$CasesList = $CASES->getCasesAdmin();?>
<div class="col-md-7">
    <div class="card">
        <div class="card-header">
            <h5 class="badge"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_CaseSettings')?></h5>
        </div>
        <div class="card-container">
                <h5 class="badge"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_CaseList')?></h5>
                <table class="table table-hover">
                    <thead>
                       <tr>
                            <th></th>
                            <th><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Name')?></th>
                            <th><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Sort')?></th>
                            <th><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Price')?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($CasesList as $list):?>
                            <tr>
                                <th><img src="<?php echo $list['case_img']?>"></th>
                                <th><?php echo $list['case_name']?></th>
                                <th><?php echo $list['case_sort']?></th>
                                <th><?php echo $list['case_price']?></th>
                                <th>
                                	<a href="<?php echo set_url_section(get_url(2), 'case', $list['id'])?>" class="btn"><i class="zmdi zmdi-edit zmdi-hc-fw"></i></a>
                                	<a href="?page=cases&section=case&id=<?php echo $list['id']?>" class="btn"><i class="zmdi zmdi-case-download zmdi-hc-fw"></i></a>

                                </th>
                            </tr>
                       <?php endforeach?>
                    </tbody>
                </table>
        </div>
    </div>
</div>
<div class="col-md-5">
    <div class="card">
        <div class="card-header">
            <h5 class="badge"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Options')?></h5>
        </div>
        <div class="card-container">
            <a class="btn" href="<?php echo set_url_section(get_url(2), 'case', 'add') ?>"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_AddCase')?></a><br>
             <div style="padding-top:15px;">
                <h5 class="badge"><?php echo $Translate->get_translate_module_phrase('module_page_lk_impulse','_DiscordMessage')?></h5>
                 <form id="webhook_discord" data-default="true" enctype="multipart/form-data" method="post">
                    <div class="input-form"><div class="input_text">Webhook URL:</div><input name="webhoock_url" value="<?php $CASES->DiscordData()['url'] && print $CASES->DiscordData()['url'];?>"></div>
                <div class="input-form">
                    <input class="border-checkbox" type="checkbox" name="webhoock_url_offon" id="webhoock_url_offon" <?php $CASES->DiscordData()['auth'] && print 'checked';?>>
                    <label class="border-checkbox-label" for="webhoock_url_offon">вкл. / выкл.</label>
                </div>
                </form>
                <input class="btn"  type="submit" form="webhook_discord" value="<?php echo $Translate->get_translate_module_phrase('module_page_lk_impulse','_Save')?>">
            </div>
        </div>
        </div>
    </div>
<?php if(isset($_GET['case']) && $_GET['case'] == 'add'):?>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h5 class="badge"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_AddCase')?></h5>
                <a class="modal-close badge"><i data-del="delete" data-get="case" class="zmdi zmdi-close zmdi-hc-fw"></i></a> 
            </div>
            <div class="card-container module_block">
                <form data-default="true" enctype="multipart/form-data" method="post">
                    <div class="row">
                       <div class="col-md-6">
                            <div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_CaseName')?></div>
                                <input type="text" name="case_name">
                            </div>
                            <div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_CaseType')?></div>
                                <select id="case_type" onchange="descript()" name="case_type" class="m-input-blue">
                                  <option value="1"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_ForMoney')?></option>
                                  <option value="2"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Free')?></option>
                                </select>
                            </div>
                            <div class="input-form"><div class="input_text" id="case_price"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_OpPrice')?></div>
                                <input type="text" name="case_price">
                            </div>
                            <div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Sort')?></div>
                                <input type="text" name="case_sort" >
                            </div>
                        </div>
                       <div class="col-md-6">
                            <div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_IMGASE')?></div></div>
                            <div id="drop-area">
                                <div id="gallery" /></div>
                                <input type="file" id="fileElem" name="case_img" accept="image/png">
                                <label class="btn float_none"  for="fileElem"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_SelectImages')?></label>
                                <p><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_InfoImages')?></p>
                            </div>
                        </div>
                    </div><!--row-->
                    </br>
                    <button type="submit" class="btn"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_AddCase')?></button>
                </form>
            </div>
        </div>
        <script type="text/javascript" src="<?php echo MODULES?>module_page_open_case/assets/js/draganddrop.js"></script>
    </div>
<?php elseif(!empty($_GET['case'])):$caseEdit = $CASES->getPriceCase($_GET['case']);?>
    <script type="text/javascript">
        setTimeout(function(){
          $(document).ready(function(){
            descript();
          });
        }, 150);
    </script>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h5 class="badge"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_EditCase')?> - <?php echo $caseEdit['case_name']?></h5>
                <a class="modal-close badge"><i data-del="delete" data-get="case" class="zmdi zmdi-close zmdi-hc-fw"></i></a> 
            </div>
            <div class="card-container module_block">
                <form id="case_edit" data-default="true" enctype="multipart/form-data" method="post">
                    <input type="hidden" name="case_id_edit" value="<?php echo $_GET['case']?>">
                    <div class="row">
                       <div class="col-md-6">
                            <div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_CaseName')?></div>
                                <input type="text" name="case_name_edit" value="<?php echo $caseEdit['case_name']?>">
                            </div>
                            <div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_CaseType')?></div>
                                <select id="case_type" onchange="descript()" name="case_type_edit" class="m-input-blue">
                                  <option value="1" <?php $caseEdit['case_type'] == 1 && print 'selected';?>><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_ForMoney')?></option>
                                  <option value="2" <?php $caseEdit['case_type'] == 2 && print 'selected';?>><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Free')?></option>
                                </select>
                            </div>
                            <div class="input-form"><div class="input_text" id="case_price"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_OpPrice')?></div>
                                <input type="text" name="case_price_edit" value="<?php echo $caseEdit['case_price']?>">
                            </div>
                            <div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Sort')?></div>
                                <input type="text" name="case_sort_edit" value="<?php echo $caseEdit['case_sort']?>">
                            </div>
                        </div>
                       <div class="col-md-6">
                            <div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_IMGASE')?></div></div>
                            <div id="drop-area">
                                <div id="gallery" ><img width="100" src="<?=$caseEdit['case_img']?>"></div>
                                <input type="file" id="fileElem" name="case_img_edit" accept="image/png">
                                <label class="btn float_none"  for="fileElem"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_SelectImages')?></label>
                                <p><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_InfoImages')?></p>
                            </div>
                        </div>
                    </div><!--row-->
                    </br>
                </form>
                <button type="submit" class="btn" form="case_edit"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Save')?></button>
                <form data-get="case" id="case_delete" data-default="true" enctype="multipart/form-data" method="post">
                     <input type="hidden" name="case_delete" value="<?php echo $_GET['case']?>">         
             </form>
            <button class="btn float-left" type="submit" form="case_delete" ><i  class='zmdi zmdi-delete zmdi-hc-fw'></i></button>
            </div>
        </div>
        <script type="text/javascript" src="<?php echo MODULES?>module_page_open_case/assets/js/draganddrop.js"></script>
    </div>
<?php endif;?>
<script type="text/javascript">
function descript(){
    if($('#case_type').val() == 1)
    {
       $('#case_price').html('<?php echo $Translate->get_translate_module_phrase('module_page_open_case','_OpPrice')?>');
    }
    else if($('#case_type').val() == 2)
    {
      $('#case_price').html('<?php echo $Translate->get_translate_module_phrase('module_page_open_case','_OpenTime')?> <a href="https://www.cy-pr.com/tools/time/" target="_blanck">UNIX TIME</a>');
    }
}
</script>