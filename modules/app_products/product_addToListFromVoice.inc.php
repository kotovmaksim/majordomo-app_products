<?php

//$command='черный молотый перец белого лука гель для душа йогурт марки денон красные розы';
require_once(ROOT . "lib/phpmorphy/common.php");
$opts = array(
 'storage' => PHPMORPHY_STORAGE_MEM,
 'predict_by_suffix' => true,
 'predict_by_db' => true,
 'graminfo_as_text' => true,
 );
$dir = ROOT . 'lib/phpmorphy/dicts';
$lang = 'ru_RU';
        try {
            $morphy = new phpMorphy($dir, $lang, $opts);
            $this->morphy =& $morphy;
        } catch (phpMorphy_Exception $e) {
            die('Error occured while creating phpMorphy instance: ' . PHP_EOL . $e);
        }
        $words = explode(' ', $command);
        $base_forms = array();
        $partsOfSpeech=array();
        $f_word=array();
        $totals = count($words);
        for ($is = 0; $is < $totals; $is++) {
            if (preg_match('/^(\d+)$/', $words[$is])) {
                $base_forms[$is] = array($words[$is]);
            } else {
                $Word = mb_strtoupper($words[$is], 'UTF-8');
                $base_forms[$is] = $morphy->getBaseForm($Word);
                $partsOfSpeech[$is] = $morphy->getPartOfSpeech($Word);
                $f_word[$is] = $morphy->getGramInfo($Word);
            if ( count($partsOfSpeech[$is])==2) {
                 if ($partsOfSpeech[$is][0]=="С" and $partsOfSpeech[$is][1]=="П") {
                  $partsOfSpeech[$is][0]="П";
                 } 
                elseif ($partsOfSpeech[$is][0]=="Г" and $partsOfSpeech[$is][1]=="С") {
          $partsOfSpeech[$is][0]="С";
                 $base_forms[$is][0]=$base_forms[$is][1];
         $chislo=array_intersect($f_word[$is][1][0]['grammems'],['ЕД', 'МН']);
         $chislo=reset($chislo);
          $rod=array_intersect($f_word[$is][1][0]['grammems'],['МР', 'ЖР', 'СР']);
          $rod=reset($rod);
                 $f_word[$is][0][0]['grammems'][0]=$cislo;
                 $f_word[$is][0][0]['grammems'][1]=$rod;
                  
                 }
                }
                $base_forms[$is][]=$words[$is];
            } 
        }


say('Добавляю в список покупок',2);

for ($is = 0; $is < $totals; $is++) {
    if ($base_forms[$is][0]=='ВОД') $base_forms[$is][0]='ВОДА';
    if ($partsOfSpeech[$is][0]=='С') {
    $chislo=array_intersect($f_word[$is][0][0]['grammems'],['ЕД', 'МН']);
    $chislo=reset($chislo);
        if (($is+1)<$totals) {
            if ($partsOfSpeech[$is+1][0]=='С') {
        if ($base_forms[$is+1][0]=='МАРКА' or $base_forms[$is+1][0]=='ФИРМА') {
            $product=$base_forms[$is][0]. ' ' . $words[$is+1] . ' ' . $words[$is+2];
            $is=$is+2;
        }
        else {
            $product=$morphy->castFormByGramInfo($base_forms[$is][0],'С',[$chislo,'ИМ']);
            $product=$product[0]['form'];
        }
            }
            elseif ($partsOfSpeech[$is+1][0]=='ПРЕДЛ') {
                $product=$base_forms[$is][0] . ' ' . $words[$is+1] . ' ' . $words[$is+2];
                $is=$is+2;
            }
             elseif ($partsOfSpeech[$is+1][0]=='П') {
        $rod=array_intersect($f_word[$is+1][0][0]['grammems'],['МР', 'ЖР', 'СР']);
        $rod=reset($rod);
        // Выбираем форму прилагательного правильного рода
        if ($chislo=='ЕД') {
            $adjective=$morphy->castFormByGramInfo($base_forms[$is+1][0],"П",[$rod,'ЕД','ИМ']);
        }
        else {
            $adjective=$morphy->castFormByGramInfo($base_forms[$is+1][0],"П",['МН','ИМ']);
        }
        $adjective=$adjective[0]['form'];
        $noun=$morphy->castFormByGramInfo($base_forms[$is][0],'С',[$chislo,'ИМ']);
        $noun=$noun[0]['form'];
        if (Get_Product_ID($adjective . " " . $noun)>0) {
            $product=$adjective . " " . $noun;
            $is=$is+1;
        }
        else {
            $product=$noun ;
        }
            }

        }
        else {
        
        $noun=$morphy->castFormByGramInfo($base_forms[$is][0],'С',[$chislo,'ИМ']);
        $noun=$noun[0]['form'];
            $product=$noun;
        }
    }
    elseif ($partsOfSpeech[$is][0]=='П') {
    $rod=array_intersect($f_word[$is][0][0]['grammems'],['МР', 'ЖР', 'СР']);
    $rod=reset($rod);
    $chislo=array_intersect($f_word[$is][0][0]['grammems'],['МН', 'ЕД']);
    $chislo=reset($chislo);
    
    // Выбираем форму прилагательного правильного рода
    if ($chislo=='ЕД') {
        $adjective=$morphy->castFormByGramInfo($base_forms[$is][0],"П",[$rod,'ЕД','ИМ']);
    }
    else {
        $adjective=$morphy->castFormByGramInfo($base_forms[$is][0],"П",['МН','ИМ']);
    }
    $adjective=$adjective[0]['form'];
        if (($is+1)<$totals) {
            if ($partsOfSpeech[$is+1][0]=='С') {
                if (count($base_forms[$is+1])>2){
                 // выбираем форму согласованную по роду
                 for ($kk= 0; $kk < count($base_forms[$is+1])-1; $kk++) {
                       $rod1=array_intersect($f_word[$is+1][$kk][0]['grammems'],['МР', 'ЖР', 'СР']);
                    $rod1=reset($rod1);
                    if ($rod==$rod1) break;
                 }
         $noun=$morphy->castFormByGramInfo($base_forms[$is+1][$kk],'С',[$chislo,'ИМ']);
         $noun=$noun[0]['form'];

                 $product=$adjective .' ' . $noun ;
                 $is=$is+1; 
                }
                else {
         $noun=$morphy->castFormByGramInfo($base_forms[$is+1][0],'С',[$chislo,'ИМ']);
         $noun=$noun[0]['form'];

                 $product=$adjective .' ' . $noun ;
                 $is=$is+1; 
                } 
            }
            elseif ($partsOfSpeech[$is+1][0]=='П') {
                if ($chislo=='ЕД') {
                    $adjective1=$morphy->castFormByGramInfo($base_forms[$is+1][0],"П",[$rod,'ЕД','ИМ']);
                }
                else {
                    $adjective1=$morphy->castFormByGramInfo($base_forms[$is+1][0],"П",['МН','ИМ']);
                }

                $adjective1=$adjective1[0]['form'];
                if (($is+2)<$totals) {
                    if ($partsOfSpeech[$is+2][0]=='С') {

                        if (count($base_forms[$is+2])>2){
                            // выбираем форму согласованную по роду
                            for ($kk= 0; $kk < count($base_forms[$is+2])-1; $kk++) {
                                $rod1=array_intersect($f_word[$is+2][$kk][0]['grammems'],['МР', 'ЖР', 'СР']);
                                $rod1=reset($rod1);
                            }
                 $noun=$morphy->castFormByGramInfo($base_forms[$is+2][$kk],'С',[$chislo,'ИМ']);
                 $noun=$noun[0]['form'];
 
                            $product=$adjective .' ' . $adjective1 . ' ' . $noun ;
                            $is=$is+2; 
                 
                        }
                        else {
                 $noun=$morphy->castFormByGramInfo($base_forms[$is+2][0],'С',[$chislo,'ИМ']);
                 $noun=$noun[0]['form'];

                            $product=$adjective .' ' . $adjective1 .' ' . $base_forms[$is+2][0];
                            $is=$is+2; 
                        }
                    }
                    else {
                        $product=$adjective .' ' . $adjective1;
                        $is=$is+1;
                    }    
                }
                else {
                    $product=$adjective .' ' . $adjective1;
                    $is=$is+1;
                }    
            }                
        }
        else {
            $product=$adjective;
        }    
    }        

    $product = strtolower($product);

    say($product,2);
     
    if($debugEnabled) debmes('Products produkt:'. $product);
                
    $id=Get_Product_ID( $product);
    if ($id > 0){
        $this->addToList($id);
        if($debugEnabled) debmes('Products produkt '.$product.' found, ID:'. $id);
    }
    Else {
        if($debugEnabled) debmes('Products produkt '.$product.' not found, adding');
        $category_id = Get_Category_ID("Неотсортированные");
        if ($category_id > 0){
            if($debugEnabled) debmes('Products category exiting unknown');
            $this->category_id = $category_id;
        } 
        Else {
            if($debugEnabled) debmes('Products creating unknown');
               $Record = Array();
               $Record['TITLE'] = "Неотсортированные";
               $Record['ID']=SQLInsert('product_categories', $Record);
            $category_id = $Record['ID'];
                            
            if($debugEnabled) debmes('Products produkt '.$product.' adding to created unknown');
        }

           $Record = Array();
           $Record['TITLE'] = $product;
          $Record['CATEGORY_ID'] = $category_id;
          $Record['QTY'] = 1;
           $Record['ID']=SQLInsert('products', $Record);
        $id = $Record['ID'];

        $this->addToList($id);
        if($debugEnabled) debmes('Products produkt '.$product.' not found, added to category id '.$category_id);
    }


} 

function Get_Product_ID($product) {
$res=SQLSelectOne("select ID from products where TITLE='" . $product . "'");

$id=0;
if ($res['ID']) {
 $id=$res['ID'];
}  

return $id;
}

function Get_Category_ID($category) {
$res=SQLSelectOne("select ID from product_categories where TITLE='" . $category . "'");
$id=0;
if ($res['ID']) {
 $id=$res['ID'];
}
return $id;

}

?>
