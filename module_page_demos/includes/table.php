<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="badge"><?php echo $Translate->get_translate_phrase('_Demos')?></h5>
                <div class="select-panel select-panel-table badge">
                    <select onChange="window.location.href=this.value">
                        <option style="display:none" value="" disabled selected><?php echo $servers[ $server_num-1 ]['name_custom']?></option>
                        <?php for ( $b = 0; $b < $General->server_list_count; $b++ ):?>
                            <option value="<?php echo set_url_section(get_url(2), 'server', $servers[ $b ]['id'] ) ?>">
                                <a href="<?php echo set_url_section(get_url(2), 'server', $servers[ $b ]['id'] ) ?>"><?php echo $servers[ $b ]['name_custom']?></a></option>
                        <?php endfor?>
                    </select>
                </div>
            </div>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th class="text-center tb-game"><?php echo $Translate->get_translate_phrase('_Game')?></th>
                    <th class="text-center"><?php echo $Translate->get_translate_phrase('_Date')?></th>
                    <th class="text-center"><?php echo $Translate->get_translate_phrase('_Server')?></th>
                    <th class="text-right"></th>
                    <th class="text-left"><?php echo $Translate->get_translate_phrase('_Map')?></th>
                    <th class="text-center"><?php echo $Translate->get_translate_phrase('_Play_time')?></th>
                    <th class="text-center"><?php echo $Translate->get_translate_phrase('_Score')?></th>
                    <th class="text-center"></th>
                </tr>
                </thead>
                <tbody>
                <?php for ( $i = 0, $sz = sizeof( $res ); $i < $sz; $i++ ):
                    $map = file_exists( STORAGE . 'cache/img/maps/730/' . $res[ $i ]["map"] . '.jpg' ) ? './storage/cache/img/maps/730/' . $res[ $i ]["map"] . '.jpg' : './storage/cache/img/maps/730/-.jpg'?>
                    <tr class="pointer"
                        onclick="location.href = '<?php echo $General->arr_general['site'] ?>?page=demos&section=match&id=<?php echo $res[ $i ]['id'] ?>';">
                        <th class="text-center tb-game"><img <?php $i  < '20' ? print 'src' : print 'data-src'?>="./storage/cache/img/mods/730.png"></th>
                        <th class="text-center"><?php echo date('Y-m-d H:i:s', $res[ $i ]['time']) ?></th>
                        <th class="text-center"><?php echo action_text_clear( action_text_trim( $res[ $i ]['name'], 17 ) )?></th>
                        <th class="text-right"><img class="circle_map" <?php $i  < '20' ? print 'src' : print 'data-src'?>="<?php echo $map?>"></th>
                        <th class="text-left"><?php echo $res[ $i ]['map']?></th>
                        <th class="text-center"><?php echo ceil( $res[ $i ]['duration'] / 60 );?> min.</th>
                        <th class="text-center"><?php echo $res[ $i ]['CT']?>/<?php echo $res[ $i ]['T']?></th>
                        <th class="text-center"><a href="<?php echo 'app/modules/module_page_demos/temp/demos/' . $res[ $i ]['id']?>.zip"><?php $General->get_icon( 'zmdi', 'download' )?></a></th>
                    </tr>
                <?php endfor; ?>
                </tbody>
            </table>
            <div class="card-bottom">
                <?php if( $page_max != 1):?>
                <div class="select-panel-pages">
                    <?php endif;?>
                    <?php if ($page_num != 1):?>
                        <a href="<?php echo set_url_section( get_url(2), 'num', $page_num - 1 ) ?>"><h5 class="badge"><?php $General->get_icon( 'zmdi', 'chevron-left' ) ?></h5></a>
                    <?php endif; ?>
                    <?php if( $page_num != $page_max ): ?>
                        <a href="<?php echo set_url_section( get_url(2), 'num', $page_num + 1 ) ?>"><h5 class="badge"><?php $General->get_icon( 'zmdi', 'chevron-right' ) ?></h5></a>
                    <?php endif; ?>
                    <?php if( $page_max != 1):?>
                </div>
            <?php endif;?>
            </div>
        </div>
    </div>
</div>