{* Datatype Template for Diff - ghstate.tpl *}

<div class="block">
{foreach $diff.changes as $change}
    {if eq( $change.status, 0 )}
        {$change.unchanged|wash|i18n( 'design/standard/content/datatype' )}
    {elseif eq( $change.status, 1 )}
        <del>{$change.removed|wash|i18n( 'design/standard/content/datatype' )}</del>
    {elseif eq( $change.status, 2 )}
        <ins>{$change.added|wash|i18n( 'design/standard/content/datatype' )}</ins>
    {/if}
{/foreach}
</div>