<?php
namespace Sdk\Controller;
use Think\Controller;
class FlowRootIndexController extends Controller {
  	public function index(){
  		$role="/Application/Sdk/View/FlowRootIndex/";
		$url=gethostwithhttp();
		$this->assign("$url",$url);
  		$this->assign("role",$role);
       	$this->display("FlowRootIndex/index");
    }
  	public function zsindex(){
  		$role="/Application/Sdk/View/FlowRootIndex/";
		$url=gethostwithhttp();
		$this->assign("$url",$url);
  		$this->assign("role",$role);
       	$this->display("FlowRootIndex/zsindex");
    }
  	public function gdindex(){
  		$role="/Application/Sdk/View/FlowRootIndex/";
		$url=gethostwithhttp();
		$this->assign("$url",$url);
  		$this->assign("role",$role);
       	$this->display("FlowRootIndex/gdindex");
    }

  	public function gdnindex(){
  		$role="/Application/Sdk/View/FlowRootIndex/";
  		$this->assign("role",$role);
       	$this->display("FlowRootIndex/gdsamtonindex");
    }


  	public function flowinfo(){
  		$role="/Application/Sdk/View/FlowRootIndex/";
  		$this->assign("role",$role);
       	$this->display("FlowRootIndex/flowinfo");
    }

		public function test()
		{
			 echo phpversion();
		}
}