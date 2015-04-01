<?php

class My_Helper_List {
  
	private $config;
	private $currentPage;
	private $sortField;
	private $sortDir;
	private $data;
	private $pager;
	private $table;
	private $columnNames;
	private $actionNames;
	private $columns;
	private $pageLinks;
	private $controller;
	private $checkbox;
	private $selectedCheckbox;
	
	public function __construct($data, $columnNames, $actionNames, $controller, $checkbox = false) {
		  
		  $this->config = Zend_Registry::get('config');
		  
		  $this->currentPage = isset($_POST['page']) ? (int) htmlentities($_POST['page']) : 1;
		  $this->sortField = isset($_POST['sort']) ? htmlentities($_POST['sort']) : 'id';
		  $this->sortDir = isset($_POST['direction']) ? htmlentities($_POST['direction']) : 'asc';
		  $this->selectedCheckbox = (isset($_POST['selected']) && $_POST['selected']) ? $this->revTable(htmlentities($_POST['selected'])) : array();
		  $this->data = $data;
		  $this->columnNames = $columnNames;
		  $this->actionNames = $actionNames;
		  $this->checkbox = $checkbox;
		  $this->columns = count($columnNames) + count($actionNames);
		  if($this->checkbox) $this->columns++;
		  $this->controller = $controller;
		  
		  $this->createDataTable();
		  $this->createDataPager();
		  $this->drawTable();
	}
  	
  	public function createDataTable() {
  		$this->pager = new Zend_Paginator(new Zend_Paginator_Adapter_Array($this->data));
  		$this->pager->setCurrentPageNumber($this->currentPage);
  		$this->pager->setItemCountPerPage($this->config->list->ItemCountPerPage);
  		$this->pager->setPageRange($this->config->list->PageRange);
  	}
  	
  	public function createDataPager() {
  		$this->pages = $this->pager->getPages('Sliding');
  		$separator = ' | ';
		$this->pageLinks[] = $this->getLink($this->pages->first, $this->config->list->ItemCountPerPage, $this->sortField, $this->sortDir, '«');        
	    
		if (!empty($this->pages->previous)) {
			$this->pageLinks[] = $this->getLink($this->pages->previous, $this->config->list->ItemCountPerPage, $this->sortField, $this->sortDir, 'poprzedni');        
		}
	  
		foreach ($this->pages->pagesInRange as $x) {
			if ($x == $this->pages->current) {
				$this->pageLinks[] = $x;      
			} else {
				$this->pageLinks[] = $this->getLink($x, $this->config->list->ItemCountPerPage, $this->sortField, $this->sortDir, $x);      
			}  
		} 
	  
		if (!empty($this->pages->next)) {
			$this->pageLinks[] = $this->getLink($this->pages->next, $this->config->list->ItemCountPerPage, $this->sortField, $this->sortDir, 'następny');        
		}  
	  
		$this->pageLinks[] = $this->getLink($this->pages->last, $this->config->list->ItemCountPerPage, $this->sortField, $this->sortDir, '»');	
  	}
  	
  	public function drawTable() {
  		if($this->checkbox)
  			$this->table = '<div style="clear:both;"><form method="POST" action="'.$this->controller.'/delete" name="form_list" id="form_list">';
  		
  		$this->table .= '<table class="tab">';
  		$this->table .= '<tr class="tab_nag">';
  		
  		if($this->checkbox)
  			$this->table .= '<th>&nbsp;</th>';
  		
  		foreach($this->columnNames as $key => $value) {
  			$this->table .= '<th>'.$value.'&nbsp;'.
  						$this->getLink($this->pages->current, $itemsPerPage, $key, 'asc', '&uArr;').
  						$this->getLink($this->pages->current, $itemsPerPage, $key, 'desc', '&dArr;').
  					'</th>';	
  		}
  		
  		if(count($this->actionNames)) {
  			$this->table .= '<th colspan="'.count($this->actionNames).'">&nbsp;</th>';	
  		}
  		
  		$this->table .= '</tr>';
  		
  		if(count($this->data)):
  			foreach($this->pager as $row):
  				$this->table .= '<tr>';
  				$id = $row["id"];
  					if($this->checkbox)
  						$this->table .= '<td><input type="checkbox" onchange="changeSelect(this);" name="check[]" value="'.$id.'" '.($this->selectedCheckbox[$id]?"checked":"").'></td>';
  				foreach($this->columnNames as $rowId => $rowName):
  					if(!is_Array($row[$rowId])) {
  						if($rowId == "status")
  							$value = $row[$rowId]?"aktywny":"nieaktywny";  	
  						else
  							$value = $row[$rowId];
						$this->table .= '<td>'.$value.'</td>';
					}
					else {
						$this->table .= '<td>';
						foreach($row[$rowId] as $name):
							$this->table .= $name."<br/>";
						endforeach;
						$this->table .= '</td>';
					}
				endforeach;
				
				foreach($this->actionNames as $rowId => $rowName):
					$this->table .= '<td><a href="'.$_SERVER['SCRIPT_NAME'].'/'.$this->controller.'/'.$rowId.'/id/'.$id.'">'.$rowName.'</a></td>';
				endforeach;
				$this->table .= '</tr>';
			endforeach;
			
			$this->table .= '<tr><td colspan="'.$this->columns.'">'.implode($this->pageLinks, " | ").'</td></tr>';
		else:
			$this->table .= '<tr><td colspan="'.$this->columns.'">Brak danych do wyświetlenia</td></tr>'; 
		endif;
		
		$this->table .= "</table>";
		if($this->checkbox) {
  			$this->table .= '</form></div>';
  			$this->table .= '<form method="POST" action="'.$_SERVER['SCRIPT_NAME'].'/'.$this->controller.'/list" name="form_pager" id="form_pager">
  				<input type="hidden" name="page" id="page" value="1">
  				<input type="hidden" name="sort" id="sort" value="id">
  				<input type="hidden" name="direction" id="direction" value="asc">
  				<input type="hidden" name="selected" id="selected" value="">
  				<input type="hidden" name="action" id="action" value="">
  			  </form>
  			  <script type="text/javascript">
  			  	var selectedElem = new Array();
  			  	function setValues(page, sort, direction, action) {
  			  		document.getElementById(\'page\').value = page;
  			  		document.getElementById(\'sort\').value = sort;
  			  		document.getElementById(\'direction\').value = direction;
  			  		document.getElementById(\'selected\').value = selectedElem.toString();
  			  		document.getElementById(\'action\').value = action;
  			  		
  			  	}
  			  	
  			  	function sendData(page, sort, direction, action) {
  			  		setValues(page, sort, direction, action);
  			  		document.getElementById(\'form_pager\').submit();
  			  	}
  			  	function changeSelect(check) {
  			  		if(check.checked) {
  			  			selectedElem.push(check.value);
  			  		}
  			  		else {
  			  			for(i=0;i<selectedElem.length;i++) {
  			  				if(selectedElem[i] == check.value) {
  			  					selectedElem[i] = null;
  			  				}
  			  			}	
  			  			
  			  		}
  			  	}';
  			  	
  			  	foreach($this->selectedCheckbox as $key => $value) {
  			  		$this->table .= "selectedElem.push($key);";		
  			  	}
  			  
  			  	$this->table .= "</script>";
  	        }
  		
	}
  	
  	public function getLink($page, $itemsPerPage, $sortField, $sortDir, $label) {
  		      /*return $this->ajaxLink("Usuń odbiorców","subscriber/test",
                          array(
                              'update' => '#list',
                              'noscript' => false,
                              'method' => 'POST',
                              'class' => 'przycisk'
                          ),
                          array(
                              'page' => $this->page,
                              'sort' => $this->sort,
                              'direction' => $this->direction,
                              'action' => "delete"
                          )
                          );*/
                       
  		      
  		      return "<a href=\"javascript:sendData($page, '$sortField', '$sortDir', 'list');\">$label</a>";
	}

  	
  	public function getTable() {
  		return $this->table;	
  	}
  	
  	public function revTable($string) {
  		$temp = explode(",", $string);
  		$temp_rev = array();
  		foreach($temp as $key => $value) {
  			if($value)
  				$temp_rev[$value] = true;
  		}
  		return $temp_rev; 
  	}
}
