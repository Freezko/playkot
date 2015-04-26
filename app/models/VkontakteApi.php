<?php

class VkontakteApi {

	public static function sendNotification($ids, $text){
		$ids_array = explode(',', $ids);

		if(count($ids_array) > 100){
			throw new ErrorException('Я не могу принять больше 100 id');
		}

		Log::useFiles(storage_path() . '/logs/vk.log');
		Log::info("Send: {$text} to {$ids} ");
	}

}