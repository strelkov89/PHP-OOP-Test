<?php

namespace app\models;

use app\models\DbConnection as DbConnection;

class Couriers extends DbConnection
{

	public function getCouriers() 
	{	
		$sqlCourier = "SELECT id, name FROM courier;";

		$resultCourier = $this->connect()->query($sqlCourier);

		if (!empty($resultCourier)) {

			$numRows = $resultCourier->num_rows;
			if ($numRows > 0) {
				while ($row = $resultCourier->fetch_assoc()) {
					$data[] = $row;
				}
				return $data;
			}
			
		}
		return false;
	}

}