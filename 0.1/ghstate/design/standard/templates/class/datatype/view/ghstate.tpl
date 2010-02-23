{* Datatype Template for Class Viewing - ghstate.tpl *}

<div class="block">
    <label>{'Multiple choice'|i18n( 'design/standard/class/datatype' )}:</label>
    <p>{$class_attribute.content.multiple_choice|choose( 'Unchecked'|i18n( 'design/standard/class/datatype' ), 'Checked'|i18n( 'design/standard/class/datatype' ) )}</p>
</div>

<div class="block">
    <label>{'Default selection'|i18n( 'design/standard/class/datatype' )}:</label>
    {foreach $class_attribute.content.default_states as $state}
         <p>{$state.Name|i18n( 'design/standard/class/datatype' )}</p>
    {/foreach}
</div>

