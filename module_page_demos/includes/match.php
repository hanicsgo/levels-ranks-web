<div class="row">
    <div class="col-md-8">
                <div class="block_total_row">
                    <div class="block_total_left">
                        <div class="block_total_left_CT">Counter Terrorist</div>
                        <div class="block_total_left_Map"><?php echo $res['map']?></div>
                    </div>
                    <div class="block_total_center">
                            <div class="block_total_center_score"><div class="log_block_line_text_CT"><?php echo $res['CT']?></div>:<div class="log_block_line_text_TERRORIST"><?php echo $res['T']?></div></div>
                    </div>
                    <div class="block_total_right">
                        <div class="block_total_right_T">Terrorist</div>
                        <div class="block_total_right_time"><?php echo date('d.m.y H:i:s', $res['time'])?></div>
                    </div>
                </div>
        <div class="block_score">
            <div class="match_block_up">
                <div class="match_block_table">
                    <table class="table table-hover demo">
                        <thead>
                        <tr class="ctTeamHeaderBg">
                            <th class="text-right"></th>
                            <th class="text-left">Игрок</th>
                            <th class="text-center">Убийства</th>
                            <th class="text-center">Смерти</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php for ( $p = 0; $p < $c_players; $p++ ):
                            if( $players[ $p ]['team'] == 'CT'):
                                $General->get_js_relevance_avatar( $players[ $p ]['steamid'] );?>
                                <tr class="pointer ctPlayerBg"
                                    onclick="location.href = '<?php echo $General->arr_general['site'] ?>?page=profiles&profile=<?php echo $players[ $p ]['steamid']?>&search=1';">
                                    <?php if( $General->arr_general['avatars'] != 0 ) {?>
                                        <th class="text-right"><img class="rounded-circle" id="<?php if ( $General->arr_general['avatars'] == 1){ echo con_steam32to64( $players[ $p ]['steamid'] );} ?>" data-src="
                                    <?php if ( $General->arr_general['avatars'] == 1){ echo $General->getAvatar( con_steam32to64($players[ $p ]['steamid']), 2);
                                            } elseif( $General->arr_general['avatars'] == 2) {
                                                echo 'storage/cache/img/avatars_random/' . rand(1,30) . '_xs.jpg';
                                            }?>"></th>
                                    <?php }?>
                                    <th class="text-left"><?php echo $players[ $p ]['name']?></th>
                                    <th class="text-center"><?php echo $players[ $p ]['kills']?></th>
                                    <th class="text-center"><?php echo $players[ $p ]['deaths']?></th>
                                </tr>
                            <?php endif;
                        endfor;?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="match_block_down">
                <div class="match_block_table">
                    <table class="table table-hover demo">
                        <thead>
                        <tr class="tTeamHeaderBg">
                            <th class="text-right"></th>
                            <th class="text-left">Игрок</th>
                            <th class="text-center">Убийства</th>
                            <th class="text-center">Смерти</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php for ( $p = 0; $p < $c_players; $p++ ):
                            if( $players[ $p ]['team'] == 'TERRORIST'):
                                $General->get_js_relevance_avatar( $players[ $p ]['steamid'] );?>
                                <tr class="pointer tPlayerBg"
                                    onclick="location.href = '<?php echo $General->arr_general['site'] ?>?page=profiles&profile=<?php echo $players[ $p ]['steamid']?>&search=1';">
                                    <?php if( $General->arr_general['avatars'] != 0 ) {?>
                                        <th class="text-right"><img class="rounded-circle" id="<?php if ( $General->arr_general['avatars'] == 1){ echo con_steam32to64( $players[ $p ]['steamid'] );} ?>" data-src="
                                    <?php if ( $General->arr_general['avatars'] == 1){ echo $General->getAvatar( con_steam32to64($players[ $p ]['steamid']), 2);
                                            } elseif( $General->arr_general['avatars'] == 2) {
                                                echo 'storage/cache/img/avatars_random/' . rand(1,30) . '_xs.jpg';
                                            }?>"></th>
                                    <?php }?>
                                    <th class="text-left"><?php echo $players[ $p ]['name']?></th>
                                    <th class="text-center"><?php echo $players[ $p ]['kills']?></th>
                                    <th class="text-center"><?php echo $players[ $p ]['deaths']?></th>
                                </tr>
                            <?php endif;
                        endfor;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
            <div class="log_block">
                <div class="log_block_pool">
                    <?php for ($l = 0; $l < $match_log_size; $l++):?>
                        <?php if( ! empty( $match_log[$l]['act'] ) && $match_log[$l]['act'] == 'quit' ):?>
                            <div class="log_block_line">
                                <div class="log_block_line_text log_block_line_text_quit"><?php echo action_text_clear( action_text_trim( $match_log[$l]['name'], 17 ) )?> quit the game</div>
                            </div>
                        <?php endif;?>
                        <?php if( ! empty( $match_log[$l]['act'] ) && $match_log[$l]['act'] == 'join' ):?>
                            <div class="log_block_line">
                                <div class="log_block_line_text log_block_line_text_join"><?php echo action_text_clear( action_text_trim( $match_log[$l]['name'], 17 ) )?> joined the game</div>
                            </div>
                        <?php endif;?>
                        <?php if( ! empty( $match_log[$l]['act'] ) && $match_log[$l]['act'] == 'Round_Start' ):?>
                            <div class="log_block_line log_block_line_margin_bottom">
                                <div class="log_block_line_text log_block_line_default_act">Round started</div>
                            </div>
                        <?php endif;?>
                        <?php if( ! empty( $match_log[$l]['act'] ) && $match_log[$l]['act'] == 'say' ):?>
                            <div class="log_block_line">
                                <div class="log_block_line_text log_block_line_text_quit"><?php echo action_text_clear( action_text_trim( $match_log[$l]['name'], 17 ) )?> say: <?php echo $match_log[$l]['say']?></div>
                            </div>
                        <?php endif;?>
                        <?php if( ! empty( $match_log[$l]['type'] ) && $match_log[$l]['type'] == 'killed' ):?>
                            <div class="log_block_line">
                                <div class="log_block_line_text log_block_line_kill"><div class="log_block_line_text_<?php echo $match_log[$l]['killer_team']?>"><?php echo action_text_clear( action_text_trim( $match_log[$l]['killer_name'], 17 ) )?></div> <?php $General->get_icon( 'custom', 'weapon_' . $match_log[$l]['killer_weapon'], 'weapons' )?> <?php $match_log[$l]['killer_type_kill'] != '' && $General->get_icon( 'custom', $match_log[$l]['killer_type_kill'], 'global' )?> <div class="log_block_line_text_<?php echo $match_log[$l]['killed_team']?>"><?php echo action_text_clear( action_text_trim( $match_log[$l]['killed_name'], 17 ) )?></div></div>
                            </div>
                        <?php endif;?>
                        <?php if( ! empty( $match_log[$l]['act'] ) && $match_log[$l]['act'] == 'Round_End' ):?>
                            <div class="log_block_line">
                                <div class="log_block_line_text log_block_line_team_win_<?php echo $match_log[$l]['team_win']?>">Round over - Winner: <div class="log_block_line_text_<?php echo $match_log[$l]['team_win']?>"><?php $match_log[$l]['team_win'] == 'CT' ? print 'CT' : print 'T'?></div>(<div class="log_block_line_text_TERRORIST"><?php echo $match_log[$l]['TERRORIST_scored']?></div> - <div class="log_block_line_text_CT"><?php echo $match_log[$l]['CT_scored']?></div>) - <div class="log_block_line_text_<?php echo $match_log[$l]['team_win']?>"><?php echo $endreasons[ $match_log[ $l ]['win_type'] ]?></div></div>
                            </div>
                        <?php endif;?>
                        <?php if( ! empty( $match_log[$l]['act'] ) && $match_log[$l]['act'] == 'Game Over' ):
                            if( $match_log[$l]['match_score_ct'] > $match_log[$l]['match_score_t'] ) {
                                $winner = 'CT';
                                $_winner = 'CT';
                            }

                            if( $match_log[$l]['match_score_t'] > $match_log[$l]['match_score_ct'] ) {
                                $winner = 'TERRORIST';
                                $_winner = 'T';
                            }

                            if( $match_log[$l]['match_score_t'] == $match_log[$l]['match_score_ct'] ) {
                                $winner = 'DRAW';
                                $_winner = 'draw';
                            }

                            ?>
                            <div class="log_block_line">
                                <div class="log_block_line_text log_block_line_team_win_<?php echo $winner?>">Game over - Winner: <div class="log_block_line_text_<?php echo $winner?>"></div><?php echo $_winner?>(<div class="log_block_line_text_TERRORIST"><?php echo $match_log[$l]['match_score_t']?></div> - <div class="log_block_line_text_CT"><?php echo $match_log[$l]['match_score_ct']?></div>)</div>
                            </div>
                        <?php endif;?>
                    <?php endfor;?>
                </div>
        </div>
        <div class="match_block_log_download"><a class="btn" href="app/modules/module_page_demos/temp/demos/<?php echo $match_id ?>.zip">Скачать демо матча</a></div>

    </div>
</div>