<?php
class GHStateFunctionCollection
{ 
    function GHStateFunctionCollection() 
    {
    }
 
    // is opened by('modul1', 'list', hash('as_object', $bool )) 
    // fetch 
    function fetchGHStates( ) 
    { 
		return array( 'result' => GHStateType::fetchStateList() ); 
    }
}
?>
