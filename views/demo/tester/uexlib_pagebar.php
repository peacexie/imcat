pagebar ::: test:
<br />
{tag:demo2=[Page][modid,demo][keywd,e,title+mname][limit,3]}
{:row}
<li><a href="{surl("demo.$t_did")}">{=$t_title} --- {stime($t_atime)}</a></li>
{/row}
{php echo $_cbase['page']['bar']; }
{/tag:demo2}