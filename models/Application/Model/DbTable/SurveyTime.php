<?php  class Application_Model_DbTable_SurveyTime extends Zend_Db_Table_Abstract
{

protected $_name = 'survey_time';


    public function start($idUser,$idForm){

        $aInsert=array(
            'start'=>new Zend_Db_Expr('now()'),
//            'stop'=>new Zend_Db_Expr('now()'),
            'id_user'=>$idUser,
            'id_survey'=>$idForm,
            'status'=>1
        );
        return $this->insert($aInsert);
    }

    public function getTime($idUser,$idForm){
        $select=$this->select();
        $select->where('id_user=?',$idUser);
        $select->where('id_survey=?',$idForm);
        $select->where('status=1');

        $select->limit(1);
        $row =$this->fetchRow($select);
        if($row instanceof Zend_Db_Table_Row){
            $datenow = new DateTime();
            $dateStart = new DateTime($row->start);
            $interval=$datenow->diff($dateStart);
//            var_dump($interval);
            $minuty=$interval->h*60+$interval->i+$interval->s/60;
            return $minuty;
        }

        return false;
    }
    public function stop($idUser,$idForm){

        $select=$this->select();
        $select->where('id_user=?',$idUser);
        $select->where('id_survey=?',$idForm);

        $select->where('status=1');

        $select->limit(1);
        $row =$this->fetchRow();
        if($row instanceof Zend_Db_Table_Row){

            $row->data_modyfikacji=new Zend_Db_Expr('now()');
            $row->stop=new Zend_Db_Expr('now()');
            $row->status=0;
            return $row->save();

        }
        return false;
    }
    public function CzyWypelnil($idUser,$idForm){

        $select=$this->select();
        $select->where('id_user=?',$idUser);
        $select->where('id_survey=?',$idForm);
        $select->where('status=0');

        $select->limit(1);
        $row =$this->fetchRow($select);
        if($row instanceof Zend_Db_Table_Row){
            return true;
        }
        return false;
    }

    public function pobierzEgzamin($idUser,$idForm){

        $select=$this->select();
        $select->where('id_user=?',$idUser);
        $select->where('id_survey=?',$idForm);

        $select->limit(1);
        $row =$this->fetchRow($select);
        if($row instanceof Zend_Db_Table_Row){
            return $row->toArray();
        }
        return false;
    }
}