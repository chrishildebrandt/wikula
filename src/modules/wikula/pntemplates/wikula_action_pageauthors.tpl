<!--[* $Id: wikula_action_pageauthors.tpl 58 2008-11-14 21:10:52Z arg $ *]-->
<div id="wiki_authorbox">
  <div class="wiki_authorboxelement">
    <h4><!--[gt text='Site creator']-->:</h4>

    <!--[pnusergetvar uid=$first_writer.uid name='_YOURAVATAR' assign='avatar']-->

    <!--[if $avatar eq '' OR $avatar eq 'blank.gif']-->
      <!--[pnimg modname='wikula' src='avatar_male.gif' width='50']-->
    <!--[else]-->
      <img src="images/avatar/<!--[$avatar|pnvarprepfordisplay]-->" width="50" />
    <!--[/if]-->
    <!--[$first_writer.uname|userprofilelink]--><br />
    <!--[$first_writer.time|date_format]-->
    <br class="clear" />
  </div>

  <div class="wiki_authorboxelement">
    <h4><!--[gt text='Last authors']-->:</h4>
    <!--[foreach from=$history item='author']-->
      <!--[pnusergetvar uid=$author.uid name='_YOURAVATAR' assign='avatar']-->
    
      <!--[if $avatar eq '' OR $avatar eq 'blank.gif']-->
        <!--[pnimg modname='wikula' src='avatar_male.gif' width='25']-->
      <!--[else]-->
        <img src="images/avatar/<!--[$avatar|pnvarprepfordisplay]-->" width="25" />
      <!--[/if]-->
      <!--[$author.uname|userprofilelink]--><br />
      <!--[$author.time|date_format]-->
      <br style="clear: both" />
    <!--[/foreach]-->
  </div>

  <a href="<!--[pnmodurl modname='wikula' func='history' tag=$tag|urlencode]-->" title="<!--[gt text='Page history']-->"><!--[gt text='Page history']--></a>
</div>
