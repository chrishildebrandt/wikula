<!--[* $Id: wikula_action_lastezcomments.tpl 41 2008-10-09 18:29:16Z quan $ *]-->

<!--[if $items]-->
<!--[assign var='olddate' value='']-->
  <!--[foreach item='item' from=$items]-->
    <!--[if $item.titledate neq $olddate]-->
      <br />
      <strong><!--[$item.titledate]--></strong><br /><br />
      <!--[assign var='olddate' value=$item.titledate]-->
    <!--[/if]-->
    <!--[pnusergetvar name='uname' uid=$item.uid assign='uname']-->
    &nbsp;&nbsp;&nbsp; <a href="<!--[pnmodurl modname='wikula' tag=$item.objectid|urlencode]-->" title="<!--[$item.objectid]-->"><!--[$item.objectid]--></a>,
    <!--[gt text='Comment by']--> <!--[$uname|userprofilelink]--> (<!--[$item.date]-->)<br />
    &nbsp;&nbsp;&nbsp; <em><!--[$item.comment|pnvarprepfordisplay]--></em>
    <br /><br />
  <!--[/foreach]-->
<!--[else]-->
  <em><!--[gt text='No comments yet...']--></em>
<!--[/if]-->
