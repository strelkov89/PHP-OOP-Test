<?php

namespace app\controllers;

use app\models\Ride as Ride;
require_once('../autoloader.php');

/**
 * Helper for getting necessary action in controller (instead of class Router)
*/
if (isset($_POST['actionFunc']))
{
    $action_func = $_POST['actionFunc'];
    $controller = new RideController;

    if (function_exists($controller->$action_func()))
    {
        $controller->$action_func();
    }   
}

class RideController 
{
	/**
	 * Helper for returning info from actions 
	*/
	private function response($status, $message)
	{
		$response = [
			'success' => $status,
			'message' => $message,			
		];
		echo json_encode($response);
	}

	/**
	 * Action for saving ride
	*/
	public function actionSave() 
	{
		$model = new Ride($_POST);
		if ($model->validateEmpty()) {
			if ($model->validateDate()) {
				if ($model->validateRegion()) {			
					if ($model->validateCourier()) {					
						if($model->saveRide()) {
							$this->response(true, 'Данные успешно сохранены.');

						} else {
							$this->response(false, 'Не удалось добавить поездку.');
						}

					} else {
						$this->response(false, 'Этот курьер занят. Пожалуйста, выберите другого курьера или измените дату выезда.');					
					}

				} else {
					$this->response(false, 'Регион не найден. Пожалуйста, проверьте название региона.');				
				}

			} else {
				$this->response(false, 'Пожалуйста, проверьте дату выезда, выезд возможен начиная с сегодняшнего дня, формат даты должен быть "ДД ММ ГГГГ".');
			}

		} else {
			$this->response(false, 'Пожалуйста, заполните все поля формы.');			
		}
	}

	/**
	 * Action for getting arrival date by ajax
	*/
	public function actionGetArrivalTime()
	{
		$model = new Ride($_POST);
		if ($model->getArrivalTime()) {
			list($status, $message) = $model->getArrivalTime();
			$this->response($status, $message);
		}
		return false;
	}

	/**
	 * Action for getting info about ride by ajax
	*/
	public function actionGetRidesModal()
	{
		$model = new Ride($_POST);
		if ($model->getRidesModal()) {
			$this->response(true, $model->getRidesModal());
		} else {
			$this->response(false, 'Error');
		}
	}

}