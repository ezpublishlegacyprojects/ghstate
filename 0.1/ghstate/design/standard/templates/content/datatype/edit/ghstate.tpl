{* Datatype Template for Content Editing - ghstate.tpl *}

{def 
	$states			= fetch( 'ghstate', 'state_list' )
    $class_content	= $attribute.class_content
	$state 			= $attribute.content.value
	$abbrev 		= ''}
	 
{if $attribute_base|not}
	{def $attribute_base = ContentObjectAttribute}
{/if}
<select id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" name="{$attribute_base}_state_{$attribute.id}[]" {if $class_content.multiple_choice}multiple="multiple"{/if}>
     {if $class_content.multiple_choice|not}
        <option value="">{'Not specified'|i18n( 'design/standard/content/datatype' )}</option>
     {/if}
{foreach $states as $key => $current_state}
     {set $abbrev = $current_state.Abbrev}
     {if $state|ne( '' )}
            <option {if is_set( $state.$abbrev )}selected="selected"{/if} value="{$abbrev}">{$current_state.Name|wash|i18n( 'design/standard/content/datatype' )}</option>
     {else}
            <option {if is_set( $class_content.default_states.$abbrev )}selected="selected"{/if} value="{$abbrev}">{$current_state.Name|wash|i18n( 'design/standard/content/datatype' )}</option>
     {/if}
{/foreach}
</select>
{undef  $states
		$class_content
		$state
		$abbrev
		$attribute_base}