{* Datatype Template for Class Editing - ghstate.tpl *}

{def 
	$content 			= $class_attribute.content
    $state_list 		= $content.default_states
    $all_state_list 	= fetch( 'ghstate', 'state_list' )
	$abbrev 			= ''}
	 
<div class="block">
    <input type="checkbox" name="ContentClass_ghstates_ismultiple_value_{$class_attribute.id}" {if $content.multiple_choice}checked="checked"{/if}/>
    <b>{'Multiple choice'|i18n( 'design/standard/class/datatype' )}</b>
    <input type="hidden" name="ContentClass_ghstates_multiple_choice_value_{$class_attribute.id}_exists" value="1" />
</div>

<div class="block">
    <label>{'Default selection'|i18n( 'design/standard/class/datatype' )}:</label>
    <select id="default_selection_{$class_attribute.id}" name="ContentClass_ghstates_default_state_list_{$class_attribute.id}[]" multiple="multiple" title="{'Select which states by default'|i18n( 'design/standard/class/datatype' )}">
    {foreach $all_state_list as $state}
         {set $abbrev = $state.Abbrev}
         <option value="{$abbrev|wash}" {if is_set( $state_list.$abbrev )}selected="selected"{/if}>{$state.Name|i18n( 'design/standard/class/datatype' )}</option>
    {/foreach}
    </select>
    <input type="hidden" name="ContentClass_ghstates_default_selection_value_{$class_attribute.id}_exists" value="1" />
</div>
{undef  $content
		$state_list
		$all_state_list}