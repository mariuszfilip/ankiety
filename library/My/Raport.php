<?php
class My_Raport{



    function pobierzWynik($iIdUser,$id_survey){
        if($id_survey > 0){
            $oSurvey = new Application_Model_DbTable_Survey();
            $oUser = new Application_Model_DbTable_User();

            $oserveyTime= new Application_Model_DbTable_SurveyTime();
            if(!$oserveyTime->CzyWypelnil($iIdUser,$id_survey)){
                return false;
            }

            $aUser = $oUser->getUser($iIdUser);

            $survey = new Application_Model_DbTable_Survey();
            $survey_element = new Application_Model_DbTable_Surveyelements();
            $survey_Time = new Application_Model_DbTable_SurveyTime();
            $survey_result = $survey->getSurvey($id_survey);
            if($survey_result){

                $page_session_space = new Zend_Session_Namespace('page');

                $survey_element = new Application_Model_DbTable_Surveyelements();
                $survey_element_list = $survey_element->getSurveyElementsByIdsurvey((int)$survey_result['id']);
                $authNamespace = new Zend_Session_Namespace('page');
                if($survey_element_list ){

                    $i=1;
                    $answer = new Application_Model_DbTable_Surveyanswer();
                    $fSumaPunktow=0;
                    $aRapoty=array();

                    foreach($survey_element_list as $key => $value){

                        $type = $value['type'];
                        $id = $value['id'];
                        $id = (int)$id;
                        switch ((int)$type) {
                            case 1:
                            case 3:
                                $aRapoty[$key]['pytanie']='<h4>Pytanie '.$i++.'</h4>'.$value['header'];
                                $odp= explode(PHP_EOL,$value['option_question']);
                                $ans=$answer->getAnswerUserBySurvayElement($value,$iIdUser);
//                                if($ans==false){return '0';}
                                foreach(explode(PHP_EOL,$value['option_question']) as $key1 =>$val1){

                                    if(is_array($ans) && in_array($key1,$ans)){
                                        $aRapoty[$key]['odpowiedziano'][]=$val1;
                                    }elseif(!is_array($ans) && $key1==$ans){

                                        $aRapoty[$key]['odpowiedziano'][]=$val1;
                                    }
                                };


                                $aPozytywne= explode(',',$value['positive_answer']);
                                $IloscPorawne=count($aPozytywne);
                                /*
                                 * obliczanie
                                 * */
                                $punkty=0;
                                foreach($aPozytywne as $pos=>$a){
                                    if(!is_array($ans) ){
                                        if($a-1==$ans){
                                            $punkty++;
                                        }else{
                                            $punkty=0;
                                            break;
                                        }

                                    }elseif(is_array($ans)){
                                        if( in_array($a-1,$ans)){
                                            $punkty++;
                                        }else{
                                            $punkty=0;
                                            break;
                                        }

                                    }
                                }
//                                $aRapoty[$key]['punkty']=$punkty/$IloscPorawne;
                                $aRapoty[$key]['punkty']=($punkty/$IloscPorawne)*$value['pionts_for_answer'];

                                $fSumaPunktow+= $aRapoty[$key]['punkty'];
                                continue;
                                break;
                        }
                    }
                    $aRapoty['podsumowanie']['suma']=$fSumaPunktow;
                    $aRapoty['podsumowanie']['wynik']=$fSumaPunktow>=$survey_result['min_points']?1:0;
                   return $aRapoty;
                }
            }
        }
        return false;
    }
}