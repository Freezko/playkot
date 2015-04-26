<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class PlayersSendEmailCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'players:send_email';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Заполняет очередь для отправки писем пользователям';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire(){
		//получаем шаблон
		$template = $this->argument('template');

		if (!View::exists($template)){
			$this->error('Такого шаблона не существует!');
		}

		//берем по 10 000 пользлователей из БД отсортированных по имени
		Players::orderBy('first_name')->chunk(10000, function($players, $name='', $ids=[]) use($template){
			foreach($players as $player){
				//если встретили новое имя или у текущего имени 100 ИД
				if($name != $player->first_name || count($ids) == 100){

					//push job
					if(!empty($ids)) {
						$ids = implode(',',$ids);
						Queue::push('SendNotification', compact('ids','template', 'name'));
					}

					//обнуляем
					$name = $player->first_name;
					$ids = [];

				}

				$ids[] = $player->id;

			}
		});
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('template', InputArgument::REQUIRED, 'Шаблон письма'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			//array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
