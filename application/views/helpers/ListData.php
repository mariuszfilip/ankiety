<?php

/**
 * Jest wykorzystywane do tworzenia listy w większości metod CRUD 
 */
class Zend_View_Helper_ListData {
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
    private $js;
    private $counter = 0;
    private $inactive;
    private $searchFields;
    private $exclude;
    private $title;
	
    public function listdata($data, $columnNames, $actionNames, $controller, $searchFields = array(), $exclude = array(), $checkbox = array(), $inactive = array()) {
		
        $this->config = Zend_Registry::get('config');
        if(isset($data['title'])){
            $this->title = $data['title'];
            unset($data['title']);
        }
        $this->currentPage = isset($_POST['page']) ? (int) htmlentities($_POST['page']) : 1;
        $this->sortField = isset($_POST['sort']) ? htmlentities($_POST['sort']) : 'id';
        $this->sortDir = isset($_POST['direction']) ? htmlentities($_POST['direction']) : 'asc';
        $this->selectedCheckbox = (isset($_POST['selected']) && $_POST['selected']) ? $this->revTable($_POST['selected']) : array();
        $this->data = $data;
        $this->columnNames = $columnNames;
        $this->actionNames = $actionNames;
        $this->checkbox = $checkbox;
        $this->inactive = $inactive;
        $this->columns = count($columnNames) + count($actionNames);
        if($this->checkbox) $this->columns++;
        $this->controller = $controller;
        $this->setSearchFields($searchFields);
        $this->setExcludeFields($exclude);
		  
        $this->createDataTable();
       // $this->createDataPager();
        $this->drawTable();
        if($this->data){
        return '<script type="text/javascript">$(document).ready(function() { '.$this->js.' });</script>'.$this->table;
        }else{
         return '<p>Brak danych do wyświetlenia</p>';
        }
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
        $this->pageLinks[] = $this->getLink($this->pages->first, $this->config->list->ItemCountPerPage, $this->sortField, $this->sortDir, '«', $this->searchFields, $this->exclude);        
	    
        if (!empty($this->pages->previous)) {
            $this->pageLinks[] = $this->getLink($this->pages->previous, $this->config->list->ItemCountPerPage, $this->sortField, $this->sortDir, 'poprzedni', $this->searchFields, $this->exclude);        
        }
	  
        foreach ($this->pages->pagesInRange as $x) {
            if ($x == $this->pages->current) {
                $this->pageLinks[] = $x;      
            } else {
                $this->pageLinks[] = $this->getLink($x, $this->config->list->ItemCountPerPage, $this->sortField, $this->sortDir, $x, $this->searchFields, $this->exclude);      
            }  
        } 

        if (!empty($this->pages->next)) {
            $this->pageLinks[] = $this->getLink($this->pages->next, $this->config->list->ItemCountPerPage, $this->sortField, $this->sortDir, 'następny', $this->searchFields, $this->exclude);        
        }  
	  
        $this->pageLinks[] = $this->getLink($this->pages->last, $this->config->list->ItemCountPerPage, $this->sortField, $this->sortDir, '»', $this->searchFields, $this->exclude);	
    }
  	
    public function drawTable() {
        if($this->checkbox && !isset($this->checkbox['form']))
            $this->table = '<div style="clear:both;"><form method="post" action="'.$this->controller.'/delete" name="form_list" id="form_list">';
            

  		$this->table .= '<div class="title">
                    <img class="titleIcon" alt="" src="images/icons/dark/stats.png">
                    <h6>'.$this->title.'</h6>
                    </div>';

        $this->table .= '<table width="100%" cellspacing="0" cellpadding="0" class="sTable">';
         $this->table .= '<thead>';
        $this->table .= '<tr>';
  		
        if($this->checkbox)
            $this->table .= '<th>&nbsp;</th>';
  		
        foreach($this->columnNames as $key => $value) {
            $this->table .= '<th>'.$value.'&nbsp;'.
  						//$this->getLink($this->pages->current, $this->config->list->ItemCountPerPage, $key, 'asc', '&uArr;', $this->searchFields, $this->exclude).
  						//$this->getLink($this->pages->current, $this->config->list->ItemCountPerPage, $key, 'desc', '&dArr;', $this->searchFields, $this->exclude).
  					'</th>';	
  		}
  		
        if(count($this->actionNames)) {
            //$this->table .= '<th colspan="'.count($this->actionNames).'">&nbsp;</th>';
			foreach($this->actionNames as $actionName):
				$this->table .= '<th>&nbsp</th>';
			endforeach;
        }
  		
        $this->table .= '</tr>';
  		 $this->table .= '</thead>';
        if(count($this->data)):
            foreach($this->pager as $row):
                $this->table .= '<tr>';
                $id = isset($row["id"])?$row["id"]:0;
                if($this->checkbox)
                    $this->table .= '<td><input type="checkbox" onchange="changeSelect(this);" name="'.$this->checkbox['name'].'[]" value="'.$id.'" '.(isset($this->selectedCheckbox[$id]) && $this->selectedCheckbox[$id]?"checked":"").'></td>';
                
                  $view = new Zend_View();
                foreach($this->columnNames as $rowId => $rowName):
                    if(!is_Array($row[$rowId])) {
                        if($rowId == "status")
                            $value = $row[$rowId]?"aktywny":"nieaktywny";  	
                        else
                            $align = '';
                            $value = $row[$rowId];
                            if($rowId == 'result'){
                                $align = 'align = "center"'; 
                            }
                            $value = $view->translate($value);
                        $this->table .= '<td '.$align.'>'.$value.'</td>';
                    }
                    else {
                        $this->table .= '<td '.$class.'>';
                        foreach($row[$rowId] as $name):
                            $name = $view->translate($name);
                            $this->table .= $name."<br/>";
                        endforeach;
                        $this->table .= '</td>';
                    }
                endforeach;
				
                foreach($this->actionNames as $rowId => $rowName):
                    if(!isset($this->inactive["$rowId"][$id])) {	
                        if(!is_array($rowName)) {
                        	$this->table .= '<td><a href="'.$this->config->system->path.'/'.$this->controller.'/'.$rowId.'/id/'.$id.'/" class="'.$rowId.'Form" rel="'.$id.'">'.$rowName.'</a></td>';
                        }
                        else {
							$otherClass = '';
							if(isset($rowName['class']) && $rowName['class']) {
								$otherClass = ' '.$rowName['class'];
							}
						
                            if(isset($rowName["thickbox"]) && $rowName["thickbox"]) {
                                $this->table .= '<td><a class="thickbox' . $otherClass . '" href="'.$this->config->system->path.'/'.$this->controller.'/'.$rowId.'/id/'.$id.'/">'.$rowName["name"].'</a></td>';
                            }
                            else {
                                $this->table .= '<td><a href="'.$this->config->system->path.'/'.$this->controller.'/'.$rowId.'/id/'.$id.'/">'.$rowName.'</a></td>';
                            }
                        }
                    }
                    else
                        $this->table .= '<td>'.$rowName.'</td>';
                endforeach;
                $this->table .= '</tr>';
            endforeach;

            //$this->table .= '<tr><td colspan="'.$this->columns.'">'.implode($this->pageLinks, " | ").'</td></tr>';
        else:
            $this->table .= '<tr><td colspan="'.$this->columns.'">Brak danych do wyświetlenia</td></tr>'; 
        endif;
		
        $this->table .= "</table>";
        //if($this->checkbox) {
            $this->table .= '</form>';
            //'.$_SERVER['SCRIPT_NAME'].'
            $this->table .= '<form method="post" action="/'.$this->controller.'/delete" name="form_pager" id="form_pager">
  				<input type="hidden" name="page" id="page" value="1">
  				<input type="hidden" name="sort" id="sort" value="id">
  				<input type="hidden" name="direction" id="direction" value="asc">
  				<input type="hidden" name="selected" id="selected" value="">
  			  </form>
  			  <script type="text/javascript">
  			  	var selectedElem = new Array();
  			  	function setValues(page, sort, direction) {
  			  		document.getElementById(\'page\').value = '.$this->currentPage.';
  			  		document.getElementById(\'sort\').value = \''.$this->sortField.'\';
  			  		document.getElementById(\'direction\').value = \''.$this->sortDir.'\';
  			  		document.getElementById(\'selected\').value = selectedElem.toString();
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
  			  					selectedElem.splice(i, 1);
  			  					//selectedElem[i] = null;
  			  				}
  			  			}	
  			  			
  			  		}
  			  	}';
  			  	
                foreach($this->selectedCheckbox as $key => $value) {
                    $this->table .= "selectedElem.push($key);";		
                }
  			  
                $this->table .= "</script>";
  	        //}
  		
    }
  	
    public function getLink($page, $itemsPerPage, $sortField, $sortDir, $label, $searchFields = array(), $exclude = array()) {
        $this->counter++;
  		//testowo dodano name
        //'.$_SERVER["SCRIPT_NAME"].'
        $this->js .= ' $(\'a.ajaxLink'.$this->counter.'\').click(function(event) { event.preventDefault(); $.post(\'/'.$this->controller.'/list\', {"page":'.$page.',"sort":"'.$sortField.'","direction":"'.$sortDir.'","action":"listdata","selected": selectedElem';
        
        if(count($searchFields)){
            foreach ($searchFields as $key => $value):
                if(is_array($value)){
                    $this->js .=',"' .$key. '": {';
                    $i =0;
                	foreach ($value as $key2 => $sub) {
                        if($i != 0) $this->js .= ',';
                		if(($key2 != '' OR $key2 == 0) AND $sub != ''){
                            $this->js .= '"' .$key2. '": "' .$sub. '"';
                        }
                        
                        $i++;
                    }
                    $this->js .= '}';
                
                }else{
                    if($key != '' AND $value != ''){
                        $this->js .= ',"' .$key. '": "' .$value. '"';
                    }
                }
/*                if($key != '' AND $value != ''){
                    $this->js .= ',"' .$key. '": "' .$value. '"';
                }
*/            endforeach;
        }
    
        if(count($exclude)){
            foreach ($exclude as $key => $value):
                if(is_array($value)){
                    $this->js .=',"' .$key. '": {';
                    $i =0;
                	foreach ($value as $key2 => $sub) {
                        if($i != 0) $this->js .= ',';
                		if($key2 != '' AND $sub != ''){
                            $this->js .= '"' .$key2. '": "' .$sub. '"';
                        }
                        
                        $i++;
                    }
                    $this->js .= '}';
                
                }else{
                    if($key != '' AND $value != ''){
                        $this->js .= ',"' .$key. '": "' .$value. '"';
                    }
                }
            endforeach;
        }

        $this->js .= '}, function(data, textStatus) { $(\'#list\').html(data); }, \'html\');return false; });';
        
        return '<a href="#" class="ajaxLink'.$this->counter.'">'.$label.'</a>';
    }

  	
  	public function getTable() {
        return $this->table;	
  	}
  	
    public function revTable($temp) {
        $temp_rev = array();
        foreach($temp as $key => $value) {
            if($value)
                $temp_rev[$value] = true;
        }
        return $temp_rev; 
    }
    
    public function setSearchFields($fields){
        foreach($fields as $key => $value):
            if($key != '' && $value != '')
                $this->searchFields[$key] = $value;
        endforeach;
    }
    
    public function setExcludeFields($fields){
        foreach($fields as $key => $value):
            if($key != '' && $value != '')
                $this->exclude[$key] = $value;
        endforeach;
    }

}
?>

