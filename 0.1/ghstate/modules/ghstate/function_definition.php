<?php
$FunctionList = array();
 
$FunctionList['state_list'] = array( 'name' => 'state_list', 
                               'operation_types' => array( 'read' ), 
                               'call_method' => array('include_file' =>'extension/ghstate/modules/ghfunctioncollection.php', 
                                                      'class' => 'GHStateFunctionCollection', 
                                                      'method' => 'fetchGHStates' ), 
                               'parameter_type' => 'standard', 
                               'parameters' => array() 
                        );

?>
