<!--[* $Id: wikula_action_wantedpages.tpl 41 2008-10-09 18:29:16Z quan $ *]-->

<!--[if !empty($items)]-->
<div class="action_wantedpages">

  <!--[* linked pages *]-->
  <!--[if !empty($linkingto)]-->
  <!--[pnmodurl modname='wikula' tag=$linkingto|urlencode assign='url']-->
  <h5><!--[gt text='Pages linking to: %s' tag1=$url]-->:</h5>
  <ul>
    <!--[foreach from=$items item='item']-->
    <li><a href="<!--[pnmodurl modname='wikula' tag=$item|urlencode]-->"><!--[$item|pnvarprepfordisplay]--></a></li>
    <!--[/foreach]-->
  </ul>

  <!--[* wanted pages *]-->
  <!--[else]-->
  <h5><!--[gt text='Wanted Pages']--> (<!--[$items|@count]-->):</h5>
  <table>
    <thead>
     <tr>
       <th><!--[gt text='Source page']--></th>
       <th>&rArr;</th>
       <th><!--[gt text='Targetted inexistent page']--></th>
     </tr>
    </thead>
    <tbody>
      <!--[foreach from=$items item='item']-->
      <tr>
        <td><a href="<!--[pnmodurl modname='wikula' func='main' tag=$item.from_tag|urlencode]-->" title="<!--[$item.from_tag|pnvarprepfordisplay]-->"><!--[$item.from_tag|pnvarprepfordisplay]--></a></td>
        <td>&rArr;</td>
        <td><!--[pnmodapifunc modname='wikula' type='user' func='Link' tag=$item.to_tag]--></td>
      </tr>
      <!--[/foreach]-->
    </tbody>
  </table>
  <!--[/if]-->

</div>
<!--[/if]-->
