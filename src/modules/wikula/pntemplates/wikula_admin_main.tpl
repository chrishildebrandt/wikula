<!--[* $Id: wikula_admin_main.tpl 79 2008-11-22 20:15:15Z mateo $ *]-->
<!--[include file='wikula_admin_menu.tpl']-->

<!--[gt text='Wikula Statistics' assign=templatetitle]-->
<div class="pn-admincontainer">
<div class="pn-adminpageicon"><!--[pnimg modname='core' src='3d.gif' set='icons/large' alt=$templatetitle]--></div>
<h2><!--[$templatetitle]--></h2>

<dl>
	<dt><!--[gt text='Pages']-->:</dt>
	<dd><!--[$pagecount]--></dd>
<dt><!--[gt text='Owners']-->:</dt>
	<dd><!--[gt text='Total']--> (<!--[$ownerscount]-->)
		<ul>
		<!--[foreach item='owner' from=$owners]-->
			<li><!--[$owner]--></li>
		<!--[/foreach]-->
		</ul>
	</dd>
</dl>

</div>
