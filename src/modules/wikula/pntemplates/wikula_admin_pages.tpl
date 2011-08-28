<!--[* $Id: wikula_admin_pages.tpl 79 2008-11-22 20:15:15Z mateo $ *]-->
<!--[include file='wikula_admin_menu.tpl']-->

<!--[gt text='Page Index' assign=templatetitle]-->
<div class="pn-admincontainer">
<div class="pn-adminpageicon"><!--[pnimg modname='core' src='bell.gif' set='icons/large' alt=$templatetitle]--></div>
<h2><!--[$templatetitle]--></h2>

<div>
<!--[if $pagelist]-->
  <strong><a href="<!--[pnmodurl modname='wikula' type='admin' func='pages']-->" title="<!--[gt text='All']-->"><!--[gt text='All']--></a></strong>&nbsp;&nbsp;
  <!--[foreach item='letter' from=$headerletters]-->
    <strong><a href="<!--[pnmodurl modname='wikula' type='admin' func='pages' letter=$letter]-->" title="<!--[$letter]-->"><!--[$letter]--></a></strong>
  <!--[/foreach]-->
  <br />
  <!--[assign var='currentchar' value='']-->


  <!--[foreach item='letter' from=$pagelist key='firstchar']-->
    <!--[if $currentchar neq $firstchar]-->
      <!--[assign var='currentchar' value=$firstchar]-->
      <br /><strong><!--[$firstchar]--></strong><br />
    <!--[/if]-->
    <!--[foreach item='page' from=$letter]-->
      &nbsp;&nbsp;&nbsp;<a href="<!--[pnmodurl modname='wikula' tag=$page.tag|urlencode]-->" title="<!--[$page.tag]-->"><!--[$page.tag]--></a>
      <!--[if $page.owner neq '(Public)' and $page.owner neq '']-->
        <!--[if $page.owner eq $username]-->
          *
        <!--[else]-->
          . . . . <!--[gt text='Owner:']--> <!--[$page.owner]-->
        <!--[/if]-->
      <!--[/if]-->
      <br />
    <!--[/foreach]-->
  <!--[/foreach]-->
  <br />

  <!--[if $userownspages]--><!--[gt text='Items marked with a * indicate pages that you own.']--><br /><!--[/if]-->
<!--[else]-->
  <span class="error"><!--[gt text='No page found']--></span>
<!--[/if]-->
</div>

</div>
