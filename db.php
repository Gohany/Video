<?php

class db
{
        
        const OBJECT_TYPE = 'pdo';
	public static $instance;
        public $pdo;
        
        private function __construct($key)
	{
		if (!file_exists('/var/www/configs/database/mysql/pdo-' . $key . '.php'))
		{
                        throw new Exception('Database not found');
		}

		require '/var/www/configs/database/mysql/pdo-' . $key . '.php';

		if (!extension_loaded('pdo_mysql'))
		{
			//error::addError('Missing PHP mysqli extension');
			//throw new error(errorCodes::ERROR_INTERNAL_ERROR);
                        throw new Exception ('Missing PHP pdo_mysql extension');
		}
                
                try
                {
                        $this->pdo = new PDO($dsn, $user, $password);
                }
		catch (PDOException $e)
                {
                        print "Connection failed: " . $e->getMessage() . PHP_EOL;
                }
	}
        
        public static function singleton($key)
	{
		if (!(self::$instance = dataStore::getObject($key, self::OBJECT_TYPE)))
		{
			self::$instance = new db($key);
			dataStore::setObject($key, self::$instance, self::OBJECT_TYPE);
		}
		return self::$instance->pdo;
	}
        
        public function __destruct()
	{
		self::destroyConnections();
	}

	public static function destroyConnections()
	{
		if ($objects = dataStore::getObjectArray(self::OBJECT_TYPE))
		{
			foreach ($objects as $object)
			{
				$object->pdo = null;
			}

			dataStore::unsetObjectType(self::OBJECT_TYPE);
		}
	}
        
        public static function linkIdentifier($key)
	{
		return self::singleton($key);
	}
        
        public static function query($key, $query)
        {
                return self::singleton($key)->query($query);
        }
        
        public static function prepare($key, $query, $options = array())
        {
                return self::singleton($key)->prepare($query, $options);
        }
        
        public static function lastInsertId($key, $name = null)
        {
                return self::singleton($key)->lastInsertId($name);
        }
        
        public static function exec($query)
        {
                return self::singleton($key)->exec($query);
        }
        
}