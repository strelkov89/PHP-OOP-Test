<?php

namespace app\views;

use app\models\Couriers as Couriers;

?>

<div class="form-container">
	<h2>Добавить новую поездку</h2>
	<form class="js-form" method="post" action="controllers/RideController.php">				
		<div id="js_message_box" class="alert fade in">				    		
    		<a href="#" class="close" onclick="closeBox(); return false;">&times;</a>
			<span></span>
		</div>
		<input type="hidden" name="actionFunc" value="actionSave">
	    <div class="col-xs-12 col-sm-12 form-group">
	    	<input type="text" name="region" placeholder="Регион" class="form-control" />
    	</div>
	    <div class="col-xs-12 col-sm-12 form-group">
	    	<input type="text" name="dateDeparture" placeholder="Дата выезда из Москвы" class="form-control date-departure" />
    	</div>
    	<div class="col-xs-12 col-sm-12 form-group">
	    	<select name="courier" placeholder="ФИО курьера" class="form-control">
	    		<option value="" hidden>ФИО курьера</option>
	    		<?php 
	    		$modelCouriers = new Couriers();					    		
	    		if (!empty($modelCouriers->getCouriers())):
					foreach($modelCouriers->getCouriers() as $unCourier): ?>						
						<option value="<?= $unCourier['id'] ?>"><?= $unCourier['name'] ?></option>
					<?php endforeach; ?>
				<?php endif; ?>								
			</select>
		</div>				    	
	    <div class="col-xs-12 col-sm-12 form-group">
	    	<input type="text" name="dateArrival" placeholder="Дата прибытия в регион" class="form-control" readonly />
    	</div>				    
	    <div class="col-xs-12 col-sm-12 form-group div-btn-container">
	    	<input class="btn btn-primary" type="submit" value="Добавить" />					    
    	</div>				    	
	</form>
</div>