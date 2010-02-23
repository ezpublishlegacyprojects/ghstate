<?php
/*!

  The list of States is fetched from content.ini.
  State is stored as text string.
*/

//require_once( 'kernel/common/i18n.php' );

class GHStateType extends eZDataType
{
    const DATA_TYPE_STRING = 'ghstate';

    const DEFAULT_LIST_FIELD = 'data_text5';

    const MULTIPLE_CHOICE_FIELD = 'data_int1';
	
    //function __construct()
	function GHStateType()
    {
        //parent::__construct( self::DATA_TYPE_STRING, ezi18n( 'extension/ghstates/datatypes', "States", 'Datatype name' ),
		$this->eZDataType( self::DATA_TYPE_STRING, ezi18n( 'extension/ghstate/datatypes', "States", 'Datatype name' ),
		array( 'serialize_supported' => true,
			'object_serialize_map' => array( 'data_text' => 'state' ) ) );

    }

    /*!
     Fetches state list from ini.
    */
    static function fetchStateList()
    {
        if ( isset( $GLOBALS['StateList'] ) )
            return $GLOBALS['StateList'];

        $ini = eZINI::instance( 'ghstate.ini' );
        $states = $ini->getNamedArray();
        //GHStateType::fetchTranslatedNames( $states );
        $GLOBALS['StateList'] = $states;

        return $states;
    }

    /**
     * Sort callback used by fetchTranslatedNames to compare two state arrays
     *
     * @param array $a State 1
     * @param array $b State 2
     * @return bool
     */
    protected static function compareStateNames( $a, $b )
    {
        return strcoll( $a["Name"], $b["Name"] );
    }

    /*!
      Fetches state by \a $fetchBy.
      if \a $fetchBy is false state name will be used.
    */
    static function fetchState( $value, $fetchBy = false )
    {
        $fetchBy = !$fetchBy ? 'Name' : $fetchBy;

        $allStates = GHStateType::fetchStateList();
        $result = false;
        if ( $fetchBy == 'Abbrev' and isset( $allStates[strtoupper( $value )] ) )
        {
            $result = $allStates[$value];
            return $result;
        }

        foreach ( $allStates as $state )
        {
            if ( isset( $state[$fetchBy] ) and $state[$fetchBy] == $value )
            {
                $result = $state;
                break;
            }
        }
		
        return $result;
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $classAttributeID = $classAttribute->attribute( 'id' );
        $content = $classAttribute->content();

        if ( $http->hasPostVariable( $base . '_ghstate_multiple_choice_value_' . $classAttribute->attribute( 'id' ) . '_exists' ) )
        {
             $content['multiple_choice'] = $http->hasPostVariable( $base . "_ghstate_ismultiple_value_" . $classAttributeID ) ? 1 : 0;
        }

        if ( $http->hasPostVariable( $base . '_ghstate_default_selection_value_' . $classAttribute->attribute( 'id' ) . '_exists' ) )
        {
            if ( $http->hasPostVariable( $base . "_ghstate_default_state_list_". $classAttributeID ) )
            {
                $defaultValues = $http->postVariable( $base . "_ghstate_default_state_list_". $classAttributeID );
                $defaultList = array();
                foreach ( $defaultValues as $abbrev )
                {
                    if ( trim( $abbrev ) == '' )
                        continue;
                    // Fetch ezstate by abbrev code (as reserved in iso-3166 code list)
                    $GHState = GHStateType::fetchState( $abbrev, 'Abbrev' );
                    if ( $GHState )
                        $defaultList[$abbrev] = $GHState;
                }
                $content['default_states'] = $defaultList;
            }
            else
            {
                $content['default_states'] = array();
            }
        }
        $classAttribute->setContent( $content );
        $classAttribute->store();
        return true;
    }

    function preStoreClassAttribute( $classAttribute, $version )
    {
        $content = $classAttribute->content();
        return GHStateType::storeClassAttributeContent( $classAttribute, $content );
    }

    function storeClassAttributeContent( $classAttribute, $content )
    {
        if ( is_array( $content ) )
        {
            $multipleChoice = $content['multiple_choice'];
            $defaultStateList = $content['default_states'];
            $defaultState = implode( ',', array_keys( $defaultStateList ) );

            $classAttribute->setAttribute( self::DEFAULT_LIST_FIELD, $defaultState );
            $classAttribute->setAttribute( self::MULTIPLE_CHOICE_FIELD, $multipleChoice );
        }
        return false;
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $dataText = $originalContentObjectAttribute->content();
            $contentObjectAttribute->setContent( $dataText );
        }
        else
        {
            $default = array( 'value' => array() );
            $contentObjectAttribute->setContent( $default );
        }
    }

    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( !$contentObjectAttribute->validateIsRequired() )
            return eZInputValidator::STATE_ACCEPTED;

        if ( $http->hasPostVariable( $base . '_state_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_state_' . $contentObjectAttribute->attribute( 'id' ) );

            if ( count( $data ) > 0 and $data[0] != '' )
                return eZInputValidator::STATE_ACCEPTED;
        }

        $contentObjectAttribute->setValidationError( ezi18n( 'extension/ghstate/datatypes',
                                                             'Input required.' ) );
        return eZInputValidator::STATE_INVALID;
    }

    function validateCollectionAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( !$contentObjectAttribute->validateIsRequired() )
            return eZInputValidator::STATE_ACCEPTED;

        if ( $http->hasPostVariable( $base . '_state_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_state_' . $contentObjectAttribute->attribute( 'id' ) );

            if ( count( $data ) > 0 and $data[0] != '' )
                return eZInputValidator::STATE_ACCEPTED;
        }

        $contentObjectAttribute->setValidationError( ezi18n( 'extension/ghstate/datatypes',
                                                             'Input required.' ) );
        return eZInputValidator::STATE_INVALID;
    }

    /*!
     Fetches the http post var and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
		
        if ( $http->hasPostVariable( $base . '_state_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_state_' . $contentObjectAttribute->attribute( 'id' ) );
            $defaultList = array();
            if ( is_array( $data ) )
            {
                foreach ( $data as $abbrev )
                {
                    if ( trim( $abbrev ) == '' )
                        continue;

                    $GHState = GHStateType::fetchState( $abbrev, 'Abbrev' );
                    if ( $GHState )
                        $defaultList[$abbrev] = $GHState;
                }
            }
            else
            {
                $states = GHStateType::fetchStateList();
				
                foreach ( $states as $state )
                {
                    if ( $state['Name'] == $data )
                    {
                        $defaultList[$state['Abbrev']] = $state['Name'];
                    }
                }
            }
            $content = array( 'value' => $defaultList );

            $contentObjectAttribute->setContent( $content );
        }
        else
        {
            $content = array( 'value' => array() );
            $contentObjectAttribute->setContent( $content );
        }

        return true;
    }

    /*!
     Fetches the http post variables for collected information
    */
    function fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_state_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $dataText = $http->postVariable( $base . "_state_" . $contentObjectAttribute->attribute( "id" ) );

            $value = implode( ',', $dataText );
			
            $collectionAttribute->setAttribute( 'data_text', $value );
            return true;
        }
        return false;
    }

    function storeObjectAttribute( $contentObjectAttribute )
    {
        $content = $contentObjectAttribute->content();

        $valueArray = $content['value'];
        $value = is_array( $valueArray ) ? implode( ',', array_keys( $valueArray ) ) : $valueArray;

        $contentObjectAttribute->setAttribute( "data_text", $value );
    }

    /*!
     Simple string insertion is supported.
    */
    function isSimpleStringInsertionSupported()
    {
        return true;
    }

    function insertSimpleString( $object, $objectVersion, $objectLanguage,
                                 $objectAttribute, $string,
                                 &$result )
    {
        $result = array( 'errors' => array(),
                         'require_storage' => true );
        $content = array( 'value' => $string );
        $objectAttribute->setContent( $content );
        return true;
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $value = $contentObjectAttribute->attribute( 'data_text' );

        $stateList = explode( ',', $value );
        $resultList = array();
        foreach ( $stateList as $abbrev )
        {
            $GHState = GHStateType::fetchState( $abbrev, 'Abbrev' );
            $resultList[$abbrev] = $GHState ? $GHState : '';
        }

        $content = array( 'value' => $resultList );
        return $content;
    }

    function classAttributeContent( $classAttribute )
    {
        $defaultState = $classAttribute->attribute( self::DEFAULT_LIST_FIELD );
        $multipleChoice = $classAttribute->attribute( self::MULTIPLE_CHOICE_FIELD );
        $defaultStateList = explode( ',', $defaultState );
        $resultList = array();
        foreach ( $defaultStateList as $abbrev )
        {
            $GHState = GHStateType::fetchState( $abbrev, 'Abbrev' );
            if ( $GHState )
                $resultList[$abbrev] = $GHState;
        }
        $content = array( 'default_states' => $resultList,
                          'multiple_choice' => $multipleChoice );

        return $content;
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        $content = $contentObjectAttribute->content();
        if ( is_array( $content['value'] ) )
        {
            $imploded = '';
            foreach ( $content['value'] as $state )
            {
                $stateName = $state['Name'];
                if ( $imploded == '' )
                    $imploded = $stateName;
                else
                    $imploded .= ',' . $stateName;
            }
            $content['value'] = $imploded;
        }
        return $content['value'];
    }

    /*!
     \return string representation of an contentobjectattribute data for simplified export
    */
    function toString( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    function fromString( $contentObjectAttribute, $string )
    {
        return $contentObjectAttribute->setAttribute( 'data_text', $string );
    }

    /*!
     Returns the state for use as a title
    */
    function title( $contentObjectAttribute, $name = null )
    {
        $content = $contentObjectAttribute->content();
        if ( is_array( $content['value'] ) )
        {
            $imploded = '';
            foreach ( $content['value'] as $state )
            {
                $stateName = $state['Name'];
                if ( $imploded == '' )
                    $imploded = $stateName;
                else
                    $imploded .= ',' . $stateName;
            }
            $content['value'] = $imploded;
        }
        return $content['value'];
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $content = $contentObjectAttribute->content();
        $result = ( ( !is_array( $content['value'] ) and trim( $content['value'] ) != '' ) or ( is_array( $content['value'] ) and count( $content['value'] ) > 0 ) );
        return $result;
    }

    function isIndexable()
    {
        return true;
    }

    function isInformationCollector()
    {
        return true;
    }

    function sortKey( $contentObjectAttribute )
    {
        $trans = eZCharTransform::instance();
        $content = $contentObjectAttribute->content();
        if ( is_array( $content['value'] ) )
        {
            $imploded = '';
            foreach ( $content['value'] as $state )
            {
                $stateName = $state['Name'];

                if ( $imploded == '' )
                    $imploded = $stateName;
                else
                    $imploded .= ',' . $stateName;
            }
            $content['value'] = $imploded;
        }
        return $trans->transformByGroup( $content['value'], 'lowercase' );
    }

    function sortKeyType()
    {
        return 'string';
    }

    function diff( $old, $new, $options = false )
    {
        return null;
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }
}

eZDataType::register( GHStateType::DATA_TYPE_STRING, 'ghstatetype' );

?>
