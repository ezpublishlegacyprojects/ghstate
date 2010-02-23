{* Datatype Template for Data Collection - ghstate.tpl *}

{def 
	$states			= fetch( 'ghstate', 'state_list' )
    $class_content	= $attribute.class_content
	$state 			= cond( is_set( $#collection_attributes[$attribute.id] ), $#collection_attributes[$attribute.id], $attribute.content.value )
	$abbrev 		= ''}
	
{if $attribute_base|not}
 {def $attribute_base = ContentObjectAttribute}
{/if}
<select name="{$attribute_base}_state_{$attribute.id}[]" {if $class_content.multiple_choice}multiple="multiple"{/if}>
    {if $class_content.multiple_choice|not}
        <option value="">{'Not specified'|i18n( 'design/standard/content/datatype' )}</option>
    {/if}
{foreach $states as $key => $current_state}
    {set $abbrev = $current_state.Abbrev}
    {if $state|ne( '' )}
            <option {if is_set( $state.$abbrev )}selected="selected"{/if} value="{$abbrev}">{$current_state.Name|i18n( 'design/standard/content/datatype' )}</option>
    {else}
            <option {if is_set( $class_content.default_states.$abbrev )}selected="selected"{/if} value="{$abbrev}">{$current_state.Name|i18n( 'design/standard/content/datatype' )}</option>
    {/if}
{/foreach}
</select>
{undef $states
		$state
		$class_content
		$abbrev
		$attribute_base}