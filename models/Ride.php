<?php

namespace app\models;

use app\models\DbConnection as DbConnection;

class Ride extends DbConnection 
{	
	public $region;
    public $courier;
    public $dateDeparture;
    public $dateArrival;
    public $classList;

	function __construct(Array $data = array())
	{
	    $this->region = isset($data['region']) ? $data['region'] : null;
	    $this->courier = isset($data['courier']) ? $data['courier'] : null;
	    $this->dateDeparture = isset($data['dateDeparture']) ? $data['dateDeparture'] : null;
	    $this->dateArrival = isset($data['dateArrival']) ? $data['dateArrival'] : null;

	    $this->classList = isset($data['classList']) ? $data['classList'] : null;
	}

	/**
	 * Method for getting all rides from the DB
	*/ 
	public function getAllRides() 
	{
		$sql = "SELECT * FROM ride";
		$results = $this->connect()->query($sql);
		
		if (!empty($results)) {
			$numRows = $results->num_rows;
			if ($numRows > 0) {
				while ($row = $results->fetch_assoc()) {
					$data[] = $row;
				}
				return $data;
			}
		}

	}

	/**
	 * Method for getting info about ride for Modal window (calendar)
	*/ 
	public function getRidesModal() 
	{
		if(!empty($this->classList)) {
			if (is_array($this->classList)) {
				foreach ($this->classList as $rideId) {
					$sql = "SELECT * FROM ride WHERE ride.id = '" . $rideId . "';";
					
					$result = $this->connect()->query($sql);
					
					if (!empty($result)) {
						$fetchPrev = $result->fetch_assoc();

						$sqlCourier = "SELECT courier.name FROM courier WHERE courier.id = '" . $fetchPrev['courier_id'] . "';";
						$resultCourier = $this->connect()->query($sqlCourier);

						if (!empty($resultCourier)) {
							$fetchPrevCourier = $resultCourier->fetch_assoc();

							$sqlRegion = "SELECT region.title, region.time FROM region WHERE region.id = '" . $fetchPrev['region_id'] . "';";
							$resultRegion = $this->connect()->query($sqlRegion);

							if (!empty($resultRegion)) {
								$fetchRegion = $resultRegion->fetch_assoc();

								$data[] = [
									'courier' => $fetchPrevCourier['name'],
									'region' => $fetchRegion['title'],
									'time' => $fetchRegion['time'],
									'dateDeparture' => date("d/m/Y", $fetchPrev['dateDeparture']),
									'dateArrival' => date("d/m/Y", $fetchPrev['dateArrival']),
								];								
							}
						}
					}
				}
				if (isset($data)) {
					return $data;
				}
				return false;

			} else {
				$rideId = trim($this->classList);
				$sql = "SELECT * FROM ride WHERE ride.id = '" . $rideId . "';";					
				$result = $this->connect()->query($sql);
					
				if (!empty($result)) {
					$fetchPrev = $result->fetch_assoc();

					$sqlCourier = "SELECT courier.name FROM courier WHERE courier.id = '" . $fetchPrev['courier_id'] . "';";
					$resultCourier = $this->connect()->query($sqlCourier);

					if (!empty($resultCourier)) {
						$fetchPrevCourier = $resultCourier->fetch_assoc();

						$sqlRegion = "SELECT region.title, region.time FROM region WHERE region.id = '" . $fetchPrev['region_id'] . "';";
						$resultRegion = $this->connect()->query($sqlRegion);

						if (!empty($resultRegion)) {
							$fetchRegion = $resultRegion->fetch_assoc();

							$data[0] = [
								'courier' => $fetchPrevCourier['name'],
								'region' => $fetchRegion['title'],
								'time' => $fetchRegion['time'],
								'dateDeparture' => date("d/m/Y", $fetchPrev['dateDeparture']),
								'dateArrival' => date("d/m/Y", $fetchPrev['dateArrival']),
							];

							return $data;
						}
					}
				}			
			}
		}
		return false; 
	}

	/**
	 * Method for checking field emptiness
	*/ 
	public function validateEmpty()
	{		
		return !empty($this->region) && !empty($this->courier) && !empty($this->dateDeparture);
	}

	/**
	 * Method for checking date format
	*/
	public function validateDate()
	{	
		list($d, $m, $y) = $this->dateHelper($this->dateDeparture);

		$today = date("d m Y");
		list($todayD, $todayM, $todayY) = $this->dateHelper($today);
		$today = mktime(0, 0, 0, $todayM, $todayD, $todayY);

		if (is_numeric($d) && is_numeric($m) && is_numeric($y)) {
			if (checkdate($m, $d, $y)) {
				$departureTime = mktime(0, 0, 0, $m, $d, $y);
				if ($departureTime >= $today) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Method for checking if a courier is busy
	*/
	public function validateCourier()
	{
		$courier_id = trim($this->courier);	

		$sql = "SELECT ride.region_id, region.time, ride.dateDeparture FROM ride INNER JOIN region ON ride.region_id = region.id WHERE ride.courier_id = '" . $courier_id . "' ORDER BY ride.dateDeparture DESC LIMIT 1;"; 

		$result = $this->connect()->query($sql);
		if (!empty($result)) {

			$fetchPrev = $result->fetch_assoc();
			$time = 2 * $fetchPrev['time'];

			$timeUnbusy = $fetchPrev['dateDeparture'] + $time * 60 * 60 * 24;

			list($necD, $necM, $necY) = $this->dateHelper($this->dateDeparture);
			$necessaryTime = mktime(0, 0, 0, $necM, $necD, $necY);

			if ($timeUnbusy <= $necessaryTime) {
				return true;
			}			
		}
		return false;

	}

	/**
	 * Method for checking if a region is in the DB
	*/
	public function validateRegion()
	{
		$region = trim($this->region);
		
		$sql = "SELECT * FROM region WHERE region.title = '" . $region . "';";

		$result = $this->connect()->query($sql);
		if (!empty($result)) {
			$fetchPrev = $result->fetch_assoc()['title'];
			if ($fetchPrev == $region) {
				return true;
			}
		}
		return false;		
		
	}

	/**
	 * Method for getting arrival time by ajax
	*/
	public function getArrivalTime()
	{
		if (!empty($this->region) && !empty($this->dateDeparture)) {
			if ($this->validateRegion()) {
				if ($this->validateDate()) {

					$sql = "SELECT * FROM region WHERE region.title = '" . trim($this->region) . "';";

					$result = $this->connect()->query($sql);
					if (!empty($result)) {
						$regionTime = $result->fetch_assoc()['time'];

						list($d, $m, $y) = $this->dateHelper($this->dateDeparture);
						$date = mktime(0, 0, 0, $m, $d, $y);						
						
						/**
						 * Getting arrival time
					    */ 
						$dateArrival = $date + $regionTime * 60 * 60 * 24;
						$response = date("d m Y", $dateArrival);

						if(!empty($response)) {
							return array(true, $response);
						} 
					}					
				}
			}
		}
		return array(false, "Error");		
	}	

	/**
	 * Method for saving ride
	*/
	public function saveRide()
	{
		
		$region = trim($this->region);
		
		$sql = "SELECT region.id, region.time FROM region WHERE region.title = '" . $region . "';";

		$result = $this->connect()->query($sql);
		if (!empty($result)) {
			$fetchPrev = $result->fetch_assoc();

			$regionId = $fetchPrev['id'];
			$regionTime = $fetchPrev['time'];

			list($d, $m, $y) = $this->dateHelper($this->dateDeparture);
			$date = mktime(0, 0, 0, $m, $d, $y);						
						
			$dateArrival = $date + $regionTime * 60 * 60 * 24;

			$sqlSave = "INSERT INTO ride (region_id, courier_id, dateDeparture, dateArrival)  VALUES ('" . $regionId . "', '" . $this->courier . "', '" . $date . "', '" . $dateArrival . "');";

			if ($this->connect()->query($sqlSave)) {
				return true;
			}
		}		

		return false;
		
	}

	/**
	 * Helper for formatting date
	*/
	private function dateHelper($date) 
	{
		$str = trim($date);

		$d = substr($str, 0, 2);
		$m = substr($str, 3, 2);
		$y = substr($str, 6, 4);

		return array($d, $m, $y);
	}
}