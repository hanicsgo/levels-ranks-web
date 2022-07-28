<?php if( !isset( $_SESSION['user_admin'] ) && IN_LR != true ) { header('Location: ' . $General->arr_general['site']); exit; };?>
<?php 
$subjects = $CASES->getCaseSubjectsAdmin($_GET['id']);$servers = $CASES->getServersAdmin();?>
<div class="col-md-7">
    <div class="card">
        <div class="card-header">
            <h5 class="badge"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_CaseContent')?></h5>
        </div>
        <div class="card-container">
			<table class="table table-hover">
				<thead>
			  		<tr>
			  			<th></th>
			  			<th><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Title')?></th>
			  			<th><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Chance')?></th>
			  			<th><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Content')?></th>
			  			<th><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_SellingPrice')?></th>
			  			<th></th>
			  		</tr>
			  	</thead>
			  	<tbody>
			  		<?php foreach($subjects as $key):?>
				    <tr>
				      	<th><img src="<?=$key['subject_img']?>"></th>
				      	<td><?=$key['subject_name']?></td>
				      	<td><?=$key['subject_chance']?></td>
				      	<td><?=$key['subject_content']?></td>
				      	<td><?=$key['subject_sale']?></td>
			      	  	<td><a href="<?php echo set_url_section(get_url(2), 'subject', $key['id'])?>" class="btn"><i class="zmdi zmdi-edit zmdi-hc-fw"></i></a></td>
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
                 <a class="btn" href="<?php echo set_url_section(get_url(2), 'subject', 'add') ?>"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_AddItem')?></a>
        </div>
    </div>
</div>
<?php if(isset($_GET['subject']) && $_GET['subject'] == 'add'):?>
	<div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h5 class="badge"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_AddItem')?></h5>
                <a class="modal-close badge"><i data-del="delete" data-get="subject" class="zmdi zmdi-close zmdi-hc-fw"></i></a> 
            </div>
            <div class="card-container module_block">
			    <form id="subject_add" data-default="true" enctype="multipart/form-data" method="post">
			    	<input type="hidden" name="case_id_subject" value="<?=$_GET['id']?>">
				    <div class="row">
				        <div class="col-md-4">
				       		<div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Server')?></div>
						        <select name="subject_server">
						        	<option value="-1">Предоставить выбор сервера игроку</option>
						          <?php foreach($General->server_list as $key ):?>
						            <option value="<?=$key['id']?>"><?=$key['name']?></option>
						          <?php endforeach?>
						        </select>
						    </div>
				        	<div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_SelectType')?></div>
						        <select name="subject_type" id="subject_type" onchange="descript()">
							        <option value="1" ><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_TypeMoney')?></option>
							        <option value="2" >Castom</option>
							        <option value="3" >VIP R1KO</option>
							        <option value="4" >ADMIN MA/SB</option>
							        <option value="5" >Credits SHOP Forzdark</option>
							        <option value="6" >VIP WCS Straker</option>
							        <option value="7" >Credits WCSGold Straker</option>
							        <option value="8" >Levels WCS Straker</option>
							        <option value="9" >Race WCS Straker</option>
							        <option value="10" >Experience LR</option>
						        </select>
						     </div>
				      		<div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Title')?></div>
				      			<input type="text" name="subject_name" >
				      		</div>
				      	</div>
				       	<div class="col-md-4">
				      		<div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Desc')?></div>
				      			<input type="text" id="subject_desc" name="subject_desc" value="<?php echo $Translate->get_translate_module_phrase('module_page_open_case','_TypeMoney')?>" readonly >
				     		</div>
				      		<div class="input-form"><div id="subject_content_html" class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Content')?></div>
				      			<input type="text" id="subject_content" name="subject_content">
						    </div>
				       		<div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Background')?></div>
						        <select name="subject_class">
						          <option value="1"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Gold')?></option>
						          <option value="2"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Red')?></option>
						          <option value="3"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Pink')?></option>
						          <option value="4"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Purple')?></option>
						          <option value="5"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Blue')?></option>
						          <option value="6"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Turquoise')?></option>
						          <option value="7"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Grey')?></option>
						        </select>
						    </div>
				      	</div>
				      	<div class="col-md-4">
					      	<div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Chance')?></div>
					      		<input type="text" name="subject_chance" >
					      	</div>
					      	<div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_PriceToSale')?></div>
					      		<input type="text" id="subject_sale" name="subject_sale" readonly>
					      	</div>
					      	<div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Sort')?></div>
					      		<input type="text" name="subject_sort">
					      	</div>
				     	</div>
					    <div class="col-md-12">
		                    <div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_IMGSUB')?></div></div>
		                    <div id="drop-area">
		                        <div id="gallery" /></div>
		                        <input type="file" id="fileElem" name="subject_img" accept="image/png">
		                        <label class="btn float_none"  for="fileElem"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_SelectImages')?></label>
		                        <p><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_InfoImages')?></p>
		                    </div>
		                </div>
					</div><br>
					<button type="submit" class="btn"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_AddItem')?></button>
				</form>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="<?php echo MODULES?>module_page_open_case/assets/js/draganddrop.js"></script>
<?php elseif(!empty($_GET['subject'])):$subjectEdit = $CASES->getSubjectData($_GET['subject']);?>
	<script type="text/javascript">
		setTimeout(function(){
		  $(document).ready(function(){
		    descript();
		  });
		}, 200);
	</script>
	<div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h5 class="badge"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_EditItem')?> - <?=$subjectEdit[0]['subject_name']?></h5>
                <a class="modal-close badge"><i data-del="delete" data-get="subject" class="zmdi zmdi-close zmdi-hc-fw"></i></a> 
            </div>
            <div class="card-container module_block">
			    <form id="subject_add" data-default="true" enctype="multipart/form-data" method="post">
			    	<input type="hidden" name="subject_id_edit" value="<?=$_GET['subject']?>">
				    <div class="row">
				        <div class="col-md-4">
				       		<div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Server')?></div>
						        <select name="subject_server_edit">
						        	<option value="-1" <?php $subjectEdit[0]['server_id'] == -1 && print 'selected';?>>Предоставить выбор сервера игроку</option>
						          <?php foreach($General->server_list as $key ):?>
						            <option value="<?=$key['id']?>" <?php $subjectEdit[0]['server_id'] == $key['id'] && print 'selected';?> ><?=$key['name']?></option>
						          <?php endforeach?>
						        </select>
						    </div>
				        	<div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_SelectType')?></div>
						        <select name="subject_type_edit" id="subject_type" onchange="descript()">
							        <option value="1" <?php $subjectEdit[0]['subject_type'] == 1 && print 'selected';?> ><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_TypeMoney')?></option>
							        <option value="2" <?php $subjectEdit[0]['subject_type'] == 2 && print 'selected';?> >Castom</option>
							        <option value="3" <?php $subjectEdit[0]['subject_type'] == 3 && print 'selected';?> >VIP R1KO</option>
							        <option value="4" <?php $subjectEdit[0]['subject_type'] == 4 && print 'selected';?> >ADMIN MA/SB</option>
							        <option value="5" <?php $subjectEdit[0]['subject_type'] == 5 && print 'selected';?> >Credits SHOP</option>
							        <option value="6" <?php $subjectEdit[0]['subject_type'] == 6 && print 'selected';?> >VIP WCS</option>
							        <option value="7" <?php $subjectEdit[0]['subject_type'] == 7 && print 'selected';?> >Credits WCSGold</option>
							        <option value="8" <?php $subjectEdit[0]['subject_type'] == 8 && print 'selected';?> >Levels WCS</option>
							        <option value="9" <?php $subjectEdit[0]['subject_type'] == 9 && print 'selected';?> >Race WCS</option>
							        <option value="10" <?php $subjectEdit[0]['subject_type'] == 10 && print 'selected';?> >Experience LR</option>
						        </select>
						     </div>
				      		<div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Title')?></div>
				      			<input type="text" name="subject_name_edit" value="<?=$subjectEdit[0]['subject_name']?>">
				      		</div>
				      	</div>
				       	<div class="col-md-4">
				      		<div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Desc')?></div>
				      			<input id="subject_desc" type="text" name="subject_desc_edit" value="<?=$subjectEdit[0]['subject_desc']?>" >
				     		</div>
				      		<div class="input-form"><div class="input_text" id="subject_content_html"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Content')?></div>
				      			<input id="subject_content" type="text" name="subject_content_edit" value="<?=$subjectEdit[0]['subject_content']?>">
						    </div>
				       		<div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Background')?></div>
						        <select name="subject_class_edit">
						          <option value="1" <?php $CASES->bg($subjectEdit[0]['subject_class']) == 1 && print 'selected';?>><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Gold')?></option>
						          <option value="2" <?php $CASES->bg($subjectEdit[0]['subject_class']) == 2 && print 'selected';?>><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Red')?></option>
						          <option value="3" <?php $CASES->bg($subjectEdit[0]['subject_class']) == 3 && print 'selected';?>><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Pink')?></option>
						          <option value="4" <?php $CASES->bg($subjectEdit[0]['subject_class']) == 4 && print 'selected';?>><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Purple')?></option>
						          <option value="5" <?php $CASES->bg($subjectEdit[0]['subject_class']) == 5 && print 'selected';?>><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Blue')?></option>
						          <option value="6" <?php $CASES->bg($subjectEdit[0]['subject_class']) == 6 && print 'selected';?>><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Turquoise')?></option>
						          <option value="7" <?php $CASES->bg($subjectEdit[0]['subject_class']) == 7 && print 'selected';?>><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Grey')?></option>
						        </select>
						    </div>
				      	</div>
				      	<div class="col-md-4">
					      	<div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Chance')?></div>
					      		<input type="text" name="subject_chance_edit" value="<?=$subjectEdit[0]['subject_chance']?>">
					      	</div>
					      	<div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_PriceToSale')?></div>
					      		<input type="text" id="subject_sale" name="subject_sale_edit" value="<?=$subjectEdit[0]['subject_sale']?>">
					      	</div>
					      	<div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Sort')?></div>
					      		<input type="text" name="subject_sort_edit" value="<?=$subjectEdit[0]['subject_sort']?>">
					      	</div>
				     	</div>
					    <div class="col-md-12">
		                    <div class="input-form"><div class="input_text"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_IMGSUB')?></div></div>
		                    <div id="drop-area">
		                        <div id="gallery" /><img width="100" src="<?=$subjectEdit[0]['subject_img']?>"></div>
		                        <input type="file" id="fileElem" name="subject_img_edit" accept="image/png">
		                        <label class="btn float_none"  for="fileElem"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_SelectImages')?></label>
		                        <p><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_InfoImages')?></p>
		                    </div>
		                </div>
					</div><br>
					<button type="submit" class="btn"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Save')?></button>
				</form>
				<form data-get="subject" id="subject_delete" data-default="true" enctype="multipart/form-data" method="post">
                     <input type="hidden" name="subject_delete" value="<?php echo $_GET['subject']?>">         
            		<button class="btn float-left" type="submit" form="subject_delete" ><i  class='zmdi zmdi-delete zmdi-hc-fw'></i></button>
				</form>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="<?php echo MODULES?>module_page_open_case/assets/js/draganddrop.js"></script>
<?php endif;?>
<script type="text/javascript">
function descript(){
	 if($('#subject_type').val() == 1){
        $('#subject_desc').attr("readonly", "readonly");
        $('#subject_desc').val("<?php echo $Translate->get_translate_module_phrase('module_page_open_case','_TypeMoney')?>");
        $('#subject_sale').attr("readonly", "readonly");
        $('#subject_content_html').html('<?php echo $Translate->get_translate_module_phrase('module_page_open_case','_ToBalance')?>');
      }else if($('#subject_type').val() == 2){

        if($('#subject_desc').attr("readonly")){
          $('#subject_desc').removeAttr("readonly");
        }

        if($('#subject_sale').attr("readonly")){
           $('#subject_sale').removeAttr("readonly");
        }
        $('#subject_content_html').html("<?php echo $Translate->get_translate_module_phrase('module_page_open_case','_SubjectContent')?>");
      }else if($('#subject_type').val() == 3 || $('#subject_type').val() == 4 || $('#subject_type').val() == 6){

        if($('#subject_desc').attr("readonly")){
          $('#subject_desc').removeAttr("readonly");
        }

        if($('#subject_sale').attr("readonly")){
           $('#subject_sale').removeAttr("readonly");
        }
        if($('#subject_type').val() == 3 || $('#subject_type').val() == 6){
          $('#subject_content_html').html("<?php echo $Translate->get_translate_module_phrase('module_page_open_case','_GroupVIP')?>");
        }else if($('#subject_type').val() == 4){
          $('#subject_content_html').html("<?php echo $Translate->get_translate_module_phrase('module_page_open_case','_GroupADM')?>");
        }
      }else if($('#subject_type').val() == 5 || $('#subject_type').val() == 7){

        if($('#subject_desc').attr("readonly")){
          $('#subject_desc').removeAttr("readonly");
        }

        if($('#subject_sale').attr("readonly")){
           $('#subject_sale').removeAttr("readonly");
        }
        $('#subject_content_html').html("<?php echo $Translate->get_translate_module_phrase('module_page_open_case','_CreditsCredited')?>");
      }else if($('#subject_type').val() == 8){

        if($('#subject_desc').attr("readonly")){
          $('#subject_desc').removeAttr("readonly");
        }

        if($('#subject_sale').attr("readonly")){
           $('#subject_sale').removeAttr("readonly");
        }
        $('#subject_content_html').html("<?php echo $Translate->get_translate_module_phrase('module_page_open_case','_LevelsCredited')?>");
      }else if($('#subject_type').val() == 9){
        if($('#subject_desc').attr("readonly")){
          $('#subject_desc').removeAttr("readonly");
        }

        if($('#subject_sale').attr("readonly")){
           $('#subject_sale').removeAttr("readonly");
        }
          $('#subject_content_html').html("<?php echo $Translate->get_translate_module_phrase('module_page_open_case','_GroupWCS')?>");
      }else if($('#subject_type').val() == 10){

        if($('#subject_desc').attr("readonly")){
          $('#subject_desc').removeAttr("readonly");
        }

        if($('#subject_sale').attr("readonly")){
           $('#subject_sale').removeAttr("readonly");
        }
        $('#subject_content_html').html("<?php echo $Translate->get_translate_module_phrase('module_page_open_case','_ExperienceCredited')?>");
      }
}
</script>