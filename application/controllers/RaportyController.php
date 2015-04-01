<?php

/**
 * Zarządzanie ankietami
 * @author Mariusz Filipkowski
 *
 */
class RaportyController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->redirector->setUseAbsoluteUri(true);
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->messages = $this->_flashMessenger->getMessages();
    }

    public function preDispatch()
    {

        $page_session_space = new Zend_Session_Namespace('page');
        if (!isset($page_session_space->user) || $page_session_space->user == null) {
            $this->_redirect('/auth/login');
        }

    }

    public function indexAction()
    {
//        error_reporting(E_ALL | E_STRICT);
//            ini_set('display_errors', 'on');
        $this->_helper->layout()->setLayout('layout');

        $iIdUser = $this->_getParam('id_user',0);
        $id_survey = intval($this->_getParam('id_survey',0));
        if($id_survey > 0){
            $oSurvey = new Application_Model_DbTable_Survey();
            $oUser = new Application_Model_DbTable_User();

            $aUser = $oUser->getUser($iIdUser);

            $this->view->user=$aUser;
                $survey = new Application_Model_DbTable_Survey();
                $survey_element = new Application_Model_DbTable_Surveyelements();
                $survey_Time = new Application_Model_DbTable_SurveyTime();
                $survey_result = $survey->getSurvey($id_survey);
                    $this->view->egzamin=$survey_result;
                if($survey_result){
                    $page_session_space = new Zend_Session_Namespace('page');


                    $this->view->id = $survey_result['id'];
                    $this->view->name = $survey_result['name'];

                    $survey_element = new Application_Model_DbTable_Surveyelements();
                    $survey_element_list = $survey_element->getSurveyElementsByIdsurvey((int)$survey_result['id']);
                    $authNamespace = new Zend_Session_Namespace('page');
                    if($survey_element_list ){
//                        echo '<table>';
                        $i=1;
                        $answer = new Application_Model_DbTable_Surveyanswer();
                        $fSumaPunktow=0;
                        $aRapoty=array();

                        $oRaporty=new My_Raport();

                        foreach($survey_element_list as $key => $value){

                            $type = $value['type'];
                            $id = $value['id'];
                            $id = (int)$id;
//                            echo '<tr>';
                            switch ((int)$type) {
                                case 1:
                                case 3:
                                $aRapoty[$key]['pytanie']='<h4>Pytanie '.$i++.'</h4>'.$value['header'];
                                 $odp= explode(PHP_EOL,$value['option_question']);
                                $ans=$answer->getAnswerUserBySurvayElement($value,$iIdUser);

                                foreach(explode(PHP_EOL,$value['option_question']) as $key1 =>$val1){

                                    if(is_array($ans) && in_array($key1,$ans)){
                                        $aRapoty[$key]['odpowiedziano'][]=$val1;
                                    }elseif(!is_array($ans) && $key1==$ans){

                                        $aRapoty[$key]['odpowiedziano'][]=$val1;
                                    }
//piedziak.pawel@gmial.com
                                };


                               $aPozytywne= explode(',',$value['positive_answer']);
                                $IloscPorawne=count($aPozytywne);
//                                var_dump($IloscPorawne);
                                /*
                                 * obliczanie
                                 * */
                                $punkty=0;
                                foreach($aPozytywne as $pos=>$a){
                                    if(!is_array($ans) && $a-1==$ans){
                                        $punkty++;
                                    }elseif(is_array($ans) && in_array($a-1,$ans)){
                                        $punkty++;
                                    }
                                }
                                $aRapoty[$key]['punkty']=$punkty/$IloscPorawne;
//                                $aRapoty[$key]['punkty']=($punkty/$IloscPorawne)*$value['pionts_for_answer'];

                                $fSumaPunktow+=$punkty/$IloscPorawne;
                                continue;
                                    break;
                            }
                        }
                        $aRapoty['suma']=$fSumaPunktow;

                        $aRaportpom=$oRaporty->pobierzWynik($iIdUser,$id_survey);
                        $this->view->raporty=$aRaportpom;
//                        $this->view->raporty=$aRapoty;
                    }
                }

        }
    }
}