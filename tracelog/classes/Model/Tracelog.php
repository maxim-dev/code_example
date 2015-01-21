<?php defined('SYSPATH') or die('No direct script access.');

class Model_Tracelog extends ORM {

	/**
	 * Table name
	 * @var string
	 */
	protected $_table_name = 'tracelog';

	/**
	 * "Belongs to" relationships
	 * @var array
	 */
	protected $_belongs_to = array(
			'user' => array(),
			);

	/**
	 *
	 * @return array
	 */
	public function filters()
	{
		return array(
				'encoded_ip' => array(
						array(array($this, 'inet_aton')),
				),
		);
	}

	/**
	 * Преобразовывает ip адрес в INT для хранения в БД
	 *
	 * @param string $ip IP адрес
	 * @return  Database_Expression
	 */
	public function inet_aton($ip)
	{
		return DB::expr("INET_ATON('".$ip."')");
	}

	/**
	 * Находим в журнале запись об удаленном пользователе, используя текущий user_id.
	 * Восстанавливаем и возвращаем пользователя, используя сериализованный массив
	 *
	 * @return ORM
	 */
	public function deleted_user()
	{
		$user = ORM::factory('User');

		$comment = DB::select()
						->from($this->table_name())
						->where('model', '=', $user->object_name())
						->and_where('model_id', '=', $this->user_id)
						->and_where('action', '=', 'delete')
						->execute()
						->get('comment');


		if ($comment)
		{
			$data = unserialize($comment);
			$user->values($data['_original_values']);
		}
		
		return $user;
	}

}