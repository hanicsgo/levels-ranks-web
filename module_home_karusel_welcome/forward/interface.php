<div class="slideshow-container">
  <?php $_Sliders = scandir("app/modules/module_home_karusel_welcome/img/");
    $_Json_Desc = json_decode(file_get_contents("app/modules/module_home_karusel_welcome/slides.json"), true);
    $_Carusel = 1;
    foreach ($_Sliders as $JPG): if(substr($JPG, 0, 1) == '.') continue; $_JSN = explode('.', $JPG)[0]; $_Carusel++;?>

      <div class="mySlides fade col-12" style="background-image: url(app/modules/module_home_karusel_welcome/img/<?=$JPG?>);">
        <?php if(isset($_Json_Desc[$_JSN])):
            if(!empty($_Json_Desc[$_JSN]['button'])):?>
              <div class="slide-info">
                <h5 class="knopka"><i class="zmdi zmdi-help-outline zmdi-hc-fw"></i>
                <a target="_self" href=<?=$_Json_Desc[$_JSN]['href']?>><?=$_Json_Desc[$_JSN]['button']?></a>
			  </div>
            <?php endif;
            if(!empty($_Json_Desc[$_JSN]['home'])):?>
                <div class="	slide-text1"><?=$_Json_Desc[$_JSN]['home']?></div>
		    <?php endif;
		    if(!empty($_Json_Desc[$_JSN]['text'])):?>
                <div class="slide-text"><?=$_Json_Desc[$_JSN]['text']?></div>				
             <?php endif;
      endif;?>
      </div>
  <?php endforeach;?>
  <a class="next" onclick="plusSlides(1)">&#10095</a>

    <?php for ($i=1; $i < $_Carusel; $i++):?>
      <span class="dot" onclick="currentSlide(<?=$i?>)"></span>
    <?php endfor;?>
</div>
</div>