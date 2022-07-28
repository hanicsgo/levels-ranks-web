<?php if( ! empty( $_SESSION['steamid'])):?>
<?php else:?>
<div class="row">
	<div class="col-md-12">		
		<div class="card">
				<a class="titletext">Welcome to ANH EM YÊN BÁI COMMUNITY SERVER</a>
				<div class="perclass">
					<div class="center-welcomers">
					<a class="vtoclass" href="https://discord.io/anhemyenbai" data-tooltip="Discord" data-tooltip-location="up"><i class="zmdi zmdi-comment-text-alt kas"></i></a>
					<a class="vtoclass" href="shop" data-tooltip="Shop" data-tooltip-location="up"><i class="zmdi zmdi-shopping-cart-plus kas"></i></a>	 
					<a class="vtoclass" href="?auth=login" data-tooltip="Login" data-tooltip-location="up"><i class="zmdi zmdi-account-circle kas"></i></a> 
					<a class="vtoclass" href="toppoints" data-tooltip="TopPoints" data-tooltip-location="up"><i class="zmdi zmdi-chart kas"></i></a>	
					<a class="vtoclass" href="rules" data-tooltip="Rules" data-tooltip-location="up"><i class="zmdi zmdi-shield-security kas"></i></a>	 
					</div>
				</div>
				<div class="gg">
					 <div class="button-welcomer">
					 <a href="?auth=login"><p class="btnText">AUTHORIZATION</p>
    					  <div class="btnTwo">
      						<p class="btnText2"><i class="zmdi zmdi-steam-square"></i></p>
    					  </div></a>
  					 </div>
				</div>
		</div>
	</div> 
</div>
<?php endif?>