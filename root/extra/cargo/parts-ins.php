
<style type='text/css'>
#umod_atts .txt{ min-width:360px; }
.tblist .uwrap td{ word-break:break-all; white-space:normal; line-height:120%; max-width:40%; }
#fmPart input.txt, #fmPart textarea{ width:100%; }
input.rdcb {
    margin:0px 0.2rem;
}
</style>
<table border="1" class=" tblist" id='partLists'>
  <tbody>
    <tr>
      <th class="hidden">ID</th>
      <th>配件名称</th>
      <th>规格</th>
      <th>价格</th>
      <th>排序</th>
      <th>数量</th>
      <th>操作</th>
    </tr>
    <tr class="tc" id="part_acts">
      <td colspan="7">
        <a onclick="partOpen()">增加</a>
        #
        <a onclick="partPick()">选取</a>
      </td>
    </tr>
  </tbody>
</table>

<div id='fmPartOut' style="display:none;">
    <table id='fmPart' class="table tbdata">
      <input name="pt_kid" id="pt_kid" type="hidden" value="">
      <input name="pt_did" id="pt_did" type="hidden" value="0">
      <tbody>
        <tr>
          <td class="tc">名称</td>
          <td class="tl"><input name="pt_title" id="pt_title" type="text" class="txt"></td>
        </tr>
        <tr>
          <td class="tc">规格</td>
          <td class="tl"><input name="pt_guige" id="pt_guige" type="text" class="txt"></td>
        </tr>
        <tr>
          <td class="tc">价格</td>
          <td class="tl"><input name="pt_price" id="pt_price" type="text" class="txt" value="0"></td>
        </tr>
        <tr>
          <td class="tc">排序</td>
          <td class="tl"><input name="pt_top" id="pt_top" type="text" class="txt" value="66"></td>
        </tr>
        <tr>
          <td class="tc">数量</td>
          <td class="tl"><input name="pt_cnt" id="pt_cnt" type="text" class="txt" value="1" placeholder="0为可选"><br>0为可选</td>
        </tr>
        <tr>
          <td class="tc">属性</td>
          <td class="tl"><textarea name="pt_attcom" id="pt_attcom" type="text" rows="5" class="txt"></textarea></td>
        </tr>
        <tr>
          <td class="tc" colspan="2"><input name="bsend" type="button" class="btn" onclick="partSave()" value="保存"></td>
        </tr>
      </tbody>
    </table>
</div>

