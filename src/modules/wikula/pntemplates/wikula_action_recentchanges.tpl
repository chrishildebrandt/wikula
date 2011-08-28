<!--[* $Id: wikula_action_recentchanges.tpl 83 2008-12-17 04:04:58Z mateo $ *]-->

<div class="action_recentchanges">

<h3><!--[gt text='Recent Changes']--></h3>
<p style="float: right;">
  <a href="<!--[pnmodurl modname='wikula' func='recentchangesxml' theme='rss']-->" title="<!--[gt text='Recent Changes']-->"><!--[pnimg modname='wikula' src='rss.png' __title='Recent changes Feed'  __alt='RSS']--></a>
</p>

<!--[if $pagelist]-->
  <!--[assign var='currentdate' value='']-->
  <!--[foreach from=$pagelist key='date' item='pages']-->
    <!--[if $currentdate neq $date]-->
      <!--[if !empty($currentdate)]--><br /><!--[/if]-->
      <!--[assign var='currentdate' value=$date]-->
      <strong><!--[$date]--></strong>
      <br />
    <!--[/if]-->
    <span class="recentchanges">
    <!--[foreach from=$pages item='page']-->
      &nbsp;&nbsp;&nbsp;&nbsp;
      (<a href="<!--[pnmodurl modname='wikula' tag=$page.tag|urlencode time=$page.time|urlencode]-->" title="<!--[gt text='Recent Changes Revisions']-->"><!--[$page.timeformatted]--></a>)
      [<a href="<!--[pnmodurl modname='wikula' func='history' tag=$page.tag|urlencode]-->" title="<!--[$page.tag]--> <!--[gt text='History']-->"><!--[gt text='History']--></a>] -
      <a href="<!--[pnmodurl modname='wikula' tag=$page.tag|urlencode]-->" title="<!--[$page.tag]-->"><!--[$page.tag]--></a>
      &rArr; <!--[$page.user]--> <span class="pagenote">[ <!--[$page.note]--> ]</span>
      <br />
    <!--[/foreach]-->
    </span>
  <!--[foreachelse]-->
    <!--[gt text='There are no recent changes']-->
  <!--[/foreach]-->
<!--[/if]-->

</div>
