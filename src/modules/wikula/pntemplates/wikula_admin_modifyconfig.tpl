<!--[*  $Id: wikula_admin_modifyconfig.tpl 79 2008-11-22 20:15:15Z mateo $  *]-->
<!--[include file='wikula_admin_menu.tpl']-->

<!--[gt text='Settings' assign=templatetitle]-->
<div class="pn-admincontainer">
<div class="pn-adminpageicon"><!--[pnimg modname='core' src='configure.gif' set='icons/large' alt=$templatetitle]--></div>
<h2><!--[$templatetitle]--></h2>

<form class="pn-adminform" action="<!--[pnmodurl modname='wikula' type='admin' func='updateconfig']-->" method="post" enctype="application/x-www-form-urlencoded">
<div>
  <input type="hidden" name="authid" value="<!--[pnsecgenauthkey module='wikula']-->" />
  <div class="pn-adminformrow">
    <label for="wikula_root_page"><!--[gt text='Root Page']--></label>
    <input id="wikula_root_page" type="text" name="root_page" size="20" value="<!--[$root_page|pnvarprepfordisplay]-->" />
  </div>
  <div style="clear:both"></div>
  <div class="pn-adminformrow">
    <label for="wikula_warning"><!--[gt text='Receive an email when a new revision is saved']--></label>
    <input id="wikula_warning" name="savewarning" type="checkbox" value="1"<!--[if $savewarning eq 1]--> checked="checked"<!--[/if]--> />
  </div>
  <div style="clear:both"></div>
  <div class="pn-adminformrow">
    <label for="wikula_hidehistory"><!--[gt text='Do not include page history info box into wiki pages']--></label>
    <input id="wikula_hidehistory" name="hidehistory" type="checkbox" value="1"<!--[if $hidehistory eq 1]--> checked="checked"<!--[/if]--> />
  </div>
  <div style="clear:both"></div>
  <div class="pn-adminformrow">
    <label for="wikula_excludefromhistory"><!--[gt text='Page tags, separated with comma, that should always be displayed without page history info box']--></label>
    <input id="wikula_excludefromhistory" name="excludefromhistory" type="text" value="<!--[$excludefromhistory|pnvarprepfordisplay]-->" />
  </div>
  <div style="clear:both"></div>
  <div class="pn-adminformrow">
    <label for="wikula_hideeditbar"><!--[gt text='Do not show the editor help bar (wiki-edit) above an articled that gets edited']--></label>
    <input id="wikula_hideeditbar" name="hideeditbar" type="checkbox" value="1"<!--[if $hideeditbar eq 1]--> checked="checked"<!--[/if]--> />
  </div>
  <div style="clear:both"></div>
  <div class="pn-adminformrow">
    <label for="wikula_logreferers"><!--[gt text='Log Referers - Note: If the Zikula HTTPReferers module is available, it will use the exclusions setting.)']--></label>
    <input id="wikula_logreferers" name="logreferers" type="checkbox" value="1"<!--[if $logreferers eq 1]--> checked="checked"<!--[/if]--> />
  </div>
  <div style="clear:both"></div>
  <div class="pn-adminformrow">
    <label for="wikula_itemsperpage"><!--[gt text='Items per page']--></label>
    <input id="wikula_itemsperpage" type="text" name="itemsperpage" size="3" value="<!--[$itemsperpage|pnvarprepfordisplay]-->" />
  </div>
  <div style="clear:both"></div>

  <!--[pnmodcallhooks hookobject='module' hookaction='modifyconfig' hookid='wikula' module='wikula']-->

  <div style="clear:both"></div>
  <div class="pn-adminformrow pn-adminformbuttons">
  <input name="submit" type="submit" value="<!--[gt text='Update Configuration']-->" />
  </div>

  <div style="clear:both"></div>
</div>
</form>

</div>
