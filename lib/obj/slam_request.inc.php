<?php

class SLAMrequest
{
	public $action;
	public $location;
	public $order;
	public $categories;
	public $search;

	function __construct(&$config=false,$db=false,$user=false)
	{
		$this->action = '';
		$this->location = '';
		$this->limit = 0;
		$this->order = array();
		$this->categories = array();
		$this->search = array();
		
		if ($user !== false)
			$this->getQuery($config);
		
		return;
	}
	
	private function getQuery($config)
	{
		$this->categories = array();
		
		foreach($_REQUEST as $key => $value)
		{
			if (!is_array($value))
				$value = array_map('urldecode',array($value));
						
			switch (strtolower($key))
			{
				case 'action':
				case 'a':
						$this->action = strtolower($value[0]);
					break;
					
				case 'loc':
					$this->location = strtolower($value[0]);
					break;
					
				case 'ord':
					$this->order['field'] = $value[0];
					break;
				case 'dir':
					$this->order['direction'] = $value[0];
					break;
				case 'lim':
					$this->limit = $value[0];
					
				case 'i':
				case 'identifier':
					/* save each identifier to the table-ordered asset array */
					for($i=0; $i<count($value); $i++)
						if (preg_match($config->values['identifier_regex'],$value[$i],$m)>0)
							$this->categories[ $config->values['lettercodes'][$m[2]] ][] = $m[0];
						
					break;
				
				case 'cat':
					if (empty($this->categories))
						$this->categories = array_fill_keys($value,null);
					break;
					
				case 's_field':
					$this->search['field'] = $value;
					break;
				case 's_value':
					$this->search['value'] = $value;
					break;
				case 's_mode':
					$this->search['mode'] = $value;
					break;
				case 's_join':
					$this->search['join'] = $value;
					break;
			}
		}
		
		$this->sanityCheck($config);
	}
	
	private function sanityCheck($config)
	{
		if (empty($this->order['field']))
			$this->order['field'] = $config->values['list_order'];
		if (empty($this->order['direction']))
			$this->order['direction'] = $config->values['list_direction'];
	}
	
	public function makeRequestURL($config,$terms,$use_existing=false)
	{
		/*
			generates an interpretable url, possibly based on the current request
		*/

		$args = array();
				
		/* fill in the arguments with the current values if requested */
		if ($use_existing)
		{
			if (!empty($this->action)){ $args['action'] = $this->action; }
			if (!empty($this->location)){ $args['location'] = $this->location; }
			if (!empty($this->limit)){ $args['limit'] = $this->limit; }
			
			if (!empty($this->order)) // if order and direction fields are default, no need to gum up url
			{
				if ($this->order['field'] != $config->values['list_order']){ $args['field'] = $this->order['field']; }
				if ($this->order['direction'] != $config->values['list_direction']){ $args['direction'] = $this->order['direction']; }
	
			}
			
			if (!empty($this->categories))
			{
				$args['category'] = array_keys($this->categories);
				$args['identifier'] = array();
				foreach($this->categories as $category)
					if (is_array($category)){ $args['identifier'] = array_merge($args['identifier'],$category); }					
			}	
			if (!empty($this->search)){ $args['search'] = $this->search; }
		}

		/* insert or replace with the passed terms */
		foreach($terms as $term => $value)
		{
			if ($term == 'action'){ $args['action'] = urlencode($value); }
			if ($term == 'location'){ $args['location'] = urlencode($value); }
			if ($term == 'limit'){ $args['limit'] = urlencode($value); }
			if ($term == 'order')
			{
				$args['order'] = $value['field'];
				$args['direction'] = $value['direction'];
			}
			if ($term == 'identifier'){ $args['identifier'] = $value; }
			if ($term == 'category'){ $args['category'] = $value; }
			if ($term == 'search'){ $args['search'] = $value; }
		}
		
		/* build the actual URL */
		$url = array();
		
		/* abbreviation translation table for convinence */
		$abbr = array('action'=>'a','location'=>'loc','limit'=>'lim','order'=>'ord','direction'=>'dir');
		
		foreach($args as $name=>$values)
			if ((!is_array($values)) && (!empty($values)))
				$url[] = "{$abbr[$name]}=".urlencode($values);			
		
		if (!empty($args['identifier']))
			$url[] = 'i[]='.implode('&i[]=',array_map(urlencode,$args['identifier']));
		if (!empty($args['category']))
			$url[] = 'cat[]='.implode('&cat[]=',array_map(urlencode,$args['category']));
			
		if (is_array($args['search']))
		{
			if (!empty($args['search']['field'])){ $url[] = 's_field[]='.implode('&s_field[]=',array_map(urlencode,$args['search']['field'])); }
			if (!empty($args['search']['value'])){ $url[] = 's_value[]='.implode('&s_value[]=',array_map(urlencode,$args['search']['value'])); }
			if (!empty($args['search']['mode'])){ $url[] = 's_mode[]='.implode('&s_mode[]=',array_map(urlencode,$args['search']['mode'])); }
			if (!empty($args['search']['join'])){ $url[] = 's_join[]='.implode('&s_join[]=',array_map(urlencode,$args['search']['join'])); }
		}

		return "{$config->html['url']}?".implode('&',$url);
	}
}

?>