<?php
	
	ini_set("display_errors", "Off");
	
	require_once dirname(__FILE__).'/core.php'; //核心文件

	try
	{
		$Reverse  = new Reverse();

		$Reverse->url();

		$Reverse->run();

	}catch(Exception $e )
	{
		echo $e->getMessage();exit;
	}

	exit;

	class Reverse{

		private $path_info;

		private $classname;

		private $methodname;

		private $class;

		private $method;

		private $instance;
		
		public function __construct()
		{

			if( !isset( $_SERVER['PATH_INFO'] ) or $_SERVER['PATH_INFO'] == '' )
			{
				throw new Exception('method error');
			}

			$path_info = substr( $_SERVER['PATH_INFO'] , 1) ;

			if( ! (  strpos(   $path_info  , '/'  ) > 0 && substr_count( $path_info , '/') == 1 ) )
			{
				throw new Exception('method error');
			}

			$this->path_info = $path_info; 
		}


		public function url()
		{
			$array = explode( '/' , $this->path_info );

			$this->classname = $array[0];
			$this->methodname = $array[1];

			$filename = $this->classname.'.php';

			if(! $this->is_file($filename) )
			{
				throw new Exception($filename.' not exists');
			}

			if(! $this->is_class( $this->classname) )
			{
				throw new Exception($this->classname.' not exists');
			}

			if(! $this->is_method( $this->methodname ) )
			{
				throw new Exception($this->methodname.' not exists');
			}

		}

		public function run()
		{
			$this->method->invoke($this->instance);

		}


		private function is_file( $filename )
		{
			if(file_exists( './reverse/'.$filename))
			{
				include './reverse/'.$filename;
				return true;
			}
			return false;
		}

		private function is_class( $classname )
		{
			if( class_exists( $classname ) )
			{
				$this->class = new ReflectionClass($classname );
				$this->instance  = $this->class->newInstanceArgs();
				return true;
			}
			return false;
		}

		private function is_method( $methodname )
		{	

			if( !$this->class->hasMethod($methodname) ) return false;
			
			$this->method = $this->class->getMethod($methodname);

			if(!$this->method->ispublic()) return false;

			return true;
			
		}

	}


