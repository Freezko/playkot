<?php


class PlayersTableSeeder extends Seeder {

	public function run(){

		$faker = Faker\Factory::create();
		$faker->seed(123); //фиксируем выдачу одинаковой

		for ($i = 0; $i < 100; $i++){
			$player = Players::create(array(
				'first_name' => $faker->firstName,
			));
		}
	}

}