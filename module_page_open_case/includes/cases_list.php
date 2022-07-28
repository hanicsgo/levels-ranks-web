<?php if( !isset( $_SESSION['user_admin'] ) && IN_LR != true ) { header('Location: ' . $General->arr_general['site']); exit; };
$List = $CASES->openCasesList()?>
<div class="col-md-10">
    <div class="card">
        <div class="card-header">
            <h5 class="badge"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_OpenCaseList')?></h5>
        </div>
        <div class="card-container">
                <table class="table table-hover">
                    <tbody>
                        <?php foreach ($List as $key):?>
                            <tr  <?php if ( $Modules->array_modules['module_page_profiles']['setting']['status'] == '1'){ ?>onclick="location.href = '<?php echo $General->arr_general['site'] ?>?page=profiles&profile=<?php print $General->arr_general['only_steam_64'] === 1 ? con_steam32to64( $key['steam_id'] ) : $key['steam_id']?>';"<?php } ?>>
 								<?php if( ! empty( $General->arr_general['avatars'] ) ):?><th class="text-right tb-avatar"><img class="rounded-circle" id="<?php $General->arr_general['avatars'] === 1 && print con_steam32to64( $key['steam_id'] )?>"<?php echo $sz_i < '20' ? 'src' : 'data-src'?>="<?php echo $General->getAvatar( con_steam32to64( $key['steam_id'] ), 2 )?>"></th><?php endif?>
			  					<td class="text-center">
			  						<a href="?page=cases&section=case&id=<?php echo $key['case_id']?>">
									<img width="32" class="circle" src="<?php echo $key['case_img']?>"><br>
									<b><?php echo $key['case_name']?></b></a>
			  					</td>
			  					<td>
									<img width="32" class="circle" src="<?php echo $key['subject_img']?>">
									<b><?php echo $key['subject_name']?></b>
			  					</td>
			  					<td><?php echo $key['date']?></td>
                            </tr>
                       <?php endforeach?>
                    </tbody>
                </table>
        </div>
    </div>
</div>