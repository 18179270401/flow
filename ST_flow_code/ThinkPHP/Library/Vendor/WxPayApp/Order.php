<?php
	class Order extends WxPayUnifiedOrder{
		public  function setOrder($k,$v){
			$this->values[$k]=$v;
		}
		public  function getOrder(){
			return $this->values;
		}
	}
 ?>