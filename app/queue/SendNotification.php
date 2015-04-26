<?php

class SendNotification implements \Kodeks\PhpResque\Lib\ResqueJobInterface {

	public $start = false;
	public $id;
	private $redis;

	public function setUp(){

		$this->redis = Redis::connection();

		$params = [
			'ids',
			'template',
			'name'
		];

		foreach($params as $param){
			if(empty($this->args[$param])){
				throw new Exception('Не передан обязательный параметр ' . $param);
			}
		}

	}

	/**
	 * Исполнение задачи
	 * @throws ErrorException
	 */
	public function perform(){

		$run = false;
		while(!$run) {
			//получаем из редиса значение
			$times = $this->redis->incr('players_notification');

			//если нужно ставим 1 секунду срок годности значения
			if($times == 1){
				$this->redis->EXPIRE('players_notification', 1);
			}

			//засыпаем если в течении 1 секунды уже было 3 запроса
			if (!empty($times) && $times > 3){
				if($times > 100000){
					$this->redis->EXPIRE('players_notification', 1); //prevent deadlock
				}
				//Log::info('sleeping');
				usleep(500000); //ждем пол секунды
			}else{
				$run = true;
			}
		}
		//получаем шаблон и компилируем его.
		$text = View::make($this->args['template'])->with('name', $this->args['name']);

		//отправляем пользователю нотификацию
		VkontakteApi::sendNotification($this->args['ids'], $text);

	}
}
