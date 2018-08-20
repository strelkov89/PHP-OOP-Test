<?php

namespace app\views;

use app\models\Ride as Ride;

$months = [	
	'6' => ['title' => 'Июнь', 'days' => '30'],
	'7' => ['title' => 'Июль', 'days' => '31'],
	'8' => ['title' => 'Август', 'days' => '31'],
	'9' => ['title' => 'Сентябрь', 'days' => '30'],
	'10' => ['title' => 'Октябрь', 'days' => '31'],	
];

$dayTitles = ['ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ', 'ВС'];

$model = new Ride(); 
$rides = (!empty($model->getAllRides())) ? $model->getAllRides() : NULL;
if ($rides) {
	foreach ($rides as $ride):
		$ridesDates[$ride['id']] = date("j-n-Y", $ride['dateDeparture']);		
	endforeach;		
}			

?>

<!-- Generating Bootstrap carousel -->
<div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="false"> 
    
    <div class="carousel-inner">
  	<?php  foreach ($months as $key => $month): ?>

	    <?php $i = 1; ?>

	    <div class="item <?= ($key == '6') ? 'active' : '' ?>">
	    	<h3><?= $month['title'] ?> 2018</h3>		    	
	    	
	    	<?php while ($i <= $month['days']): ?> 
	    		
	    		<?php if ($i == 1): 
	    			/* Generating Day titles */
	    			foreach ($dayTitles as $dayTitle): 
	    				echo "<div class=\"day no-pointer\"><span>$dayTitle</span></div>";
	    			endforeach;

	    			$date = '2018-' . $key . '-01';
					$dayOfWeek = strftime("%a", strtotime($date));					
					$blankDiv = '<div class="day blank"><span>&nbsp;</span></div>';

					/* Generating blank offsets */
					switch ($dayOfWeek):
					    case "Mon":					        
					        break;
					    case "Tue":
					        echo str_repeat($blankDiv, 1);
					        break;
					    case "Wed":
					        echo str_repeat($blankDiv, 2);
					        break;
				        case "Thu":
					        echo str_repeat($blankDiv, 3);
					        break;
				        case "Fri":
					        echo str_repeat($blankDiv, 4);
					        break;
				        case "Sat":
					        echo str_repeat($blankDiv, 5);
					        break;
				        case "Sun":
					        echo str_repeat($blankDiv, 6);
					        break;
					endswitch;

	    		?>
    			<?php endif; ?>

    			<!-- Generating classes for Days -->
    			<?php $classes = array_keys($ridesDates, $i."-".$key."-2018"); ?>   			

	    		<div class="day"><span id="<?= $i.'_'.$key.'_2018' ?>" class="span_day <?= (!empty($classes)) ? implode(' ', $classes) . ' blue' : '' ?>"><?= $i ?></span></div>
    		<?php $i++; endwhile; ?>	    		
	    </div>
	    
    <?php endforeach; ?>
    </div>  
  	<!-- Left, right carousel controls -->
    <a class="left carousel-control" href="#myCarousel" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#myCarousel" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right"></span>
        <span class="sr-only">Next</span>
    </a>
</div>

<!-- Modal window for calendar -->
<div id="myModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">	
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Поездки</h4>
			</div>
			<div class="modal-body">
				<p class="js-modal-p"></p>
			</div>      
		</div>

	</div>
</div>