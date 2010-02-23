{if $attribute.has_content}
   {if is_array( $attribute.content.value )}
       {foreach $attribute.content.value as $state}
           {$state.Name|i18n( 'design/standard/content/datatype' )|wash}
       {/foreach}
   {else}
       {$attribute.content.value|i18n( 'design/standard/content/datatype' )|wash}
   {/if}
{else}
   {'Not specified'|i18n( 'design/standard/content/datatype' )}
{/if}