<!--[* $Id: wikula_user_show.tpl 83 2008-12-17 04:04:58Z mateo $ *]-->
<!--[include file='wikula_user_menu.tpl' tag=$tag]-->

<div id="wikula">
  <!--[if $showpage.latest eq 'N']-->
    <!--[pnmodurl modname='wikula' tag=$showpage.tag assign='showpageurl']-->
    <!--[pnmodurl modname='wikula' tag=$showpage.tag func='revisions' assign='revisionsurl']-->
    <div class="revisioninfo">
      <h4><!--[gt text='Revision [%s]' tag1=$showpage.id]--></h4>
      <p><!--[gt text='This is a past revision of <a href="%2$s">%1$s</a> made by %3$s on <a class="datetime" href="%4$s">%5$s</a>' tag1=$showpage.tag tag2=$showpageurl tag3=$showpage.user tag4=$revisionsurl tag5=$time]--></p>
      <form class="left" action="<!--[pnmodurl modname='wikula' tag=$tag|urlencode]-->" method="post" enctype="application/x-www-form-urlencoded">
        <input type="hidden" value="<!--[$showpage.time|pnvarprepfordisplay]-->" name="time"/>
        <input type="hidden" value="1" name="raw"/>
        <input type="submit" value="<!--[gt text='Show Source']-->"/> 
      </form>
      <form action="<!--[pnmodurl modname='wikula' func='edit' tag=$tag|urlencode]-->" method="post" enctype="application/x-www-form-urlencoded">
        <input type="hidden" value="<!--[$showpage.id|pnvarprepfordisplay]-->" name="previous" />
        <input type="hidden" value="<!--[$showpage.time|pnvarprepfordisplay]-->" name="time"/>
        <input type="submit" value="<!--[gt text='Edit Revision']-->"/>
      </form>
      <div class="clear"></div>
    </div>
  <!--[/if]-->

  <div class="page">
	<!--[if $modvars.hidehistory neq true]-->
	  <!--[* invokes the pagehistory directly *]-->
	  <!--[pnmodapifunc modname='wikula' type='action' func='pageauthors' tag=$tag page=$page]-->
	<!--[/if]-->

    <!--[* $body is the variable containing the stuff *]-->
    <!--[$showpage.body|wakka|pnmodcallhooks:'wikula']-->
  </div>

  <div class="wiki_footer">
    <div style="text-align:left; padding:4px;">
      <form action="<!--[textsearchlink]-->" method="post" enctype="application/x-www-form-urlencoded">
      <div>
        <!--[if $canedit eq true]-->
        <a href="<!--[pnmodurl modname='wikula' func='edit' previous=$showpage.id|urlencode tag=$tag|urlencode]-->" title="<!--[gt text='Edit page']-->"><!--[gt text='Edit page']--></a>
        <span class="text_separator">::</span>
        <!--[/if]-->
        <a href="<!--[pnmodurl modname='wikula' func='history' tag=$tag|urlencode]-->" title="<!--[gt text='Page history']-->"><!--[gt text='Page history']--></a>
        <span class="text_separator">::</span>
        <!--[gt text='Revisions of "%tag%" Feed' tag=$tag assign='altrssfeed']-->
        <a href="<!--[pnmodurl modname='wikula' tag=$tag|urlencode time=$showpage.time|urlencode]-->" class="datetime"><!--[$showpage.time|date_format]--></a> <a href="<!--[pnmodurl modname='wikula' func='RevisionsXML' tag=$tag|urlencode theme='rss']-->" title="<!--[$altrssfeed]-->"><!--[pnimg src='rss.png' alt=$altrssfeed modname='wikula']--></a>
        <span class="text_separator">::</span>
        <!--[gt text='Owner']-->: <!--[$showpage.owner|userprofilelink]-->
        <span class="text_separator">::</span>
        <!--[pnuserloggedin assign='islogged']-->
        <!--[if $islogged]-->
        <a href="<!--[pnmodurl modname='wikula' func='referrers' tag=$tag|urlencode]-->" title="<!--[gt text='Referrers']-->"><!--[gt text='Referrers']--></a>
        <!--[/if]-->
      </div>
      </form>
    </div>
  </div>
</div>

<div class="clear"></div>
<div>
  <!--[* the next code is to display any hooks (e.g. comments, ratings) *]-->
  <!--[pnmodurl modname='wikula' func='display' tag=$tag assign='returnurl']-->
  <!--[pnmodcallhooks hookobject='item' hookaction='display' hookid=$tag module='wikula' returnurl=$returnurl]-->
</div>
<!--[*include file='wikula_user_footer.tpl'*]-->
