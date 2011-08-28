<!--[* $Id: wikula_admin_deletepages.tpl 41 2008-10-09 18:29:16Z quan $ *]-->
<!--[include file='wikula_admin_menu.tpl']-->

<h4 style="text-align:center;"><!--[gt text="Page Administration"]--></h4>

<div id="wikkaadmin">
<!--[if $submit]-->
  <p class="center">Please confirm the suppression of these revisions.<br />
    The most recent revision left will be set as "Latest".<br />
    If there is no revisions left, the page will be completly deleted.</p>
  <form action="<!--[pnmodurl modname='wikula' type='admin' func='confirmdeletepage']-->" method="post" enctype="application/x-www-form-urlencoded">
  <div>
    <input type="hidden" name="tag"    value="<!--[$tag|urlencode]-->" />
    <input type="hidden" name="authid" value="<!--[pnsecgenauthkey module='wikula']-->" />
    <table style="width:100%;" summary="Choose revisions to delete">
    <thead>
      <tr>
        <th><!--[gt text="Page name"]--></th>
        <th><!--[gt text="Owner"]--></th>
        <th><!--[gt text="Latest author"]--></th>
        <th><!--[gt text="Last edit"]--></th>
        <th><!--[gt text="Note"]--></th>
      </tr>
    </thead>
    <tbody>
    <!--[foreach item='revision' from=$revisions]-->
    <!--[cycle values=", class=\"alt\"" assign='trclass']-->
      <tr<!--[$trclass]-->>
        <td>
          <a href="<!--[pnmodurl modname='wikula' tag=$revision.tag|urlencode]-->" title="<!--[$revision.tag]-->"><!--[$revision.tag]--></a>
          <input type="hidden" name="revids[<!--[$revision.id]-->]" value="on" />
        </td>
        <td><!--[if $item.owner neq '(Public)']--><a href="<!--[pnmodurl modname='wikula' tag='MyPages' uname=$revision.owner|urlencode]-->" title="<!--[$revision.owner|pnvarprepfordisplay]-->"><!--[/if]--><!--[$revision.owner]--><!--[if $revision.owner neq '(Public)']--></a><!--[/if]--></td>
        <td><a href="user.php?op=userinfo&amp;uname=<!--[$revision.user|pnvarprepfordisplay|urlencode]-->" title="<!--[$revision.user|pnvarprepfordisplay]-->"><!--[$revision.user]--></a></td>
        <td class="time"><!--[$revision.time]--></td>
        <td class="time" title="[<!--[$revision.note]-->]"><!--[$revision.note|default:"[Empty note]"]--></td>
      </tr>
    <!--[/foreach]-->
    </tbody>
    </table>
    <div class="center">
      <input type="submit" name="deleterevisions" value="Confirm" /> - <a href="<!--[pnmodurl modname='wikula' type='admin' func='pages']-->" title="<!--[gt text='Cancel']-->"><!--[gt text='Cancel']--></a>
    </div>
  </div>
  </form>
<!--[else]-->
  <p class="center">Delete selected revisions.</p>
  <form action="<!--[pnmodurl modname='wikula' type='admin' func='delete']-->" method="post" enctype="application/x-www-form-urlencoded">
  <div>
    <input type="hidden" name="authid" value="<!--[pnsecgenauthkey module="wikula"]-->" />
    <table style="width:100%;" summary="Choose revisions to delete">
    <thead>
      <tr>
        <th><!--[gt text="Page name"]--></th>
        <th><!--[gt text="Owner"]--></th>
        <th><!--[gt text="Latest author"]--></th>
        <th><!--[gt text="Last edit"]--></th>
        <th><!--[gt text="Note"]--></th>
        <th class="center"><!--[gt text="Actions"]--></th>
      </tr>
    </thead>
    <tbody>
    <!--[foreach item='revision' from=$revisions]-->
    <!--[cycle values=", class=\"alt\"" assign='trclass']-->
      <tr<!--[$trclass]-->>
        <td>
          <a href="<!--[pnmodurl modname='wikula' tag=$revision.tag|urlencode]-->" title="<!--[$revision.tag]-->"><!--[$revision.tag]--></a>
          <input type="hidden" name="tag" value="<!--[$revision.tag]-->" />
        </td>
        <td><!--[if $item.owner neq '(Public)']--><a href="<!--[pnmodurl modname='wikula' tag='MyPages' uname=$revision.owner|urlencode]-->" title="<!--[$revision.owner|pnvarprepfordisplay]-->"><!--[/if]--><!--[$revision.owner]--><!--[if $revision.owner neq '(Public)']--></a><!--[/if]--></td>
        <td><a href="user.php?op=userinfo&amp;uname=<!--[$revision.user|pnvarprepfordisplay|urlencode]-->" title="<!--[$revision.user|pnvarprepfordisplay]-->"><!--[$revision.user]--></a></td>
        <td class="time"><!--[$revision.time]--></td>
        <td class="time" title="[<!--[$revision.note]-->]"><!--[$revision.note|default:"[Empty note]"]--></td>
        <td class="center"><input type="checkbox" name="revids[<!--[$revision.id]-->]" title="Select <!--[$revision.tag]-->" /></td>
      </tr>
    <!--[/foreach]-->
    </tbody>
    </table>
    <div class="center">
      <input type="submit" name="submit" value="Select revisions" />
    </div>
  </div>
  </form>
<!--[/if]-->
</div>
