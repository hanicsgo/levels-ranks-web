<?php
/**
 * @author SAPSAN 隼 #3604
 *
 * @link https://hlmod.ru/members/sapsan.83356/
 * @link https://github.com/M0st1ce
 *
 * @license GNU General Public License Version 3
 */
if( IN_LR != true ) { header('Location: ' . $General->arr_general['site']); exit;}

if(  isset( $_SESSION['user_admin'] ) ):?>
<aside class="sidebar-right unshow">
    <section class="sidebar">
        <div class="user-sidebar-right-block">
            <div class="info">
                <div class="details">
                    <div class="admin_type"><?php echo $Translate->get_translate_module_phrase( 'module_page_adminpanel','_Chief_admin')?></div>
                    <div class="admin_rights"><?php echo $Translate->get_translate_module_phrase( 'module_page_adminpanel','_All_access_rights')?></div>
                </div>
            </div>
        </div>
        <div class="card menu">
            <ul class="nav">
                <li <?php get_section( 'section', 'admin' ) == 'admin' && print 'class="table-active"'?> onclick="location.href = '<?php echo set_url_section(get_url( 2 ),'section','admin')?>';">
                    <a><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_CaseSettings')?></a>
                </li>
                <li <?php get_section( 'section', 'admin' ) == 'cases_list' && print 'class="table-active"'?> onclick="location.href = '<?php echo set_url_section(get_url( 2 ),'section','cases_list')?>';">
                    <a><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_OpenCaseList')?></a>
                </li>
                <li <?php get_section( 'section', 'admin' ) == 'updates' && print 'class="table-active"'?> onclick="location.href = '<?php echo set_url_section(get_url( 2 ),'section','updates')?>';">
                    <a><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Updates')?></a>
                </li>
            </ul>
        </div>
    </section>
</aside>
<?php endif;
if(isset($_GET['section'])):?>
<div class="row">
    <?php switch ( $_GET['section'] ) {
        case  'admin':
            require MODULES . 'module_page_open_case'. '/includes/admin.php';
        break;
        case  'case':
            require MODULES . 'module_page_open_case'. '/includes/case.php';
        break;
        case  'cases_list':
            require MODULES . 'module_page_open_case'. '/includes/cases_list.php';
        break;
    }?>
</div>
<?php else:?>
<script type="text/javascript">setTimeout(function(){live_load();}, 300);</script>
<div class="cases-main-block">
    <div class="live-list-wrap"><ul class="live-list" id="live_content"></ul></div>
    <?php if(get_section( 'case','' )): $subjects = $CASES->getCaseSubjects($_GET['case']);$price = $CASES->getPriceCase($_GET['case']);?>
        <?php if(empty($subjects)):?>
            <script type="text/javascript">window.location.replace("?page=cases");</script>
        <?php else:?>
            <h2><?=$price['case_name']?></h2>
            <?php if(empty($_SESSION['steamid32'])):?>
                    <p style="text-align: center;color: #ffc607;text-shadow: 0 0 8px #ff7202;font-size: 18px;margin-top: 50px;"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_AuthInfo')?></p>
                    <a href="?auth=login"><button class="open-case" ><i class="zmdi zmdi-steam-square zmdi-hc-fw"></i> <?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Auth')?></button></a>
            <?php else:?>
                <a href="?page=cases" class="icon-back-to-cases" title="Назад"></a><a href="?page=cases&wins" class="icon-open-cases" title="<?php echo $Translate->get_translate_module_phrase('module_page_open_case','_MyWins')?>"></a>
                <div class="roulette">
                    <div id="sound-point" class="sound-on" onclick="roulette_sound();"></div>
                    <div class="roulette-slider">
                        <div class="r-side"></div>
                        <div class="r-side2"></div>
                        <div class="top-arr"></div>
                        <div class="bottom-arr"></div>
                        <div class="roulette-area">
                            <div id="roulette"> 
                                <div class="roulette-inner" style="transform: translateX(-190px);"></div>
                            </div>  
                        </div>
                    </div>
                </div>
                <?php if($price['case_type'] == 2):$free = $CASES->getTimeFreeOpen($_SESSION['steamid32'], $price['id']);
                    $openDate = $price['case_price']+$free['date'];
                    if($openDate > time()):?>
                        <script type="text/javascript">
                            setTimeout(function() {
                                $(".Timer").eTimer({
                                    etType: 0, etDate: "<?=date('d.m.Y.H.i',$openDate)?>", etTitleText: "", etTitleSize: 10, etShowSign: "<?php if(!empty($_GET['language'])): echo $_GET['language'];else: echo $General->arr_general['language'];endif;?>", etSep: ":", etFontFamily: "Arial Black", etTextColor: "white", etPaddingTB: 0, etPaddingLR: 0, etBackground: "transparent", etBorderSize: 0, etBorderRadius: 0, etBorderColor: "white", etShadow: " 0px 0px 0px 0px #333333", etLastUnit: 4, etNumberFontFamily: "Arial Black", etNumberSize: 32, etNumberColor: "white", etNumberPaddingTB: 0, etNumberPaddingLR: 3, etNumberBackground: "var(--span-color)", etNumberBorderSize: 0, etNumberBorderRadius: 0, etNumberBorderColor: "white", etNumberShadow: "inset 0px 0px 9px 0px rgba(0, 0, 0, 0.5)"
                                });
                            },300);
                        </script>
                        <h3 style="text-transform: uppercase; font-size: 22px; color: #fff;"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_THROUGH')?></h3>
                        <div class="Timer"></div>
                    <?php else:?>
                    	<div id="open-sum"> <?php if($price['case_type'] == 1): echo $Translate->get_translate_module_phrase('module_page_open_case','_AmountCourse').' '.$price['case_price']; else: echo "FREE";endif?></div>
                        <button style="margin-left: 40px;" id="open-case" class="open-case" onclick="open_case(<?=$_GET['case']?>);"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Open')?></button>
                        <button id="open-case-fast" class="open-case" onclick="open_case_fast(<?=$_GET['case']?>);"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_OpenFast')?></button>
                    <?php endif?>
                <?php else:?>
                		<div id="open-sum"> <?php if($price['case_type'] == 1): echo $Translate->get_translate_module_phrase('module_page_open_case','_AmountCourse').' '.$price['case_price']; else: echo "FREE";endif?></div>
                        <button style="margin-left: 40px;" id="open-case" class="open-case" onclick="open_case(<?=$_GET['case']?>);"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Open')?></button>
                    <button id="open-case-fast" class="open-case" onclick="open_case_fast(<?=$_GET['case']?>);"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_OpenFast')?></button>
                <?php endif?>
            <?php endif?>
            <div id="case-subjects">
            <br>
            <br>
            <h2><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_ContentCase')?></h2>
            <div id="subjects">
            <?php foreach($subjects as $key):?>
                <div class="subject-block <?=$key['subject_class']?>">
                    <div class="b-top"></div>
                    <div class="b-bottom"></div>
                    <div class="b-left"></div>
                    <div class="b-right"></div>
                    <div class="subject-services">
                        <div class="subject-fix">
                            <div class="subject-image-wrapper">
                                <img width="100" class="subject-image" src="<?=$key['subject_img']?>" alt="<?=$key['subject_name']?> <?=$key['subject_desc']?>">
                            </div>
                            <div class="subject">
                                <span><?=$key['subject_name']?></span>
                                <span><?=$key['subject_desc']?></span>
                            </div>
                        </div>
                    </div>
                </div>
        <?php endforeach?>
        </div>
    <script type="text/javascript" src="<?php echo MODULES?>module_page_open_case/assets/js/sweetalert2.all.js"></script>
    <script>setTimeout(function(){
            let script = document.createElement('script');
            script.src = "<?php echo MODULES?>module_page_open_case/assets/js/roulette.js";
            document.body.append(script);
             setTimeout(function(){load_roulette(<?=$_GET['case']?>)}, 200);
    }, 300);</script>
    <?php endif?>
    <?php elseif(isset($_GET['wins']) && isset($_SESSION['steamid32'])):$wins = $CASES->getWins();?>
    <a href="?page=cases" class="icon-back-to-cases" title="Назад"></a>
    <h2><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_MyWins')?></h2>
     <div id="wins">
            <?php foreach($wins as $key):?>
                <div class="subject-block <?=$key['subject_style']?>">
                    <?php if($key['sale']):?><div class="subject-price"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_AmountCourse')?><?=$key['sale']?></div><?php endif;?>
                    <div class="b-top"></div>
                    <div class="b-bottom"></div>
                    <div class="b-left"></div>
                    <div class="b-right"></div>
                    <div class="subject-services">
                        <div class="subject-fix">
                            <div class="subject-image-wrapper">
                                <img width="100" class="subject-image" src="<?=$key['subject_img']?>" alt="<?=$key['subject_name']?> <?=$key['subject_desc']?>">
                            </div>
                            <div class="subject" style="position: absolute;bottom: 0px;">
                                        <span><?=$key['subject_name']?></span>
                                        <span><?=$key['subject_desc']?></span>
                            </div>
                        </div>
                    </div>
                    <?if(empty($key['up']) && empty($key['sell'])):?><div class="subject-hover" id="rem<?=$key['id']?>"><a onclick="pick_up_wins(<?=$key['id']?>)"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Activate')?></a><a onclick="to_sale_wins(<?=$key['id']?>)"><?php echo $Translate->get_translate_module_phrase('module_page_open_case','_Sell')?> <?php echo $Translate->get_translate_module_phrase('module_page_open_case','_AmountCourse').$key['sale']?></a> </div><?php endif?>
                    <?if(empty($key['up']) && !empty($key['sell'])):?><div class="subject-hover"><a>ПРОДАНО</a></div><?php endif?>
                    <?if(!empty($key['up']) && empty($key['sell'])):?><div class="subject-hover"><a onclick="my_wins(<?=$key['id']?>)">СМОТРЕТЬ ВЫИГРЫШ</a></div><?php endif?>
                </div>
        <?php endforeach?>
        </div>
        <script type="text/javascript" src="<?php echo MODULES?>module_page_open_case/assets/js/sweetalert2.all.js"></script>
    <?php else: $cases = $CASES->getCases();?>
            <div id="cases">
                <?php foreach($cases as $key):$opens = $CASES->getOpens($key['id']);?>
                <div class="case-block">
                    <a class="case-link" href="<?php echo set_url_section(get_url(2), 'case', $key['id']) ?>">
                        <span class="case-img-wrap">
                            <img class="case-img" src="<?=$key['case_img']?>">
                        </span>
                        <span class="case-span-wrap">
                            <?php if($key['case_type'] == 2):
                                if(isset($_SESSION['steamid32'])):
                                    $free = $CASES->getTimeFreeOpen($_SESSION['steamid32'], $key['id']);
                                    $openDate = $key['case_price'] + $free['date'];
                                    if($openDate > time()):?>
                                        <script type="text/javascript">
                                            setTimeout(function() {
                                                $(".eTimer<?=$key['id']?>").eTimer({
                                                    etType: 0, etDate: "<?=date('d.m.Y.H.i',$openDate)?>", etTitleText: "", etTitleSize: 10, etShowSign: "<?php if(!empty($_GET['language'])): echo $_GET['language'];else: echo $General->arr_general['language'];endif;?>", etSep: ":", etFontFamily: "Arial Black", etTextColor: "white", etPaddingTB: 0, etPaddingLR: 0, etBackground: "transparent", etBorderSize: 0, etBorderRadius: 0, etBorderColor: "white", etShadow: " 0px 0px 0px 0px #333333", etLastUnit: 4, etNumberFontFamily: "Arial Black", etNumberSize: 20, etNumberColor: "white", etNumberPaddingTB: 0, etNumberPaddingLR: 3, etNumberBackground: "var(--span-color)", etNumberBorderSize: 0, etNumberBorderRadius: 0, etNumberBorderColor: "white", etNumberShadow: "inset 0px 0px 9px 0px rgba(0, 0, 0, 0.5)"
                                                });
                                            }, 300);
                                        </script>
                                        <div class="eTimer<?=$key['id']?>" style="top: -11px;position: relative;"></div>
                                        <span class="case-name" style="top: -13px;position: relative;"><?=$key['case_name']?></span>
                                        <span class="case-open" style="top: -13px;position: relative;"><?=$opens?></span>
                                    <?php else:?>
                                        <span class="case-price">FREE</span>
                                        <span class="case-name"><?=$key['case_name']?></span>
                                        <span class="case-open"><?=$opens?></span>
                                    <?php endif?>
                                <?php else:?>
                                    <span class="case-price">FREE</span>
                                    <span class="case-name"><?=$key['case_name']?></span>
                                <?php endif?>
                            <?php else:?>
                                <span class="case-price"><?=$key['case_price']?> ₽</span>
                                <span class="case-name"><?=$key['case_name']?></span>
                                <span class="case-open"><?=$opens?></span>
                            <?php endif?>
                        </span>
                        <span></span>
                    </a>
                </div>
                <?php endforeach?>
            </div>
<?php endif?>
</div>
<?php endif?>
<style type="text/css">
    .align-center
    {
        margin: 25px auto;
        padding: 25px;
        border: 2px solid var(--span-color);
        box-shadow: var(--span-color-back) 5px 5px;
        border-radius: 2px;
    }
    .input-form
    {
        margin-bottom: 10px
    }
</style>
</div>