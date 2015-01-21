<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Класс реализует журнал изменений.
 *
 * @package    Kohana
 * @category   Logging
 * @author     Maxim Kovtun (letter.for.maxim@gmail.com)
 * @license    http://kohanaframework.org/license
 */
class Kohana_Tracelog {

	/**
	 * @var  Tracelog  Singleton instance container
	 */
	protected static $_instance;

	/**
	 * @var ORM
	 */
	protected $_orm;

	/**
	 * Get the singleton instance of this class.
	 *
	 *     $tracelog = Tracelog::instance();
	 *
	 * @return  Tracelog
	 */
	public static function instance()
	{
		if (Tracelog::$_instance === NULL)
		{
			// Create a new instance
			Tracelog::$_instance = new Tracelog;
		}

		return Tracelog::$_instance;
	}

	/**
	 * Define ORM object.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->_orm = ORM::factory('Tracelog');
	}

	/**
	 *
	 * @param ORM $model
	 * @param string $action
	 * @param string $comment
	 * @return void
	 */
	public function add($model, $action, $comment = '', Model_User $user = NULL)
	{
		if ($model->do_not_track())
		{
			return;
		}

		if ($user === NULL)
		{
			$author = Auth::instance()->get_user() ? : ORM::factory('User');
		}
		else
		{
			$author = $user;
		}

		$this->_orm->values(array(
				'user_id'		 => $author->id,
				'model'			 => $model->object_name(),
				'model_id'	 => $model->id,
				'action'		 => $action,
				'comment'		 => $comment,
				'encoded_ip' => Request::$client_ip,
				'created'		 => time(),
		))->save();
	}

	/**
	 * @param ORM $model
	 * @return Database_Result
	 */
	public function get($model)
	{
		return $this->_orm
						->select(array(DB::expr('INET_NTOA(encoded_ip)'), 'ip'))
						->where('model', '=', $model->object_name())
						->and_where('model_id', '=', $model->id)
						->order_by('created', 'DESC')
						->limit(10)
						->find_all();
	}

	/**
	 *
	 * @param ORM $model
	 * @return View
	 */
	public function show($model)
	{
		if ( ! $model OR ! $model->loaded())
		{
			return '';
		}

		return View::factory('tracelog/admin/list')
						->set('list', Tracelog::instance()->get($model))
						->set('actions', Kohana::$config->load('tracelog/admin/actions'));
	}
}
