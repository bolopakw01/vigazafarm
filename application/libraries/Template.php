<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
#[\AllowDynamicProperties]
class Template {
		var $template_data = array();	

		function set($name, $value){
			$this->template_data[$name] = $value;
		}
		
		function kepala($template = '', $view = '' , $view_data = array(), $return = FALSE)
		{               
			$this->CI =& get_instance();
			$this->set('kepala', $this->CI->load->view($view, $view_data, TRUE));	
			return $this->CI->load->view($template, $this->template_data, $return);
		}
		
		function samping($template = '', $view = '' , $view_data = array(), $return = FALSE)
		{               
			$this->CI =& get_instance();
			$this->set('samping', $this->CI->load->view($view, $view_data, TRUE));	
			return $this->CI->load->view($template, $this->template_data, $return);
		}
		
		function jidat($template = '', $view = '' , $view_data = array(), $return = FALSE)
		{               
			$this->CI =& get_instance();
			$this->set('jidat', $this->CI->load->view($view, $view_data, TRUE));	
			return $this->CI->load->view($template, $this->template_data, $return);
		}

		function isi($template = '', $view = '' , $view_data = array(), $return = FALSE)
		{               
			$this->CI =& get_instance();
			$this->set('isi', $this->CI->load->view($view, $view_data, TRUE));	
			return $this->CI->load->view($template, $this->template_data, $return);
		}
		
		function kaki($template = '', $view = '' , $view_data = array(), $return = FALSE)
		{               
			$this->CI =& get_instance();
			$this->set('kaki', $this->CI->load->view($view, $view_data, TRUE));	
			return $this->CI->load->view($template, $this->template_data, $return);
		}
		
		
}


