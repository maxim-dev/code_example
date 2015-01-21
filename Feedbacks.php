<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Feedbacks extends Controller_Admin {

	/**
	 *
	 * @var string Имя группы в конфиге
	 */
	public static $config_group = 'feedback';

	public function before()
	{
		parent::before();

		// некоторые действия запрещены
		if (in_array(strtolower($this->request->action()), array('add', 'save', 'remote_save')))
		{
			throw new HTTP_Exception_404();
		}
	}

	public function action_save_config()
	{
		if ($this->request->method() != HTTP_Request::POST)
		{
			throw new HTTP_Exception_404();
		}
		
		// ожидаемые ключи в post
		$expected = array('recipients', 'from');

		// из массива post получаем только $expected ключи
		$values = array_intersect_key($this->request->post(), array_flip($expected));
		
		foreach ($values as $key=>$value)
		{
			Kohana::$config->_write_config(static::$config_group, $key, $value);
		}

		$this->redir_to_index();
	}


}