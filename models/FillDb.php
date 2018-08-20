<?php 

namespace app\models;

use app\models\DbConnection as DbConnection;
require_once('../autoloader.php');

class FillDb extends DbConnection
{

	private $regions = [
		['Санкт-Петербург', '2'],
		['Уфа', '4'],
		['Нижний Новгород', '1'],
		['Владимир', '1'],
		['Кострома', '3'],
		['Екатеринбург', '6'],
		['Ковров', '7'],
		['Воронеж', '5'],
		['Самара', '6'],
		['Астрахань', '9'],
	];

	private $couriers = [
		['Петров И.Н.'],
		['Иванов Л.Г.'],		
		['Сидоров Т.Е.'],
		['Яковлев И.К.'],				
		['Копенкин Р.Е.'],
		['Николаев А.Г.'],				
		['Сергеев А.К.'],
		['Владимиров А.К.'],
		['Клубнев В.А.'],		
		['Петренко А.П.'],
		['Васильев В.Е.'],
		['Радченко А.Е.'],
	];

	/**
	 * Method for filling in table 'region'
	*/ 
	public function fillRegions()
	{
		$sql = "INSERT INTO region (title, time) VALUES ";		
		
		foreach ($this->regions as $key => $region) {
			if ($key !== (count($this->regions) - 1)) {
				$sql .= "('" . $region[0] . "', '" . $region[1] . "'), ";
			} else {
				$sql .= "('" . $region[0] . "', '" . $region[1] . "'); ";	
			}	
		}		
		
		if ($this->connect()->query($sql)) {
			return true;
		}
		return false;
	}

	/**
	 * Method for filling in table 'courier'
	*/
	public function fillCouriers()
	{
		$sql = "INSERT INTO courier (name) VALUES ";		
		
		foreach ($this->couriers as $key => $courier) {
			if ($key !== (count($this->couriers) - 1)) {
				$sql .= "('" . $courier[0] . "'), ";
			} else {
				$sql .= "('" . $courier[0] . "'); ";	
			}	
		}	

		if ($this->connect()->query($sql)) {
			return true;
		}
		return false;
	}

	/**
	 * Method for filling in table 'ride'
	*/
	public function fillRides()
	{
		$sql = "INSERT INTO ride (region_id, courier_id, dateDeparture, dateArrival) VALUES ";
			
		foreach ($this->couriers as $key => $courier) {

			/**
			 * 01.06.2018 00:00:00
			*/
			$dateDeparture = 1527811200;

			/**
			 * 17.08.2018 00:00:00
			*/  
			$presentDate = 1534464000;
			
			$sqlCourier = "SELECT id FROM courier WHERE courier.name = '" . $courier[0]. "';";
			$resultCourier = $this->connect()->query($sqlCourier);
			
			if (!empty($resultCourier)) {					
				$courierId = $resultCourier->fetch_assoc()['id'];					
			} else {
				print_r('Couldn\'t get the courier\'s ID.');
			}

			/**
			 * Departure date of the first ride since 01.06.2018 00:00:00 (1527811200)
			*/  
			$dateDeparture = 1527811200 + 60 * 60 * 24 * rand(0, 7);
			$i = 0;			

			while ($dateDeparture < 1534464000) {

				$region = $this->regions[rand(0,9)];					
				$sqlRegion = "SELECT id FROM region WHERE region.title = '" . trim($region[0]) . "';";
				
				$resultRegion = $this->connect()->query($sqlRegion);

				if (!empty($resultRegion)) {					
					$regionId = $resultRegion->fetch_assoc()['id'];					
				} else {
					print_r('Couldn\'t get the region\'s ID.');
				}		
				
				$dateArrival = $dateDeparture + 60 * 60 * 24 * $region[1];

				/**
				 * Right syntax for MYSQL
			    */  
				if ($key == 0 && $i == 0) {
					$sql .= "('" . $regionId . "', '" . $courierId . "', '" . $dateDeparture . "', '" . $dateArrival . "')";
				} else {
					$sql .= ", ('" . $regionId . "', '" . $courierId . "', '" . $dateDeparture . "', '" . $dateArrival . "')";
				}
				
				/**
				 * Time needed for a courier to get back
				*/ 
				$dateDeparture = $dateDeparture + 2 * 60 * 60 * 24 * $region[1];
				$i++;
			}	
		}	

		$sql .= ";";
	
		if ($this->connect()->query($sql)) {
			return true;
		}
		return false; 
	}

}

$model = new FillDb();

if ($model->fillRegions() && $model->fillCouriers()) {
	if ($model->fillRides()) {
		print_r("Database is filled successfully.");
	}
}